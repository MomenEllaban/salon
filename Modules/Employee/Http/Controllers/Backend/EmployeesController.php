<?php

namespace Modules\Employee\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingService;
use Modules\Commission\Models\EmployeeCommission;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Modules\Employee\Http\Requests\EmployeeRequest;
use Modules\Employee\Models\BranchEmployee;
use Modules\Employee\Models\EmployeeRating;
use Modules\Service\Models\ServiceEmployee;
use Yajra\DataTables\DataTables;

class EmployeesController extends Controller
{
    // use Authorizable;

    protected string $exportClass = '\App\Exports\EmployeeExport';

    public function __construct()
    {
        // Page Title
        $this->module_title = 'employee.title';

        // module name
        $this->module_name = 'employees';

        // directory path of the module
        $this->module_path = 'employee::backend';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'fa-regular fa-sun',
            'module_name' => $this->module_name,
            'module_path' => $this->module_path,
        ]);
        $this->middleware(['permission:view_staff'])->only('index');
        $this->middleware(['permission:edit_staff'])->only('edit', 'update');
        $this->middleware(['permission:add_staff'])->only('store');
        $this->middleware(['permission:delete_staff'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $module_action = 'List';
        $columns = CustomFieldGroup::columnJsonValues(new User());
        $customefield = CustomField::exportCustomFields(new User());

        $export_import = true;
        $export_columns = [
            [
                'value' => 'first_name',
                'text' => 'First Name',
            ],
            [
                'value' => 'last_name',
                'text' => 'Last Name',
            ],
            [
                'value' => 'email',
                'text' => 'E-mail',
            ],
            [
                'value' => 'branches',
                'text' => 'Branch',
            ],
            [
                'value' => 'role',
                'text' => 'Role',
            ],
            [
                'value' => 'varification_status',
                'text' => 'Verification Status',
            ],
            [
                'value' => 'is_banned',
                'text' => 'Banned',
            ],
            [
                'value' => 'status',
                'text' => 'Status',
            ],
        ];
        $export_url = route('backend.employees.export');

        return view('employee::backend.employees.index', compact('module_action', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $term = trim($request->q);

        $query_data = Branch::where('status', 1)
            ->where(function ($q) use ($term) {
                if (!empty($term)) {
                    $q->orWhere('name', 'LIKE', "%$term%");
                }
            })->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'name' => $row->name,

            ];
        }

        return response()->json($data);
    }

    public function employee_list(Request $request)
    {
        $term = trim($request->q);

        $branchId = $request->branch_id;

        $role = $request->role;

        // Need To Add Role Base
        $query_data = User::role('employee')->with('media', 'branches')->where(function ($q) use ($term) {
            if (!empty($term)) {
                $q->orWhere('first_name', 'LIKE', "%$term%");
                $q->orWhere('last_name', 'LIKE', "%$term%");
            }
        });

        if ($request->show_in_calender) {
            $query_data->CalenderResource();
        }

        if (!empty($role)) {
            $query_data->role($role);
        }

        if (isset($branchId) && !empty($branchId)) {
            $query_data->whereHas('branches', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $query_data = $query_data->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'name' => $row->full_name,
                'avatar' => $row->profile_image,
            ];
        }

        return response()->json($data);
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                // Need To Add Role Base
                $employee = User::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_employee_update');
                break;

            case 'delete':

                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                ServiceEmployee::whereIn('employee_id', $ids)->delete();
                BranchEmployee::whereIn('employee_id', $ids)->delete();
                User::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_employee_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $module_name = $this->module_name;
        $query = User::select('users.*')->role(['employee', 'manager'])->branch()->with('media', 'mainBranch');

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }

        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->addColumn('action', function ($data) {
                return view('employee::backend.employees.action_column', compact('data'));
            })
            // ->editColumn('image', function ($data) {
            //     return "<img src='".$data->profile_image."'class='avatar avatar-50 rounded-pill'>";
            // })
            ->addColumn('employee_id', function ($data) {
                $Profile_image = $data->profile_image ?? default_user_avatar();
                $name = $data->full_name ?? default_user_name();
                $email = $data->email ?? '--';
                return view('booking::backend.bookings.datatable.employee_id', compact('Profile_image', 'name', 'email'));
            })

            ->orderColumn('employee_id', function ($query, $order) {
                $query->orderBy('users.first_name', $order)
                    ->orderBy('users.last_name', $order);
            }, 1)

            ->filterColumn('employee_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('last_name', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%');
                    });
                }
            })

            ->editColumn('email_verified_at', function ($data) {
                $checked = '';
                if ($data->email_verified_at) {
                    return '<span class="badge bg-soft-success"><i class="fa-solid fa-envelope" style="margin-right: 2px"></i>' . __('employee.msg_verified') . ' </span>';
                }

                return '<button  type="button" data-url="' . route('backend.employees.verify-employee', $data->id) . '" data-token="' . csrf_token() . '" class="button-status-change btn btn-text-danger btn-sm  bg-soft-danger"  id="datatable-row-' . $data->id . '"  name="is_verify" value="' . $data->id . '" ' . $checked . '>Verify</button>';
            })
            ->editColumn('service', function ($data) {
                return " <button type='button' data-custom-module='{$data->id}' data-assign-module='{$data->id}' data-assign-target='#package-service-form' data-custom-event='custom_form'  data-assign-event='package_service_form' class='btn btn-primary btn-sm rounded'>{$data->services->count()}</button>";
            })
            ->orderColumn('service', function ($query, $direction) {
                $query->select('packages.*')
                    ->leftJoin('package_services', 'package_services.package_id', '=', 'packages.id')
                    ->selectRaw('COUNT(package_services.id) as service_count')
                    ->groupBy('packages.id');
                $query->orderBy('service_count', $direction);
            })
            ->editColumn('is_manager', function ($data) {
                if ($data->is_manager) {
                    return '<span class="badge bg-soft-danger">Manager</span>';
                }

                return '<span class="badge bg-soft-info">Staff</span>';
            })
            ->addColumn('branch_id', function ($data) {
                return optional($data->mainBranch)->pluck('name')->toArray() ?? '-';
            })
            ->editColumn('is_banned', function ($data) {
                $checked = '';
                if ($data->is_banned) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.employees.block-employee', $data->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $data->id . '"  name="is_banned" value="' . $data->id . '" ' . $checked . '>
                    </div>
                 ';
            })

            ->editColumn('status', function ($data) {
                $checked = '';
                if ($data->status) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.employees.update_status', $data->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $data->id . '"  name="status" value="' . $data->id . '" ' . $checked . '>
                    </div>
                ';
            })

            ->editColumn('updated_at', function ($data) {
                $module_name = $this->module_name;

                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }

            })
            ->rawColumns(['service'])
            ->orderColumns(['id'], '-:column $1');

        // Custom Fields For export
        $customFieldColumns = CustomField::customFieldData($datatable, User::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['employee_id', 'action', 'service', 'status', 'is_banned', 'email_verified_at', 'check', 'image', 'is_manager'], $customFieldColumns))
            ->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(EmployeeRequest $request)
    {
        $data = $request->all();

        $data['password'] = Hash::make($data['password']);

        if ($request->confirmed == 1) {
            $data = \Arr::add($data, 'email_verified_at', Carbon::now());
        } else {
            $data = \Arr::add($data, 'email_verified_at', null);
        }

        $data = User::create($data);

        $profile = [
            'about_self' => $request->about_self,
            'expert' => $request->expert,
            'facebook_link' => $request->facebook_link,
            'instagram_link' => $request->instagram_link,
            'twitter_link' => $request->twitter_link,
            'dribbble_link' => $request->dribbble_link,
        ];

        $data->profile()->updateOrCreate([], $profile);

        if ($request->custom_fields_data) {
            $data->updateCustomFieldData(json_decode($request->custom_fields_data));
        }

        if ($request->has('profile_image')) {
            $request->file('profile_image');

            storeMediaFile($data, $request->file('profile_image'), 'profile_image');
        }

        $employee_id = $data['id'];

        $roles = ['employee'];

        if ($request->is_manager) {
            array_push($roles, 'manager');
            if ($request->has('branch_id')) {
                $branch = Branch::where('id', $request->branch_id)->first();
                $branch->update(['manager_id' => $employee_id]);
            }
        }

        $data->syncRoles($roles);

        \Artisan::call('cache:clear');

        if ($request->has('branch_id')) {
            $branch_data = [
                'employee_id' => $employee_id,
                'branch_id' => $request->branch_id,
            ];
            BranchEmployee::create($branch_data);
        }

        if ($request->has('service_id')) {
            if ($request->service_id !== null) {
                $services = explode(',', $request->service_id);

                foreach ($services as $value) {
                    $service_data = [

                        'employee_id' => $employee_id,
                        'service_id' => $value,

                    ];
                    ServiceEmployee::create($service_data);
                }
            }
        }
        if (isset($request->commission_id) && $request->has('commission_id')) {
            $commission_data = [
                'employee_id' => $employee_id,
                'commission_id' => $request->commission_id,
            ];

            EmployeeCommission::updateOrCreate($commission_data, $commission_data);
        }

        $message = __('messages.create_form', ['form' => __('employee.singular_title_manager')]);

        return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $module_action = 'Show';

        $data = User::role('employee')->findOrFail($id);

        return view('employee::backend.employees.show', compact('module_action', "$data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = User::role('employee')->with('branches', 'services', 'commissions', 'profile')->findOrFail($id);
        if (!is_null($data)) {
            $custom_field_data = $data->withCustomFields();
            $data['custom_field_data'] = collect($custom_field_data->custom_fields_data)
                ->filter(function ($value) {
                    return $value !== null;
                })
                ->toArray();
        }

        $data['branch_id'] = $data->branch->branch_id ?? null;

        $data['service_id'] = $data->services->pluck('service_id') ?? [];

        $data['commission_id'] = $data->commissions()->first()->commission_id ?? null;

        $data['profile_image'] = $data->profile_image;

        $data['about_self'] = $data->profile->about_self ?? null;

        $data['expert'] = $data->profile->expert ?? null;

        $data['facebook_link'] = $data->profile->facebook_link ?? null;

        $data['instagram_link'] = $data->profile->instagram_link ?? null;

        $data['twitter_link'] = $data->profile->twitter_link ?? null;

        $data['dribbble_link'] = $data->profile->dribbble_link ?? null;

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(EmployeeRequest $request, $id)
    {
        $data = User::role('employee')->findOrFail($id);

        $request_data = $request->except('profile_image');

        if (isset($request->password) && $request->password !== 'undefined' && !empty($request->password)) {
            $request_data['password'] = Hash::make($request_data['password']);
        } else {
            $request_data = $request->except('password');
        }

        $data->update($request_data);

        $profile = [
            'about_self' => $request->about_self,
            'expert' => $request->expert,
            'facebook_link' => $request->facebook_link,
            'instagram_link' => $request->instagram_link,
            'twitter_link' => $request->twitter_link,
            'dribbble_link' => $request->dribbble_link,
        ];

        $data->profile()->updateOrCreate([], $profile);

        if ($request->custom_fields_data) {
            $data->updateCustomFieldData(json_decode($request->custom_fields_data));
        }

        if ($request->has('profile_image') && $request->file('profile_image')) {

            storeMediaFile($data, $request->file('profile_image'), 'profile_image');
        }

        BranchEmployee::where('employee_id', $id)->delete();

        ServiceEmployee::where('employee_id', $id)->delete();

        EmployeeCommission::where('employee_id', $id)->delete();

        $roles = ['employee'];

        $employee_id = $data->id;

        if ($request->is_manager) {
            array_push($roles, 'manager');
            if ($request->has('branch_id')) {
                $branch = Branch::where('id', $request->branch_id)->first();
                $branch->update(['manager_id' => $employee_id]);
            }
        }

        // $data->syncRoles($roles);

        \Artisan::call('cache:clear');

        if ($request->has('branch_id')) {
            $branch_data = [
                'employee_id' => $id,
                'branch_id' => $request->branch_id,
            ];

            BranchEmployee::create($branch_data);
        }

        if ($request->has('service_id')) {
            if ($request->service_id !== null) {
                $services = explode(',', $request->service_id);

                foreach ($services as $value) {
                    $service_data = [

                        'employee_id' => $employee_id,
                        'service_id' => $value,

                    ];
                    ServiceEmployee::create($service_data);
                }
            }
        }

        if ($request->commission_id) {
            $commission_data = [

                'employee_id' => $id,
                'commission_id' => $request->commission_id,
            ];

            EmployeeCommission::updateOrCreate($commission_data, $commission_data);
        }

        $message = __('messages.update_form', ['form' => __('employee.singular_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if (env('IS_DEMO')) {
            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }
    
        // Find user by ID with role 'employee'
        $data = User::role('employee')->findOrFail($id);
        
        $bookingIds = BookingService::where('employee_id', $id)->pluck('booking_id');

        $statusUpdate = Booking::whereIn('id', $bookingIds)
            ->where('status', '!=', 'completed')
            ->update(['status' => 'cancelled']);

        $data->services()->forceDelete();
        $data->tokens()->delete();

        $data->forceDelete();

        $message = __('messages.delete_form', ['form' => __('employee.singular_title')]);
    
        return response()->json(['message' => $message, 'status' => true], 200);
    }
    

    public function update_status(Request $request, $id)
    {
        $data = User::role('employee')->findOrFail($id);
        $data->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('branch.status_update')]);
    }

    public function change_password(Request $request)
    {
        $data = $request->all();

        $employee_id = $data['employee_id'];

        $data = User::role('employee')->findOrFail($employee_id);

        $request_data = $request->only('password');
        $request_data['password'] = Hash::make($request_data['password']);

        $data->update($request_data);

        $message = __('messages.password_update');

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function block_employee(Request $request, User $id)
    {
        $id->update(['is_banned' => $request->status]);

        if ($request->status == 1) {
            $message = __('messages.employee_block');
        } else {
            $message = __('messages.employee_unblock');
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function verify_employee(Request $request, $id)
    {
        $data = User::role('employee')->findOrFail($id);

        $current_time = Carbon::now();

        $data->update(['email_verified_at' => $current_time]);

        return response()->json(['status' => true, 'message' => __('messages.employee_verify')]);
    }

    public function review(Request $request)
    {
        $module_title = __('employee.review_title');

        $module_name = 'review';

        $filter = $request->filter;
        $export_import = true;
        $export_columns = [
            [
                'value' => 'user_id',
                'text' => 'Client Name',
            ],
            [
                'value' => 'employee_id',
                'text' => 'Employee Name',
            ],
            [
                'value' => 'review_msg',
                'text' => 'Message',
            ],
            [
                'value' => 'rating',
                'text' => 'Rating',
            ],
            [
                'value' => 'updated_at',
                'text' => 'Updated',
            ],
        ];
        $export_url = route('backend.employees.reviewExport');

        return view('employee::backend.employees.review', compact('module_title', 'module_name', 'filter', 'export_import', 'export_columns', 'export_url'));
    }

    public function reviewExport(Request $request)
    {
        $this->exportClass = '\App\Exports\ReviewsExport';

        return $this->export($request);
    }

    public function review_data(Datatables $datatable, Request $request)
    {
        $query = EmployeeRating::with('user', 'employee');
        $filter = $request->filter;
        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheck(' . $data->id . ')">';
            })
            ->addColumn('image', function ($data) {
                return '<img src=' . $data->user->profile_image . " class='avatar avatar-50 rounded-pill'>";
            })
            ->addColumn('action', function ($data) {
                return view('employee::backend.employees.review_action_column', compact('data'));
            })

            // ->editColumn('employee_id', function ($data) {
            //     $employee_id = isset($data->employee->full_name) ? $data->employee->full_name : '-';

            //     return $employee_id;
            // })
            ->editColumn('employee_id', function ($data) {
                $Profile_image = $data->employee->profile_image ?? default_user_avatar();
                $name = $data->employee->full_name ?? default_user_name();
                $email = $data->employee->email ?? '--';
                return view('booking::backend.bookings.datatable.employee_id', compact('Profile_image', 'name', 'email'));
            })
            ->filterColumn('employee_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('employee', function ($q) use ($keyword) {
                        $q->where('first_name', 'like', '%' . $keyword . '%');
                        $q->orWhere('last_name', 'like', '%' . $keyword . '%');
                        $q->orWhere('email', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('employee_id', function ($query, $direction) {
                $query->select('employee_rating.*')
                    ->leftJoin('users', 'users.id', '=', 'employee_rating.employee_id')
                    ->orderBy('users.first_name', $direction)
                    ->orderBy('users.last_name', $direction);
            })

            ->editColumn('user_id', function ($data) {
                $Profile_image = $data->user->profile_image ?? default_user_avatar();
                $name = $data->user->full_name ?? default_user_name();
                $email = $data->user->email ?? '--';
                return view('booking::backend.bookings.datatable.user_id', compact('Profile_image', 'name', 'email'));
                // return view('employee::backend.employees.review_user_id', compact('data'));
            })
            ->filterColumn('user_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('first_name', 'like', '%' . $keyword . '%');
                        $q->orWhere('last_name', 'like', '%' . $keyword . '%');
                        $q->orWhere('email', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('user_id', function ($query, $direction) {
                $query->select('employee_rating.*')
                    ->leftJoin('users', 'users.id', '=', 'employee_rating.user_id')
                    ->orderBy('users.first_name', $direction)
                    ->orderBy('users.last_name', $direction);
            })

            // ->editColumn('user_id', function ($data) {
            //     $user_id = isset($data->user->full_name) ? $data->user->full_name : '-';

            //     return $user_id;
            // })

            ->editColumn('updated_at', function ($data) {
                $module_name = $this->module_name;

                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->created_at->diffForHumans();
                } else {
                    return $data->created_at->isoFormat('llll');
                }
            })
            ->orderColumns(['id'], '-:column $1');

        return $datatable->rawColumns(array_merge(['action', 'image', 'check']))
            ->toJson();
    }

    public function bulk_action_review(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'delete':

                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                EmployeeRating::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_review_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }

    public function destroy_review($id)
    {
        $module_title = __('employee.review');

        if (env('IS_DEMO')) {
            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }

        $data = EmployeeRating::findOrFail($id);

        $data->delete();

        $message = __('messages.delete_form', ['form' => __($module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }
    public function employeeServices($id)
    {
        // Find the user with the specified ID and role 'employee'
        $user = User::role('employee')->with('services.service')->findOrFail($id);

        $data = [];

        // Check if the user has any services
        if ($user->services->isEmpty()) {
            return response()->json(['data' => [], 'status' => false, 'message' => 'No services found for this employee.'], 404);
        }

        foreach ($user->services as $serviceEmployee) {
            // Assuming 'service' relationship exists on ServiceEmployee
            $data[] = [
                'service_id' => $serviceEmployee->service->id,
                'service_name' => $serviceEmployee->service->name,
                'duration_min' => $serviceEmployee->service->duration_min,
                'service_price' => $serviceEmployee->service->default_price,
            ];
        }

        return response()->json(['data' => $data, 'status' => true], 200);
    }

}

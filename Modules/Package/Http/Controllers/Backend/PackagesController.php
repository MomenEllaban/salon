<?php

namespace Modules\Package\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Modules\Package\Models\Package;
use Modules\Package\Models\PackageService;
use Modules\Package\Models\userPackageServices;
use Modules\Service\Models\Service;
use Yajra\DataTables\DataTables;
use Modules\Package\Models\UserPackage;
use App\Models\User;

class PackagesController extends Controller
{
    // use Authorizable;
    protected string $exportClass = '\App\Exports\PackageExport';
    public function __construct()
    {
        // Page Title
        $this->module_title = 'package.title';
        // module name
        $this->module_name = 'package';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {


        $filter = [
            'status' => $request->status,
        ];

        $module_action = 'List';
        $columns = CustomFieldGroup::columnJsonValues(new Package());
        $customefield = CustomField::exportCustomFields(new Package());

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => ' Name',
            ],
            [
                'value' => 'Service',
                'text' => ' Service',
            ],
            [
                'value' => 'Status',
                'text' => ' Status',

            ],
            [
                'value' => 'start_date',
                'text' => ' Start Date',
            ],
            [
                'value' => 'end_date',
                'text' => 'Expiry Date',
            ],
        ];
        $export_url = route('backend.package.export');

        return view('package::backend.packages.index_datatable', compact('module_action', 'filter', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {

        // $query_data = Package::with('service.services','branch')->get();
        $branchId = $request->get('branch_id');

        // Query the packages with or without filtering by branch
        $query_data = Package::with('service.services', 'branch')
            ->when($branchId, function ($query, $branchId) {
                // If branch_id is provided, filter by it
                return $query->where('branch_id', $branchId);
            })
            ->get();

        $data = [];
        $today = date('Y-m-d');
        foreach ($query_data as $row) {
            if ($row->status == 1 && $row->end_date >= $today) {
                $services = [];
                foreach ($row->service as $service) {
                    if ($service->qty > 0) {
                        $services[] = [
                            'id' => $service->id,
                            'service_name' => $service->service_name,
                            'service_id' => $service->service_id,
                            'quantity' => $service->qty,
                            'duration_min' => $service->services->duration_min,
                            'service_price' => $service->service_price,
                            'purchase_date' => $row->start_date,
                            'discount_price' => $service->discounted_price
                        ];
                    }
                }
                if ($services) {
                    $data[] = [
                        'id' => $row->id,
                        'name' => $row->name,
                        'description' => $row->description,
                        'services' => $services,
                        'branch_name' => $row->branch->name,
                        'package_price' => $row->package_price,
                        'purchase_date' => $row->start_date,
                        'start_date' => $row->start_date,
                        'end_date' => $row->end_date,
                        'package_validity' => $row->package_validity,
                    ];
                }
            }
        }

        return response()->json($data);
    }


    public function userPackageList(Request $request, $user_id)
    {
        $query_data = UserPackage::with('userPackageServices.packageService.services', 'package.branch', 'bookingTransaction')->where('user_id', $user_id)->get();

        $data = [];
        $today = date('Y-m-d');

        foreach ($query_data as $row) {
            $services = [];
            foreach ($row->userPackageServices as $service) {
                if ($service->qty > 0 && $row->package->end_date >= $today) {
                    $services[] = [
                        'id' => $service->packageService->id,
                        'user_package_id' => $row->id,
                        'service_name' => $service->packageService->service_name,
                        'service_id' => $service->packageService->service_id,
                        'total_qty' => $service->packageService->qty,
                        'remaining_qty' => $service->qty,
                        'service_price' => $service->packageService->service_price,
                        'duration_min' => $service->packageService->services->duration_min,
                        'purchase_date' => $row->package->start_date,
                        'end_date' => $row->package->end_date,
                        'branch_name' => $row->package->branch->name,
                        'branch_id' => $row->package->branch_id,
                        'discount_price' => $service->packageService->discounted_price
                    ];
                }
            }

            if ($services) {
                $data[] = [
                    'id' => $row->id,
                    'name' => $row->package->name,
                    'description' => $row->package->description,
                    'services' => $services,
                    'branch_name' => $row->package->branch->name,
                    'branch_id' => $row->package->branch_id,
                    'package_id' => $row->package_id,
                    'package_price' => $row->package->package_price,
                    'purchase_date' => $row->package->start_date,
                    'start_date' => $row->package->start_date,
                    'end_date' => $row->package->end_date,
                    'payment_status' => $row->bookingTransaction ? 1 : 0,
                ];
            }

        }

        return response()->json($data);
    }

    public function index_data(Request $request)
    {
        $query = Package::with('branch'); // Eager-load branch relationship

        $filter = $request->filter;

        if (isset($filter['search'])) {
            $searchTerm = $filter['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%$searchTerm%")
                    ->orWhere('package_price', 'like', "%$searchTerm%");
            });
        }


        return Datatables::of($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheck(' . $data->id . ')">';
            })
            ->addColumn('action', function ($data) {
                return view('package::backend.packages.action_column', compact('data'));
            })
            ->addColumn('branch', function ($data) {
                return $data->branch ? $data->branch->name : '-'; // Branch name or fallback
            })
            ->editColumn('status', function ($data) {
                $checked = '';
                if ($data->status) {
                    $checked = 'checked="checked"';
                }

                return '
                                        <div class="form-check form-switch  ">
                                            <input type="checkbox" data-url="' . route('backend.package.update_status', $data->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $data->id . '"  name="status" value="' . $data->id . '" ' . $checked . '>
                                        </div>
                                      ';
            })
            // package_price
            ->addColumn('qty', function ($data) {
                return "
                     <button type='button' data-custom-module='{$data->id}' data-assign-module='{$data->id}' data-assign-target='#package-service-form' data-custom-event='custom_form'  data-assign-event='package_service_form' class='btn btn-primary btn-sm rounded'>{$data->service->count()}</button>";
            })
            ->orderColumn('qty', function ($query, $direction) {
                $query->select('packages.*')
                    ->leftJoin('package_services', 'package_services.package_id', '=', 'packages.id')
                    ->selectRaw('COUNT(package_services.id) as service_count')
                    ->groupBy('packages.id');
                $query->orderBy('service_count', $direction);
            })

            ->editColumn('package_price', function ($data) {

                return '<span>' . \Currency::format($data->package_price) . '</span>';
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
            ->rawColumns(['action', 'status', 'package_price', 'check', 'qty', 'branch']) // Include branch
            ->orderColumns(['id'], '-:column $1')
            ->make(true);

    }

    public function services_index_list(Request $request)
    {
        $employee_id = $request->employee_id;
        $branch_id = $request->branch_id;
        $data = Service::select('services.name as service_name', 'service_branches.*')
            ->with('employee')
            ->leftJoin('service_branches', 'service_branches.service_id', 'services.id')
            ->whereHas('category', function ($q) {
                $q->active();
            })
            ->where('branch_id', $branch_id);

        if (isset($employee_id)) {
            $data = $data->whereHas('employee', function ($q) use ($employee_id) {
                $q->where('employee_id', $employee_id);
            });
        }

        $data = $data->get();

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
    
        $request['services'] = is_string($request->services) && !empty(is_string($request->services)) ? json_decode($request->services) : [];
        $request['employee_id'] = is_string($request->employee_id) && !empty($request->employee_id) ? explode(',', $request->employee_id) : [];
        $request['category_id'] = is_string($request->category_id) && !empty($request->category_id) ? explode(',', $request->category_id) : [];

    
        $totalprice = 0;
        foreach ($request['services'] as $serviceItem) {
            $filteredService = [];
            foreach ($serviceItem as $key => $value) {
                if ($key !== 'totalPrice') {
                    $filteredService[$key] = $value;
                } else {
                    $totalprice = $totalprice + $value;
                }

            }
            $service[] = $filteredService;
        }
    
        $request['package_price'] = $totalprice;
    
        $data = Package::create($request->all());
    
        $data->employees()->sync($request['employee_id']);
    
        $data->services()->sync($service);

        if ($request->hasFile('package_image')) {
            storeMediaFile($data, $request->file('package_image'), 'package_image');
        }
    
        $message = 'New Package Added';



        $message = __('messages.create_form', ['form' => __('package.singular_title')]);
    
        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Package::where('id', $id)->with('service')->first();
        // dd($data);
        $data['employee_id'] = $data->employee()->pluck('employee_id');

        $media = $data->getFirstMedia('package_image');
        $data->package_image = $media ? $media->getUrl() : '';


        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $request['services'] = is_string($request->services) && !empty(is_string($request->services)) ? json_decode($request->services) : [];
        $request['employee_id'] = is_string($request->employee_id) && !empty($request->employee_id) ? explode(',', $request->employee_id) : [];
        $request['category_id'] = is_string($request->category_id) && !empty($request->category_id) ? explode(',', $request->category_id) : [];

        $data = Package::findOrFail($id);


        $data->employees()->sync($request['employee_id']);
        $totalprice = 0;
        $serviceId = collect($request['services'])->pluck('service_id')->toArray();

        $PackageService = PackageService::where('package_id', $data->id);
        if (count($request['services']) > 0) {
            $PackageService = $PackageService->whereNotIn('service_id', $serviceId);
        }
        $PackageService->delete();



        foreach ($request['services'] as $serviceItem) {
            $filteredService = [];
            foreach ($serviceItem as $key => $value) {
                if ($key !== 'totalPrice') {
                    $filteredService[$key] = $value;
                } else {
                    $totalprice = $totalprice + $value;
                }
            }

            // Update or create the package_service
            PackageService::updateOrCreate(
                ['service_id' => $filteredService['service_id'], 'package_id' => $data->id],
                array_merge(
                    $filteredService,
                    ['package_id' => $data->id]
                )
            );

        }



        $request['package_price'] = $totalprice;
        $data->update($request->all());

        if ($request->package_image == null) {
            $data->clearMediaCollection('package_image');
        }

        if ($request->hasFile('package_image')) {
            storeMediaFile($data, $request->file('package_image'), 'package_image');
        }
        
        $message = 'Packages Updated Successfully';

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
        $data = Package::findOrFail($id);

        $data->delete();

        $message = 'Packages Deleted Successfully';

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function update_status(Request $request, Package $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('booking.status_update')]);
    }

    public function update_is_featured(Request $request, Package $id)
    {
        $id->update(['is_featured' => $request->status]);

        return response()->json(['status' => true, 'message' => __('package.is_featured_update')]);
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {

            case 'change-status':
                $packages = Package::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_status_update');
                break;

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                $packages = Package::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_status_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('package.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }

    public function clientView(Request $request)
    {

        // Page Title
        $this->module_title = 'sidebar.customer_packages';
        // module name
        $this->module_name = 'package';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
        $filter = [
            'status' => $request->status,
        ];

        $module_action = 'List';
        $columns = CustomFieldGroup::columnJsonValues(new Package());
        $customefield = CustomField::exportCustomFields(new Package());

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => ' Name',
            ],
        ];
        $export_url = route('backend.package.export');

        return view('package::backend.packages.clientPacakge_datatable', compact('module_action', 'filter', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url'));
    }

    public function clientPackageData()
    {
        $query = UserPackage::with('booking.user', 'package.service', 'userPackageServices');
        $query->whereHas('bookingTransaction');

        return Datatables::of($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheck(' . $data->id . ')">';
            })
            ->addColumn('username', function ($data) {
                $user = optional($data->user);
                $Profile_image = $user->profile_image ?? default_user_avatar();
                $name = $user->full_name ?? default_user_name();
                $email = $user->email ?? '--';
                return view('booking::backend.bookings.datatable.user_id', compact('Profile_image', 'name', 'email'));
            })
            ->orderColumn('username', function ($query, $order) {
                $query->select('user_packages.*')
                    ->leftJoin('users', 'users.id', '=', 'user_packages.user_id')
                    ->orderBy('users.first_name', $order);
            })
            ->addColumn('packagename', function ($data) {
                return $data->package->name;
            })->orderColumn('packagename', function ($query, $order) {
                $query->select('user_packages.*')
                    ->leftJoin('packages', 'packages.id', '=', 'user_packages.package_id')
                    ->orderBy('packages.name', $order);
            })
            ->addColumn('qty', function ($data) {
                return $data->userPackageServices->count();
            })

            ->orderColumn('qty', function ($query, $direction) {
                $query->select('user_packages.*')
                    ->leftJoin('user_package_services', 'user_package_services.user_package_id', '=', 'user_packages.id')
                    ->selectRaw('SUM(user_package_services.qty) as service_count')
                    ->groupBy('user_packages.id')
                    ->orderBy('service_count', $direction);
            })
            ->editColumn('package_price', function ($data) {

                return '<span>' . \Currency::format($data->package->package_price) . '</span>';
            })
            ->orderColumn('package_price', function ($query, $order) {
                $query->select('user_packages.*')
                    ->leftJoin('packages', 'packages.id', '=', 'user_packages.package_id')
                    ->orderBy('packages.package_price', $order);
            })
            ->addColumn('startdate', function ($data) {
                return date('Y-m-d', strtotime($data->package->start_date));
            })
            ->orderColumn('startdate', function ($query, $order) {
                $query->select('user_packages.*')
                    ->leftJoin('packages', 'packages.id', '=', 'user_packages.package_id')
                    ->orderBy('packages.start_date', $order);
            })
            ->addColumn('expirydate', function ($data) {
                $expiryDate = date('Y-m-d', strtotime($data->package->end_date));
                return $expiryDate;
            })
            ->orderColumn('expirydate', function ($query, $order) {
                $query->select('user_packages.*')
                    ->leftJoin('packages', 'packages.id', '=', 'user_packages.package_id')
                    ->orderBy('packages.end_date', $order);
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
            ->addColumn('action', function ($data) {
                return "
                        <button type='button' data-custom-module='{$data->id}' data-assign-module='{$data->id}' data-assign-target='#client-package-form' data-assign-event='client-package-form' class='btn btn-primary btn-sm rounded btn-icon'><i class='fa-solid fa-eye'></i></button>";
            })
            ->rawColumns(['action', 'check', 'package_price'])
            ->orderColumns(['id'], '-:column $1')
            ->make(true);

    }

    public function clientPackageView($id)
    {

        $data = UserPackage::where('id', $id)->with('booking.user', 'package.service', 'userPackageServices.packageService')->first();


        return response()->json(['data' => $data, 'status' => true]);
    }


}


<?php

namespace Modules\Booking\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Modules\Booking\Http\Requests\BookingRequest;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingProduct;
use Modules\Package\Models\BookingPackages;
use Modules\Package\Models\UserPackage;
use Modules\Booking\Models\BookingService;
use Modules\Booking\Models\BookingTransaction;
use Modules\Booking\Trait\BookingTrait;
use Modules\Booking\Trait\PaymentTrait;
use Modules\Booking\Transformers\BookingResource;
use Modules\Constant\Models\Constant;
use Modules\Product\Trait\ProductTrait;
use Modules\Service\Models\Service;
use Modules\Tax\Models\Tax;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Modules\Promotion\Models\UserCouponRedeem;
use Modules\Package\Models\PackageService;
use Modules\Package\Models\UserPackageRedeem;
use Modules\Package\Models\UserPackageServices;
class BookingsController extends Controller
{
    // use Authorizable;
    use BookingTrait;
    use PaymentTrait;
    use ProductTrait;

    protected string $exportClass = '\App\Exports\BookingsExport';

    public function __construct()
    {
        // Page Title
        $this->module_title = 'booking.title';

        // module name
        $this->module_name = 'bookings';

        // module icon
        $this->module_icon = 'fa-regular fa-sun';

        view()->share([
            'module_title' => $this->module_title,
            'module_name' => $this->module_name,
            'module_icon' => $this->module_icon,
        ]);
        $this->middleware(['permission:view_booking'])->only('index');
        $this->middleware(['permission:edit_booking'])->only('edit', 'update');
        $this->middleware(['permission:add_booking'])->only('store');
        $this->middleware(['permission:delete_booking'])->only('destroy');
        $this->middleware(['permission:booking_booking_tableview'])->only('datatable_view');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $module_action = 'List';

        $statusList = $this->statusList();

        $booking = Booking::find(request()->booking_id);

        $date = $booking->start_date_time ?? date('Y-m-d');

        return view('booking::backend.bookings.index', compact('module_action', 'statusList', 'date'));
    }

    public function statusList()
    {
        $booking_status = Constant::getAllConstant()->where('type', 'BOOKING_STATUS');
        $checkout_sequence = $booking_status->where('name', 'check_in')->first()->sequence ?? 0;
        $bookingColors = Constant::getAllConstant()->where('type', 'BOOKING_STATUS_COLOR');
        $statusList = [];

        foreach ($booking_status as $key => $value) {
            if ($value->name !== 'cancelled') {
                $statusList[$value->name] = [
                    'title' => $value->value,
                    'color_hex' => $bookingColors->where('sub_type', $value->name)->first()->name,
                    'is_disabled' => $value->sequence >= $checkout_sequence,
                ];
                $nextStatus = $booking_status->where('sequence', $value->sequence + 1)->first();
                if ($nextStatus) {
                    $statusList[$value->name]['next_status'] = $nextStatus->name;
                }
            } else {
                $statusList[$value->name] = [
                    'title' => $value->value,
                    'color_hex' => $bookingColors->where('sub_type', $value->name)->first()->name,
                    'is_disabled' => true,
                ];
            }
        }

        return $statusList;
    }

    /**
     * @return Response
     */
    public function index_list(Request $request)
    {
        $date = $request->date;
        $page = $request->page ?? 1;
        $perPage = $request->per_page ?? 6;
        $data = BookingService::with('booking', 'employee', 'service')
            ->whereHas('booking', function ($q) use ($date) {
                if (!empty($date)) {
                    $q->whereDate('start_date_time', $date);
                }
                $q->where('status', '!=', 'cancelled');
            })
            ->get();

        $package = BookingPackages::with('booking', 'employee', 'services')
            ->whereHas('booking', function ($q) use ($date) {
                if (!empty($date)) {
                    $q->whereDate('start_date_time', $date);
                }
                $q->where('status', '!=', 'cancelled');
            })
            ->get();

        $service_updated = [];
        $statusList = $this->statusList();
        foreach ($data as $key => $value) {
            $duration = $value->duration_min;

            $startTime = $value->start_date_time;

            $endTime = Carbon::parse($startTime)->addMinutes($duration);

            $serviceName = $value->service->name ?? '';

            $customerName = $value->booking->user->full_name ?? 'Anonymous';

            $service_updated[$key] = [
                'id' => $value->booking_id,
                'start' => customDate($startTime, 'Y-m-d H:i'),
                'end' => customDate($endTime, 'Y-m-d H:i'),
                'resourceId' => $value->employee_id,
                'title' => $serviceName,
                'titleHTML' => view('booking::backend.bookings.calender.event', compact('serviceName', 'customerName'))->render(),
                'color' => $statusList[$value->booking->status]['color_hex'],
            ];
            $startTime = $endTime;
        }
        $package_updated = [];
        foreach ($package as $key => $value) {
            $duration = $value->services->sum('duration_min');

            $startTime = $value->booking->start_date_time;

            $endTime = Carbon::parse($startTime)->addMinutes($duration);

            $serviceName = $value->package->name ?? '';

            $customerName = $value->booking->user->full_name ?? 'Anonymous';

            $package_updated[$key] = [
                'id' => $value->booking_id,
                'start' => customDate($startTime, 'Y-m-d H:i'),
                'end' => customDate($endTime, 'Y-m-d H:i'),
                'resourceId' => $value->employee_id,
                'title' => $serviceName,
                'titleHTML' => view('booking::backend.bookings.calender.event', compact('serviceName', 'customerName'))->render(),
                'color' => $statusList[$value->booking->status]['color_hex'],
            ];
            $startTime = $endTime;
        }
        $updated_data = array_merge($service_updated, $package_updated);
        $employees = User::bookingEmployeesList()->paginate($perPage, ['*'], 'page', $page);
        $resource = [];
        foreach ($employees as $employee) {
            $resource[] = [
                'id' => $employee->id,
                'title' => $employee->full_name,
                'titleHTML' => '<div class="d-flex gap-3 justify-content-center align-items-center py-3"><img src="' . $employee->profile_image . '" class="avatar avatar-40 rounded-pill" alt="employee" />' . $employee->full_name . '</div>',
            ];
        }

        return response()->json([
            'data' => $updated_data,
            'employees' => $resource,
            'total_count' => $employees->total(),
        ]);
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

    public function datatable_view(Request $request)
    {
        $module_action = 'List';

        $filter = [
            'status' => $request->status,
        ];

        $booking_status = Constant::getAllConstant()->where('type', 'BOOKING_STATUS');

        $export_import = true;
        $export_columns = [
            [
                'value' => 'date',
                'text' => 'Date',
            ],
            [
                'value' => 'customer',
                'text' => 'Customer Name',
            ],
            [
                'value' => 'service_amount',
                'text' => 'Amount',
            ],
            [
                'value' => 'service_duration',
                'text' => 'Duration',
            ],
            [
                'value' => 'employee',
                'text' => 'Staff Name',
            ],
            [
                'value' => 'services',
                'text' => 'Services',
            ],
            [
                'value' => 'status',
                'text' => 'Status',
            ],
            [
                'value' => 'updated_at',
                'text' => 'Updated At',
            ],
        ];
        $export_url = route('backend.bookings.export');

        return view('booking::backend.bookings.index_datatable', compact('module_action', 'filter', 'booking_status', 'export_import', 'export_columns', 'export_url'));
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $module_name = $this->module_name;

        $query = Booking::with('branch', 'user', 'services', 'mainServices', 'payment', 'bookingPackages', 'bookedPackageService', 'userPackageServices');

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
            if (isset($filter['booking_date'])) {
                try {
                    $startDate = explode(' to ', $filter['booking_date'])[0];
                    $endDate = explode(' to ', $filter['booking_date'])[1];
                    $query->whereDate('start_date_time', '>=', $startDate);
                    $query->whereDate('start_date_time', '<=', $endDate);
                } catch (\Exception $e) {
                    \Log::error($e->getMessage());
                }
            }
            if (isset($filter['user_id'])) {
                $query->where('user_id', $filter['user_id']);
            }
            if (isset($filter['emploee_id'])) {
                $query->whereHas('services', function ($q) use ($filter) {
                    $q->where('employee_id', $filter['emploee_id']);
                });
            }
            if (isset($filter['service_id'])) {
                $query->whereHas('services', function ($q) use ($filter) {
                    $q->whereIn('service_id', $filter['service_id']);
                });
            }
        }

        $booking_status = Constant::getAllConstant()->where('type', 'BOOKING_STATUS')->where('name', '!=', 'completed');
        $booking_colors = Constant::getAllConstant()->where('type', 'BOOKING_STATUS_COLOR');

        $payment_status = Constant::getAllConstant()->where('type', 'PAYMENT_STATUS')->where('status', '=', '1');

        return $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->addColumn('action', function ($data) use ($module_name) {
                return view('booking::backend.bookings.datatable.action_column', compact('module_name', 'data'));
            })
            ->editColumn('status', function ($data) use ($booking_status, $booking_colors) {
                return view('booking::backend.bookings.datatable.select_column', compact('data', 'booking_status', 'booking_colors'));
            })
            ->editColumn('payment_status', function ($data) use ($payment_status, $booking_colors) {

                return view('booking::backend.bookings.datatable.select_payment_status', compact('data', 'payment_status', 'booking_colors'));
            })
            ->editColumn('user_id', function ($data) {
                $user = optional($data->user);
                $Profile_image = $user->profile_image ?? default_user_avatar();
                $name = $user->full_name ?? default_user_name();
                $email = $user->email ?? '--';

                return view('booking::backend.bookings.datatable.user_id', compact('Profile_image', 'name', 'email'));
            })
            ->editColumn('employee_id', function ($data) {
                // $Profile_image = $data->services->first()->employee?->profile_image ?? $data->bookingPackages->first()->employee?->profile_image ?? default_user_avatar() ;
                // $name = $data->services->first()->employee?->full_name ?? $data->bookingPackages->first()->employee?->full_name ?? default_user_name();
                // $email = $data->services->first()->employee?->email ?? $data->bookingPackages->first()->employee?->email ?? '--';

                $employee = optional($data->services->first())->employee
                    ?: optional($data->bookingPackages->first())->employee;

                $Profile_image = $employee->profile_image ?? default_user_avatar();
                $name = $employee->full_name ?? default_user_name();
                $email = $employee->email ?? '--';

                return view('booking::backend.bookings.datatable.employee_id', compact('Profile_image', 'name', 'email'));
            })
            ->editColumn('service_amount', function ($data) {
                $serviceAmount = $data->services->sum('service_price');
                if ($data->bookingPackages->isNotEmpty()) {

                    foreach ($data->bookingPackages as $bookingPackage) {
                        if ($bookingPackage->is_reclaim == 0) {
                            $serviceAmount += $bookingPackage->package_price;
                        }
                    }
                }
                return '<span>' . \Currency::format($serviceAmount) . '</span>';
            })
            ->editColumn('service_duration', function ($data) {

                return '<span>' . $data->calculateServiceDuration() . ' Min</span>';

            })
            ->editColumn('services', function ($data) {
                return view('booking::backend.bookings.datatable.services', compact('data'));
            })
            ->editColumn('packages', function ($data) {
                if ($data->bookingPackages->isNotEmpty()) {
                    $packageNames = $data->bookingPackages->pluck('name')->implode(', ');
                    return '<small class="badge bg-primary">' . $packageNames . '</small>';
                }
                return '<span class="badge bg-primary">' . '-' . '</span>';
            })
            ->editColumn('start_date_time', function ($data) {
                return customDate($data->start_date_time);
            })
            ->editColumn('updated_at', function ($data) {
                $diff = timeAgoInt($data->updated_at);

                if ($diff < 25) {
                    return timeAgo($data->updated_at);
                } else {
                    return customDate($data->updated_at);
                }
            })
            ->editColumn('id', function ($row) {
                return "<a href='" . route('backend.bookings.index', ['booking_id' => $row->id]) . "'>$row->id</a>";
            })
            ->orderColumn('service_amount', function ($query, $order) {
                $query->orderBy(new Expression('(SELECT SUM(service_price) FROM booking_services WHERE booking_id = bookings.id)'), $order);
            }, 1)
            ->orderColumn('service_duration', function ($query, $order) {
                $query->orderBy(new Expression('(SELECT SUM(duration_min) FROM booking_services WHERE booking_id = bookings.id)'), $order);
            }, 1)
            ->orderColumn('employee_id', function ($query, $order) {
                $query->select('bookings.*')
                    ->leftJoin('booking_services', 'booking_services.booking_id', '=', 'bookings.id')
                    ->leftJoin('users', 'users.id', '=', 'booking_services.employee_id')
                    ->orderBy('users.first_name', $order);
            }, 1)
            ->filterColumn('services', function ($query, $keyword) {
                $query->whereHas('mainServices', function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%');
                });
            })
            ->filterColumn('employee_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('services', function ($q) use ($keyword) {
                        $q->whereHas('employee', function ($qn) use ($keyword) {
                            $qn->where('first_name', 'like', '%' . $keyword . '%');
                            $qn->orWhere('last_name', 'like', '%' . $keyword . '%');
                            $qn->orWhere('email', 'like', '%' . $keyword . '%');
                        });
                    });
                }
            })
            ->orderColumn('user_id', function ($query, $order) {
                $query->select('bookings.*')
                    ->leftJoin('users', 'users.id', '=', 'bookings.user_id')
                    ->orderByRaw('CONCAT(users.first_name, " ", users.last_name) ' . $order);
            })
            ->filterColumn('user_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->whereRaw('CONCAT(first_name, " ", last_name) LIKE ?', ['%' . $keyword . '%']);
                        $q->orWhere('email', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->rawColumns(['check', 'id', 'action', 'status', 'services', 'service_duration', 'service_amount', 'start_date_time', 'payment_status', 'packages'])
            // ->orderColumn('updated_at', 'desc')
            ->orderColumns(['id'], '-:column $1')
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(BookingRequest $request)
    {
        $bookingData = $request->except(['services_id', 'employee_id', '_token']);

        $bookingData['status'] = 'confirmed';

        $booking = Booking::create($bookingData);

        $this->updateBookingService($request->services, $booking->id);
        $this->updateBookingPackage($request->purchase_packages, $booking->id);
        $this->storeUserPackage($booking->id);
        $message = __('messages.create_form', ['form' => __('booking.singular_title')]);

        try {
            $type = 'new_booking';
            $messageTemplate = 'New booking #[[booking_id]] has been booked.';
            $notify_message = str_replace('[[booking_id]]', $booking->id, $messageTemplate);
            $this->sendNotificationOnBookingUpdate($type, $notify_message, $booking);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

        $data = Booking::with('services', 'user', 'products', 'packages', 'bookingPackages.services', )->findOrFail($booking->id);

        return response()->json(['message' => $message, 'status' => true, 'data' => new BookingResource($data)], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $booking = Booking::with(['services', 'user', 'products', 'userCouponRedeem'])->find($id);

        if (is_null($booking)) {
            return response()->json(['message' => __('messages.booking_not_found')], 404);
        }

        $bookingTransaction = BookingTransaction::where('booking_id', $booking->id)->where('payment_status', 1)->first();

        $booking_product = BookingProduct::where('booking_id', $booking->id)->get();

        $sumDiscountedPrice = 0;

        if ($booking_product != '') {
            $sumDiscountedPrice = $booking_product->sum('discounted_price');
        }

        $data = [
            'booking' => new BookingResource($booking),
            'services_total_amount' => $booking->services->sum('service_price'),
            'booking_transaction' => $bookingTransaction,
            'product_amount' => $sumDiscountedPrice,
            'package_amount' => $booking->packages->sum('package_price'),
            'coupon_discount' => $booking->userCouponRedeem->discount ?? 0
        ];
        return response()->json(['status' => true, 'data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Booking::with([
            'services',
            'user',
            'products',
            'packages',
            'bookingPackages.services',
            'userCouponRedeem'  // Ensure this is included
        ])->findOrFail($id);

        return response()->json(['data' => new BookingResource($data), 'status' => true]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(BookingRequest $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $booking->update($request->all());

        $this->updateBookingService($request->services, $booking->id);
        $this->updateBookingPackage($request->purchase_packages, $booking->id);
        $message = __('booking.booking_service_update', ['form' => __('booking.singular_title')]);

        $data = Booking::with('services', 'user', 'products', 'packages', 'bookingPackages.services')->findOrFail($booking->id);

        return response()->json(['message' => $message, 'status' => true, 'data' => new BookingResource($data)], 200);
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
        $booking = Booking::findOrFail($id);

        $booking->delete();

        $message = __('messages.delete_form', ['form' => __('booking.singular_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function updateStatus($id, Request $request)
    {
        $booking = Booking::with('services', 'user', 'products', 'packages', 'bookingPackages.services')->findOrFail($id);
        $status = $request->status;

        if (isset($request->action_type) && $request->action_type == 'update-status') {
            $status = $request->value;
        }

        $booking->update(['status' => $status]);

        $notify_type = null;

        switch ($status) {
            case 'check_in':
                $notify_type = 'check_in_booking';
                $messageTemplate = '#[[booking_id]] has been check-in successfully.';
                $notify_message = str_replace('[[booking_id]]', $id, $messageTemplate);
                break;
            case 'checkout':
                $notify_type = 'checkout_booking';
                $messageTemplate = '#[[booking_id]] has been check-out successfully.';
                $notify_message = str_replace('[[booking_id]]', $id, $messageTemplate);
                break;
            case 'completed':
                $notify_type = 'complete_booking';
                $messageTemplate = 'Booking #[[booking_id]] has been completed. Please find the attached invoice in your email.';
                $notify_message = str_replace('[[booking_id]]', $id, $messageTemplate);
                break;
            case 'cancelled':
                $notify_type = 'cancel_booking';
                $messageTemplate = 'Booking #[[booking_id]] has been cancelled.';
                $notify_message = str_replace('[[booking_id]]', $id, $messageTemplate);
                break;
        }

        if (isset($notify_type)) {
            try {
                $this->sendNotificationOnBookingUpdate($notify_type, $notify_message, $booking);
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }

        $message = __('booking.status_update');

        return response()->json(['data' => new BookingResource($booking), 'message' => $message, 'status' => true]);
    }

    public function updatePaymentStatus($id, Request $request)
    {
        if (isset($request->action_type) && $request->action_type == 'update-payment-status') {
            $status = $request->value;
        }

        BookingTransaction::where('booking_id', $id)->update(['payment_status' => $request->value]);

        $message = __('booking.status_update');

        return response()->json(['message' => $message, 'status' => true]);
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $branches = Booking::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_booking_update');
                break;

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                Booking::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_booking_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('booking.booking_action_invalid')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function booking_slots(Request $request)
    {
        $day = date('l', strtotime($request->date));

        $branch_id = $request->branch_id;

        $slots = $this->getSlots($request->date, $day, $branch_id);

        return response()->json(['status' => true, 'data' => $slots]);
    }

    public function payment_create(Request $request)
    {

        $booking_id = $request->booking_id;
        $booking = Booking::find($booking_id);
        $booking_services = BookingService::where('booking_id', $booking_id)->get();

        if ($request->has('userPackageserviceIds') && !empty($request->userPackageserviceIds)) {
            $userPackageserviceIds = $request->userPackageserviceIds;
            if (is_string($userPackageserviceIds)) {
                $userPackageserviceIds = explode(',', $userPackageserviceIds);
                $userPackageserviceIds = array_map('intval', $userPackageserviceIds); // Convert each ID to integer
            }
            $userPackageServices = UserPackageServices::whereIn('package_service_id', $userPackageserviceIds)
                ->with('packageService')
                ->get();
            if ($userPackageServices) {
                $coveredServiceIds = $userPackageServices->pluck('packageService.service_id')->toArray();

                $total_service_amount = $booking_services->reduce(function ($carry, $bookingService) use ($coveredServiceIds) {
                    if (!in_array($bookingService->service_id, $coveredServiceIds)) {
                        $carry += $bookingService->service_price;
                    } else {
                        $carry += 0;
                    }
                    return $carry;
                }, 0);
            }
        } else {
            $total_service_amount = $booking_services->sum('service_price');
        }

        $booking_products = BookingProduct::where('booking_id', $booking_id)->with('product')->get();

        $discounted_product_amount = getproductDiscountAmount($booking_products);
        $total_product_amount = BookingProduct::where('booking_id', $booking_id)->sum(\DB::raw('product_qty * product_price'));
        $userPackageRedeem = UserPackageRedeem::where('booking_id', $booking_id)->get();
        $discountedservice_amount = $userPackageRedeem->sum('service_price');
        // $package_amount = UserPackage::where('booking_id', $booking_id)->with('package')->get();
        // $total_package_amount = $package_amount->sum('package_price');
        $package_amount = BookingPackages::where('booking_id', $booking_id)->with('package')->get();
        $total_package_amount = $package_amount->sum('package_price');
        $product_amount = $total_product_amount - $discounted_product_amount;
        if ($discountedservice_amount) {
            $total_service_amount = $total_service_amount - $discountedservice_amount;
        }
        $currency = \Currency::getDefaultCurrency();
        $payment_methods = $booking->branch->payment_method;
        $constant = Constant::where('type', 'PAYMENT_METHODS')->whereIn('name', $payment_methods)->get();
        $payment_methods = $constant->map(function ($row) {
            return [
                'id' => $row->name,
                'text' => $row->value,
            ];
        })->toArray();
        $taxes = Tax::active();
        $coupon = UserCouponRedeem::where('booking_id', $booking_id)->first();
        $data = [
            'booking_amounts' => [
                'amount' => $total_service_amount,
                'product_amount' => $product_amount,
                'package_amount' => $total_package_amount,
                'currency' => $currency->currency_symbol,
            ],
            'PAYMENT_METHODS' => $payment_methods,
            'tax' => $taxes
                ->whereNull('module_type')
                ->orWhere('module_type', 'services')->where('status', 1)->get(),
            'userpackageRedeem' => $userPackageRedeem,
            'coupon' => $coupon,
        ];

        return response()->json(['status' => true, 'data' => $data]);
    }

    public function booking_payment(Request $request, Booking $booking_id)
    {
        $data = $request->all();

        $booking_id = $booking_id['id'];
        if ($request->has('packageService') && !empty($request->packageService)) {
            foreach ($request->packageService as $service) {
                $serviceId = $service['service_id'];
                $discountPrice = $service['discount_price'];
                BookingService::where('booking_id', $booking_id)
                    ->where('service_id', $serviceId)
                    ->update(['service_price' => 0]);
            }
        }


        $responseData = $this->getpayment_method($data, $booking_id);
        $this->updateUserPackageRedeem($request->packageService, $booking_id);
        $booking_product = BookingProduct::where('booking_id', $booking_id)->get();

        $booking_details = Booking::where('id', $booking_id)->with('payment')->first();
        if ($booking_product->isNotEmpty()) {
            $orderId = $this->createCart($booking_product, $booking_details);

            BookingProduct::where('booking_id', $booking_id)->update(['order_id' => $orderId]);
        }

        return response()->json(['status' => true, 'data' => $responseData]);
    }

    public function booking_payment_update(Request $request, $booking_transaction_id)
    {
        $data = $request->all();

        $responseData = $this->getrazorpaypayments($data, $booking_transaction_id);

        if (isset($responseData['booking'])) {
            $queryData = Booking::find($responseData['booking']->id);

            $messageTemplate = 'Booking #[[booking_id]] has been completed. Please find the attached invoice in your email.';
            $notify_message = str_replace('[[booking_id]]',$responseData['booking']->id, $messageTemplate);
            try {
                $this->sendNotificationOnBookingUpdate('complete_booking', $notify_message,$queryData);
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }


        return response()->json(['status' => true, 'data' => $responseData]);
    }

    public function checkout(Booking $booking_id, Request $request)
    {

        // $this->updateBookingPackage($request->purchase_package, $booking_id->id);


        $this->updateBookingService($request->services, $booking_id->id);


        $this->updateBookingProduct($request->products, $booking_id->id);

        $queryData = Booking::with('services', 'user', 'products', 'packages', 'bookingPackages.services')->findOrFail($booking_id->id);

        return response()->json(['status' => true, 'data' => new BookingResource($queryData), 'message' => __('booking.booking_service_update')]);
    }

    public function stripe_payment(Request $request)
    {
        $data = $request->data;

        $checkout_session = $this->getstripepayments($data);

        if (isset($checkout_session['message'])) {
            return response()->json(['status' => false, 'data' => $checkout_session]);
        } else {
            BookingTransaction::where('id', $data['booking_transaction_id'])->update(['request_token' => $checkout_session['id']]);

            return response()->json(['status' => true, 'data_url' => $checkout_session->url, 'data' => $checkout_session]);
        }
    }

    public function payment_success($id)
    {
        $booking_transaction = BookingTransaction::where('id', $id)->first();

        $request_token = $booking_transaction['request_token'];

        $booking_id = $booking_transaction['booking_id'];

        $session_object = $this->getstripePaymnetId($request_token);

        if ($session_object['payment_intent'] !== '' && $session_object['payment_status'] == 'paid') {
            BookingTransaction::where('id', $id)->update(['external_transaction_id' => $session_object['payment_intent'], 'payment_status' => 1]);

            Booking::where('id', $booking_id)->update(['status' => 'completed']);

            $queryData = Booking::where('id', $booking_id)->first();
            try {
                $messageTemplate = 'Booking #[[booking_id]] has been completed. Please find the attached invoice in your email.';
                $notify_message = str_replace('[[booking_id]]',  $queryData->id, $messageTemplate);
                $this->sendNotificationOnBookingUpdate('complete_booking',$notify_message, $queryData);
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }

        return redirect()->route('backend.bookings.index');
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function viewInvoice(Request $request)
    {
        $order = Booking::find($request->id);


        $booking = Booking::with(['services', 'user', 'products', 'userCouponRedeem', 'packages'])->where('status', 'completed')->find($request->id);

        if ($booking == null) {
            return abort(500);
        }

        if (is_null($booking)) {
            return response()->json(['message' => __('messages.booking_not_found')], 404);
        }

        $data = $this->bookingDetail($booking);

        $data = (object) [
            'booking' => new BookingResource($booking),
            'services_total_amount' => $data['serviceAmount'],
            'booking_transaction' => $data['bookingTransaction'],
            'product_amount' => $data['sumDiscountedPrice'],
            'tax_amount' => $data['tax_amount'],
            'coupon_discount' => $data['coupon_discount'],
            'grand_total' => $data['grand_total'],
            'package_amount' => $data['packageAmount'],
        ];

        return view('booking::backend.invoice', compact('data'));
    }

    public function downloadInvoice(Request $request)
    {
        $booking = Booking::with(['services', 'user', 'products'])->where('status', 'completed')->find($request->id);

        $booking['detail'] = $this->bookingDetail($booking);
        $filename = 'Invoice_' . $request->id . '.pdf';
        // Prepare data for notification
        $data = $this->sendNotificationOnBookingUpdate('complete_booking', 'Notification message', $booking, false);
        if ($data === false) {
            return response()->json(['status' => false, 'message' => 'Failed to prepare booking data for notification'], 500);
        }

        // Render the view for the PDF
        $view = view("mail.invoice-templates." . setting('template'), ['data' => $data['booking']])->render();
        $pdf = Pdf::loadHTML($view);

        if ($request->is('api/*')) {
            // Handle API request
            $baseDirectory = storage_path('app/public');
            $highestDirectory = collect(File::directories($baseDirectory))->map(function ($directory) {
                return basename($directory);
            })->max() ?? 0;
            $nextDirectory = intval($highestDirectory) + 1;
            while (File::exists($baseDirectory . '/' . $nextDirectory)) {
                $nextDirectory++;
            }
            $newDirectory = $baseDirectory . '/' . $nextDirectory;
            File::makeDirectory($newDirectory, 0777, true);

            $filename = 'invoice_' . $request->id . '.pdf';
            $filePath = $newDirectory . '/' . $filename;

            $pdf->save($filePath);

            $url = url('storage/' . $nextDirectory . '/' . $filename);
            if (!empty($url)) {
                return response()->json(['status' => true, 'link' => $url], 200);
            } else {
                return response()->json(['status' => false, 'message' => 'Url Not Found'], 404);
            }
        } else {
            // Handle non-API request
            return $pdf->download($filename);
        }
    }

}

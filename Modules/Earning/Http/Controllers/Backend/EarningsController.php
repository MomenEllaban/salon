<?php

namespace Modules\Earning\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Currency;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Modules\Commission\Models\CommissionEarning;
use Modules\Earning\Models\EmployeeEarning;
use Modules\Earning\Trait\EarningTrait;
use Modules\Tip\Models\TipEarning;
use Yajra\DataTables\DataTables;

class EarningsController extends Controller
{
    // use Authorizable;
    use EarningTrait;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'earning.title';

        // module name
        $this->module_name = 'earnings';

        // directory path of the module
        $this->module_path = 'earning::backend';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'fa-regular fa-sun',
            'module_name' => $this->module_name,
            'module_path' => $this->module_path,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $module_action = 'List';

        $module_title = 'earning.lbl_title_earning';

        return view('earning::backend.earnings.index_datatable', compact('module_action', 'module_title'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $term = trim($request->q);

        if (empty($term)) {
            return response()->json([]);
        }

        $query_data = Earning::where('name', 'LIKE', "%$term%")->orWhere('slug', 'LIKE', "%$term%")->limit(7)->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'text' => $row->name . ' (Slug: ' . $row->slug . ')',
            ];
        }

        return response()->json($data);
    }

    public function update_status(Request $request, Earning $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('branch.status_update')]);
    }

    public function index_data(DataTables $datatable)
    {
        $module_name = $this->module_name;


        // dd(
        //     User::select('users.id', 'users.first_name', 'users.last_name', 'users.email')
        //         ->join('commission_earnings', 'users.id', '=', 'commission_earnings.employee_id')
        //         ->join('booking_services', 'booking_services.booking_id', '=', 'commission_earnings.commissionable_id')
        //         ->where('commission_earnings.commission_status', 'unpaid')
        //         ->selectRaw('users.id, SUM(service_price) as total_service_amount')
        //         ->groupBy('users.id')
        //         ->get()
        // );

        $query = User::select('users.id', 'users.first_name', 'users.last_name', 'users.email')
            ->withCount([
                'commission_earning as totalBookings' => function ($q) {
                    $q->where('commission_status', 'unpaid')
                        ->groupBy('employee_id');
                }
            ])
            // ->withCount([
            //     'bookingPackages as totalPackageBookings' => function ($q) {
            //         $q->groupBy('employee_id');
            //     }
            // ])
            // ->with([
            //     'employeeBooking' => function ($q) {
            //         $q->select('employee_id')
            //             ->selectRaw('COUNT(DISTINCT booking_id) as totalBookings')
            //             ->selectRaw('SUM(service_price) as total_service_amount')
            //             ->groupBy('employee_id');
            //     }
            // ])
            ->WithTotalUnpaidServiceAmount()
            ->with([
                'bookingPackages' => function ($q) {
                    $q->select('employee_id')
                        ->selectRaw('COUNT(DISTINCT id) as totalBookings')
                        ->selectRaw('COALESCE(SUM(package_price), 0) as total_package_amount')
                        ->groupBy('employee_id');
                }
            ])
            ->with('commission_earning')
            ->with('tip_earning')
            ->with('employeeEarnings')
            ->whereHas('commission_earning', function ($q) {
                $q->where('commission_status', 'unpaid');
            })->orderBy('users.updated_at', 'desc');

        return $datatable->eloquent($query)
            ->addColumn('action', function ($data) use ($module_name) {
                $commissionAmount = $data->commission_earning->where('commission_status', 'unpaid')->sum('commission_amount');
                $tipAmount = $data->tip_earning->where('tip_status', 'unpaid')->sum('tip_amount');
                $data['total_pay'] = $commissionAmount + $tipAmount;

                return view('earning::backend.earnings.action_column', compact('module_name', 'data'));
            })
            // ->editColumn('first_name', function ($data) {
            //     return $data->full_name;
            // })
            ->editColumn('first_name', function ($data) {
                $Profile_image = optional($data)->profile_image ?? default_user_avatar();
                $name = optional($data)->full_name ?? default_user_name();
                $email = optional($data)->email ?? '--';
                return view('booking::backend.bookings.datatable.employee_id', compact('Profile_image', 'name', 'email'));
            })
            ->filterColumn('first_name', function ($query, $keyword) {
                $cleanedKeyword = preg_replace('/[^A-Za-z\s]/', '', $keyword); // Clean the keyword for letters
    
                if ($cleanedKeyword !== '') {
                    $query->where(function ($query) use ($cleanedKeyword) {
                        $query->whereRaw("CONCAT(users.first_name, ' ', users.last_name) LIKE ?", ["%{$cleanedKeyword}%"]);
                    });
                }
            })
            ->orderColumn('first_name', function ($query, $order) {
                $query->orderBy('users.first_name', $order)
                    ->orderBy('users.last_name', $order);
            })
            ->editColumn('total_booking', function ($data) {
                $totalBookings = ($data->totalBookings ?? 0) + ($data->totalPackageBookings ?? 0);
                return $totalBookings;
            })
            ->editColumn('total_service_amount', function ($data) {
                return $data->total_service_amount;
                // $totalServiceAmount = $data->employeeBooking->isEmpty() ? 0 : $data->employeeBooking->first()->total_service_amount;
                // // $totalPackageAmount = $data->bookingPackages->isEmpty() ? 0 : $data->bookingPackages->first()->total_package_amount;
                // $totalAmount = $totalServiceAmount;// + $totalPackageAmount;
                // return Currency::format($totalAmount);
            })
            ->editColumn('total_commission_earn', function ($data) {
                return Currency::format($this->getUnpaidAmount($data, 'commission'));
            })
            ->editColumn('total_tips_earn', function ($data) {
                return Currency::format($this->getUnpaidAmount($data, 'tip'));
            })
            ->editColumn('total_pay', function ($data) {
                return Currency::format($this->getUnpaidAmount($data)->total_pay);
            })
            ->orderColumn('total_services', function ($query, $order) {
                $query->orderBy('booking_servicesdata_count', $order);
            }, 'total_services')
            ->orderColumn('total_service_amount', function ($query, $order) {
                $query->orderBy(new Expression('(SELECT SUM(service_price) FROM booking_services WHERE employee_id = users.id)'), $order);
            }, 1)
            ->orderColumn('total_commission_earn', function ($query, $order) {
                $query->orderBy(new Expression('(SELECT SUM(commission_amount) FROM commission_earnings WHERE employee_id = users.id)'), $order);
            }, 1)
            ->orderColumn('total_tips_earn', function ($query, $order) {
                $query->orderBy(new Expression('(SELECT SUM(tip_amount) FROM tip_earnings WHERE employee_id = users.id)'), $order);
            }, 1)
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->toJson();
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

        $data = Earning::findOrFail($id);

        return view('earning::backend.earning.show', compact('module_action', "$data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $query = User::where('id', $id)->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.mobile')
            ->withCount([
                'employeeBooking as totalBookings' => function ($q) {
                    $q->groupBy('employee_id');
                }
            ])
            ->with([
                'employeeBooking' => function ($q) {
                    $q->select('employee_id')
                        ->selectRaw('COUNT(DISTINCT booking_id) as totalBookings')
                        ->selectRaw('SUM(service_price) as total_service_amount')
                        ->groupBy('employee_id');
                }
            ])
            ->with('commission_earning')
            ->with('tip_earning')
            ->with('employeeEarnings')
            ->whereHas('commission_earning', function ($q) {
                $q->where('commission_status', 'unpaid');
            })->first();

        $unpaidAmount = $this->getUnpaidAmount($query);
        $data = [
            'id' => $query->id,
            'full_name' => $query->full_name,
            'email' => $query->email,
            'mobile' => $query->mobile,
            'profile_image' => $query->profile_image,
            'description' => '',
            'commission_earn' => Currency::format($unpaidAmount->total_commission_earn),
            'tip_earn' => Currency::format($unpaidAmount->total_tips_earn),
            'amount' => Currency::format($unpaidAmount->total_pay),
            'payment_method' => '',
        ];

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
        $data = $request->all();

        $query = User::role('employee')->with('commission_earning', 'tip_earning')->find($id);

        $unpaidAmount = $this->getUnpaidAmount($query);

        $earning_data = [
            'employee_id' => $id,
            'total_amount' => $unpaidAmount->total_pay,
            'payment_date' => Carbon::now(),
            'payment_type' => $data['payment_method'],
            'description' => $data['description'],
            'tip_amount' => $unpaidAmount->total_tips_earn,
            'commission_amount' => $unpaidAmount->total_commission_earn,
        ];

        $earning_data = EmployeeEarning::create($earning_data);

        CommissionEarning::where('employee_id', $id)->update(['commission_status' => 'paid']);
        TipEarning::where('employee_id', $id)->update(['tip_status' => 'paid']);

        $message = __('messages.payment_done');

        return response()->json(['message' => $message, 'status' => true], 200);
    }
}

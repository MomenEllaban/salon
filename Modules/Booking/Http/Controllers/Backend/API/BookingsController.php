<?php

namespace Modules\Booking\Http\Controllers\Backend\API;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingService;
use Modules\Booking\Trait\BookingTrait;
use Modules\Booking\Trait\PaymentTrait;
use Modules\Booking\Transformers\BookingDetailResource;
use Modules\Booking\Transformers\BookingPackageDetailResource;
use Modules\Booking\Transformers\BookingListResource;
use Modules\Booking\Transformers\BookingResource;
use Modules\Constant\Models\Constant;
use Modules\Package\Models\BookingPackages;
use Modules\Package\Models\Package;
use Modules\Package\Models\PackageService;
use Modules\Package\Models\UserPackage;
use Modules\Package\Models\UserPackageServices;
use Modules\Promotion\Models\Coupon;
use Modules\Promotion\Models\Promotion;
use Modules\Promotion\Models\UserCouponRedeem;
use Modules\Package\Models\BookingPackageService;
//use Modules\Booking\Trait\BookingTrait;

class BookingsController extends Controller
{
    use BookingTrait;
    use PaymentTrait;
    public function __construct()
    {
        // Page Title
        $this->module_title = 'Bookings';
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if (!empty($request->date) && !empty($request->date)) {
            $data['start_date_time'] = Carbon::createFromFormat('d/m/Y h:i A', $data['date'] . ' ' . $data['time']);
        }

        $data['user_id'] = !empty($request->user_id) ? $request->user_id : auth()->user()->id;
        $userId = $data['user_id'];
        $is_reclaim = false;
        $alreadyPurchased = false;
        if (!empty($request->packages) && (!$request->has('is_reclaim') || !$request->is_reclaim)) {

            foreach ($request->packages as $key => $value) {
                $existingPackage = UserPackage::where('package_id', $value['id'])
                    ->where('user_id', $userId)
                    ->exists();
                if ($existingPackage) {
                    $alreadyPurchased = true;
                    return response()->json(['message' => 'Package already purchased.', 'status' => false], 200);
                }
            }
        }

        $booking = Booking::create($data);
      
        if (!empty($data['coupon_code'])) {
            $coupon = UserCouponRedeem::where('coupon_code', $data['coupon_code'])->first();
            $coupon_data = Coupon::where('coupon_code', $data['coupon_code'])->first();

            $totalPrice = 0;

            // Calculate the total price based on services or packages
            if (!empty($data['services'])) {
                $totalPrice = array_sum(array_column($data['services'], 'service_price'));
            } elseif (!empty($data['packages'])) {
                $totalPrice = array_sum(array_column($data['packages'], 'package_price'));
            }
            // Apply the discount validation
            if ($data['couponDiscountamount'] > $totalPrice) {
                return response()->json(['valid' => false, 'message' => 'Discount exceeds the total price', 'status' => false], 200);
            }
            if (!$coupon) {
                if ($coupon_data->is_expired == 1) {
                    $message = 'Coupon has expired.';
                    return response()->json(['message' => $message, 'status' => false], 200);
                } else {
                    $redeemCoupon = [
                        'coupon_code' => $data['coupon_code'],
                        'discount' => $data['couponDiscountamount'],
                        'user_id' => $data['user_id'],
                        'coupon_id' => $coupon_data->id,
                        'booking_id' => $booking->id,
                    ];

                    $user_coupon = UserCouponRedeem::create($redeemCoupon);

                    $couponRedemptionsCount = UserCouponRedeem::where('coupon_id', $user_coupon->coupon_id)->count();
                    if ($coupon_data->use_limit && $couponRedemptionsCount >= $coupon_data->use_limit) {
                        Coupon::where('coupon_code', $data['coupon_code'])->update(['is_expired' => 1]);
                        if ($coupon = Coupon::where('coupon_code', $data['coupon_code'])->first()) {
                            Promotion::where('id', $coupon->promotion_id)->update(['status' => 0]);
                        }
                    }
                }
            } else {
                if ($coupon_data->is_expired == 1) {
                    $message = 'Coupon has expired.';
                    return response()->json(['message' => $message, 'status' => false], 200);
                } else {
                    $couponRedemptionsCount = UserCouponRedeem::where('coupon_id', $coupon->coupon_id)->count();
                    if ($coupon_data->use_limit && $couponRedemptionsCount >= $coupon_data->use_limit) {
                        $message = 'Your coupon limit has been reached.';
                        return response()->json(['message' => $message, 'status' => false], 200);
                    } else {
                        $redeemCoupon = [
                            'coupon_code' => $data['coupon_code'],
                            'discount' => $data['couponDiscountamount'],
                            'user_id' => $data['user_id'],
                            'coupon_id' => $coupon_data->id,
                            'booking_id' => $booking->id,
                        ];
                  
                        UserCouponRedeem::create($redeemCoupon);
                        $total_coupon = UserCouponRedeem::where('coupon_code', $data['coupon_code'])->count();
                        if ($total_coupon == $coupon_data->use_limit) {
                            Coupon::where('coupon_code', $data['coupon_code'])->update(['is_expired' => 1]);
                            if ($coupon = Coupon::where('coupon_code', $data['coupon_code'])->first()) {
                                Promotion::where('id', $coupon->promotion_id)->update(['status' => 0]);
                            }
                        }
                    }
                }
            }
        }
        //if package reclaim


        if ($request->has('is_reclaim') && isset($request->packages) && $request->is_reclaim == true) {
            $is_reclaim = true;

            $this->updateAPIBookingPackage($request->packages, $booking->id, $request->employee_id, $userId, $is_reclaim);
            foreach ($request->packages as $key => $value) {
                $UserPackages = UserPackage::with('bookings')
                    ->where('package_id', $value['id'])
                    ->where('user_id', $userId)
                    ->get();

                $bookingPackage = BookingPackages::where('booking_id', $booking->id)->first();

                if ($UserPackages->isNotEmpty()) {
                    foreach ($UserPackages as $UserPackage) {
                        foreach ($value['services'] as $service) {
                            $userPackageService = UserPackageServices::where('user_package_id', $UserPackage->id)
                                ->whereHas('packageService', function ($query) use ($service) {
                                    $query->where('service_id', $service['service_id']);
                                })->first();

                            if ($userPackageService) {
                                if ($userPackageService->qty >= 1) {
                                    $bookingPackageService = BookingPackageService::Create([
                                        'booking_id' => $booking->id,
                                        'package_id' => $value['id'],
                                        'user_id' => $userId,
                                        'package_service_id' => $userPackageService->package_service_id,
                                        'booking_package_id' => $bookingPackage->id,
                                        'service_name' => $userPackageService->service_name,
                                        'qty' => $userPackageService->qty - 1,
                                        'service_id' => $service['service_id'],
                                    ]);
                                    $userPackageService->qty -= 1;
                                    $userPackageService->save();
                                }

                                if ($userPackageService->qty == 0) {
                                    $userPackageService->delete();
                                }
                            }
                        }

                        $remainingServices = UserPackageServices::where('user_package_id', $UserPackage->id)->count();
                        if ($remainingServices == 0) {
                            $UserPackage->delete();
                        } else {
                            $UserPackage->type = 'reclaimed';
                            $UserPackage->save();
                        }
                    }
                }
            }
        } else  if ($alreadyPurchased == false) {
            $this->updateAPIBookingPackage($request->packages, $booking->id, $request->employee_id, $userId, $is_reclaim);
            $this->storeApiUserPackage($booking->id);


        }





        //service
        if (!empty($request->services)) {
            $this->updateBookingService($request->services, $booking->id);
        }

        $message = 'New ' . Str::singular($this->module_title) . ' Added';
        try {
            $type = 'new_booking';
            $messageTemplate = 'New booking #[[booking_id]] has been booked.';
            $notify_message = str_replace('[[booking_id]]', $booking->id, $messageTemplate);
            $this->sendNotificationOnBookingUpdate($type,$notify_message,$booking);

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    
        return response()->json(['message' => $message, 'status' => true, 'booking_id' => $booking->id], 200);
    }

    public function update(Request $request)
    {
        $booking = Booking::findOrFail($request->id);

        if ($request->has('status') && $request->status == 'cancelled') {

            if (!in_array($booking->status, ['check_in', 'checkout', 'completed'])) {

                $booking->update(['status' => 'cancelled']);
            } else {

                return response()->json(['message' => "Cannot cancel a booking with status: {$booking->status}"], 422);
            }
        } else {

            $booking->update($request->all());

            if (!empty($request->packages)) {

                $this->updateAPIBookingPackage($request->packages, $booking->id, $request->employee_id);
            }
            if (!empty($request->services)) {
                $this->updateBookingService($request->services, $booking->id);
            }
        }

        $message = __('booking.booking_update');

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function updateStatus(Request $request)
    {
        $id = $request->id;
        $booking = Booking::with('services', 'user', 'products')->findOrFail($id);
        $status = $request->status;

        if (isset($request->action_type) && $request->action_type == 'update-status') {
            $status = $request->value;
        }

        $booking->update(['status' => $status]);

        $notify_type = null;

        switch ($status) {
            case 'check_in':
                $notify_type = 'check_in_booking';
                break;
            case 'checkout':
                $notify_type = 'checkout_booking';
                break;
            case 'completed':
                $notify_type = 'complete_booking';
                break;
            case 'cancelled':
                $notify_type = 'cancel_booking';
                break;
        }

        if (isset($notify_type)) {
            try {
                $this->sendNotificationOnBookingUpdate($notify_type,'',$booking);
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }

        $message = __('booking.status_update');

        return response()->json(['data' => new BookingResource($booking), 'message' => $message, 'status' => true]);
    }

    public function bookingList(Request $request)
    {
        $user = \Auth::user();

        $booking = Booking::where('user_id', $user->id)->with('booking_service', 'bookingTransaction', 'bookingPackages.bookedPackageService');

        if ($request->has('status') && isset($request->status)) {

            $status = explode(',', $request->status);
            $booking->whereIn('status', $status);
        }
        if ($request->has('branch_id') && !empty($request->branch_id)) {
            $booking->where('branch_id', $request->branch_id);
        }
        $per_page = $request->input('per_page', 10);
        if ($request->has('per_page') && !empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page === 'all') {
                $per_page = $booking->count();
            }
        }
        $orderBy = 'desc';
        if ($request->has('order_by') && !empty($request->order_by)) {
            $orderBy = $request->order_by;
        }
        // Apply search conditions for booking ID, employee name, and service name
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $booking->where(function ($query) use ($search) {
                $query->where('id', 'LIKE', "%$search%")
                    ->orWhereHas('services', function ($subquery) use ($search) {
                        $subquery->whereHas('employee', function ($employeeQuery) use ($search) {
                            $employeeQuery->where(function ($nameQuery) use ($search) {
                                $nameQuery->where('first_name', 'LIKE', "%$search%")
                                    ->orWhere('last_name', 'LIKE', "%$search%");
                            });
                        });
                    })
                    ->orWhereHas('services', function ($subquery) use ($search) {
                        $subquery->whereHas('service', function ($employeeQuery) use ($search) {
                            $employeeQuery->where('name', 'LIKE', "%$search%");
                        });
                    });
            });
        }

        $booking = $booking->orderBy('updated_at', $orderBy)->paginate($per_page);

        $items = BookingListResource::collection($booking);

        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => __('booking.booking_list'),
        ], 200);
    }

    public function bookingDetail(Request $request)
    {
        $id = $request->id;

        $booking_data = Booking::with(['branch', 'user', 'booking_service', 'payment', 'products', 'bookingPackages'])->where('id', $id)->first();

        if ($booking_data == null) {
            $message = __('booking.booking_not_found');

            return response()->json([
                'status' => false,
                'message' => __('booking.booking_not_found'),
            ], 200);
        }



        $booking_detail = new BookingDetailResource($booking_data);


        return response()->json([
            'status' => true,
            'data' => $booking_detail,
            'message' => __('booking.booking_detail'),
        ], 200);
    }

    public function searchBookings(Request $request)
    {
        $keyword = $request->input('keyword');

        $bookings = Booking::where('note', 'like', "%{$keyword}%")
            ->with('branch', 'user')
            ->get();

        return response()->json([
            'status' => true,
            'data' => BookingResource::collection($bookings),
            'message' => __('booking.search_booking'),
        ], 200);
    }

    public function statusList()
    {
        $booking_status = Constant::getAllConstant()->where('type', 'BOOKING_STATUS');
        $checkout_sequence = $booking_status->where('name', 'check_in')->first()->sequence ?? 0;
        $bookingColors = Constant::getAllConstant()->where('type', 'BOOKING_STATUS_COLOR');
        $statusList = [];
        $finalstatusList = [];

        foreach ($booking_status as $key => $value) {
            if ($value->name !== 'cancelled') {
                $statusList = [
                    'status' => $value->name,
                    'title' => $value->value,
                    'color_hex' => $bookingColors->where('sub_type', $value->name)->first()->name,
                    'is_disabled' => $value->sequence >= $checkout_sequence,
                ];
                array_push($finalstatusList, $statusList);
                $nextStatus = $booking_status->where('sequence', $value->sequence + 1)->first();
                if ($nextStatus) {
                    $statusList[$value->name]['next_status'] = $nextStatus->name;
                }
            } else {
                $statusList = [
                    'status' => $value->name,
                    'title' => $value->value,
                    'color_hex' => $bookingColors->where('sub_type', $value->name)->first()->name,
                    'is_disabled' => true,
                ];
                array_push($finalstatusList, $statusList);
            }
        }

        return response()->json([
            'status' => true,
            'data' => $finalstatusList,
            'message' => __('booking.booking_status_list'),
        ], 200);
    }

    public function bookingUpdate(Request $request)
    {
        $data = $request->all();
        $id = $request->id;

        if (!empty($request->date)) {
            $data['start_date_time'] = $request->date;
        }
        $bookingdata = Booking::find($id);

        $bookingdata->update($data);

        $booking = Booking::findOrFail($id);

        $booking->update($data);

        $bookingService = BookingService::where('booking_id', $booking->id)->get();

        if (!empty($request->packages)) {

            $this->updateAPIBookingPackage($request->packages, $booking->id, $request->employee_id);
        }
        $this->updateBookingService($bookingService, $booking->id);

        if (!empty($request->packages)) {
            $this->updateBookingPackage($request->packages, $booking->id);
        }

        return response()->json([
            'status' => true,
            'message' => __('booking.booking_update'),
        ], 200);
    }
}

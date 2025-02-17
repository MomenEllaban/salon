<?php

namespace Modules\QuickBooking\Http\Controllers\Backend;

use App\Events\Backend\UserCreated;
use App\Http\Controllers\Controller;
use App\Models\Address;
// Traits
use App\Models\Branch;
use Modules\BussinessHour\Models\BussinessHour;
use Modules\Holiday\Models\Holiday;
// Listing Models
use App\Models\User;
use App\Notifications\UserAccountCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\Booking\Models\Booking;
// Events
use Modules\Booking\Trait\BookingTrait;
use Modules\Service\Transformers\ServiceResource;
use Modules\Tax\Models\Tax;
use Carbon\Carbon;
use Modules\Service\Models\Service;
class QuickBookingsController extends Controller
{
    use BookingTrait;

    public function index()
    {
        if (! setting('is_quick_booking')) {
            return abort(404);
        }

        return view('quickbooking::backend.quickbookings.index');
    }

    // API Methods for listing api
    public function branch_list()
    {
        $list = Branch::active()->with('address')->select('id', 'name', 'branch_for', 'contact_number', 'contact_email')->get();

        return $this->sendResponse($list, __('booking.booking_branch'));
    }

    public function slot_time_list(Request $request)
    {
        $day = date('l', strtotime($request->date));

        $data = $this->requestData($request);
        $businessHours = BussinessHour::where('branch_id', $data['branch_id'])->get();
       $service=Service::where('id',$data['service_id'])->first();
       $serviceDuration=$service->duration_min;

        $slots = $this->getSlots($data['date'], $day, $data['branch_id'], $serviceDuration, $data['employee_id']);
        
        return $this->sendResponse($slots,$businessHours, __('booking.booking_timeslot'));
    }


    public function slot_date_list(Request $request)
    {
        $data = $this->requestData($request);
    
        $businessHours = BussinessHour::where('branch_id', $data['branch_id'])->get();
        $holidays = Holiday::where('branch_id', $data['branch_id'])->get();
            $holidayDates = $holidays->map(function ($holiday) {
            return Carbon::parse($holiday->date)->format('Y-m-d');
        });
    
        return response()->json([
            'data' => $businessHours,
            'holidays' => $holidayDates,
        ]);
    }
    
    public function services_list(Request $request)
    {
        $branch_id = $request->branch_id;

        $data = $this->requestData($request);

        $item = Branch::find($data['branch_id']);

        $items = $item->services->where('status', 1);

        $list = ServiceResource::collection($items);

        return $this->sendResponse($list, __('booking.booking_sevice'));
    }

    public function employee_list(Request $request)
    {
        $data = $this->requestData($request);

        $list = User::whereHas('services', function ($query) use ($data) {
            $query->where('service_id', $data['service_id']);
        })
            ->whereHas('branches', function ($query) use ($data) {
                $query->where('branch_id', $data['branch_id']);
            })
            ->get();

        return $this->sendResponse($list, __('booking.booking_employee'));
    }

    // Create Method for Booking API
    public function create_booking(Request $request)
    {
        $userRequest = $request->user;
        $user = User::where('email', $userRequest['email'])->first();

        if (! isset($user)) {
            $userRequest['password'] = Hash::make('12345678');
            $user = User::create($userRequest);
            // Sync Roles
            $roles = ['user'];
            $user->syncRoles($roles);

            \Artisan::call('cache:clear');

            event(new UserCreated($user));

            $data = [
                'password' => '12345678',
            ];

            try {
                $user->notify(new UserAccountCreated($data));
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        }

        $bookingData = $request->booking;
        $bookingData['user_id'] = $user->id;
        $bookingData['created_by'] = $user->id;
        $bookingData['updated_by'] = $user->id;
        $booking = Booking::create($bookingData);

        $this->updateBookingService($bookingData['services'], $booking->id);

        $booking['user'] = $booking->user;

        $booking['services'] = $booking->services;

        $booking['branch'] = $booking->branch;

        $branchAddress = Address::where('addressable_id', $booking['branch']->id)
            ->where('addressable_type', get_class($booking['branch']))
            ->first();

        $booking['branch_address'] = $branchAddress;

        try {
            $notify_type = 'cancel_booking';
            $messageTemplate = 'New booking #[[booking_id]] has been booked.';
            $notify_message = str_replace('[[booking_id]]', $booking->id, $messageTemplate);
            $this->sendNotificationOnBookingUpdate($notify_type,$notify_message,$booking);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

              $booking['tax'] = Tax::active()
                  ->whereNull('module_type')
                  ->orWhere('module_type', 'services')
                  ->where('status',1)
                  ->get()
                  ->map(function ($tax) {
                      return [
                          'name' => $tax->title,
                          'type' => $tax->type,
                          'percent' => $tax->type == 'percent' ? $tax->value : 0,
                          'tax_amount' => $tax->type != 'percent' ? $tax->value : 0,
                      ];
                  })
                  ->toArray();

        return $this->sendResponse($booking, __('booking.booking_create'));
    }

    public function requestData($request)
    {
        return [
            'branch_id' => $request->branch_id,
            'service_id' => $request->service_id,
            'date' => $request->date,
            'employee_id' => $request->employee_id,
            'start_date_time' => $request->start_date_time,
        ];
    }
}

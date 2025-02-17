<?php

namespace Modules\Booking\Http\Controllers\Backend\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingTransaction;
use Modules\Booking\Trait\PaymentTrait;
use Modules\Commission\Models\CommissionEarning;
use Modules\Package\Models\BookingPackages;
use Modules\Package\Models\UserPackage;
use Modules\Tip\Models\TipEarning;

class PaymentController extends Controller
{
    use PaymentTrait;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Payment';
    }

    public function savePayment(Request $request)
    {
        $data = $request->all();
        $data['tip_amount'] = $data['tip'] ?? 0;

        $booking = Booking::where('id', $data['booking_id'])->first();

        $payment = BookingTransaction::create($data);

        $earning_data = $this->commissionData($payment);
        $this->storeApiUserPackage($data['booking_id']);

        if (isset($earning_data['commission_data']) && $earning_data['commission_data'] != null) {
            $booking->commission()->save(new CommissionEarning($earning_data['commission_data']));
        }

        if ($data['tip_amount'] > 0) {
            $booking->tip()->save(new TipEarning([
                'employee_id' => $earning_data['employee_id'],
                'tip_amount' => number_format($data['tip_amount'], 2),
                'tip_status' => 'unpaid',
                'payment_date' => null,
            ]));
        }

        $message = __('booking.payment_done');

        return response()->json(['message' => $message, 'status' => true], 200);
    }
}

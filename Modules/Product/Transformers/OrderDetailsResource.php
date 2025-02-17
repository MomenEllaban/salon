<?php

namespace Modules\Product\Transformers;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $order_prefix_data = Setting::where('name', 'inv_prefix')->first();
        $order_prefix = $order_prefix_data ? $order_prefix_data->val : '';

        return [

            'id' => $this->id,
            'user_id' => $this->user_id,
            'delivery_status' => $this->delivery_status,
            'payment_status' => $this->payment_status,
            'order_code' => $order_prefix.optional($this->orderGroup)->order_code,
            'sub_total_amount' => optional($this->orderGroup)->sub_total_amount,
            'total_tax_amount' => optional($this->orderGroup)->total_tax_amount,
            'logistic_charge' => optional($this->orderGroup)->total_shipping_cost,
            'total_amount' => $this->total_admin_earnings,
            'payment_method' => optional($this->orderGroup)->payment_method,
            'order_date' => $this->created_at,
            'logistic_name' => $this->logistic_name,
            'logistic_contact' => optional($this->logistic)->mobile,
            'logistic_address' => optional($this->logistic)->description,
            'standard_delivery_charge' => optional($this->logistic)->standard_delivery_charge,
            'total_payable_amount' => $this->total_payable_amount,
            'total_price_amount' => optional($this->logistic)->standard_delivery_charge + $this->total_payable_amount,
            'expected_delivery_date' => $this->calculateExpectedDeliveryDate(),
            'delivery_days' => optional($this->logistic)->standard_delivery_time,
            'delivery_time' => optional($this->logistic)->standard_delivery_time,
            'user_name' => optional(optional($this->orderGroup)->shippingAddress)->first_name.' '.optional(optional($this->orderGroup)->shippingAddress)->last_name,
            'address_line_1' => optional(optional($this->orderGroup)->shippingAddress)->address_line_1,
            'address_line_2' => optional(optional($this->orderGroup)->shippingAddress)->address_line_2,
            'phone_no' => optional($this->orderGroup)->phone_no,
            'alternative_phone_no' => optional($this->orderGroup)->alternative_phone_no,
            'city' => optional($this->orderGroup->shippingAddress->city_data)->name,
            'state' => optional($this->orderGroup->shippingAddress->state_data)->name,
            'country' => optional($this->orderGroup->shippingAddress->country_data)->name,
            'postal_code' => optional($this->orderGroup->shippingAddress)->postal_code,
            'product_details' => OrderItemResource::collection($this->orderItems),

        ];
    }

    private function calculateExpectedDeliveryDate()
    {
        $orderDate = Carbon::parse($this->created_at);

        if ($this->logistic != null) {
            $deliveryTimeInDays = intval($this->logistic->standard_delivery_time);

            return $orderDate->addDays($deliveryTimeInDays);

            //  return $expectedDeliveryDate;
        }

        return null;
    }
}

<?php

namespace Modules\Booking\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [

            'id' => $this->id,
            'product_name' => $this->product_name,
            'booking_id' => $this->booking_id,
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'product_variation_id' => $this->product_variation_id,
            'employee_id' => $this->employee_id,
            'product_qty' => $this->product_qty,
            'product_price' => $this->product_price,
            'discounted_price' => $this->discounted_price,
            'discount_value' => $this->discount_value,
            'discount_type' => $this->discount_type,
            'variation_name' => $this->variation_name,
            'product_image' => $this->product->feature_image,

        ];
    }
}

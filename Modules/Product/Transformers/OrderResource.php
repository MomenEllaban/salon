<?php

namespace Modules\Product\Transformers;

use App\Models\Setting;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        $order_prefix_data = Setting::where('name', 'inv_prefix')->first();
        $order_prefix = $order_prefix_data ? $order_prefix_data->val : '';

        return [

            'id' => $this->id,
            'user_id' => $this->user_id,
            'delivery_status' => $this->delivery_status,
            'payment_status' => $this->payment_status,
            'total_amount' => $this->total_admin_earnings,
            'order_code' => $order_prefix.optional($this->orderGroup)->order_code,
            'product_details' => OrderItemResource::collection($this->orderItems),

        ];
    }
}

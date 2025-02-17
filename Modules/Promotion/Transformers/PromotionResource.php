<?php

namespace Modules\Promotion\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        // dd($this);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'coupon_image' => $this->feature_image,            
            'coupon_code' => optional($this->coupon->first())->coupon_code,
            'coupon_type' => optional($this->coupon->first())->coupon_type,
            'start_date_time' => optional($this->coupon->first())->start_date_time,
            'end_date_time' => optional($this->coupon->first())->end_date_time,
            'is_expired' => optional($this->coupon->first())->is_expired,
            'discount_type' => optional($this->coupon->first())->discount_type,
            'discount_percentage' =>optional($this->coupon->first())->discount_percentage,
            'discount_amount' => optional($this->coupon->first())->discount_amount,
            'use_limit' => optional($this->coupon->first())->use_limit,
            'used_by' => optional($this->coupon->first())->used_by,
            'promotion_id' => optional($this->coupon->first())->promotion_id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,        
        ];
    }

}
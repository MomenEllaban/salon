<?php

namespace Modules\Product\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class WishListResource extends JsonResource
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
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'product_name' => optional($this->product)->name,
            'product_image' => optional($this->product)->media->pluck('original_url')->first(),
            'product_description' => optional($this->product)->short_description,
            'discount_value' => optional($this->product)->discount_value,
            'discount_type' => optional($this->product)->discount_type,
            'min_discounted_product_amount' => getDiscountedProductPrice($this->product->min_price, $this->product_id),
            'max_discounted_product_amount' => getDiscountedProductPrice($this->product->max_price, $this->product_id),
            'discount_start_date' => $this->product->discount_start_date ? Carbon::createFromTimestamp($this->product->discount_start_date)->format('Y-m-d') : null,
            'discount_end_date' => $this->product->discount_end_date ? Carbon::createFromTimestamp($this->product->discount_end_date)->format('Y-m-d') : null,
            'variation_data' => ProductVariationResource::collection($this->product->product_variations),
            'in_wishlist' => $request->has('user_id') ? checkInWishList($this->product_id, $request->input('user_id')) : 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}

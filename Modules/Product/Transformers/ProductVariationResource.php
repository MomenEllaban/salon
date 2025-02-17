<?php

namespace Modules\Product\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationResource extends JsonResource
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
            'variation_key' => $this->id,
            'sku' => $this->sku,
            'code' => $this->code,
            'location_id' => optional($this->product_variation_stock)->location_id,
            'product_stock_qty' => optional($this->product_variation_stock)->stock_qty,
            'is_stock_avaible' => optional($this->product_variation_stock)->stock_qty > 0 ? 1 : 0,
            'combination' => $this->combinations,
            'combination' => ProductCombinationResource::collection($this->combinations),
            'product_amount' => $this->price,
            'in_cart' => $request->has('user_id') ? checkInCart($this->id, $request->input('user_id')) : 0,
            'tax_include_product_price' => $this->price,
            'discounted_product_price' => getDiscountedProductPrice($this->price, $this->product_id),

        ];
    }
}

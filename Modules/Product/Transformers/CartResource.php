<?php

namespace Modules\Product\Transformers;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'product_variation_id' => $this->product_variation_id,
            'qty' => $this->qty,
            'unit_name' => optional($this->product->unit)->name,
            'product_name' => optional($this->product)->name,
            'product_image' => $this->product ? $this->product->media->pluck('original_url')->first() : null,
            'product_description' => optional($this->product)->short_description,
            'discount_value' => ($this->product && $this->product->discount_start_date && $this->product->discount_end_date && Carbon::now()->between(Carbon::createFromTimestamp($this->product->discount_start_date), Carbon::createFromTimestamp($this->product->discount_end_date))) ? optional($this->product)->discount_value : 0,
            'discount_type' => optional($this->product)->discount_type,
            'product_variation' => new ProductVariationResource($this->product_variation),
            'product_variation_type' => $this->product_variation?->combination?->variation_combination_data?->name ?? null,
            'product_variation_name' => $this->product_variation?->combination?->variation_combination_value?->name ?? null,
            'product_variation_value' => $this->product_variation?->combination?->variation_combination_value?->value ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}

<?php

namespace Modules\Product\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductCombinationResource extends JsonResource
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
            'product_variation_type' => optional($this->variation_combination_data)->name,
            'product_variation_name' => optional($this->variation_combination_value)->name,
            'product_variation_value' => optional($this->variation_combination_value)->value,

        ];
    }
}

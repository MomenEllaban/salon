<?php

namespace Modules\Logistic\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class LogisticZoneResource extends JsonResource
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
            'name' => $this->name,
            'contact'=>$this->mobile,
            'address'=>$this->description,
            'logistic_id' => $this->logistic_id,
            'logistic_name' => $this->logistic->name,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'standard_delivery_charge' => $this->standard_delivery_charge,
            'express_delivery_charge' => $this->express_delivery_charge,
            'standard_delivery_time' => $this->standard_delivery_time,
            'express_delivery_time' => $this->express_delivery_time,
            'cities' => $this->cities,

        ];
    }
}

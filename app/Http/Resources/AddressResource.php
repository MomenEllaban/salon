<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'postal_code' => $this->postal_code,
            'city' => $this->city ?? '',
            'state' => $this->state ?? '',
            'country' => $this->country ?? '',

            // Use optional chaining to handle null relationships
            'city_name' => optional($this->city_data)->name ?? '',
            'state_name' => optional($this->state_data)->name ?? '',
            'country_name' => optional($this->country_data)->name ?? '',
            'is_primary' => $this->is_primary,
        ];
    }
}

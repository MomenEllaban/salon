<?php

namespace Modules\Employee\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeReviewResource extends JsonResource
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
            'staff_id' => $this->employee_id,
            'review_msg' => $this->review_msg,
            'rating' => $this->rating,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'username' => optional($this->user)->full_name ?? default_user_name(),
        ];
    }
}

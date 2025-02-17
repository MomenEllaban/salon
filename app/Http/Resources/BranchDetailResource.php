<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BranchDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $workingDays = $this->businessHours->map(function ($hour) {
            return [
                'day' => $hour['day'],
                'start_time' => $hour['start_time'],
                'end_time' => $hour['end_time'],
                'is_holiday' => $hour['is_holiday'],
                'breaks' => $hour['breaks'],
            ];
        });

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'address_line_1' => $this->address->address_line_1,
            'latitude' => $this->address->latitude,
            'longitude' => $this->address->longitude,
            'contact_email' => $this->contact_email,
            'contact_number' => $this->contact_number,
            'description' => $this->description,
            'payment_method' => $this->payment_method,
            'manager_id' => $this->manager_id,
            'branch_for' => $this->branch_for,
            'branch_image' => $this->media->pluck('original_url')->first(),
            'gallery' => $this->gallerys->pluck('full_url'),
            'rating_star' => round(($this->average_rating), 1),
            'total_review' => $this->total_review,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'working_days' => $workingDays,
        ];
    }
}

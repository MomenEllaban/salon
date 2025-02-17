<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Employee\Models\BranchEmployee;
use Modules\Employee\Models\EmployeeRating;

class BranchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $employeeIds = BranchEmployee::where('branch_id', $this->id)
            ->distinct()
            ->pluck('employee_id');

        $averageRating = EmployeeRating::whereIn('employee_id', $employeeIds)->avg('rating');
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
            'address_line_1' => optional($this->address)->address_line_1,
            'latitude' => optional($this->address)->latitude,
            'longitude' => optional($this->address)->longitude,
            'payment_method' => $this->payment_method,
            'manager_id' => $this->manager_id,
            'branch_for' => $this->branch_for,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'contact_email' => $this->contact_email,
            'contact_number' => $this->contact_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'rating_star' => round(($averageRating), 1),
            'total_review' => EmployeeRating::whereIn('employee_id', $employeeIds)->count(),
            'branch_image' => $this->media->pluck('original_url')->first(),
            'working_days' => $workingDays,

        ];
    }
}

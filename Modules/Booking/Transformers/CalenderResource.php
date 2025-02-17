<?php

namespace Modules\Booking\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CalenderResource extends JsonResource
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
            'start' => date('Y-m-d H:i', strtotime($this->start_date_time)),
            'end' => date('Y-m-d H:i', strtotime($this->end_date_time)),
            'resourceId' => $this->employee_id,
            'title' => $this->name,
            'color' => $this->setColor($this->status),
        ];
    }

    public function setColor(string $status)
    {
        return config('booking.STATUS')[$status]['color_hex'];
    }
}

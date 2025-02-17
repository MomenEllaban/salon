<?php

namespace Modules\Booking\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Service\Models\Service;

class BookingService extends Model
{
    use HasFactory;

    protected $fillable = ['sequance', 'booking_id', 'service_id', 'employee_id', 'service_price', 'duration_min', 'status', 'start_date_time'];

    protected $casts = [

        'sequance' => 'integer',
        'booking_id' => 'integer',
        'service_id' => 'integer',
        'employee_id' => 'integer',
        'service_price' => 'double',
        'duration_min' => 'integer',

    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class);
    }
}

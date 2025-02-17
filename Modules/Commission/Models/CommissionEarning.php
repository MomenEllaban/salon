<?php

namespace Modules\Commission\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Booking\Models\Booking;
use Modules\Earning\Models\Earning;

class CommissionEarning extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'commissionable', 'model_id', 'commission_amount', 'commission_status', 'payment_date'];

    protected $casts = [

        'employee_id' => 'integer',
        'commissionable_id' => 'integer',
        'commission_amount' => 'double',

    ];

    protected static function newFactory()
    {
        return \Modules\Commission\Database\factories\CommissionEarningFactory::new();
    }

    public function getbooking()
    {
        return $this->belongsTo(Booking::class, 'commissionable_id');
    }

    public function earning()
    {
        return $this->hasMany(Earning::class, 'employee_id');
    }
}

<?php

namespace Modules\Booking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Commission\Models\CommissionEarning;
use Modules\Tip\Models\TipEarning;

class BookingTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'external_transaction_id', 'transaction_type', 'discount_percentage', 'discount_amount', 'tip_amount', 'tax_percentage', 'payment_status'];

    protected $casts = [
        'tax_percentage' => 'array',
        'booking_id' => 'integer',
        'discount_percentage' => 'double',
        'discount_amount' => 'double',
        'tip_amount' => 'double',
    ];

    protected static function newFactory()
    {
        return \Modules\Booking\Database\factories\BookingTransactionFactory::new();
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class)->with('services');
    }

    public function commissions()
    {
        return $this->hasMany(CommissionEarning::class, 'employee_id');
    }

    public function tipEarnings()
    {
        return $this->hasMany(TipEarning::class, 'tippable_id', 'booking_id');
    }

    public function commissionEarnings()
    {
        return $this->hasMany(CommissionEarning::class, 'commissionable_id', 'booking_id');
    }
}

<?php

namespace Modules\Product\Models;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderGroup extends Model
{
    use HasFactory;

    protected $casts = [
        'user_id' => 'integer',
        'order_code' => 'integer',
        'shipping_address_id' => 'integer',
        'billing_address_id' => 'integer',
        'location_id' => 'integer',
        'sub_total_amount' => 'double',
        'total_tax_amount' => 'double',
        'total_coupon_discount_amount' => 'double',
        'total_shipping_cost' => 'double',
        'grand_total_amount' => 'double',
        'is_manual_payment' => 'integer',
        'additional_discount_value' => 'integer',
        'total_discount_amount' => 'double',
        'total_tips_amount' => 'double',
    ];

    protected static function newFactory()
    {
        return \Modules\Product\Database\factories\OrderGroupFactory::new();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (OrderGroup::first() == null) {
                $model->order_code = setting('order_code_start') != null ? (int) setting('order_code_start') : 1;
            } else {
                $model->order_code = (int) OrderGroup::max('order_code') + 1;
            }
        });
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id', 'id');
    }

    public function billingAddress()
    {
        return $this->belongsTo(Address::class, 'billing_address_id', 'id');
    }

    // public function billingAddress()
    // {
    //     return $this->belongsTo(UserAddress::class, 'billing_address_id', 'id');
    // }

    // public function location()
    // {
    //     return $this->belongsTo(Location::class);
    // }

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}

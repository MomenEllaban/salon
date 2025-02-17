<?php

namespace Modules\Promotion\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Promotion\Database\factories\CouponFactory;
use App\Models\User;
class Coupon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'promotions_coupon';

    protected $fillable = ['coupon_code','coupon_type','is_expired', 'timezone', 'discount_type', 'discount_percentage', 'discount_amount', 'start_date_time', 'end_date_time', 'promotion_id', 'use_limit'];

    protected static function newFactory(): CouponFactory
    {
        //return CouponFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::retrieved(function ($coupon) {
            $today = now();
            if ($coupon->end_date_time < $today && !$coupon->is_expired) {
                $coupon->is_expired = 1;
                $coupon->save();
            }
        });
    }
    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promotion_id');
    }
    public function userCouponRedeem(){
        return $this->hasMany(UserCouponRedeem::class, 'coupon_code');
    }
    public function userRedeems()
    {
        return $this->hasManyThrough(User::class, UserCouponRedeem::class, 'coupon_id', 'id', 'id', 'user_id');
    }
}

<?php

namespace Modules\Package\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Package\Models\UserPackage;

class UserPackageRedeem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['employee_id', 'package_id', 'discounted_price','booking_id','service_id','service_price'];

    public function package()
    {
        return $this->belongsTo(UserPackage::class, 'user_packages', 'employee_id', 'package_price');
    }
}

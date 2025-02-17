<?php

namespace Modules\Package\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Package\Database\factories\UserPackageFactory;
use Modules\Package\Models\Package;
use App\Models\User;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingTransaction;
class UserPackage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['booking_id','user_id','employee_id','package_price','package_id', 'type','purchase_date'];

    protected $casts = [
        'booking_id' => 'integer',
        'package_id' => 'integer',
        'employee_id' => 'integer',
        'package_price' => 'double',
        'type' => 'string'
     ];

    public function employee()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class)->withTrashed();;
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
    public function packageService()
    {
        return $this->hasMany(PackageService::class, 'package_id');
    }

    public function bookings()
    {
        return $this->belongsTo(Booking::class);
    }

    public function userPackageServices(){
        return $this->hasMany(UserPackageServices::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function bookingTransaction()
    {
        return $this->hasOne(BookingTransaction::class,'booking_id','booking_id')->where('payment_status', 1);
    }

}

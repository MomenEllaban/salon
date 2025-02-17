<?php

namespace Modules\Package\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Package\Database\factories\BookingPackagesFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Package\Models\Package;
use Modules\Package\Models\UserPackageServices;
use App\Models\User;
use Modules\Booking\Models\Booking;
use Illuminate\Database\Eloquent\Builder;

class BookingPackages extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */


    protected $fillable = ['sequance', 'booking_id', 'package_id', 'user_id','employee_id', 'package_price', 'package_validity', 'is_reclaim','status'];

    protected $casts = [
        'sequance' => 'integer',
        'booking_id' => 'integer',
        'package_id' => 'integer',
        'user_id' => 'integer',
        'employee_id' => 'integer',
        'package_price' => 'double',
        'package_validity' => 'integer',

    ];
    protected static function newFactory(): BookingPackagesFactory
    {
        //return BookingPackagesFactory::new();
    }


    public function employee()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class)->withTrashed();
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
    public function packageService()
    {
        return $this->hasMany(PackageService::class,'package_id', 'package_id');
    }

    public function services(){
        return $this->hasMany(PackageService::class,'package_id', 'package_id')
        ->leftJoin('services', 'services.id', 'package_services.service_id');
    }
    public function userpackages()
    {
        return $this->hasMany(UserPackage::class,'booking_id', 'booking_id');
    }
    public function bookedUserPackage()
    {
        return $this->hasMany(UserPackage::class, 'user_id', 'user_id')
                    ->whereColumn('user_packages.package_id', 'package_id');

    }
    public function bookedPackageService()
    {
        return $this->hasMany(BookingPackageService::class, 'booking_package_id', 'id');
    }

    public function userPackageServices($bookingId)
    {
        return $this->belongsTo(UserPackageServices::class, 'user_id', 'user_id')
                    ->where('booking_packages.booking_id', $bookingId)
                    ->whereColumn('user_package_services.package_id', 'booking_packages.package_id');
    }


}

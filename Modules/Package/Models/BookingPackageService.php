<?php

namespace Modules\Package\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Package\Database\factories\BookingPackageServiceFactory;
use Modules\Service\Models\Service;

class BookingPackageService extends Model
{
    use HasFactory;
    protected $table = 'booking_package_services';

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'booking_package_id',
        'booking_id',
        'package_id',
        'package_service_id',
        'user_id',
        'qty',
        'service_id',
        'service_name',
    ];
    protected static function newFactory(): BookingPackageServiceFactory
    {
        //return BookingPackageServiceFactory::new();
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
    public function packageservice()
    {
        return $this->belongsTo(PackageService::class, 'package_service_id');
    }

    public function services()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}

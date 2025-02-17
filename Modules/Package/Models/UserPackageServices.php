<?php

namespace Modules\Package\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Package\Database\factories\UserPackageServicesFactory;

class userPackageServices extends Model
{
    use HasFactory;

    protected $table = 'user_package_services';
    protected $fillable = [
        'user_package_id',
        'package_service_id',
        'package_id',
        'user_id',
        'qty',
        'service_name',
    ];

    public function userPackage()
    {
        return $this->belongsTo(UserPackage::class);
    }

    public function packageService()
    {
        return $this->belongsTo(PackageService::class, 'package_service_id','id');
    }

}

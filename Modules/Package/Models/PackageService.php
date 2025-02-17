<?php

namespace Modules\Package\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Package\Models\Package;
use Modules\Service\Models\Service;

class PackageService extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'package_id',
        'service_id',
        'service_price',
        'qty',
        'service_name',
        'discounted_price',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    public function userpackage()
    {
        return $this->belongsTo(UserPackage::class, 'package_id');
    }
    public function services()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}

<?php

namespace App\Models;

use App\Models\Traits\HasSlug;
use App\Trait\CustomFieldsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Modules\Booking\Models\Booking;
use Modules\BussinessHour\Models\BussinessHour;
use Modules\Employee\Models\BranchEmployee;
use Modules\Service\Models\Service;
use Modules\Service\Models\ServiceBranches;

class Branch extends BaseModel
{
    use CustomFieldsTrait;
    use HasFactory;
    use HasSlug;

    const CUSTOM_FIELD_MODEL = 'App\Models\Branch';

    protected $casts = [
        'contact_number' => 'string',
        'payment_method' => 'array',
        'city' => 'integer',
        'state' => 'integer',
        'country' => 'integer',
    ];

    protected $appends = ['feature_image'];

    /**
     * Get all the settings.
     *
     * @return mixed
     */
    public static function getAllBranches()
    {
        return Cache::rememberForever('branch.all', function () {
            return self::get();
        });
    }

    /**
     * Flush the cache.
     */
    public static function flushCache()
    {
        Cache::forget('branch.all');
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::updated(function () {
            self::flushCache();
        });

        static::created(function () {
            self::flushCache();
        });

        static::deleted(function () {
            self::flushCache();
        });
    }

    public function gallery()
    {
        return $this->hasMany(BranchGallery::class, 'id', 'feature_image');
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function branchEmployee()
    {
        return $this->hasMany(BranchEmployee::class, 'branch_id');
    }

    protected function getFeatureImageAttribute()
    {
        $media = $this->getFirstMediaUrl('feature_image');

        return isset($media) && ! empty($media) ? $media : default_feature_image();
    }

    public function gallerys()
    {
        return $this->hasMany(BranchGallery::class, 'branch_id', 'id');
    }

    public function businessHours()
    {
        return $this->hasMany(BussinessHour::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_branches');
    }

    public function branchServices()
    {
        return $this->hasMany(ServiceBranches::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}

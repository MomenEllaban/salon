<?php

namespace Modules\Package\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Branch;
use Carbon\Carbon;

class Package extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'packages';



    const CUSTOM_FIELD_MODEL = 'Modules\Package\Models\Package';

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Package\database\factories\PackageFactory::new();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }



    public function scopeBranch($query)
    {
        $branch_id = request()->selected_session_branch_id;
        if (isset($branch_id)) {
            return $query->where('branch_id', $branch_id);
        } else {
            return $query->whereNotNull('branch_id');
        }
    }

    public function employees()
    {
        return $this->belongsToMany(PackageEmployee::class, 'package_employees', 'package_id', 'employee_id');
    }

    public function employee()
    {
        return $this->hasMany(PackageEmployee::class, 'package_id');
    }

    public function services()
    {
        return $this->belongsToMany(PackageService::class, 'package_services', 'package_id', 'service_id');
    }

    public function service()
    {
        return $this->hasMany(PackageService::class, 'package_id');
    }

    public function userPackage(){
        return $this->hasMany(UserPackage::class, 'package_id');
    }

    protected function getFeatureImageAttribute()
    {
        $media = $this->getFirstMediaUrl('package_image');

        return isset($media) && ! empty($media) ? $media : default_feature_image();
    }

}

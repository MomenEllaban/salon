<?php

namespace Modules\Subscriptions\Models;

use App\Models\BaseModel;
use App\Models\User;
use App\Trait\CustomFieldsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends BaseModel
{
    use CustomFieldsTrait,HasFactory,SoftDeletes;

    protected $table = 'plan';

    protected $fillable = ['name', 'type', 'duration', 'amount', 'identifier', 'trial_period', 'planlimitation', 'description', 'status'];

    const CUSTOM_FIELD_MODEL = 'Modules\Subscriptions\Models\Plan';

    public function planLimitation()
    {
        return $this->hasMany(PlanLimitationMapping::class, 'plan_id', 'id')->with('limitation_data');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withTrashed();
    }

    protected static function newFactory()
    {
        return \Modules\Subscriptions\Database\factories\PlanFactory::new();
    }
}

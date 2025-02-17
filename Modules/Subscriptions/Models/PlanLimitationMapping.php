<?php

namespace Modules\Subscriptions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanLimitationMapping extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'planlimitation_mapping';

    protected $fillable = ['plan_id', 'planlimitation_id', 'limit'];

    public function limitation_data()
    {
        return $this->belongsTo(PlanLimitation::class, 'planlimitation_id', 'id')->withTrashed();
    }

    protected static function newFactory()
    {
        return \Modules\Subscriptions\Database\factories\PlanLimitationMappingFactory::new();
    }
}

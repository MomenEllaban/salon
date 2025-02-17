<?php

namespace Modules\Subscriptions\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanLimitation extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'planlimitation';

    protected $fillable = ['name', 'limit', 'status'];

    const CUSTOM_FIELD_MODEL = 'Modules\Subscriptions\Models\PlanLimitation';

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withTrashed();
    }

    protected static function newFactory()
    {
        return \Modules\Plan\Database\factories\PlanLimitationFactory::new();
    }
}

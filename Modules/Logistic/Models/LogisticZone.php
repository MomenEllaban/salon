<?php

namespace Modules\Logistic\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\World\Models\City;

class LogisticZone extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'logistic_zones';

    const CUSTOM_FIELD_MODEL = 'Modules\Logistic\Models\Logistic';

    protected $fillable = ['name','description','mobile', 'logistic_id', 'standard_delivery_charge', 'express_delivery_charge', 'standard_delivery_time', 'express_delivery_time', 'country_id', 'state_id'];

    protected $casts = [

        'logistic_id' => 'integer',
        'standard_delivery_charge' => 'double',
        'express_delivery_charge' => 'double',
        'country_id' => 'integer',
        'state_id' => 'integer',
        'status' => 'integer',

    ];

    protected static function newFactory()
    {
        return \Modules\Logistic\Database\factories\LogisticZoneFactory::new();
    }

    public function logistic()
    {
        return $this->belongsTo(Logistic::class, 'logistic_id');
    }

    public function cities()
    {
        return $this->belongsToMany(City::class, 'logistic_zone_city', 'logistic_zone_id', 'city_id');
    }
}

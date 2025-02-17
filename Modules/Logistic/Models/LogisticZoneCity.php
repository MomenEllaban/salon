<?php

namespace Modules\Logistic\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogisticZoneCity extends Model
{
    use HasFactory;

    protected $table = 'logistic_zone_city';

    protected $fillable = ['logistic_id', 'logistic_zone_id', 'city_id'];

    protected $casts = [

        'logistic_id' => 'integer',
        'logistic_zone_id' => 'integer',
        'city_id' => 'integer',

    ];

    protected static function newFactory()
    {
        return \Modules\Logistic\Database\factories\LogisticZoneCityFactory::new();
    }

    public function logistic()
    {
        return $this->belongsTo(Logistic::class, 'logistic_id');
    }

    public function logisticZone()
    {
        return $this->belongsTo(LogisticZone::class, 'logistic_zone_id');
    }
}

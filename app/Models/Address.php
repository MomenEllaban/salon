<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\World\Models\City;
use Modules\World\Models\Country;
use Modules\World\Models\State;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'postal_code',
        'city',
        'state',
        'country',
        'latitude',
        'logitude',
        'address_line_1',
        'address_line_2',
        'first_name',
        'last_name',
        'is_primary',
    ];

    public function addressable()
    {
        return $this->morphTo();
    }

    public function city_data()
    {
        return $this->belongsTo(City::class, 'city');
    }

    public function state_data()
    {
        return $this->belongsTo(State::class, 'state');
    }

    public function country_data()
    {
        return $this->belongsTo(Country::class, 'country');
    }

    public function setLatitudeAttribute($value)
    {
        $this->attributes['latitude'] = $value !== '' ? $value : null;
    }

    public function setLongitudeAttribute($value)
    {
        $this->attributes['longitude'] = $value !== '' ? $value : null;
    }
}

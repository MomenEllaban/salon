<?php

namespace Modules\Location\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'locations';

    const CUSTOM_FIELD_MODEL = 'Modules\Location\Models\Location';

    protected $fillable = ['name', 'banner', 'address_line_1', 'address_line_2', 'country', 'state', 'city', 'pincode', 'latitude', 'longitude', 'is_default', 'status'];

    protected $casts = [

        'country' => 'integer',
        'state' => 'integer',
        'city' => 'integer',
        'is_default' => 'integer',
        'status' => 'integer',

    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Location\database\factories\LocationFactory::new();
    }
}

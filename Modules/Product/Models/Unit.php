<?php

namespace Modules\Product\Models;

use App\Models\BaseModel;
use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends BaseModel
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    protected $table = 'units';

    const CUSTOM_FIELD_MODEL = 'Modules\Product\Models\Product';

    protected $fillable = ['name', 'slug', 'status'];

    protected $casts = [
        'status' => 'integer',
    ];

    protected static function newFactory()
    {
        return \Modules\Product\Database\factories\UnitFactory::new();
    }
}

<?php

namespace Modules\Product\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VariationValue extends BaseModel
{
    use HasFactory;

    protected $fillable = ['variation_id', 'name', 'status', 'value'];

    protected $casts = [
        'status' => 'integer',
    ];

    protected static function newFactory()
    {
        return \Modules\Product\Database\factories\VariationValueFactory::new();
    }

    public function scopeIsActive($query)
    {
        return $query->where('status', 1);
    }
}

<?php

namespace Modules\Product\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductGallery extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['product_id', 'full_url', 'status'];

    protected $casts = [
        'status' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}

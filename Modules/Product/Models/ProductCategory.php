<?php

namespace Modules\Product\Models;

use App\Models\BaseModel;
use App\Trait\CustomFieldsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends BaseModel
{
    use CustomFieldsTrait;
    use HasFactory;

    const CUSTOM_FIELD_MODEL = 'Modules\Product\Models\ProductCategory';

    protected $casts = [
        'status' => 'integer',
    ];

    protected $appends = ['feature_image'];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Product\Database\factories\ProductCategoryFactory::new();
    }

    public function mainCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function brands()
    {
        return $this->belongsToMany(Brands::class, 'product_category_brands', 'category_id', 'brand_id');
    }

    public function subCategories()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    protected static function boot()
    {
        parent::boot();

        // create a event to happen on creating
        static::creating(function ($table) {
            //
        });

        static::saving(function ($table) {
            //
        });

        static::updating(function ($table) {
            //
        });
    }

    protected function getFeatureImageAttribute()
    {
        $media = $this->getFirstMediaUrl('feature_image');

        return isset($media) && ! empty($media) ? $media : default_feature_image();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

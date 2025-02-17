<?php

namespace Modules\Product\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Tag\Models\Tag;

class Product extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'brand_id',
        'unit_id',
        'product_tags',
        'min_price',
        'max_price',
        'discount_value',
        'discount_type',
        'discount_start_date',
        'discount_end_date',
        'sell_target',
        'stock_qty',
        'status',
        'is_featured',
        'min_purchase_qty',
        'max_purchase_qty',
        'has_variation',
        'has_warranty',
        'total_sale_count',
        'standard_delivery_hours',
        'express_delivery_hours',
        'size_guide',
        'reward_points',
    ];

    protected $appends = ['feature_image'];

    protected $casts = [
        'min_purchase_qty' => 'double',
        'max_purchase_qty' => 'double',
        'min_price' => 'double',
        'max_price' => 'double',
        'brand_id' => 'integer',
        'unit_id' => 'integer',
        'stock_qty' => 'integer',
        'is_featured' => 'integer',
        'status' => 'integer',
        'has_variation' => 'integer',
        'has_warranty' => 'integer',
    ];

    const CUSTOM_FIELD_MODEL = 'Modules\Product\Models\Product';

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Product\database\factories\ProductFactory::new();
    }

    public function scopeIsPublished($query)
    {
        return $query->where('status', 1);
    }

    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_category_mappings', 'product_id', 'category_id');
    }

    public function product_category()
    {
        return $this->hasMany(ProductCategoryMapping::class);
    }

    public function gallery()
    {
        return $this->hasMany(ProductGallery::class);
    }

    public function product_review()
    {
        return $this->hasMany(Review::class)->with('gallery', 'likes');
    }

    public function brand()
    {
        return $this->belongsTo(Brands::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function product_variations()
    {
        return $this->hasMany(ProductVariation::class)->with('product_variation_stock', 'combinations');
    }

    public function variation_combinations()
    {
        return $this->hasMany(ProductVariationCombination::class);
    }

    public function tags_data()
    {
        return $this->belongsToMany(Tag::class, 'product_tags', 'product_id', 'tag_id');
    }

    protected function getFeatureImageAttribute()
    {
        $media = $this->getFirstMediaUrl('feature_image');

        return isset($media) && ! empty($media) ? $media : 'https://dummyimage.com/600x300/cfcfcf/000000.png';
    }
}

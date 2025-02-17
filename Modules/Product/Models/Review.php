<?php

namespace Modules\Product\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\LikeModule\Models\Likes;

class Review extends BaseModel
{
    use HasFactory;

    protected $table = 'product_review';

    protected $fillable = ['user_id', 'product_id', 'product_variation_id', 'rating', 'review_msg'];

    protected $casts = [
        'user_id' => 'integer',
        'product_id' => 'integer',
        'rating' => 'integer',
    ];

    protected $appends = ['feature_image'];

    protected static function newFactory()
    {
        return \Modules\Product\Database\factories\ReviewFactory::new();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likes()
    {
        return $this->morphMany(Likes::class, 'likeable');
    }

    public function gallery()
    {
        return $this->hasMany(ReviewGallery::class);
    }

    protected function getFeatureImageAttribute()
    {
        $media = $this->getFirstMediaUrl('feature_image');

        return isset($media) && ! empty($media) ? $media : 'https://dummyimage.com/600x300/cfcfcf/000000.png';
    }
}

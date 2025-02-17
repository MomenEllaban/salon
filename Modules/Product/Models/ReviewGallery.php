<?php

namespace Modules\Product\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReviewGallery extends BaseModel
{
    use HasFactory;

    protected $fillable = ['review_id', 'full_url', 'status'];

    protected $casts = [
        'review_id' => 'integer',
        'status' => 'integer',
    ];

    protected static function newFactory()
    {
        return \Modules\Product\Database\factories\ReviewGalleryFactory::new();
    }

    public function review()
    {
        return $this->belongsTo(Review::class, 'review_id', 'id');
    }
}

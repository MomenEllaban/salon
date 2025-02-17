<?php

namespace Modules\LikeModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Likes extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'is_like', 'dislike_like', 'is_view'];

    protected $casts = [

        'user_id' => 'integer',
        'is_like' => 'integer',
        'dislike_like' => 'integer',
        'is_view' => 'integer',
    ];

    protected static function newFactory()
    {
        return \Modules\LikeModule\Database\factories\LikesFactory::new();
    }

    public function likeable()
    {
        return $this->morphTo();
    }
}

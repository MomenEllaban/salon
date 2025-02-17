<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderUpdate extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'user_id', 'note'];

    protected $casts = [
        'order_id' => 'integer',
        'user_id' => 'integer',
    ];

    protected static function newFactory()
    {
        return \Modules\Product\Database\factories\OrderUpdateFactory::new();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}

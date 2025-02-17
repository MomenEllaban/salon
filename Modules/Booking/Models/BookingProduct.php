<?php

namespace Modules\Booking\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\Models\Product;

class BookingProduct extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'order_id', 'product_id', 'product_variation_id', 'employee_id', 'product_qty', 'product_price', 'discounted_price', 'discount_value', 'discount_type', 'variation_name'];

    protected $casts = [
        'booking_id' => 'integer',
        'order_id' => 'integer',
        'product_id' => 'integer',
        'product_variation_id' => 'integer',
        'employee_id' => 'integer',
        'product_qty' => 'integer',
        'product_price' => 'double',
        'discounted_price' => 'double',
        'discount_value' => 'double',

    ];

    protected static function newFactory()
    {
        return \Modules\Booking\Database\factories\BookingProductFactory::new();
    }

    public function employee()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

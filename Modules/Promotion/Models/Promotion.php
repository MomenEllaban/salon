<?php

namespace Modules\Promotion\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'promotions';

    protected $fillable = ['name', 'description', 'start_date_time', 'end_date_time', 'status'];

    protected $appends = ['feature_image'];

    const CUSTOM_FIELD_MODEL = 'Modules\Promotion\Models\Promotion';

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Promotion\database\factories\PromotionFactory::new();
    }

    public function coupon()
    {
        return $this->hasMany(Coupon::class, 'promotion_id');
    }

    public function userCouponRedeem(){
        return $this->hasMany(UserCouponRedeem::class, 'promotion_id');
    }

    protected function getFeatureImageAttribute()
    {
        $media = $this->getFirstMediaUrl('feature_image');

        return isset($media) && !empty($media) ? $media : promotion_image();
    }
}

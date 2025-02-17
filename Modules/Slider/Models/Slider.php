<?php

namespace Modules\Slider\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Category\Models\Category;
use Modules\Service\Models\Service;

class Slider extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'sliders';

    const CUSTOM_FIELD_MODEL = 'Modules\Slider\Models\Slider';

    protected $appends = ['feature_image'];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Slider\database\factories\SliderFactory::new();
    }

    public function module()
    {
        switch ($this->type) {
            case 'category':
                return $this->belongsTo(Category::class, 'link_id');
                break;
            case 'service':
                return $this->belongsTo(Service::class, 'link_id');
                break;
        }
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);

    }
    protected function getFeatureImageAttribute()
    {
        $media = $this->getFirstMediaUrl('feature_image');

        return isset($media) && ! empty($media) ? $media : asset(config('app.avatar_base_path').'avatar.png');
    }
}

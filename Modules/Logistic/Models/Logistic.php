<?php

namespace Modules\Logistic\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Logistic extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'logistics';

    const CUSTOM_FIELD_MODEL = 'Modules\Logistic\Models\Logistic';

    protected $appends = ['feature_image'];

    protected $casts = [

        'status' => 'integer',

    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Logistic\database\factories\LogisticFactory::new();
    }

    protected function getFeatureImageAttribute()
    {
        $media = $this->getFirstMediaUrl('feature_image');

        return isset($media) && ! empty($media) ? $media : default_feature_image();
    }
}

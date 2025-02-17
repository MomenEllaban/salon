<?php

namespace Modules\LikeModule\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LikeModule extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'likemodules';

    const CUSTOM_FIELD_MODEL = 'Modules\LikeModule\Models\LikeModule';

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\LikeModule\database\factories\LikeModuleFactory::new();
    }
}

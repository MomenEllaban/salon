<?php

namespace Modules\BussinessHour\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BussinessHour extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'bussinesshours';

    // Cast
    protected $casts = [
        'breaks' => 'array',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\BussinessHour\database\factories\BussinessHourFactory::new();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}

<?php

namespace Modules\Commission\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'commissions';

    protected $fillable = ['title', 'commission_type', 'commission_value', 'commissionable'];

    protected $casts = [

        'commission_value' => 'float',
        'status' => 'integer',

    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Commission\database\factories\CommissionFactory::new();
    }
}

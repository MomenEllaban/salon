<?php

namespace Modules\Tip\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipEarning extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'tippable', 'model_id', 'tip_amount', 'tip_status', 'payment_date'];

    protected static function newFactory()
    {
        return \Modules\Tip\Database\factories\TipEarningFactory::new();
    }
}

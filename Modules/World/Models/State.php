<?php

namespace Modules\World\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'states';

    protected $fillable = ['name', 'country_id', 'status'];

    protected $casts = [

        'country_id' => 'integer',
        'status' => 'integer',

    ];

    protected static function newFactory()
    {
        return \Modules\World\Database\factories\StateFactory::new();
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}

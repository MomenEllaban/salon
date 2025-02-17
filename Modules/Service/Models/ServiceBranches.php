<?php

namespace Modules\Service\Models;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceBranches extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'branch_id', 'service_price', 'duration_min'];

    protected $casts = [

        'service_id' => 'integer',
        'branch_id' => 'integer',
        'service_price' => 'double',
        'duration_min' => 'double',

    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}

<?php

namespace Modules\Service\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceGallery extends BaseModel
{
    use HasFactory;

    protected $fillable = ['service_id', 'full_url', 'status'];

    protected $casts = [

        'service_id' => 'integer',
        'status' => 'integer',
    ];

    protected static function newFactory()
    {
        return \Modules\Service\Database\factories\ServiceGalleryFactory::new();
    }

    /**
     * Get the service that owns the ServiceGallery
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }
}

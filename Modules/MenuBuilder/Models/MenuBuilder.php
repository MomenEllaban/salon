<?php

namespace Modules\MenuBuilder\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class MenuBuilder extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'menubuilders';

    protected $fillable = [
        'menu_type', 'menu_item_type', 'title', 'short_title',
        'is_route', 'route', 'url', 'active',
        'order', 'menu_level', 'start_icon', 'end_icon',
        'link_class', 'item_class', 'is_disabled', 'parent_id',
        'status',
        'target_type',
    ];

    protected $casts = [
        'permission' => 'array',
        'role' => 'array',
        'active' => 'array',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\MenuBuilder\database\factories\MenuBuilderFactory::new();
    }

    public function children()
    {
        return $this->hasMany(MenuBuilder::class, 'parent_id')->with('children')->orderBy('order', 'ASC');
    }

    public function parent()
    {
        return $this->belongsTo(MenuBuilder::class, 'parent_id');
    }

    /**
     * Get all the settings.
     *
     * @return mixed
     */
    public static function getAllMenu()
    {
        return Cache::rememberForever('menu.builder', function () {
            return self::with('children')->whereNull('parent_id')->get();
        });
    }

    /**
     * Flush the cache.
     */
    public static function flushCache()
    {
        Cache::forget('menu.builder');
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::updated(function () {
            self::flushCache();
        });

        static::created(function () {
            self::flushCache();
        });

        static::deleted(function () {
            self::flushCache();
        });
    }
}

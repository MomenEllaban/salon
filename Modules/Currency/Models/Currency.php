<?php

namespace Modules\Currency\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Currency extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'currencies';

    protected $fillable = [
        'currency_name', 'currency_symbol', 'currency_code', 'currency_position', 'no_of_decimal', 'thousand_separator', 'decimal_separator', 'is_primary',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Currency\database\factories\CurrencyFactory::new();
    }

    /**
     * Get all the settings.
     *
     * @return mixed
     */
    public static function getAllCurrency()
    {
        return Cache::rememberForever('currency.all', function () {
            return self::get();
        });
    }

    /**
     * Flush the cache.
     */
    public static function flushCache()
    {
        Cache::forget('currency.all');
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

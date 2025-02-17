<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Expense\Database\factories\ExpenseFactory;
use App\Models\BaseModel;

class ExpenseCategory extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    
    const CUSTOM_FIELD_MODEL = 'Modules\Expense\Models\ExpenseCategory';

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return;
        return \Modules\Expense\database\factories\ExpenseFactory::new();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 0);
    }

    public function mainCategory()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function subCategories()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function product()
    {
        return $this->belongsTo(Expense::class);
    }

}

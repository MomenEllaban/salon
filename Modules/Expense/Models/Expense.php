<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Expense\Database\factories\ExpenseFactory;
use App\Models\BaseModel;
use App\Models\Branch;

class Expense extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    
    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\Expense\database\factories\ExpenseFactory::new();
    }

    public function scopePaid($query)
    {
        return $query->where('status', 0);
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function expenseSubCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'sub_category_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}

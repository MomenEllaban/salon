<?php

namespace Modules\Expense\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Expense\Database\factories\ExpenseFactory;
use App\Models\BaseModel;
use App\Models\Branch;
use App\Models\User;

class Expense extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected $appends = ['feature_image'];

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
        return $this->belongsTo(ExpenseCategory::class, 'subcategory_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id')->where('is_manager', 1);
    }

    protected function getFeatureImageAttribute()
    {
        $media = $this->getFirstMediaUrl('feature_image');

        return isset($media) && !empty($media) ? $media : default_feature_image();
    }
}

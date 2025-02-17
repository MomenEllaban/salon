<?php

namespace Modules\Employee\Models;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchEmployee extends Model
{
    use HasFactory;

    protected $table = 'branch_employee';

    protected $fillable = [
        'employee_id', 'branch_id', 'is_primary',
    ];

    protected static function newFactory()
    {
        return \Modules\Employee\Database\factories\BranchEmployeeFactory::new();
    }

    public function getBranch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function rating()
    {
        return $this->hasMany(EmployeeRating::class, 'id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}

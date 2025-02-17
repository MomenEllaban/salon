<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class BranchGallery extends BaseModel
{
    use HasFactory;

    protected $fillable = ['branch_id', 'full_url', 'status'];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}

<?php

namespace App\Trait;

trait CommonQuery
{
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    public function scopeBranchBased($query, $branch_id = null)
    {
        return $query->where('branch_id', $branch_id);
    }

    public function scopeMultiBranchBased($query, $branch_id = [])
    {
        return $query->whereIn('branch_id', $branch_id);
    }
}

<?php

namespace Modules\Commission\Trait;

use Modules\Commission\Models\CommissionEarning;

trait CommissionTrait
{
    public function commission()
    {
        return $this->morphOne(CommissionEarning::class, 'commissionable');
    }
}

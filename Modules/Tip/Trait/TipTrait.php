<?php

namespace Modules\Tip\Trait;

use Modules\Tip\Models\TipEarning;

trait TipTrait
{
    public function tip()
    {
        return $this->morphOne(TipEarning::class, 'tippable');
    }
}

<?php

namespace Modules\Earning\Trait;

trait EarningTrait
{
    public function getUnpaidAmount($data, $type = null)
    {
        $classData = new \stdClass();
        switch ($type) {
            case 'tip':
                return $data->tip_earning->where('tip_status', 'unpaid')->sum('tip_amount');
                break;
            case 'commission':
                return $data->commission_earning->where('commission_status', 'unpaid')->sum('commission_amount');
                break;
            default:
                $classData->total_commission_earn = $data->commission_earning->where('commission_status', 'unpaid')->sum('commission_amount');
                $classData->total_tips_earn = $data->tip_earning->where('tip_status', 'unpaid')->sum('tip_amount');
                $classData->total_pay = $classData->total_commission_earn + $classData->total_tips_earn;

                return $classData;
                break;
        }

        return 0;
    }
}

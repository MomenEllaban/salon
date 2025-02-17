<?php

namespace Modules\Product\Trait;

use App\Jobs\BulkNotification;

trait OrderTrait
{
    protected function sendNotificationOnOrderUpdate($type,$notify_message, $order)
    {
        $data = mail_footer($type,$notify_message, $order);

        $data['order'] = $order;

        BulkNotification::dispatch($data);
    }
}

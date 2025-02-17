<?php

namespace Modules\Package\Trait;

use App\Jobs\BulkNotification;
use App\Models\User;

trait PackageTrait
{
    protected function sendPackageExpireNotification($type, $notify_message, $package, $user_id = null, $notify = true)
    {
        $data = mail_footer($type, $notify_message, $package);
        $user = User::where('id', $user_id)->first(); // Fetch the user instance
        // Attach the package data to the notification payload
        $data['package'] = $package;
        $data['user_id'] = $user_id;
        $data['user_name'] = $user->first_name.' '.$user->last_name ?? default_user_name();;
        $data['logged_in_user_fullname'] = $user->first_name.' '.$user->last_name ?? default_user_name();
        $data['logged_in_user_role'] = $user->role ?? 'N/A'; // Adjust to your role logic
        
        if ($notify) {
            BulkNotification::dispatch($data);
        } else {
            return $data;
        }
    }
}

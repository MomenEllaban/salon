<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Constant\Models\Constant;
use Modules\NotificationTemplate\Models\NotificationTemplate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::table('notification_templates', function (Blueprint $table) {
        //     //
        // });

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Insert new notification type into the constants table
        $types = [
            [
                'type' => 'notification_type',
                'value' => 'package_expiry',
                'name' => 'Package Expiry',
            ],
        ];


        foreach ($types as $value) {
            Constant::updateOrCreate(['type' => $value['type'], 'value' => $value['value']], $value);
        }

        // echo " Insert: notificationtempletes \n\n";
        $newChannels = [
            'PUSH_NOTIFICATION' => '1',
            'IS_MAIL' => '0',
            'IS_CUSTOM_WEBHOOK' => '0',
        ];

        NotificationTemplate::query()
            ->update(['channels' => $newChannels]);
        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $template = NotificationTemplate::create([
            'type' => 'package_expiry',
            'name' => 'package_expiry',
            'label' => 'Package Expiry',
            'status' => 1,
            'to' => '["user","admin"]',
            'channels' => ['PUSH_NOTIFICATION' => '1','IS_MAIL' => '0', 'IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        // User notification template
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Your package is going to expire soon. Please renew it to avoid any interruptions.',
            'status' => 1,
            'user_type' => 'user',
            'template_detail' => '<p>Dear [[ user_name ]],</p>
                                  <p>We would like to inform you that your package is about to expire soon.</p>
                                  <p>Package Name: [[ package_name ]]</p>
                                  <p>Package Expiry Date: [[ package_expiry_date ]]</p>
                                  <p>Please renew your package to ensure uninterrupted service.</p>
                                  <p>Best regards,</p>
                                  <p>&nbsp;</p>
                                  <p>[[ company_name ]]</p>',
            'subject' => 'Your Package is Expiring Soon',
            'notification_subject' => 'Package Expiry Notification',
            'notification_template_detail' => '<p>Your package is about to expire. Renew it soon to continue enjoying our services.</p>',
        ]);
        
        // Admin notification template
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'One of the user packages is about to expire soon. Please take the necessary actions.',
            'status' => 1,
            'user_type' => 'admin',
            'template_detail' => '<p>Dear [[ admin_name ]],</p>
                                  <p>This is a friendly reminder that one of the user packages is nearing its expiration date.</p>
                                  <p>User Name: [[ user_name ]]</p>
                                  <p>Package Name: [[ package_name ]]</p>
                                  <p>Package Expiry Date: [[ package_expiry_date ]]</p>
                                  <p>Please take the necessary actions to ensure seamless service for the user.</p>
                                  <p>Best regards,</p>
                                  <p>&nbsp;</p>
                                  <p>[[ company_name ]]</p>',
            'subject' => 'Package Expiry Alert',
            'notification_subject' => 'Package Expiry Alert',
            'notification_template_detail' => '<p>This is a reminder that one of the user packages is nearing its expiration date.</p>',
        ]);
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('notification_templates', function (Blueprint $table) {
        //     //
        // });
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Remove the constant type for package expiry
        Constant::where('type', 'notification_type')
            ->where('value', 'package_expiry')
            ->delete();

            $newChannels = ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0'];
    
            NotificationTemplate::query()
                ->update(['channels' => $newChannels]);

        // Remove the notification template and its mappings
        $template = NotificationTemplate::where('type', 'package_expiry')->first();

        if ($template) {
            // Delete all associated mappings
            $template->defaultNotificationTemplateMap()->delete();

            // Delete the template
            $template->delete();
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
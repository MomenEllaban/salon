<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\NotificationTemplate\Models\NotificationTemplate;
use Modules\Constant\Models\Constant;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('alter_notification_template', function (Blueprint $table) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        /*
         * NotificationTemplates Seed
         * ------------------
         */

        // DB::table('notificationtemplates')->truncate();
        // echo "Truncate: notificationtemplates \n";

        $types = [
            [
                'type' => 'notification_type',
                'value' => 'new_booking',
                'name' => 'New Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'check_in_booking',
                'name' => 'Check-In On Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'checkout_booking',
                'name' => 'Checkout On Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'complete_booking',
                'name' => 'Complete On Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'cancel_booking',
                'name' => 'Cancel On Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'quick_booking',
                'name' => 'Quick Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'change_password',
                'name' => 'Chnage Password',
            ],
            [
                'type' => 'notification_type',
                'value' => 'forget_email_password',
                'name' => 'Forget Email/Password',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'id',
                'name' => 'ID',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'user_name',
                'name' => 'Customer Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'description',
                'name' => 'Description / Note',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'booking_id',
                'name' => 'Booking ID',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'booking_date',
                'name' => 'Booking Date',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'booking_time',
                'name' => 'Booking Time',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'service_name',
                'name' => 'Booking Services Names',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'booking_duration',
                'name' => 'Booking Duration',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'employee_name',
                'name' => 'Staff Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'venue_address',
                'name' => 'Venue / Address',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'logged_in_user_fullname',
                'name' => 'Your Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'logged_in_user_role',
                'name' => 'Your Position',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'company_name',
                'name' => 'Company Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'company_contact_info',
                'name' => 'Company Info',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'user_id',
                'name' => 'User\' ID',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'user_password',
                'name' => 'User Password',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'link',
                'name' => 'Link',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'site_url',
                'name' => 'Site URL',
            ],
            [
                'type' => 'notification_to',
                'value' => 'user',
                'name' => 'User',
            ],
            [
                'type' => 'notification_to',
                'value' => 'manager',
                'name' => 'Manager',
            ],
            [
                'type' => 'notification_to',
                'value' => 'admin',
                'name' => 'Admin',
            ],
        ];

        foreach ($types as $value) {
            Constant::updateOrCreate(['type' => $value['type'], 'value' => $value['value']], $value);
        }

        echo " Insert: notificationtempletes \n\n";

        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('notification_templates')->delete();
        DB::table('notification_template_content_mapping')->delete();

        $template = NotificationTemplate::create([
            'type' => 'new_booking',
            'name' => 'new_booking',
            'label' => 'Booking confirmation',
            'status' => 1,
            'to' => '["admin", "manager", "user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0'],
        ]);

        // Notification template for admin
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Thank you for choosing our services! Your booking has been successfully confirmed. We look forward to serving you and providing an exceptional experience. Stay tuned for further updates.',
            'status' => 1,
            'user_type' => 'admin',
            'template_detail' => '
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Subject: Appointment Confirm - Thank You!</span>
            </p>
            <p><strong id="docs-internal-guid-7d6bdcce-7fff-5035-731b-386f9021a5db" style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Dear [[ user_name ]],</span>
            </p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">We are delighted to inform you that your appointment has been successfully confirmed! Thank you for choosing our services. We are excited to have you as our valued customer and are committed to providing you with a wonderful experience.</span>
            </p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <h4>Appointment Details</h4>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Appointment ID: [[ id ]]</span>
            </p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Appointment Date: [[ booking_date ]]</span>
            </p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Service/Event: [[ booking_services_names ]]</span>
            </p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Date: [[ booking_date ]]</span>
            </p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Time: [[ booking_time ]]</span>
            </p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Location: [[ venue_address ]]</span>
            </p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">We want to assure you that we have received your appointment details and everything is in order. Our team is eagerly preparing to make this a memorable experience for you. If you have any specific requirements or questions regarding your appointment, please feel free to reach out to us.</span>
            </p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">We recommend marking your calendar and setting a reminder for the date and time of the event to ensure you don\'t miss your appointment. Should there be any updates or changes to your appointment, we will promptly notify you.</span>
            </p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Once again, thank you for choosing our services. We look forward to providing you with exceptional service and creating lasting memories. If you have any further queries, please do not hesitate to contact our friendly customer support team.</span>
            </p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Best regards,</span>
            </p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ logged_in_user_fullname ]],</span>
            </p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ logged_in_user_role ]],</span>
            </p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ company_name ]],</span>
            </p>
            <p>&nbsp;</p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ company_contact_info ]]</span>
            </p>
            <p><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">&nbsp;</span></p>
            ',
            'subject' => 'Booking Confirmation Received!',
            'notification_subject' => 'New Booking Alert.',
                'notification_template_detail' => '<p>New booking: [[ user_name ]] has booked [[ booking_services_names ]].</p>',
        ]);


        // Notification template for manager
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Thank you for choosing our services! Your booking has been successfully confirmed. We look forward to serving you and providing an exceptional experience. Stay tuned for further updates.',
            'status' => 1,
            'user_type' => 'manager',
            'template_detail' => '
<p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
<span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Subject: Appointment Confirm - Thank You!</span>
</p>
<p><strong id="docs-internal-guid-7d6bdcce-7fff-5035-731b-386f9021a5db" style="font-weight: normal;">&nbsp;</strong></p>
<p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
<span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Dear [[ user_name ]],</span>
</p>
<p><strong style="font-weight: normal;">&nbsp;</strong></p>
<p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
<span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">We are delighted to inform you that your appointment has been successfully confirmed! Thank you for choosing our services. We are excited to have you as our valued customer and are committed to providing you with a wonderful experience.</span>
</p>
<p><strong style="font-weight: normal;">&nbsp;</strong></p>
<h4>Appointment Details</h4>
<p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
<span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Appointment ID: [[ id ]]</span>
</p>
<p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
<span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Appointment Date: [[ booking_date ]]</span>
</p>
<p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
<span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Service/Event: [[ booking_services_names ]]</span>
</p>
<p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
<span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Date: [[ booking_date ]]</span>
</p>
<p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
<span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Time: [[ booking_time ]]</span>
</p>
<p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
<span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Location: [[ venue_address ]]</span>
</p>
<p><strong style="font-weight: normal;">&nbsp;</strong></p>
<p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
<span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">We want to assure you that we have received your appointment details and everything is in order. Our team is eagerly preparing to make this a memorable experience for you. If you have any specific requirements or questions regarding your appointment, please feel free to reach out to us.</span>
</p>
<p><strong style="font-weight: normal;">&nbsp;</strong></p>
<p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
<span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">We recommend marking your calendar and setting a reminder for the date and time of the event to ensure you don\'t miss your appointment. Should there be any updates or changes to your appointment, we will promptly notify you.</span>
</p>
<p><strong style="font-weight: normal;">&nbsp;</strong></p>
<p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
<span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Once again, thank you for choosing our services. We look forward to providing you with exceptional service and creating lasting memories. If you have any further queries, please do not hesitate to contact our friendly customer support team.</span>
</p>
<p><strong style="font-weight: normal;">&nbsp;</strong></p>
<p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
<span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Best regards,</span>
</p>
<p><strong style="font-weight: normal;">&nbsp;</strong></p>
<p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
<span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ logged_in_user_fullname ]],</span>
</p>
<p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
<span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ logged_in_user_role ]],</span>
</p>
<p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
<span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ company_name ]],</span>
</p>
<p>&nbsp;</p>
<p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
<span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ company_contact_info ]]</span>
</p>
<p><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">&nbsp;</span></p>
',
                'subject' => 'Booking Confirmation Received!',
                'notification_subject' => 'New Booking Alert',
                'notification_template_detail' => '<p>New booking: [[ user_name ]] has booked [[ booking_services_names ]].</p>',
        ]);


        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Thank you for choosing our services! Your booking has been successfully confirmed. We look forward to serving you and providing an exceptional experience. Stay tuned for further updates.',
            'status' => 1,
            'user_type' => 'user',
            'template_detail' => '
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Subject: Appointment Confirm - Thank You!</span>
            </p>
            <p><strong id="docs-internal-guid-7d6bdcce-7fff-5035-731b-386f9021a5db" style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Dear [[ user_name ]],</span>
            </p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">We are delighted to inform you that your appointment has been successfully confirmed! Thank you for choosing our services. We are excited to have you as our valued customer and are committed to providing you with a wonderful experience.</span>
            </p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <h4>Appointment Details</h4>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Appointment ID: [[ id ]]</span>
            </p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Appointment Date: [[ booking_date ]]</span>
            </p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Service/Event: [[ booking_services_names ]]</span>
            </p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Date: [[ booking_date ]]</span>
            </p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Time: [[ booking_time ]]</span>
            </p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Location: [[ venue_address ]]</span>
            </p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">We want to assure you that we have received your appointment details and everything is in order. Our team is eagerly preparing to make this a memorable experience for you. If you have any specific requirements or questions regarding your appointment, please feel free to reach out to us.</span>
            </p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">We recommend marking your calendar and setting a reminder for the date and time of the event to ensure you don\'t miss your appointment. Should there be any updates or changes to your appointment, we will promptly notify you.</span>
            </p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Once again, thank you for choosing our services. We look forward to providing you with exceptional service and creating lasting memories. If you have any further queries, please do not hesitate to contact our friendly customer support team.</span>
            </p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Best regards,</span>
            </p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ logged_in_user_fullname ]],</span>
            </p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">
            <span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ company_name ]]</span>
            </p>
            ',
            'subject' => 'Booking Confirmation Received!',
            'notification_subject' => 'Booking Confirmed',
                'notification_template_detail' => 'We are delighted to confirm your appointment. Thank you for choosing our services. See details below.',
        ]);



        $template = NotificationTemplate::create([
            'type' => 'check_in_booking',
            'name' => 'check_in_booking',
            'label' => 'Check-In On Booking',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Welcome to your booked accommodation. We hope you have a pleasant stay!',
            'status' => 1,
            'user_type' => 'user',
            'template_detail' => '<p><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Subject: Appointment C<span style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 14px; white-space-collapse: collapse;">heck in</span> - Thank You!</span></p>
            <p><span id="docs-internal-guid-7d6bdcce-7fff-5035-731b-386f9021a5db">&nbsp;</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Dear [[ user_name ]],</span></p>
            <p>&nbsp;</p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">Welcome to your booked accommodation. We hope you have a pleasant stay!</p>
            <p>&nbsp;</p>
            <h4>Appointment Details</h4>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Appointment ID: [[ id ]]</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Appointment Date: [[ booking_date ]]</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Service/Event: [[ booking_services_names ]]</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Date: [[ booking_date ]]</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Time: [[ booking_time ]]</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Location: [[ venue_address ]]</span></p>
            <p>&nbsp;</p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">We want to assure you that we have received your appointment details and everything is in order. Our team is eagerly preparing to make this a memorable experience for you. If you have any specific requirements or questions regarding your appointment, please feel free to reach out to us.</span></p>
            <p>&nbsp;</p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">We recommend marking your calendar and setting a reminder for the date and time of the event to ensure you don\'t miss your appointment. Should there be any updates or changes to your appointment, we will promptly notify you.</span></p>
            <p>&nbsp;</p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Once again, thank you for choosing our services. We look forward to providing you with exceptional service and creating lasting memories. If you have any further queries, please do not hesitate to contact our friendly customer support team.</span></p>
            <p>&nbsp;</p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Best regards,</span></p>
            <p>&nbsp;</p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ logged_in_user_fullname ]],</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ logged_in_user_role ]],</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ company_name ]],</span></p>
            <p>&nbsp;</p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ company_contact_info ]]</span></p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">&nbsp;</span></p>',
                            'subject' => 'Check-in Successful!',
           'notification_subject' => 'Check-in Successful!',
                'notification_template_detail' => '<p>Welcome to your booked accommodation. We hope you have a pleasant stay!</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'checkout_booking',
            'name' => 'checkout_booking',
            'label' => 'Checkout On Booking',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Thank you for choosing our services. Please remember to check out by [check-out time]. We hope you had a wonderful experience!',
            'status' => 1,
            'user_type' => 'user',
            'template_detail' => '<p><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Subject: Appointment C<span style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 14px; white-space-collapse: collapse;">heck out</span> - Thank You!</span></p>
            <p><span id="docs-internal-guid-7d6bdcce-7fff-5035-731b-386f9021a5db">&nbsp;</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Dear [[ user_name ]],</span></p>
            <p><span>&nbsp;</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;">Thank you for choosing our services. Please remember to check out by [check-out time]. We hope you had a wonderful experience!</p>
            <p><span>&nbsp;</span></p>
            <h4>Appointment Details</h4>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Appointment ID: [[ id ]]</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Appointment Date: [[ booking_date ]]</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Service/Event: [[ booking_services_names ]]</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Date: [[ booking_date ]]</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Time: [[ booking_time ]]</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Location: [[ venue_address ]]</span></p>
            <p><span>&nbsp;</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">We want to assure you that we have received your appointment details and everything is in order. Our team is eagerly preparing to make this a memorable experience for you. If you have any specific requirements or questions regarding your appointment, please feel free to reach out to us.</span></p>
            <p><span>&nbsp;</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">We recommend marking your calendar and setting a reminder for the date and time of the event to ensure you don\'t miss your appointment. Should there be any updates or changes to your appointment, we will promptly notify you.</span></p>
            <p><span>&nbsp;</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Once again, thank you for choosing our services. We look forward to providing you with exceptional service and creating lasting memories. If you have any further queries, please do not hesitate to contact our friendly customer support team.</span></p>
            <p><span>&nbsp;</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Best regards,</span></p>
            <p><span>&nbsp;</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ logged_in_user_fullname ]],</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ logged_in_user_role ]],</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ company_name ]],</span></p>
            <p>&nbsp;</p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ company_contact_info ]]</span></p>
            <p>&nbsp;</p>
            <p><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">&nbsp;</span></p>',
        'subject' => 'Check-out Reminder',
        'notification_subject' => 'Check-out Reminder',
        'notification_template_detail' => '<p>Thank you for choosing our services. Please remember to check out by [check-out time]. We hope you had a wonderful experience!</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'complete_booking',
            'name' => 'complete_booking',
            'label' => 'Complete On Booking',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Congratulations! Your booking has been successfully completed. We appreciate your business and look forward to serving you again.',
            'status' => 1,
            'user_type' => 'user',
            'language' => 'en',
            'template_detail' => '<p>Subject: Appointment Completion and Invoice</p>
<p>&nbsp;</p>
<p>Dear [[ user_name ]],</p>
<p>&nbsp;</p>
<p>We are writing to inform you that your recent appointment with us has been successfully completed. We sincerely appreciate your trust in our services and the opportunity to serve you.</p>
<p>&nbsp;</p>
<p>[[ company_name ]]</p>',
            'subject' => 'Appointment complete email with invoice',
            'notification_subject' => 'Appointment complete email with invoice',
            'notification_template_detail' => '<p>We are writing to inform you that your recent appointment with us has been successfully completed.</p>',
        ]);


        $template = NotificationTemplate::create([
            'type' => 'cancel_booking',
            'name' => 'cancel_booking',
            'label' => 'Cancel On Booking',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'We regret to inform you that your booking has been cancelled. If you have any questions or need further assistance, please contact our support team.',
            'status' => 1,
            'user_type' => 'user',
            'template_detail' => '<p>Dear [[ user_name ]],</p>
            <p>&nbsp;</p>
            <p>We regret to inform you that your booking has been cancelled. If you have any questions or need further assistance, please contact our support team.</p>
            <p>&nbsp;</p>
            <p>Thank you for your understanding.</p>
            <p>&nbsp;</p>
            <p>Best regards,</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>[[ company_name ]]</p>',
                            'subject' => 'Booking Cancellation',
                            'notification_subject' => 'Important: Booking Cancellation Notice',
                            'notification_template_detail' => '<p><span style="font-family: Arial; font-size: 14.6667px; white-space-collapse: preserve;">We regret to inform you that your booking has been cancelled. If you have any questions or need further assistance, please contact our support team.</span></p>',
        ]);
        $template = NotificationTemplate::create([
            'type' => 'quick_booking',
            'name' => 'quick_booking',
            'label' => 'Quick Booking',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'status' => 1,
            'user_type' => 'user',
            'subject' => 'Quick Booking',
            'template_detail' => '
                <p>We are pleased to inform you that your appointment has been successfully booked. We value your time and are committed to providing you with excellent service.</p>
            ',
            'notification_subject' => 'Your Appointment Confirmation',
            'notification_template_detail' => '
                <p>Dear [[ user_name ]],</p>
                <p>&nbsp;</p>
                <p>Your appointment has been confirmed. Below are the details:</p>
                <p>&nbsp;</p>
                <p>Appointment Date: [[ booking_date ]]</p>
                <p>Appointment Time: [[ booking_time ]]</p>
                <p>Appointment Duration: [[ booking_duration ]]</p>
                <p>Location: [[ venue_address ]]</p>
                <p>&nbsp;</p>
                <p>Please arrive a few minutes early to ensure a smooth experience. If you need to reschedule or cancel, notify us at least [[ link ]] in advance.</p>
                <p>&nbsp;</p>
                <p>Thank you for choosing our services.</p>
                <p>&nbsp;</p>
                <p>Best regards,</p>
                <p>&nbsp;</p>
                <p>[[ company_name ]]</p>
            ',
        ]);


        $template = NotificationTemplate::create([
            'type' => 'change_password',
            'name' => 'change_password',
            'label' => 'Change Password',
            'status' => 1,
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'status' => 1,
            'subject' => 'Change Password',
            'template_detail' => '
            <p>Subject: Password Change Confirmation</p>
            <p>Dear [[ user_name ]],</p>
            <p>&nbsp;</p>
            <p>We wanted to inform you that a recent password change has been made for your account. If you did not initiate this change, please take immediate action to secure your account.</p>
            <p>&nbsp;</p>
            <p>To regain control and secure your account:</p>
            <p>&nbsp;</p>
            <p>Visit [[ link ]].</p>
            <p>Follow the instructions to verify your identity.</p>
            <p>Create a strong and unique password.</p>
            <p>Update passwords for any other accounts using similar credentials.</p>
            <p>If you have any concerns or need assistance, please contact our customer support team.</p>
            <p>&nbsp;</p>
            <p>Thank you for your attention to this matter.</p>
            <p>&nbsp;</p>
            <p>Best regards,</p>
            <p>[[ logged_in_user_fullname ]]<br />[[ logged_in_user_role ]]<br />[[ company_name ]]</p>
            <p>[[ company_contact_info ]]</p>
          ',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'forget_email_password',
            'name' => 'forget_email_password',
            'label' => 'Forget Email/Password',
            'status' => 1,
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'status' => 1,
            'subject' => 'Forget Email/Password',
            'template_detail' => '
            <p>Subject: Password Reset Instructions</p>
            <p>&nbsp;</p>
            <p>Dear [[ user_name ]],</p>
            <p>A password reset request has been initiated for your account. To reset your password:</p>
            <p>&nbsp;</p>
            <p>Visit [[ link ]].</p>
            <p>Enter your email address.</p>
            <p>Follow the instructions provided to complete the reset process.</p>
            <p>If you did not request this reset or need assistance, please contact our support team.</p>
            <p>&nbsp;</p>
            <p>Thank you.</p>
            <p>&nbsp;</p>
            <p>Best regards,</p>
            <p>[[ logged_in_user_fullname ]]<br />[[ logged_in_user_role ]]<br />[[ company_name ]]</p>
            <p>[[ company_contact_info ]]</p>
            <p>&nbsp;</p>
          ',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'order_placed',
            'name' => 'order_placed',
            'label' => 'Order Placed',
            'status' => 1,
            'to' => '["user","admin"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0'],
        ]);


        // Admin notification template
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Thank you for choosing Us for your recent order. We are delighted to confirm that your order has been successfully placed!',
            'status' => 1,
            'user_type' => 'admin',
            'template_detail' => '<p>Dear Admin,</p>
<p>Thank you for choosing Us for your recent order. We are delighted to confirm that your order has been successfully placed.</p>
<p>Best regards,</p>
<p>&nbsp;</p>
<p>[[ company_name ]]</p>',
                'subject' => 'Order Placed!',
           'notification_subject' => 'Order Confirmation',
                'notification_template_detail' => '<p>We are delighted to confirm that your order has been successfully placed.</p>',
        ]);

        // User notification template
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'Thank you for placing your order with us! We are processing it and will notify you once it\'s ready to be shipped.',
            'status' => 1,
            'user_type' => 'user',
            'template_detail' => '<p>Dear Admin,</p>
            <p>&nbsp;</p>
            <p>Thank you for choosing Us for your recent order. We are delighted to confirm that your order has been successfully placed.</p>
            <p>&nbsp;</p>
            <p>Best regards,</p>
            <p>&nbsp;</p>
            <p>[[ company_name ]]</p>',
            'subject' => 'Your Order has been Placed!',
            'notification_subject' => 'Order Confirmation',
            'notification_template_detail' => '<p>We are delighted to confirm that your order has been successfully placed.</p>',
        ]);


        $template = NotificationTemplate::create([
            'type' => 'order_proccessing',
            'name' => 'order_proccessing',
            'label' => 'Order Processing',
            'status' => 1,
            'to' => '["user","admin"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0'],
        ]);

        // User notification template
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => "We're excited to let you know that your order is now being prepared and will soon be on its way to satisfy your taste buds!",
            'status' => 1,
            'user_type' => 'user',
            'template_detail' => '<p>Dear [[ user_name ]],</p>
            <p>&nbsp;</p>
            <p>We\'re excited to let you know that your order is now being prepared and will soon be on its way to satisfy your taste buds!</p>
            <p>&nbsp;</p>
            <p>Thank you for choosing us. We hope you enjoy your meal!</p>
            <p>&nbsp;</p>
            <p>Best regards,</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>[[ company_name ]]</p>',
                            'subject' => 'Order Processing!',
         'notification_subject' => 'Your Order is Being Prepared',
                'notification_template_detail' => '<p>We\'re excited to let you know that your order is now being prepared and will soon be on its way to satisfy your taste buds!</p>',
        ]);

        // Admin notification template
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => "An order is currently being processed and will soon be ready for dispatch.",
            'status' => 1,
            'user_type' => 'admin',
            'template_detail' => '<p>Dear [[ admin_name ]],</p>
            <p>An order is currently being processed and will soon be ready for dispatch.</p>
            <p>Order ID: [[ order_id ]]</p>
            <p>Order Date: [[ order_date ]]</p>
            <p>Customer Name: [[ user_name ]]</p>
            <p>Best regards,</p>
            <p>[[ company_name ]]</p>',
                            'subject' => 'Order Processing!',
                            'notification_subject' => 'Order Processing Notification',
                            'notification_template_detail' => '<p>An order is currently being processed and will soon be ready for dispatch.</p>
            <p>&nbsp;</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'order_delivered',
            'name' => 'order_delivered',
            'label' => 'Order Delivered',
            'status' => 1,
            'to' => '["user","admin"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0'],
        ]);

        // User notification template
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => "We're delighted to inform you that your order has been successfully delivered to your doorstep.",
            'status' => 1,
            'user_type' => 'user',
            'template_detail' => '<p>Dear [[ user_name ]],</p>
            <p>We\'re delighted to inform you that your order has been successfully delivered to your doorstep.</p>
            <p>Order ID: [[ order_id ]]</p>
            <p>Order Date: [[ order_date ]]</p>
            <p>If you have any questions or need further assistance, please feel free to contact us.</p>
            <p>Best regards,</p>
            <p>[[ company_name ]]</p>',
                            'subject' => 'Your Order is Delivered!',
                            'notification_subject' => 'Order Delivery Confirmation',
                            'notification_template_detail' => '<p>We\'re delighted to inform you that your order has been successfully delivered to your doorstep.</p>',
        ]);

        // Admin notification template
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => "An order has been delivered to the customer.",
            'status' => 1,
            'user_type' => 'admin',
            'template_detail' => '<p>Dear [[ admin_name ]],</p>
            <p>An order has been delivered to the customer.</p>
            <p>Order ID: [[ order_id ]]</p>
            <p>Order Date: [[ order_date ]]</p>
            <p>Customer Name: [[ user_name ]]</p>
            <p>Delivery Address: [[ delivery_address ]]</p>
            <p>Best regards,</p>
            <p>[[ company_name ]]</p>',
                            'subject' => 'Order Delivered!',
                            'notification_subject' => 'Order Delivery Notification',
                            'notification_template_detail' => '<p>An order has been delivered to the customer.</p>',
        ]);


        $template = NotificationTemplate::create([
            'type' => 'order_cancelled',
            'name' => 'order_cancelled',
            'label' => 'Order Cancelled',
            'status' => 1,
            'to' => '["user","admin"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_CUSTOM_WEBHOOK' => '0'],
        ]);

        // User notification template
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'We regret to inform you that your recent order has been cancelled.',
            'status' => 1,
            'user_type' => 'user',
            'template_detail' => '<p>Dear [[ user_name ]],</p>
            <p>We regret to inform you that your recent order has been cancelled. Please contact us if you have any questions or need further assistance.</p>
            <p>Order ID: [[ order_id ]]</p>
            <p>Order Date: [[ order_date ]]</p>
            <p>Best regards,</p>
            <p>[[ company_name ]]</p>',
                            'subject' => 'Order Cancelled!',

                            'notification_subject' => 'Order Cancellation Notification',
                            'notification_template_detail' => '<p>We regret to inform you that your recent order has been cancelled. Please contact us if you have any questions or need</p>',
        ]);

        // Admin notification template
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'An order has been cancelled by the user.',
            'status' => 1,
            'user_type' => 'admin',
            'language' => 'en',
            'template_detail' => '<p>Dear [[ admin_name ]],</p>
<p>An order has been cancelled by the user. Please review the order details and take any necessary actions.</p>
<p>Order ID: [[ order_id ]]</p>
<p>Order Date: [[ order_date ]]</p>
<p>Customer Name: [[ user_name ]]</p>
<p>Best regards,</p>
<p>[[ company_name ]]</p>',
            'subject' => 'Order Cancelled!',
            'notification_subject' => 'Order Cancellation Alert',
            'notification_template_detail' => '<p>An order has been cancelled by the user. Please review the order details and take any necessary actions.</p>',
           ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alter_notification_template');
    }
};

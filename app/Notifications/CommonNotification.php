<?php

namespace App\Notifications;

use App\Broadcasting\CustomWebhook;
use App\Mail\MailMailableSend;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\NotificationTemplate\Models\NotificationTemplate;
use Modules\NotificationTemplate\Models\NotificationTemplateContentMapping;
use Spatie\WebhookServer\WebhookCall;
use App\Broadcasting\FcmChannel;
use Illuminate\Support\Facades\Log;

class CommonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $type;

    public $data;

    public $subject;

    public $notification;

    public $notification_message;

    public $notification_link;

    public $appData;

    public $custom_webhook;

    /**
     * Create a new notification instance.
     */
    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;

        $userType = $data['user_type'];
        $notifications = NotificationTemplate::where('type', $this->type)
            ->with('defaultNotificationTemplateMap')
            ->first();

        $notify_data = NotificationTemplateContentMapping::where('template_id', $notifications->id)->get();

        $templateData = $notify_data->where('user_type', $userType)->first();
        $templateDetail = $templateData->mail_template_detail ?? null;
        foreach ($this->data as $key => $value) {
            // $templateDetail = str_replace('[[ ' . $key . ' ]]', $this->data[$key], $templateDetail);
            $replacement = is_scalar($value) ? (string)$value : json_encode($value);
            $templateDetail = str_replace('[[ ' . $key . ' ]]', $replacement, $templateDetail);
        }
        $this->data['type'] = $templateData->subject ?? 'None';
        $this->data['message'] = $templateDetail ?? __('messages.default_notification_body');
        $this->appData = $notifications->channels;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {

        $notificationSettings = $this->appData;
        $notification_settings = [];
        $notification_access = isset($notificationSettings[$this->type]) ? $notificationSettings[$this->type] : [];

        if (isset($notificationSettings)) {

            foreach ($notificationSettings as $key => $notification) {
                if ($notification) {

                    switch ($key) {

                        case 'PUSH_NOTIFICATION':
                            array_push($notification_settings, FcmChannel::class);

                            break;

                        case 'IS_MAIL':
                            array_push($notification_settings, 'mail');
                            break;

                        case 'IS_CUSTOM_WEBHOOK':
                            if (setting('is_custom_webhook_notification') == 1) {
                                array_push($notification_settings, CustomWebhook::class);
                            }

                            break;
                    }
                }
            }
        }
        return array_merge($notification_settings, ['database']);
    }



    public function toFcm($notifiable)
    {

        $msg = strip_tags($this->data['message']);
        if (! isset($msg) && $msg == '') {
            $msg = __('message.notification_body');
        }
        $type = 'booking';
        if (isset($this->data['type']) && $this->data['type'] !== '') {
            $type = $this->data['type'];
        }

        $heading =  $this->data['type'] ?? '';

        $additionalData = json_encode($this->data);
        return fcm([
            "message" => [
                "topic" => 'user_'.$notifiable->id,
                "notification" => [
                    "title" => $heading,
                    "body" => $msg,
                ],
                "data" => [
                    "sound"=>"default",
                    "story_id" => "story_12345",
                    "type" => $type,
                    "additional_data" => $additionalData,
                    "click_action"=> "FLUTTER_NOTIFICATION_CLICK",
                ],
                "android" => [
                    "priority" => "high",
                    "notification" => [
                        "click_action"=> "FLUTTER_NOTIFICATION_CLICK",
                    ],
                ],
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "category" => "NEW_MESSAGE_CATEGORY",
                        ],
                    ],
                ],
            ],
        ]);
    }
    /**
     * Get mail notification
     *
     * @param  mixed  $notifiable
     * @return MailMailableSend
     */
    public function toMail($notifiable)
    {
        $email = '';

        if (isset($notifiable->email)) {
            $email = $notifiable->email;
        } else {
            $email = $notifiable->routes['mail'];
        }

        return (new MailMailableSend($this->notification, $this->data, $this->type))->to($email)
            ->bcc(isset($this->notification->bcc) ? json_decode($this->notification->bcc) : [])
            ->cc(isset($this->notification->cc) ? json_decode($this->notification->cc) : [])
            ->subject($this->subject);
    }

    public function toWebhook($notifiable)
    {
        $key = setting('custom_webhook_content_key');
        $url = setting('custom_webhook_url');
        $secrate_key = setting('app_name');
        $msg = 'Subject: '.$this->subject."\nDescription: ".strip_tags($this->notification_message)."\n".'Link: '.$this->notification_link;

        return WebhookCall::create()
            ->url($url)
            ->payload([$key => $msg])
            ->useSecret($secrate_key)
            ->dispatch();
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'subject' => $this->subject,
            'data' => $this->data,
        ];
    }
    public function Types($type)
    {
        switch ($type) {
            case 'new_booking':
                return $this->type = 'New Booking Received';
                break;
            case 'check_in_booking':
                return $this->type = 'Check-in Successful';
                break;
            case 'checkout_booking':
                return $this->type = 'Check-out Successful';
                break;
            case 'complete_booking':
                return $this->type = 'Booking Completed';
                break;
            case 'cancel_booking':
                return $this->type = 'Booking Cancelled';
                break;
            case 'order_placed':
                return $this->type = 'Order Successfully Placed';
                break;
            case 'order_proccessing':
                return $this->type = 'Order Processing';
                break;
            case 'order_delivered':
                return $this->type = 'Order Delivered';
                break;
            case 'order_cancelled':
                return $this->type = 'Order Cancelled';
                break;
            case 'quick_booking':
                return $this->type = 'New Booking Received';
                break;
            case 'package_expiry':
                return $this->type = 'Package Expiry';
                break;
            default:
                return $this->type = 'Unknown Type';
                break;
        }
    }

}

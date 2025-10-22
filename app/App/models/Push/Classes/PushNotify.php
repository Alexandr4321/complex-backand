<?php

namespace App\App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\ApnsFcmOptions;

class PushNotify extends Notification implements ShouldQueue
{
    use Queueable;

    private $title;
    private $content;

    public function __construct($title, $content = '')
    {
        $this->title = $title;
        $this->content = $content;
    }

    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                ->setTitle($this->title)
                ->setBody($this->content)
                ->setImage('http://example.com/url-to-image-here.png'))
            ->setAndroid(AndroidConfig::create()
                ->setData(['route' => 'notifications'])
                ->setFcmOptions(AndroidFcmOptions::create()->setAnalyticsLabel('analytics'))
            )->setApns(ApnsConfig::create()
                ->setFcmOptions(ApnsFcmOptions::create()->setAnalyticsLabel('analytics_ios'))
                ->setPayload(['aps' => ['sound' => 'default',],'route' => 'notifications']));
    }
}

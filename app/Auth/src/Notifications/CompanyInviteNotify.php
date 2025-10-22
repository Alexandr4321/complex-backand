<?php

namespace App\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CompanyInviteNotify extends Notification implements ShouldQueue
{
    use Queueable;
    
    public $tries = 2;

    protected $token;
    protected $companyName;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($company, $token)
    {
        $this->token = $token;
        $this->companyName = $company ? $company->name : '';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [ 'mail', ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(config('front.url').'/register/'.$this->token);
        $company = $this->companyName ? ' в компанию '.$this->companyName.'.' : '.';
        
        return (new MailMessage)
            ->subject('Приглашение в систему')
            ->greeting('Добро пожаловать!')
            ->line('Вас пригласили в систему E-Qurylys'.$company)
            ->action('Зарегистрироваться в системе', $url);
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
            //
        ];
    }
}

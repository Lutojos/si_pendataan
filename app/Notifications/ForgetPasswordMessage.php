<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class ForgetPasswordMessage extends Notification
{
    use Queueable;

    private $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database', 'firebase'];
    }

    /**
     * Get the firebase representation of the notification.
     */
    public function toFirebase($notifiable)
    {
        if (!empty($this->data['fcm_token'])) {
            return (new FirebaseMessage())
                ->withTitle('Reset Password')
                ->withBody('Kami menerima permintaan Anda untuk mereset password.')
                ->withPriority('high')
                ->asNotification([$this->data['fcm_token']]);
        } else {
            return [
                //
            ];
        }
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Reset Password')
            ->line('Kami menerima permintaan Anda untuk mereset password.')
            ->action('Reset Password', route('reset.password.get', $this->data['token']));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => 'Kami menerima permintaan Anda untuk mereset password. Segera cek email untuk menyelesaikannya.',
        ];
    }
}

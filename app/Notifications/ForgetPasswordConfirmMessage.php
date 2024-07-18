<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class ForgetPasswordConfirmMessage extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
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
                ->withTitle('Reset Password Sukses')
                ->withBody('Password Anda berhasil diubah. Jika Anda tidak merasa mengubahnya, segera hubungi Admin.')
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
            ->subject('Reset Password Sukses')
            ->line('Password Anda berhasil diubah. Jika Anda tidak merasa mengubahnya, segera hubungi Admin.');
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
            'message' => 'Password Anda berhasil diubah. Jika Anda tidak merasa mengubahnya, segera hubungi Admin.',
        ];
    }
}

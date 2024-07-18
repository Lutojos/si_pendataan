<?php

namespace App\Notifications;

use App\Models\HelpCenter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class KaryawanNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $data;
    private $title;
    private $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;

        $this->title   = "Perubahan Task Notification";
        $this->message = "Anda mendapatkan task baru mohon segera cek detail task Anda.";
    }

    public function isPending()
    {
        $check = HelpCenter::where('id', $this->data['help_center_id'])->where('task_status', '1')->exists();

        return $check;
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        return $this->isPending();
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
                ->withTitle($this->title)
                ->withBody($this->message)
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
            ->subject($this->title)
            ->line($this->message);
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
            'message' => $this->message,
        ];
    }
}

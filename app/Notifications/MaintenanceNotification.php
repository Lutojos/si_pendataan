<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class MaintenanceNotification extends Notification
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

        //Assigned
        $this->title   = "Perubahan Status Maintenance";
        $this->message = "Request maintenance Anda sudah dijadwalkan. Mohon tunggu petugas akan segera datang. Silakan cek pada menu Help Center untuk melihat detail.";

        if ($data['action'] == '2') {
            //On Progress
            $this->message = "Request maintenance Anda sedang dikerjakan mohon tunggu pengerjaan selesai. Silakan cek pada menu Help Center untuk melihat detail.";
        } elseif ($data['action'] == '3') {
            //Selesai
            $this->message = "Request maintenance Anda sudah diselesaikan. Silakan cek pada menu Help Center untuk melihat detail.";
        } elseif ($data['action'] == '4') {
            $this->message = "Request maintenance Anda dibatalkan. Silakan cek pada menu Help Center untuk melihat detail.";
        }
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

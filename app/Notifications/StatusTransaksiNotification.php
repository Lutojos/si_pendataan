<?php

namespace App\Notifications;

use App\Models\OrderPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class StatusTransaksiNotification extends Notification implements ShouldQueue
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
        $this->title   = "Perubahan Status Transaksi";
        $this->message = "Mohon segera melakukan pembayaran pada nomor transaksi " . $data['trx_number'] . ". Silakan cek pada menu Transaksi untuk melihat detail";

        if ($data['action'] == '1') {
            //On Progress
            $this->message = "Selamat transaksi Anda dengan nomor " . $data['trx_number'] . " berhasil!. Silakan cek pada menu Transaksi untuk melihat detail.";
        } elseif ($data['action'] == '2') {
            //Selesai
            $this->message = "Transaksi Anda dengan nomor transaksi " . $data['trx_number'] . " dibatalkan secara otomatis karena pembayaran tidak ditemukan. Silakan cek pada menu Transaksi untuk melihat detail.";
        }
    }

    public function isPending()
    {
        $check = OrderPayment::where('trx_number', $this->data['trx_number'])->where('payment_status', '1')->exists();

        return $check;
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        if ($this->data['action'] == '0') {
            return $this->isPending();
        }

        return true;
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

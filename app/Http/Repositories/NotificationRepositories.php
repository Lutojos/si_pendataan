<?php

namespace App\Http\Repositories;

use App\Models\User;
use App\Notifications\ForgetPasswordConfirmMessage;
use App\Notifications\ForgetPasswordMessage;
use App\Notifications\KaryawanNotification;
use App\Notifications\MaintenanceNotification;
use App\Notifications\MessageFromAdminNotification;
use App\Notifications\RegistrationMessage;
use App\Notifications\StatusTransaksiNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class NotificationRepositories
{
    public function getMyNotification($request)
    {
        $user   = User::find(Auth::user()->id);
        $offset = $request->offset ?? 0;
        $limit  = $request->limit ?? 10;
        $notif  = [];

        foreach ($user->notifications->skip($offset)->take($limit) as $notification) {
            $notif[] = [
                'id'         => $notification->id,
                'data'       => $notification->data,
                'read_at'    => (!$notification->read_at) ? $notification->read_at : time_elapsed_string($notification->read_at),
                'created_at' => time_elapsed_string($notification->created_at),
                'updated_at' => time_elapsed_string($notification->updated_at),
            ];
        }

        return $notif;
    }

    public function markAsReadById($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }

        return 'Mark as Read Success';
    }

    public function markAsRead()
    {
        $notification = Auth::user()->notifications;
        if ($notification) {
            $notification->markAsRead();
        }

        return 'Mark as Read Success';
    }

    public function sendRegistrationNotification($to)
    {
        $user = User::find($to);

        // send notification using the "user" model, when the user receives new message
        Notification::send($user, new RegistrationMessage());
    }

    public function forgetPasswordNotification($email, $token = 'confirm')
    {
        $user = User::where('email', $email)->first();
        if ($token == 'confirm') {
            Notification::send($user, new ForgetPasswordConfirmMessage(['fcm_token' => $user->firebase_token]));
        } else {
            Notification::send($user, new ForgetPasswordMessage(['token' => $token, 'fcm_token' => $user->firebase_token]));
        }
    }

    public function messageFromAdminNotification($req)
    {
        $user = User::find($req->to);
        Notification::send($user, new MessageFromAdminNotification(['fcm_token' => $user->firebase_token]));
    }

    public function updateStatusPembayaran($to, $status_pembayaran, $trx_number)
    {
        $user  = User::find($to);
        $delay = Carbon::now()->addHours(6);

        Notification::send($user, new StatusTransaksiNotification(['fcm_token' => $user->firebase_token, 'action' => $status_pembayaran, 'trx_number' => $trx_number]));

        if ($status_pembayaran == '0') {
            $user->notify((new StatusTransaksiNotification(['fcm_token' => $user->firebase_token, 'action' => $status_pembayaran, 'trx_number' => $trx_number]))->delay($delay));
        }
    }

    public function assignedMaintenance($email, $data)
    {
        $user  = User::where('email', $email)->first();
        $delay = Carbon::now()->addHours(6);

        $user->notify((new KaryawanNotification(['fcm_token' => $user->firebase_token, 'help_center_id' => $data->id]))->delay($delay));
        //send now notification
        return Notification::send($user, new KaryawanNotification(['fcm_token' => $user->firebase_token, 'help_center_id' => $data->id]));
    }

    public function requestedMaintenance($email, $actionNumber = '')
    {
        $user = User::where('email', $email)->first();

        if ($actionNumber == '') {
            Notification::send(
                $user,
                new MaintenanceNotification(['fcm_token' => $user->firebase_token, 'action' => 1]),
            );
        } else {
            Notification::send(
                $user,
                new MaintenanceNotification(['fcm_token' => $user->firebase_token, 'action' => $actionNumber]),
            );
        }
    }

    public function finishMaintenance($email): void
    {
        $user = User::where('email', $email)->first();

        Notification::send($user, new MaintenanceNotification(['fcm_token' => $user->firebase_token, 'action' => 3]));
    }

    public function cancelMaintenance($email): void
    {
        $user = User::where('email', $email)->first();

        Notification::send($user, new MaintenanceNotification(['fcm_token' => $user->firebase_token, 'action' => 4]));
    }
}

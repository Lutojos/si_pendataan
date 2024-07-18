<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\NotificationRepositories;
use App\Http\Requests\Admin\ForgotPasswordRequest;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Throwable;

class ForgotPasswordController extends Controller
{
    private $notificationRepo;

    public function __construct(NotificationRepositories $notifactionRepo)
    {
        $this->notificationRepo = $notifactionRepo;
    }

    public function index()
    {
        return view('content.auth.forgetPassword');
    }

    public function submitForgetPasswordForm(ForgotPasswordRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->checkTooManyFailedAttempts('forget-password');

            $token = Str::random(64);

            $reset = PasswordReset::updateOrCreate(
                [
                    'email' => $request->email,
                ],
                [
                    'token'      => $token,
                    'created_at' => Carbon::now(),
                ],
            );

            if ($reset) {
                $this->notificationRepo->forgetPasswordNotification($request->email, $token);
            }

            RateLimiter::hit($this->throttleKey('forget-password'), $seconds = 120);

            DB::commit();

            return redirect()->route('reset.password.form')->with('success', __('Kami menerima permintaan Anda untuk mereset password. Segera cek email untuk menyelesaikannya.'));
        } catch (Throwable $t) {
            DB::rollBack();

            return redirect()->route('reset.password.form')->with('error', __($t->getMessage()));
        }
    }

    public function showResetPasswordForm($token)
    {
        return view('content.auth.forgetPasswordLink', ['token' => $token]);
    }

    public function submitResetPasswordForm(ResetPasswordRequest $request)
    {
        DB::beginTransaction();
        try {
            $updatePassword = PasswordReset::where([
                'email' => $request->email,
                'token' => $request->token,
            ])
                ->first();

            if (!$updatePassword) {
                DB::rollBack();

                return back()->withInput()->with('error', 'Invalid token for this email!');
            }

            User::where('email', $request->email)
                ->update(['password' => Hash::make($request->password)]);

            $reset = PasswordReset::where(['email' => $request->email])->delete();

            if ($reset) {
                $this->notificationRepo->forgetPasswordNotification($request->email);
            }
            DB::commit();

            return back()->with('message', 'Your password has been changed! Please Login <a href="' . route('auth.login') . '">here</a>');
        } catch (Throwable $t) {
            DB::rollBack();

            return back()->withInput()->with('error', $t->getMessage());
        }
    }
}

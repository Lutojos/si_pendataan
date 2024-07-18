<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\AuthRepositories;
use App\Http\Requests\Admin\AuthRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

class AuthController extends Controller
{
    protected $maxAttempts  = 3; // Default is 5
    protected $decayMinutes = 2; // Default is 1

    protected $authRepo;

    public function __construct(AuthRepositories $authRepo)
    {
        $this->authRepo = $authRepo;
    }

    public function index(Request $request)
    {
        return view('login');
    }

    public function login(AuthRequest $request)
    {
        try {
            $this->checkTooManyFailedAttempts();

            $login = $this->authRepo->loginCms($request);
            if ($login['status']) {
                $request->session()->regenerate();
                RateLimiter::clear($this->throttleKey());

                return redirect()->intended('dashboard');
            } else {
                RateLimiter::hit($this->throttleKey(), $seconds = 120);

                return back()->withErrors($login['errors'])->onlyInput('email');
            }
        } catch (Throwable $th) {
            return back()->withErrors($th->getMessage())->onlyInput('email');
        }
    }

    public function logout(Request $request)
    {
        $this->authRepo->logoutCms($request);

        return redirect()->route('auth.form');
    }
}

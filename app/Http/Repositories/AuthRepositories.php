<?php

namespace App\Http\Repositories;

use App\Http\Requests\Admin\AuthRequest as CMSAuthRequest;
use App\Http\Requests\Api\AuthRequest as ApiAuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthRepositories
{
    public function loginCms(CMSAuthRequest $request)
    {
        $credentials      = $request->safe()->only(['email', 'password']);
        $result['status'] = false;
        $result['errors'] = ['email' => 'Invalid Username or Password'];
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role_id' => [1, 2]])) {
            $result['status'] = true;
            $result['errors'] = false;
        }

        return $result;
    }

    public function loginApi(ApiAuthRequest $request)
    {
        $credentials      = $request->safe()->only(['email', 'password']);
        $result['status'] = false;
        $result['errors'] = ['email' => 'Invalid Username or Password'];

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role_id' => [3, 4]])) {
            $result['status'] = true;
            $result['errors'] = false;
            $user             = User::where('email', $credentials['email'])->first();

            //clear all token same user
            PersonalAccessToken::where('tokenable_id', $user->id)->delete();

            // TODO will add user ability to this token later
            $token             = $user->createToken('login')->plainTextToken;
            $result['token']   = $token;
            $result['role_id'] = $user->role_id;
        }

        return $result;
    }

    public function logoutCms(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function logoutApi(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
    }

    public function updateFirebase($request)
    {
        return $request->user()->update(['firebase_token' => $request->firebase_token]);
    }
}

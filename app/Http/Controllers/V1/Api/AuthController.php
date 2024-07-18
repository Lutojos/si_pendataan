<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use App\Http\Repositories\AuthRepositories;
use App\Http\Repositories\NotificationRepositories;
use App\Http\Requests\Api\AuthRequest;
use App\Http\Requests\Api\ForgotPasswordRequest;
use App\Models\PasswordReset;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class AuthController extends Controller
{
    protected $authRepo;
    private $notificationRepo;

    public function __construct(AuthRepositories $authRepo, NotificationRepositories $notifactionRepo)
    {
        $this->authRepo         = $authRepo;
        $this->notificationRepo = $notifactionRepo;
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     operationId="login",
     *     tags={"Auth"},
     *     summary="Login",
     *     description="Login",
     *     security={{ "mobilekey":{} }},
     *     @OA\RequestBody(
     *          description="User credential",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email", "password"},
     *              @OA\Property(property="email", type="string", format="email", example="karyawansambina@mail.com"),
     *              @OA\Property(property="password", type="string", format="password", example="Qwer1234!")
     *          ),
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="Successfully login")
     *          )
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Login Failed",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example="false"),
     *              @OA\Property(property="message", type="string", example="The provided credentials are incorrect")
     *                  )
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation Failed",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example="false"),
     *              @OA\Property(property="message", type="string", example="Email and password required")
     *                  )
     *              ),
     *          )
     *     )
     * )
     */
    public function login(AuthRequest $request)
    {
        $login = $this->authRepo->loginApi($request);

        if ($login['status']) {
            return _200([
                'token'   => $login['token'],
                'role_id' => $login['role_id'],
            ]);
        }

        return _401($login['errors']);
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     operationId="logout",
     *     tags={"Auth"},
     *     summary="Logout",
     *     description="Logout",
     *     security={{ "mobilekey":{} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="Token has been deleted")
     *          )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $this->authRepo->logoutApi($request);

        return _200([], true, 'Token has been deleted');
    }

    /**
     * @OA\Post(
     *     path="/forget-password",
     *     operationId="forgetpassword",
     *     tags={"Auth"},
     *     summary="Forget Password",
     *     description="Forget Password",
     *     security={{ "mobilekey":{} }},
     *     @OA\RequestBody(
     *          description="Forget Password",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email"},
     *              @OA\Property(property="email", type="string", format="email", example="adminsambina@mail.com")
     *          ),
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="We have e-mailed your password reset link!")
     *          )
     *     )
     * )
     */
    public function submitForgetPasswordForm(ForgotPasswordRequest $request)
    {
        DB::beginTransaction();
        try {
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

            DB::commit();

            return _200('Kami menerima permintaan Anda untuk mereset password. Segera cek email untuk menyelesaikannya.');
        } catch (Throwable $t) {
            DB::rollBack();

            return _500($t->getMessage());
        }
    }

    /**
     * @OA\Patch(
     *     path="/fcm-token",
     *     operationId="fcmToken",
     *     tags={"Auth"},
     *     summary="Update Firebase Token",
     *     description="Update Firebase Token",
     *     security={{ "token":{}, "mobilekey":{} }},
     *     @OA\RequestBody(
     *          description="Forget Password",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"firebase_token"},
     *              @OA\Property(property="firebase_token", type="string", format="string", example="{token firebase}")
     *          ),
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="Success")
     *          )
     *     )
     * )
     */
    public function updateToken(Request $request)
    {
        try {
            $update = $this->authRepo->updateFirebase($request);

            return _200($update);
        } catch (Throwable $t) {
            return _500($t->getMessage());
        }
    }
}

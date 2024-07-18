<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use App\Http\Repositories\NotificationRepositories;
use App\Http\Repositories\UserRepositories;
use App\Http\Requests\Api\UserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserController extends Controller
{
    private $userRepo;
    private $notificationRepo;

    public function __construct(UserRepositories $userRepo, NotificationRepositories $notifactionRepo)
    {
        $this->userRepo         = $userRepo;
        $this->notificationRepo = $notifactionRepo;
    }

    /**
     * @OA\Get(
     *     path="/user/me",
     *     operationId="api.user.index",
     *     tags={"User"},
     *     summary="User Me",
     *     description="Get Me Data",
     *     security={{ "token":{}, "mobilekey":{} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="Successfully get data"),
     *              @OA\Property(property="data", type="object",
     *
     *                  @OA\Property(property="data", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer", example="1"),
     *                          @OA\Property(property="name", type="string", example="Administrator"),
     *                      )
     *                  ),
     *              )
     *          )
     *     )
     * )
     */
    public function index()
    {
        $user = Auth::user()->id;
        $user = $this->userRepo->getMe($user);

        return _200($user);
    }

    /**
     * @OA\Post(
     *     path="/user/store",
     *     operationId="api.user.store",
     *     tags={"User"},
     *     summary="User Registration",
     *     security={{ "mobilekey":{} }},
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="text", example="John"),
     *                 @OA\Property(property="email", type="text", example="jhondoe@gmail.com"),
     *                 @OA\Property(property="password", type="password", example="Qwer1234!"),
     *                 @OA\Property(property="password_confirmation", type="password", example="Qwer1234!"),
     *                 @OA\Property(property="placeofbirth", type="text", example="Jakarta"),
     *                 @OA\Property(property="dateofbirth", type="date", example="1991-02-24"),
     *                 @OA\Property(property="address", type="text", example="Jl Bunga No 12"),
     *                 @OA\Property(property="gender", type="integer", example="1",description="1 lanang 2 wadon"),
     *                 @OA\Property(property="phone_number", type="text", example="08123456789"),
     *                 @OA\Property(property="ktp", type="file"),
     *                 @OA\Property(property="avatar", type="file"),
     *                 required={"name", "email", "ktp","password", "password_confirmation", "placeofbirth", "phone_number", "address","gender","dateofbirth"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="Successfully save data"),
     *          )
     *     )
     * )
     */
    public function store(UserRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = [
                'name'         => $request->name,
                'email'        => $request->email,
                'password'     => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'role_id'      => 3, //penghuni
                'gender'       => $request->gender,
                'birthofplace' => $request->placeofbirth,
                'birthofdate'  => $request->dateofbirth,
                'ktp'          => $request->ktp,
                'avatar'       => $request->avatar,
                'address'      => $request->address,
            ];

            $registration = $this->userRepo->registration($data);

            if ($registration) {
                $this->notificationRepo->sendRegistrationNotification($registration->id);
            }

            DB::commit();

            return _200($registration);
        } catch (Throwable $t) {
            DB::rollBack();

            return _500($t->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/user/update-my-profile",
     *     operationId="api.user.updatemyprofile",
     *     tags={"User"},
     *     summary="Update My Profile",
     *     security={{ "token":{}, "mobilekey":{} }},
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="text", example="John"),
     *                 @OA\Property(property="email", type="text", example="jhondoe@gmail.com"),
     *                 @OA\Property(property="password", type="password", description="Biarkan kosong jika tidak diganti"),
     *                 @OA\Property(property="password_confirmation", type="password", description="Biarkan kosong jika tidak diganti"),
     *                 @OA\Property(property="placeofbirth", type="text", example="Jakarta"),
     *                 @OA\Property(property="dateofbirth", type="date", example="1991-02-24"),
     *                 @OA\Property(property="address", type="text", example="Jl Bunga No 12"),
     *                 @OA\Property(property="gender", type="integer", example="1",description="1 lanang 2 wadon"),
     *                 @OA\Property(property="phone_number", type="text", example="08123456789"),
     *                 @OA\Property(property="ktp", type="file"),
     *                 @OA\Property(property="avatar", type="file"),
     *                 required={"name", "email", "placeofbirth", "phone_number", "address","gender","dateofbirth"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="Berhasil memperbarui data"),
     *          )
     *     )
     * )
     */
    public function updateMyProfile(UserRequest $request)
    {
        $data = [
            'name'         => $request->name,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'gender'       => $request->gender,
            'birthofplace' => $request->placeofbirth,
            'birthofdate'  => $request->dateofbirth,
            'ktp'          => $request->ktp,
            'avatar'       => $request->avatar,
            'address'      => $request->address,
        ];

        if (!empty($request->password)) {
            $data = array_merge($data, [
                'password' => Hash::make($request->password),
            ]);
        }

        $updateData = $this->userRepo->updateProfile($data, Auth::user()->id);

        return $updateData;
    }

    /**
     * @OA\Get(
     *     path="/user/my-room",
     *     operationId="api.user.myroom",
     *     tags={"User"},
     *     summary="Get My Room",
     *     description="Get My Room",
     *     security={{ "token":{}, "mobilekey":{} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="Successfully get data"),
     *              @OA\Property(property="data", type="object",
     *
     *                  @OA\Property(property="data", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="room_name", type="integer", example="room name"),
     *                          @OA\Property(property="description", type="string", example="room blablabla"),
     *                          @OA\Property(property="entry_date", type="date", example="2023-02-24"),
     *                          @OA\Property(property="checkout_date", type="date", example="2023-03-03"),
     *                          @OA\Property(property="image_path", type="string", example="path image"),
     *                          @OA\Property(property="it_will_end", type="string", example="1 = will end , 0 = not yet"),
     *                      )
     *                  ),
     *              )
     *          )
     *     )
     * )
     */
    public function myRoom()
    {
        $room = $this->userRepo->getMyRoom();

        if ($room) {
            return _200($room);
        }

        return _404('Data tidak ditemukan');
    }
}

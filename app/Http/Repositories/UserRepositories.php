<?php

namespace App\Http\Repositories;

use App\Library\Upload;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

/**
 * UserRepositories.
 */
class UserRepositories
{
    public $pathAvatar = 'uploads/avatars/';
    public $PathKtp    = 'uploads/ktp/';

    public function registration($data)
    {
        DB::beginTransaction();
        try {
            $upload = new Upload();
            $ktp    = null;
            $avatar = null;
            if ($data['ktp']) {
                $ktp = $upload->upload($data['ktp'], $this->PathKtp);
            }
            if ($data['avatar']) {
                $avatar = $upload->upload($data['avatar'], $this->pathAvatar);
            }
            $user = User::create([
                'name'         => $data['name'],
                'email'        => $data['email'],
                'password'     => $data['password'],
                'phone_number' => $data['phone_number'],
                'role_id'      => 3, //penghuni
                'gender'       => $data['gender'],
                'birthofplace' => $data['birthofplace'],
                'birthofdate'  => $data['birthofdate'],
                'image_path'   => $ktp,
                'avatar'       => $avatar,
                'address'      => $data['address'],
            ]);

            $user->assignRole($data['role_id']);
            Artisan::call('optimize:clear');
            DB::commit();

            return $user;
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();

            return $th->getMessage();
        }
    }

    public function updateProfile($data, $id)
    {
        try {
            //code...
            $user     = User::find($id);
            $upload   = new Upload();
            $ktp      = $user->image_path;
            $avatar   = $user->avatar;
            $password = $user->password;

            if ($ktp == null && $data['ktp'] == null) {
                return _400('KTP is required');
            }
            if ($data['ktp']) {
                if ($ktp) {
                    $upload->delete($this->PathKtp . '/' . $ktp);
                }
                $ktp = $upload->upload($data['ktp'], $this->PathKtp);
            }
            if ($data['avatar']) {
                if ($avatar) {
                    $upload->delete($this->pathAvatar . '/' . $avatar);
                }

                $avatar = $upload->upload($data['avatar'], $this->pathAvatar);
            }
            $user->update([
                'name'         => $data['name'],
                'email'        => $data['email'],
                'password'     => $data['password'] ?? $password,
                'phone_number' => $data['phone_number'],
                'gender'       => $data['gender'],
                'birthofplace' => $data['birthofplace'],
                'birthofdate'  => $data['birthofdate'],
                'image_path'   => $ktp,
                'avatar'       => $avatar,
                'address'      => $data['address'],
            ]);

            return _200($user);
        } catch (\Throwable $th) {
            //throw $th;
            return $th->getMessage();
        }
    }

    public function getMe($id)
    {
        $user = User::with(['roles'])->find($id);

        return [
            'id'           => $user->id,
            'name'         => $user->name,
            'email'        => $user->email,
            'phone_number' => $user->phone_number,
            'gender'       => $user->gender,
            'birthofdate'  => $user->birthofdate,
            'birthofplace' => $user->birthofplace,
            'address'      => $user->address,
            'ktp'          => $user->getKtp(),
            'avatar'       => $user->getAvatar(),
            'myroom'       => $this->getMyRoom($id) ? $this->getMyRoom($id) : (object) [],
        ];
    }

    public function getOptionUser($search = null, $id = null, $roleId = null, $order = null)
    {
        $cur_date = Carbon::now()->toDateString();
        $datas    = User::select('users.id', 'users.name', 'users.email')
            ->whereNull('users.deleted_at');
        if ($search != null) {
            $datas = $datas->where('users.name', 'like', "%{$search}%");
        }
        if ($id != null) {
            $datas = $datas->where('users.id', '=', "{$id}");
        }
        if ($roleId != null) {
            $datas = $datas->where('users.role_id', '=', "{$roleId}");
        }
        $datas = $datas->orderBy('users.name');
        $datas = $datas->groupBy('users.id')->get();

        return $datas;
    }

    /**
     * all.
     *
     * @param mixed $request
     * @return array
     */
    public function all($request)
    {
        $cur_date  = Carbon::now()->toDateString();
        $filter    = $request['filter'];
        $searchVal = (isset($filter['search'])) ? $filter['search'] : false;

        $data = User::select([
            DB::raw("md5(concat(users.id,'-',date_format(curdate(), '%Y%m%d'))) as _token"),
            'users.id',
            'users.name',
            'users.email',
            'roles.name as role',
            'users.avatar',
            'roles.id as roles_id',
            'users.created_at',
        ])

            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->when($searchVal, function ($query) use ($searchVal) {
                $query->where('users.name', 'like', "%{$searchVal}%")
                    ->orWhere('users.email', 'like', "%{$searchVal}%")
                    ->orWhere('roles.name', 'like', "%{$searchVal}%");
            })->groupBy('users.id');

        return $data;
    }

    public function store($data)
    {
        DB::beginTransaction();
        try {
            $upload = new Upload();
            $ktp    = null;
            $avatar = null;
            if ($data['ktp'] != 'undifined') {
                $ktp = $upload->upload($data['ktp'], $this->PathKtp);
            }
            if ($data['avatar'] != 'undifined') {
                $avatar = $upload->upload($data['avatar'], $this->pathAvatar);
            }
            $ttl = null;
            if ($data['tanggal_lahir'] != null && !in_array($data['role'], [1, 2])) {
                //data tanggal lahir = dd/mm/yyyy
                $ttl = Carbon::createFromFormat('d/m/Y', $data['tanggal_lahir'])->format('Y-m-d');
            }

            //check age
            if ($ttl != null) {
                $age = Carbon::parse($ttl)->age;
                if ($data['role'] == 4) {
                    if ($age < 17) {
                        return jsonError('Umur minimal 17 tahun', 400);
                    }
                }
            }
            $data['password'] = bcrypt($data['password']);
            $user             = User::create([
                'name'         => $data['name'],
                'email'        => $data['email'],
                'password'     => $data['password'],
                'image_path'   => $ktp,
                'avatar'       => $avatar,
                'role_id'      => $data['role'],
                'gender'       => $data['jenis_kelamin'],
                'birthofdate'  => $ttl,
                'birthofplace' => $data['tempat_lahir'],
                'address'      => $data['alamat'],
                'phone_number' => $data['nomor_telepon'],
            ]);
            //asign role
            $user->assignRole($data['role']);
            Artisan::call('optimize:clear');
            DB::commit();

            return jsonSuccess('User has been created');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();

            return jsonError($th->getMessage());
        }
    }

    public function getUserByToken($token, $role = false)
    {
        $cur_date = Carbon::now()->toDateString();
        $user     = User::select(
            [
                'users.*',
                DB::Raw("md5(concat(users.id, '-', date_format(curdate(), '%Y%m%d'))) as _token"),
                'roles.name as role',
                'roles.id as roles_id',

            ],
        )
            ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->token($token)
            ->first();

        return $user;
    }

    public function update($data, $token)
    {
        DB::beginTransaction();
        try {
            $upload = new Upload();
            $user   = User::token($token)->first();
            $ktp    = $user->image_path;
            $avatar = $user->avatar;
            if ($data['ktp'] != 'undifined') {
                $ktp = $upload->upload($data['ktp'], $this->PathKtp);
                $upload->delete($this->PathKtp . $user->image_path);
            }
            if ($data['avatar'] != 'undifined') {
                $avatar = $upload->upload($data['avatar'], $this->pathAvatar);
                $upload->delete($this->pathAvatar . $user->avatar);
            }
            $ttl = $user->birthofdate;
            ;
            if ($data['tanggal_lahir'] != null && !in_array($data['role'], [1, 2])) {
                //data tanggal lahir = dd/mm/yyyy
                $ttl = Carbon::createFromFormat('d/m/Y', $data['tanggal_lahir'])->format('Y-m-d');
            }
            if ($ttl != null) {
                $age = Carbon::parse($ttl)->age;
                if ($data['role'] == 4) {
                    if ($age < 17) {
                        return jsonError('Umur minimal 17 tahun', 400);
                    }
                }
            }
            $user->update([
                'name'         => $data['name'],
                'email'        => $data['email'],
                'image_path'   => $ktp,
                'avatar'       => $avatar,
                'role_id'      => $data['role'],
                'gender'       => $data['jenis_kelamin'],
                'birthofdate'  => $ttl,
                'birthofplace' => $data['tempat_lahir'],
                'address'      => $data['alamat'],
                'phone_number' => $data['nomor_telepon'],
            ]);
            //update password if not null
            if ($data['password'] != null) {
                $user->update([
                    'password' => bcrypt($data['password']),
                ]);
            }
            //asign role
            $user->syncRoles($data['role']);
            Artisan::call('optimize:clear');
            DB::commit();

            return jsonSuccess('User has been updated');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();

            return jsonError($th->getMessage());
        }
    }

    public function delete($token)
    {
        $user   = User::token($token)->first();
        $upload = new Upload();
        $upload->delete($this->PathKtp . $user->image_path);
        $upload->delete($this->pathAvatar . $user->avatar);
        $user->delete();

        return jsonSuccess('User has been deleted');
    }
}

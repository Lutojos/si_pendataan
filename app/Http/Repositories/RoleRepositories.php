<?php

namespace App\Http\Repositories;

use App\Http\Requests\Admin\RoleRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoleRepositories
{
    public function getRole(Request $request)
    {
        $ret                  = new \stdClass();
        $ret->draw            = $request->draw;
        $ret->recordsTotal    = 0;
        $ret->recordsFiltered = 0;
        $ret->data            = [];

        $filter    = $request['filter'];
        $searchVal = (isset($filter['search'])) ? $filter['search'] : false;

        $data = Role::select('*', DB::raw("md5(concat(roles.id,'-',date_format(curdate(), '%Y%m%d'))) as _token"))
            ->when($searchVal, function ($query) use ($searchVal) {
                $query->where('roles.name', 'like', "%{$searchVal}%");
            });

        if (isset($request['order'])) {
            foreach ($request["order"] as $i => $order) {
                $data->orderBy($order["column"], $order["dir"]);
            }
        }

        $ret->recordsTotal    = $data->get()->count();
        $ret->recordsFiltered = $data->get()->count();
        $ret->data            = $data->skip($request->start)->take($request->length)->get()->toArray();

        return response()->json($ret, 200);
    }

    public function getRoleById($id)
    {
        return Role::find($id);
    }

    public function getRoleByToken($token)
    {
        return Role::select(
            'roles.*',
            DB::Raw("md5(concat(roles.id, '-', date_format(curdate(), '%Y%m%d'))) as _token"),
        )
            ->token($token)->first();
    }

    public function storeRole(RoleRequest $request)
    {
        return Role::create([
            'name'       => $request->name,
            'created_by' => Auth::user()->id,
        ]);
    }

    public function updateRole(RoleRequest $request, $token)
    {
        $role = Role::token($token)->update([
            'name'       => $request->name,
            'updated_by' => Auth::user()->id,
        ]);

        return $role;
    }

    public function deleteRole($token)
    {
        $role = Role::token($token)->first();

        if (!$role) {
            $delete = [
                'status'  => false,
                'message' => __('Data tidak ditemukan'),
            ];
        }

        $delete = [
            'status'  => false,
            'message' => __('This Role still has user'),
        ];

        if ($role->users()->count() == 0) {
            $role->delete();
            $delete['status']  = true;
            $delete['message'] = __('Successfully delete role');
        }

        return $delete;
    }

    public function getOptionRole($search = null, $id = null, $detail = false)
    {
        //get property_id count from user login

        $datas = Role::select('id', 'name')->whereNull('deleted_at')
            ->when(!$detail, function ($query) {
                return $query->whereIn('id', [1, 2, 4]);
            })
            ->when($detail, function ($query) {
                return $query->whereIn('id', [1, 2, 3, 4]);
            });

        if ($search != null) {
            $datas = $datas->where('name', 'like', "%{$search}%");
        }
        if ($id != null) {
            $datas = $datas->where('id', '=', "{$id}");
        }
        $datas = $datas->when(!auth()->user()->hasRole('Superadmin') && !$detail, function ($query) {
            return $query->whereIn('id', [2, 4]);
        })->when(auth()->user()->hasRole('Admin Property') && !$detail, function ($query) {
            return $query->whereIn('id', [4]);
        })->get();

        return $datas;
    }
}

<?php

namespace App\Http\Repositories;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Artisan;

class PermissionRepositories
{
    protected $roleRepo;
    protected $main_capabilities = [
        'Read',
        'Create',
        'Edit',
        'Delete',
    ];

    public function __construct(RoleRepositories $roleRepo)
    {
        $this->roleRepo = $roleRepo;
    }

    public function getMainCapabilities()
    {
        return $this->main_capabilities;
    }

    public function getAllPermission()
    {
        $permissions = Permission::get()->mapToGroups(function ($item, $key) {
            return [$item->module_name => $item];
        });

        return $permissions;
    }

    public function getRolePermission(Role $role)
    {
        return $role->permissions->pluck('name')->toArray();
    }

    public function assignPermission($request)
    {
        $token       = $request->role;
        $permissions = $request->permissions;
        if ($role = $this->roleRepo->getRoleByToken($token)) {
            $role->syncPermissions($permissions);
        }
        Artisan::call('optimize:clear');
    }
}

<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\PermissionRepositories;
use App\Http\Repositories\RoleRepositories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    protected $permissionRepo;
    protected $roleRepo;

    public function __construct(PermissionRepositories $permissionRepo, RoleRepositories $roleRepo)
    {
        $this->permissionRepo = $permissionRepo;
        $this->roleRepo       = $roleRepo;
    }

    public function index(Request $request, $token = '')
    {
        if (!Auth::user()->can('assign permission')) {
            abort(404);
        }

        if (!$role = $this->roleRepo->getRoleByToken($token)) {
            return redirect()->route('role.index')->with('error', 'Data tidak ditemukan');
        }

        // main capabilities
        $capabilities = $this->permissionRepo->getMainCapabilities();

        // get all permission
        $permissions = $this->permissionRepo->getAllPermission();

        // role permission
        $role_permissions = $this->permissionRepo->getRolePermission($role);

        return view('content.permission.index', compact('capabilities', 'role', 'permissions', 'role_permissions', 'token'));
    }

    public function assign(Request $request)
    {
        if (!Auth::user()->can('assign permission')) {
            return _404();
        }

        if (!$role = $this->roleRepo->getRoleByToken($request->role)) {
            return _404(__('Invalid role token'));
        }

        if ($role->name == 'Superadmin') {
            return _200();
        }

        $this->permissionRepo->assignPermission($request);

        return _200([], true, __('Successfully set role permissions'));
    }
}

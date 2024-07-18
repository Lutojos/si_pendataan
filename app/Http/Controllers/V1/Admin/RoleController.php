<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\RoleRepositories;
use App\Http\Requests\Admin\RoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    protected $roleRepo;

    public function __construct(RoleRepositories $roleRepo)
    {
        $this->roleRepo = $roleRepo;
    }

    public function index(Request $request)
    {
        if (!Auth::user()->can('list role')) {
            abort(404);
        }

        return view('content.role.index');
    }

    public function list(Request $request)
    {
        if (!Auth::user()->can('list role')) {
            abort(404);
        }

        return $this->roleRepo->getRole($request);
    }

    public function create(Request $request)
    {
        if (!Auth::user()->can('create role')) {
            abort(404);
        }

        return view('content.role.create');
    }

    public function store(RoleRequest $request)
    {
        if (!Auth::user()->can('create role')) {
            abort(404);
        }

        $role = $this->roleRepo->storeRole($request);

        return redirect()->route('role.index')->with('success', __('Successfully create role'));
    }

    public function edit(Request $request, $token = '')
    {
        if (!Auth::user()->can('edit role')) {
            abort(404);
        }

        if (!$role = $this->roleRepo->getRoleByToken($token)) {
            return redirect()->route('role.index')->with('error', __('Data tidak ditemukan'));
        }

        return view('content.role.edit', compact('role'));
    }

    public function update(RoleRequest $request, $token = '')
    {
        if (!Auth::user()->can('edit role')) {
            abort(404);
        }

        $this->roleRepo->updateRole($request, $token);

        return redirect()->route('role.index')->with('success', __('Successfully edit role'));
    }

    public function delete(Request $request, $token = '')
    {
        if (!Auth::user()->can('delete role')) {
            abort(404);
        }

        $delete_status = $this->roleRepo->deleteRole($token);

        if ($delete_status['status']) {
            return redirect()->route('role.index')->with('success', $delete_status['message']);
        }

        return redirect()->route('role.index')->with('error', $delete_status['message']);
    }
}

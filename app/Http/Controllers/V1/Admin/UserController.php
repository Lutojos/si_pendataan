<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\RoleRepositories;
use App\Http\Repositories\UserRepositories;
use App\Http\Requests\Admin\UserRequest as AdminUserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userRepo;
    protected $role;

    /**
     * __construct.
     *
     * @param mixed $userRepo
     * @return void
     */
    public function __construct(UserRepositories $userRepo, RoleRepositories $role)
    {
        $this->userRepo = $userRepo;
        $this->role     = $role;
    }

    /**
     * index.
     *
     * @param mixed $request
     * @return void
     */
    public function index(Request $request)
    {
        $this->authorize('list users');

        return view('content.users.index');
    }

    public function show(Request $request, $token)
    {
        $role     = $request->role ? true : false;
        $data     = $this->userRepo->getUserByToken($token, $role);
        $role     = $this->role->getOptionRole(null, null, true);

        return view('content.users.detail', compact('data', 'role'));
    }

    public function list(Request $request)
    {
        $datatables = datatables($this->userRepo->all($request));

        return $datatables
            //index
            ->addIndexColumn()
            //action
            ->addColumn('action', function ($row) {
                $html = '';
                if (can('edit users') && $row->roles_id != 3) {
                    $html .= '<a href="' . route('user.edit', $row->_token) . '" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#myModal"><i class="fa fa-edit"></i></a>';
                }

                $id = $row->_token;
                if (can('delete users')) {
                    $html .= ' &nbsp;<a class="btn btn-danger btn-sm" href="javascript:void(0)" onclick="deleteData(' . "'$id'" . ',' . "'$row->name'" . ')"><i class="fa fa-trash"></i></a>';
                }
                $role = $row->roles_id == 3 ? '?role=3' : '';
                //view
                $html .= ' &nbsp;<a class="btn btn-info btn-sm" href="' . route('user.detail') . '/' . $row->_token . $role . '" data-toggle="modal" data-target="#myModal"><i class="fa fa-eye"></i></a>';

                return $html;
            })
            ->editColumn('avatar', function ($row) {
                return '<img src="' . $row->getAvatar() . '" width="50px" height="50px">';
            })
            //filter
            ->filter(function ($query) use ($request) {
                if ($request->search) {
                    $query->where(
                        function ($q) use ($request) {
                            $q->where('users.name', 'like', '%' . $request->search . '%')
                                ->orWhere('users.email', 'like', '%' . $request->search . '%')
                                ->orWhere('roles.name', 'like', '%' . $request->search . '%');
                        },
                    );
                }
            })

            ->escapeColumns([])->toJson();
    }

    public function create(Request $request)
    {
        $role     = $this->role->getOptionRole();

        return view('content.users.create', compact('role'));
    }

    public function store(AdminUserRequest $request)
    {
        $data = $request->validated();

        return $this->userRepo->store($data);
    }

    public function edit(Request $request, $token = '')
    {
        $data     = $this->userRepo->getUserByToken($token);
        $role     = $this->role->getOptionRole();

        return view('content.users.edit', compact('data', 'role'));
    }

    public function update(AdminUserRequest $request, $token = '')
    {
        $data = $request->validated();

        return $this->userRepo->update($data, $token);
    }

    public function delete(Request $request, $token = '')
    {
        return $this->userRepo->delete($token);
    }

    public function option(Request $request)
    {
        $users = $this->userRepo->getOptionUser($request->search);
        $list     = [];
        foreach ($users as $key => $row) {
            $list[$key]['id']   = $row->id;
            $list[$key]['text'] = $row->name;
        }

        return json_encode($list);
    }
}

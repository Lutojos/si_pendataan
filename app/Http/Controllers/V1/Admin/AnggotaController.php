<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\ProvinsiRepositories;
use App\Http\Repositories\KotaRepositories;
use App\Http\Repositories\KecamatanRepositories;
use App\Http\Repositories\DesaRepositories;
use App\Http\Repositories\AnggotaRepositories;
use App\Http\Requests\Admin\AnggotaRequest;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AnggotaController extends Controller
{
    private $constanta;

    private $repository;

    public function __construct(AnggotaRepositories $repository)
    {
        $this->repository = $repository;
        $this->constanta  = (object) [
            'listed'  => 'list anggota',
            'created' => 'create anggota',
            'edited'  => 'edit anggota',
            'deleted' => 'delete anggota',
        ];
    }

    public function authValidate($name)
    {
        if (!Auth::user()->can($name)) {
            abort(403);
        }
    }

    public function index()
    {
        $this->authValidate($this->constanta->listed);
        return view('content.anggota.index');
    }

    public function list(Request $request)
    {
        $this->authValidate($this->constanta->listed);
        $user = auth()->user();
        if ($request->ajax()) {
            $data = $this->repository->getListData($request);

            return Datatables::of($data)
                ->editColumn('provinsi_name', function ($row) {
                    return batasString($row->provinsi_name, 200);
                })
                ->addColumn('action', function ($row) use ($user) {
                    $actionBtn = "<div class=\"btn btn-group\">";

                    if ($user->can('edit anggota')) {
                        $actionBtn .= "<a href=\"" . route('anggota.edit', $row->_token) . "\" class=\"btn btn-sm btn-default\" title=\"Edit\"><i class=\"fas fa-edit\"></i> Edit</a>";
                    }

                    if ($user->can('delete anggota')) {
                        $actionBtn .= "<a href=\"" . route('anggota.delete', $row->_token) . "\" class=\"btn btn-sm btn-danger\" title=\"Hapus\" onclick=\"return confirm('Hapus Data?');\"><i class=\"fas fa-trash\"></i> Hapus</a>";
                    }
                    $actionBtn .= "</div>";

                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function create(Request $request)
    {
        if (!Auth::user()->can('create anggota')) {
            abort(403);
        }

        return view('content.anggota.create');
    }

    public function store(AnggotaRequest $request)
    {
        $this->authValidate($this->constanta->created);
        try {
            $insert                  = $this->repository->storeData($request);

            if ($insert) {
                return jsonSuccess('Data berhasil disimpan', route('anggota.index'));
            }
        } catch (Throwable $th) {
            return jsonError($th->getMessage());
        }
    }

    public function edit($token = '')
    {
        if (!Auth::user()->can('edit anggota')) {
            abort(403);
        }
        $data = $this->repository->getDataByToken($token);
        if (!$data['datas']) {
            return redirect()->route('anggota.index')->with('error', __('Data tidak ditemukan'));
        }
        $data  = $data['datas'];
        $images = $data['images'];

        $provinsi = new ProvinsiRepositories();
        $provinsi = $provinsi->getOptionData('', $data->provinsi_id);

        return view('content.anggota.edit', compact('data', 'provinsi', 'images'));
    }

    public function update(AnggotaRequest $request, $token = '')
    {
        $this->authValidate($this->constanta->edited);
        try {
            $update                  = $this->repository->updateData($request, $token);

            if ($update) {
                return jsonSuccess('Data berhasil disimpan', route('anggota.index'));
            }
        } catch (Throwable $th) {
            return  jsonError($th->getMessage());
        }
    }

    public function delete(Request $request, $token = '')
    {
        $this->authValidate($this->constanta->deleted);
        $delete_status = $this->repository->deleteData($token);

        if ($delete_status['status']) {
            return redirect()->route('anggota.index')->with('success', $delete_status['message']);
        }

        return redirect()->route('anggota.index')->with('error', $delete_status['message']);
    }

    public function option(Request $request)
    {
        $datas = $this->repository->getOptionData($request->search);
        $list     = [];
        foreach ($datas as $key => $row) {
            $list[$key]['id']   = $row->id;
            $list[$key]['text'] = $row->kecamatan_name;
        }

        if ($request->all == true) {
            $newElement = [
                'id'   => 0,
                'text' => 'All',
            ];
            array_unshift($list, $newElement);
        }

        return json_encode($list);
    }
}

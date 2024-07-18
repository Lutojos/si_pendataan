<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\ProvinsiRepositories;
use App\Http\Repositories\KotaRepositories;
use App\Http\Repositories\KecamatanRepositories;
use App\Http\Repositories\DesaRepositories;
use App\Http\Requests\Admin\DesaRequest;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Throwable;

class DesaController extends Controller
{
    private $constanta;

    private $repository;

    public function __construct(DesaRepositories $repository)
    {
        $this->repository = $repository;
        $this->constanta  = (object) [
            'listed'  => 'list desa',
            'created' => 'create desa',
            'edited'  => 'edit desa',
            'deleted' => 'delete desa',
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
        return view('content.desa.index');
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

                    if ($user->can('edit desa')) {
                        $actionBtn .= "<a href=\"" . route('desa.edit', $row->_token) . "\" class=\"btn btn-sm btn-default\" title=\"Edit\"><i class=\"fas fa-edit\"></i> Edit</a>";
                    }

                    if ($user->can('delete desa')) {
                        $actionBtn .= "<a href=\"" . route('desa.delete', $row->_token) . "\" class=\"btn btn-sm btn-danger\" title=\"Hapus\" onclick=\"return confirm('Hapus Data?');\"><i class=\"fas fa-trash\"></i> Hapus</a>";
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
        if (!Auth::user()->can('create desa')) {
            abort(403);
        }

        return view('content.desa.create');
    }

    public function store(DesaRequest $request)
    {
        $this->authValidate($this->constanta->created);
        try {
            $insert                  = $this->repository->storeData($request);

            if ($insert) {
                return jsonSuccess('Data berhasil disimpan', route('desa.index'));
            }
        } catch (Throwable $th) {
            return jsonError($th->getMessage());
        }
    }

    public function edit($token = '')
    {
        if (!Auth::user()->can('edit desa')) {
            abort(403);
        }
        $datas = $this->repository->getDataByToken($token);
        if (!$datas['datas']) {
            return redirect()->route('desa.index')->with('error', __('Data tidak ditemukan'));
        }
        $datas  = $datas['datas'];

        $provinsi = new ProvinsiRepositories();
        $provinsi = $provinsi->getOptionData('', $datas->provinsi_id);

        return view('content.desa.edit', compact('datas', 'provinsi'));
    }

    public function update(DesaRequest $request, $token = '')
    {
        $this->authValidate($this->constanta->edited);
        try {
            $update                  = $this->repository->updateData($request, $token);

            if ($update) {
                return jsonSuccess('Data berhasil disimpan', route('desa.index'));
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
            return redirect()->route('desa.index')->with('success', $delete_status['message']);
        }

        return redirect()->route('desa.index')->with('error', $delete_status['message']);
    }

    public function option(Request $request)
    {
        $datas = $this->repository->getOptionData($request);
        $list     = [];
        foreach ($datas as $key => $row) {
            $list[$key]['id']   = $row->id;
            $list[$key]['text'] = $row->desa_name;
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

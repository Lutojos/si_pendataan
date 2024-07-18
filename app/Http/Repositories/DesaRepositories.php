<?php

namespace App\Http\Repositories;

use App\Models\Desa;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class DesaRepositories
{

    public function getListData($request)
    {
        $filter    = $request['filter'];
        $rekomendasi = $request['rekomendasi'];
        $search      = $request->search ?? false;

        $searchVal = (isset($filter['search'])) ? $filter['search'] : $search;

        $data = Desa::select(
            'desa.id',
            'desa.desa_name',
            'kecamatan.kecamatan_name',
            'kota.kota_name',
            'provinsi.provinsi_name',
            DB::raw("md5(concat(desa.id,'-',date_format(curdate(), '%Y%m%d'))) as _token"),
        )
            ->join('kecamatan', 'desa.kecamatan_id', '=', 'kecamatan.id')
            ->join('kota', 'kecamatan.kota_id', '=', 'kota.id')
            ->join('provinsi', 'kota.provinsi_id', '=', 'provinsi.id');

        $data = $data->when($searchVal, function ($query) use ($searchVal) {
            $query->where('provinsi.provinsi_name', 'like', "%{$searchVal}%")
                ->orWhere('kota.kota_name', 'like', "%{$searchVal}%")
                ->orWhere('kecamatan.kecamatan_name', 'like', "%{$searchVal}%")
                ->orWhere('desa.desa_name', 'like', "%{$searchVal}%");
        });

        if ($rekomendasi) {
            $data->whereNull('desa.deleted_at');
        } else {
            if (isset($request['order'])) {
                foreach ($request["order"] as $i => $order) {
                    $data->orderBy($order["column_name"], $order["dir"]);
                }
            }
        }

        return $data;
    }

    public function getDataByToken($token)
    {
        $datas = Desa::select(
            'desa.*',
            DB::Raw("md5(concat(desa.id, '-', date_format(curdate(), '%Y%m%d'))) as _token"),
        )
            ->token($token)->first();

        $return = [
            'datas'  => $datas
        ];

        return $return;
    }

    public function storeData($request)
    {
        DB::beginTransaction();
        try {
            $store = Desa::insertGetId(
                [
                    'provinsi_id'         => $request->provinsi_id,
                    'kota_id'         => $request->kota_id,
                    'kecamatan_id'         => $request->kecamatan_id,
                    'desa_name'         => $request->desa_name,
                    'created_by'   => \Auth::user()->id,
                    'created_at'   => Carbon::now(),
                ],
            );
            DB::commit();

            return $store;
        } catch (Throwable $t) {
            DB::rollBack();

            throw new \Exception($t->getMessage());
        }
    }

    public function updateData($request, $token)
    {
        DB::beginTransaction();
        try {
            $getData = Desa::token($token);
            $update      = $getData->update(
                [
                    'provinsi_id'         => $request->provinsi_id,
                    'kota_id'         => $request->kota_id,
                    'kecamatan_id'         => $request->kecamatan_id,
                    'desa_name'         => $request->desa_name,
                    'updated_by'   => \Auth::user()->id,
                ],
            );

            DB::commit();

            return $update;
        } catch (Throwable $t) {
            DB::rollBack();

            throw new \Exception($t->getMessage());
        }
    }

    public function deleteData($token)
    {
        DB::beginTransaction();
        try {
            $dataToken = Desa::token($token);
            $data      = $dataToken->first();
            if (!$data) {
                $delete = [
                    'status'  => false,
                    'message' => __('Data tidak ditemukan'),
                ];
            } else {
                $data->delete();
                $delete['status']  = true;
                $delete['message'] = __('Berhasil menghapus data');
            }
            DB::commit();

            return $delete;
        } catch (Throwable $t) {
            DB::rollBack();
            $delete = [
                'status'  => false,
                'message' => __('Data tidak ditemukan'),
            ];

            return $delete;
        }
    }

    public function getOptionData($request)
    {
        $search = $request->search ?? null;
        $kecamatan_id   = $request->kecamatan_id;
        $id     = $request->id;
        $datas  = Desa::select('id', 'desa_name')
            ->when($kecamatan_id, function ($query) use ($kecamatan_id) {
                return $query->where('kecamatan_id', $kecamatan_id);
            })
            ->whereNull('deleted_at');
        if ($search != null) {
            $datas = $datas->where('desa_name', 'like', "%{$search}%");
        }
        if ($id != null) {
            $datas = $datas->where('id', '=', "{$id}");
        }
        $datas = $datas->orderBy('desa_name', 'asc');
        $datas = $datas->get();

        return $datas;
    }
}

<?php

namespace App\Http\Repositories;

use App\Models\Kecamatan;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class KecamatanRepositories
{

    public function getListData($request)
    {
        $filter    = $request['filter'];
        $rekomendasi = $request['rekomendasi'];
        $search      = $request->search ?? false;

        $searchVal = (isset($filter['search'])) ? $filter['search'] : $search;

        $data = Kecamatan::select(
            'kecamatan.id',
            'kecamatan.kecamatan_name',
            'kota.kota_name',
            'provinsi.provinsi_name',
            DB::raw("md5(concat(kecamatan.id,'-',date_format(curdate(), '%Y%m%d'))) as _token"),
        )
        ->join('kota', 'kecamatan.kota_id', '=', 'kota.id')
        ->join('provinsi', 'kota.provinsi_id', '=', 'provinsi.id');

        $data = $data->when($searchVal, function ($query) use ($searchVal) {
            $query->where('provinsi.provinsi_name', 'like', "%{$searchVal}%")
                ->orWhere('kota.kota_name', 'like', "%{$searchVal}%")
                ->orWhere('kecamatan.kecamatan_name', 'like', "%{$searchVal}%");
        });

        if ($rekomendasi) {
            $data->whereNull('kecamatan.deleted_at');
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
        $datas = Kecamatan::select(
            'kecamatan.*',
            DB::Raw("md5(concat(kecamatan.id, '-', date_format(curdate(), '%Y%m%d'))) as _token"),
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
            $store = Kecamatan::insertGetId(
                [
                    'provinsi_id'         => $request->provinsi_id,
                    'kota_id'         => $request->kota_id,
                    'kecamatan_name'         => $request->kecamatan_name,
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
            $getData = Kecamatan::token($token);
            $update      = $getData->update(
                [
                    'provinsi_id'         => $request->provinsi_id,
                    'kota_id'         => $request->kota_id,
                    'kecamatan_name'         => $request->kecamatan_name,
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
            $dataToken = Kecamatan::token($token);
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
        $kota_id   = $request->kota_id;
        $id     = $request->id;
        $datas  = Kecamatan::select('id', 'kecamatan_name')
            ->when($kota_id, function ($query) use ($kota_id) {
                return $query->where('kota_id', $kota_id);
            })
            ->whereNull('deleted_at');
        if ($search != null) {
            $datas = $datas->where('kecamatan_name', 'like', "%{$search}%");
        }
        if ($id != null) {
            $datas = $datas->where('id', '=', "{$id}");
        }
        $datas = $datas->orderBy('kecamatan_name', 'asc');
        $datas = $datas->get();

        return $datas;
    }
}

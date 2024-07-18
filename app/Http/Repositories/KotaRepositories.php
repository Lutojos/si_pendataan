<?php

namespace App\Http\Repositories;

use App\Models\Kota;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class KotaRepositories
{

    public function getListData($request)
    {
        $filter    = $request['filter'];
        $rekomendasi = $request['rekomendasi'];
        $search      = $request->search ?? false;

        $searchVal = (isset($filter['search'])) ? $filter['search'] : $search;

        $data = Kota::select(
            'kota.id',
            'kota.kota_name',
            'provinsi.provinsi_name',
            DB::raw("md5(concat(kota.id,'-',date_format(curdate(), '%Y%m%d'))) as _token"),
        )->leftjoin('provinsi', 'kota.provinsi_id', '=', 'provinsi.id');

        $data = $data->when($searchVal, function ($query) use ($searchVal) {
            $query->where('provinsi.provinsi_name', 'like', "%{$searchVal}%")
                ->orWhere('kota.kota_name', 'like', "%{$searchVal}%");
        });

        if ($rekomendasi) {
            $data->whereNull('kota.deleted_at');
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
        $datas = Kota::select(
            'kota.*',
            DB::Raw("md5(concat(kota.id, '-', date_format(curdate(), '%Y%m%d'))) as _token"),
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
            $store = Kota::insertGetId(
                [
                    'provinsi_id'         => $request->provinsi_id,
                    'kota_name'         => $request->kota_name,
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
            $getData = Kota::token($token);
            $update      = $getData->update(
                [
                    'provinsi_id'  => $request->provinsi_id,
                    'kota_name'    => $request->kota_name,
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
            $dataToken = Kota::token($token);
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
        $search = $request->search;
        $provinsi_id   = $request->provinsi_id;
        $id     = $request->id;
        $datas  = Kota::select('id', 'kota_name')
            ->when($provinsi_id, function ($query) use ($provinsi_id) {
                return $query->where('provinsi_id', $provinsi_id);
            })
            ->whereNull('deleted_at');
        if ($search != null) {
            $datas = $datas->where('kota_name', 'like', "%{$search}%");
        }
        if ($id != null) {
            $datas = $datas->where('id', '=', "{$id}");
        }
        $datas = $datas->orderBy('kota_name', 'asc');
        $datas = $datas->get();

        return $datas;
    }
}

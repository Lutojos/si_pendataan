<?php

namespace App\Http\Repositories;

use App\Models\Provinsi;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProvinsiRepositories
{

    public function getListData($request)
    {
        $filter    = $request['filter'];
        $rekomendasi = $request['rekomendasi'];
        $search      = $request->search ?? false;

        $searchVal = (isset($filter['search'])) ? $filter['search'] : $search;

        $data = Provinsi::select(
            'provinsi.id',
            'provinsi.provinsi_name',
            DB::raw("md5(concat(provinsi.id,'-',date_format(curdate(), '%Y%m%d'))) as _token"),
        );

        $data = $data->when($searchVal, function ($query) use ($searchVal) {
            $query->where('provinsi.provinsi_name', 'like', "%{$searchVal}%");
        })
            ->groupBy('provinsi.id');

        if ($rekomendasi) {
            $data->whereNull('provinsi.deleted_at');
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
        $datas = Provinsi::select(
            'provinsi.*',
            DB::Raw("md5(concat(provinsi.id, '-', date_format(curdate(), '%Y%m%d'))) as _token"),
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
            $store = Provinsi::insertGetId(
                [
                    'provinsi_name'         => $request->provinsi_name,
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
            $getData = Provinsi::token($token);
            $update      = $getData->update(
                [
                    'provinsi_name'         => $request->provinsi_name,
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
            $dataToken = Provinsi::token($token);
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

    public function getOptionData($search = null, $id = null)
    {
        $datas = Provinsi::select('id', 'provinsi_name')
            ->whereNull('deleted_at');
        if ($search != null) {
            $datas = $datas->where('provinsi_name', 'like', "%{$search}%");
        }
        if ($id != null) {
            $datas = $datas->where('id', '=', "{$id}");
        }
        $datas = $datas->orderBy('provinsi_name', 'asc');
        $datas = $datas->get();

        return $datas;
    }
}

<?php

namespace App\Http\Repositories;

use App\Models\Anggota;
use App\Models\AnggotaImage;
use App\Library\Upload;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AnggotaRepositories
{
    public $path = 'anggota_images';

    public function getListData($request)
    {
        $filter    = $request['filter'];
        $rekomendasi = $request['rekomendasi'];
        $search      = $request->search ?? false;

        $searchVal = (isset($filter['search'])) ? $filter['search'] : $search;

        $data = Anggota::select(
            'anggota.id',
            'anggota.name',
            'anggota.umur',
            'anggota.gender',
            'anggota.phone_number',
            'desa.desa_name',
            'kecamatan.kecamatan_name',
            'kota.kota_name',
            'provinsi.provinsi_name',
            DB::raw("md5(concat(anggota.id,'-',date_format(curdate(), '%Y%m%d'))) as _token"),
        )
            ->join('desa', 'anggota.desa_id', '=', 'desa.id')
            ->join('kecamatan', 'anggota.kecamatan_id', '=', 'kecamatan.id')
            ->join('kota', 'anggota.kota_id', '=', 'kota.id')
            ->join('provinsi', 'anggota.provinsi_id', '=', 'provinsi.id');

        $data = $data->when($searchVal, function ($query) use ($searchVal) {
            $query->where('provinsi.provinsi_name', 'like', "%{$searchVal}%")
                ->orWhere('kota.kota_name', 'like', "%{$searchVal}%")
                ->orWhere('kecamatan.kecamatan_name', 'like', "%{$searchVal}%")
                ->orWhere('anggota.name', 'like', "%{$searchVal}%")
                ->orWhere('desa.desa_name', 'like', "%{$searchVal}%");
        });

        if ($rekomendasi) {
            $data->whereNull('anggota.deleted_at');
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
        $datas = Anggota::select(
            'anggota.*',
            'desa.desa_name',
            'kecamatan.kecamatan_name',
            'kota.kota_name',
            'provinsi.provinsi_name',
            DB::Raw("md5(concat(anggota.id, '-', date_format(curdate(), '%Y%m%d'))) as _token"),
        )->join('desa', 'anggota.desa_id', '=', 'desa.id')
            ->join('kecamatan', 'anggota.kecamatan_id', '=', 'kecamatan.id')
            ->join('kota', 'anggota.kota_id', '=', 'kota.id')
            ->join('provinsi', 'anggota.provinsi_id', '=', 'provinsi.id')
            ->token($token)->first();

        $images        = $datas->images()->get();
        $return_images = [];
        if (count($images) > 0) {
            foreach ($images as $key => $value) {
                $value['image_path']   = Storage::url($value->image_path);
                $value['image_id']     = $value->id;
                $value['is_thumbnail'] = $value->is_thumbnail;
                array_push($return_images, $value);
            }
        }

        $return = [
            'datas'  => $datas,
            'images' => $return_images,
        ];

        return $return;
    }

    public function storeData($request)
    {
        DB::beginTransaction();
        try {
            $anggota_id = Anggota::insertGetId(
                [
                    'name' => $request->name,
                    'umur' => $request->umur,
                    'gender' => $request->gender,
                    'address' => $request->address,
                    'provinsi_id'    => !empty($request->provinsi_id) ? $request->provinsi_id : null,
                    'kota_id'    => !empty($request->kota_id) ? $request->kota_id : null,
                    'kecamatan_id'    => !empty($request->kecamatan_id) ? $request->kecamatan_id : null,
                    'desa_id'    => !empty($request->desa_id) ? $request->desa_id : null,
                    'phone_number'   => $request->phone_number,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'created_by'  => \Auth::user()->id,
                    'created_at'  => Carbon::now(),
                ],
            );

            if ($anggota_id) {
                $upload   = new Upload();
                //profil
                if (file_exists($request->file('image_path'))) {
                    $diskProfil = $this->path . '/' . $upload->upload($request->file('image_path'), $this->path);
                    if ($diskProfil) {
                        Anggota::where('id', $anggota_id)
                            ->update(['image_path' => $diskProfil]);
                    }
                }

                $jmlFile = count($request->file('files'));
                for ($i = 0; $i < $jmlFile; $i++) {
                    try {
                        //gallery
                        if (file_exists($request->file('files')[$i])) {
                            $disk = $this->path . '/' . $upload->upload($request->file('files')[$i], $this->path);
                            if ($disk) {
                                AnggotaImage::create(
                                    [
                                        'anggota_id'      => $anggota_id,
                                        'image_path'   => $disk,
                                        'created_by'   => \Auth::user()->id,
                                    ],
                                );
                            }
                        }
                    } catch (\Throwable $th) {
                        DB::rollBack();

                        throw new \Exception($th->getMessage());
                    }
                }
            }
            DB::commit();

            return $anggota_id;
        } catch (Throwable $t) {
            DB::rollBack();

            throw new \Exception($t->getMessage());
        }
    }

    public function updateData($request, $token)
    {
        DB::beginTransaction();
        try {
            $getData = Anggota::token($token);
            $update      = $getData->update(
                [
                    'name' => $request->name,
                    'umur' => $request->umur,
                    'gender' => $request->gender,
                    'address' => $request->address,
                    'provinsi_id'    => !empty($request->provinsi_id) ? $request->provinsi_id : null,
                    'kota_id'    => !empty($request->kota_id) ? $request->kota_id : null,
                    'kecamatan_id'    => !empty($request->kecamatan_id) ? $request->kecamatan_id : null,
                    'desa_id'    => !empty($request->desa_id) ? $request->desa_id : null,
                    'phone_number'   => $request->phone_number,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'updated_by'   => \Auth::user()->id,
                ],
            );

            $datas = $getData->first();
            if (!empty($request->delete_images[0])) {
                $delete_images = explode("," , $request->delete_images[0]);
                foreach ($delete_images as $key => $value) {
                    $getDataImages = AnggotaImage::where('id', $value)->first();
                    if ($getDataImages) {
                        Storage::delete($getDataImages->image_path);
                        AnggotaImage::where('id', $value)->delete();
                    }
                }
            }

            $upload   = new Upload();
            //profil
            if (file_exists($request->file('image_path'))) {
                $diskProfil = $this->path . '/' . $upload->upload($request->file('image_path'), $this->path);
                if ($diskProfil) {
                    Anggota::where('id', $datas->id)
                        ->update(['image_path' => $diskProfil]);
                }
            }
            if(!empty($request->file('files'))){
                $jmlFile = count($request->file('files'));
                for ($i = 0; $i < $jmlFile; $i++) {
                    try {
                        //gallery
                        if (file_exists($request->file('files')[$i])) {
                            $disk = $this->path . '/' . $upload->upload($request->file('files')[$i], $this->path);
                            if ($disk) {
                                AnggotaImage::create(
                                    [
                                        'anggota_id'   => $datas->id,
                                        'image_path'   => $disk,
                                        'created_by'   => \Auth::user()->id,
                                    ],
                                );
                            }
                        }
                    } catch (\Throwable $th) {
                        DB::rollBack();
    
                        throw new \Exception($th->getMessage());
                    }
                }
            }

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

    public function getOptionData($search = null, $id = null)
    {
        $datas = Desa::select('id', 'desa_name')
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

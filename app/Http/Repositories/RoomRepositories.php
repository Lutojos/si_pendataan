<?php

namespace App\Http\Repositories;

use App\Library\Upload;
use App\Models\Room;
use App\Models\RoomImages;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class RoomRepositories
{
    public $path = 'room_images';

    public function getRoom($request)
    {
        $filter      = $request['filter'];
        $rekomendasi = $request['rekomendasi'];
        $search      = $request->search ?? false;

        $searchVal = (isset($filter['search'])) ? $filter['search'] : $search;

        $data = Room::select(
            'properties.name as property_name',
            'properties.address as property_address',
            'rooms.room_name',
            'rooms.description',
            'rooms.id',
            'rooms.price',
            'promo.promo_name',
            'promo.promo_type',
            'promo.discount_amount',
            'promo.discount_percentage',
            'room_images.image_path',
            'room_images.is_thumbnail',
            'users.name as nama_penyewa',
            DB::raw("md5(concat(rooms.id,'-',date_format(curdate(), '%Y%m%d'))) as _token"),
            DB::Raw("case when promo.promo_type = '0' then rooms.price - promo.discount_amount when promo.promo_type = '1' then rooms.price - ((rooms.price * promo.discount_percentage) / 100) else 0 end as promo_price"),
        )
            ->leftjoin('properties', 'rooms.property_id', '=', 'properties.id')
            ->leftJoin('promo', function ($join) {
                $join->on('rooms.promo_id', '=', 'promo.id')
                    ->where(DB::raw('DATE(start_date)'), '<=', date('Y-m-d'))
                    ->where(DB::raw('DATE(end_date)'), '>=', date('Y-m-d'));
            })
            ->leftJoin('orders', function ($join) {
                $join->on('rooms.order_id', '=', 'orders.id')
                    ->where('rooms.is_booked', '=', '1')
                    ->whereIn('orders.status_order', ['1', '2'])
                    ->where('orders.checkout_date', '>=', Carbon::now()->toDateString());
            })
            ->leftjoin('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('room_images', function ($join) {
                $join->on('rooms.id', '=', 'room_images.room_id')
                    ->where('room_images.is_thumbnail', '1')
                    ->whereNull('room_images.deleted_at');
            })->property();

        if ($rekomendasi) {
            $data->whereNull('users.name');
            $data->inRandomOrder();
            $data->whereNotNull('promo.promo_name');
        } else {
            if (isset($request['order'])) {
                foreach ($request["order"] as $i => $order) {
                    $data->orderBy($order["column_name"], $order["dir"]);
                }
            }
        }

        $data = $data->when($searchVal, function ($query) use ($searchVal) {
            $query->where(function ($subquery) use ($searchVal) {
                $subquery->where('properties.name', 'like', "%{$searchVal}%")
                    ->orWhere('rooms.room_name', 'like', "%{$searchVal}%")
                    ->orWhere('users.name', 'like', "%{$searchVal}%");
            });
        })->groupBy('rooms.id');

        return $data;
    }

    public function getDataByToken($token)
    {
        $datas = Room::select(
            'rooms.*',
            'properties.name as property_name',
            'properties.address as property_address',
            'promo.promo_name',
            'promo.promo_name',
            'promo.promo_type',
            'promo.discount_amount',
            'promo.discount_percentage',
            'users.name as nama_penyewa',
            'promo.start_date as periode_promo_start',
            'promo.end_date as periode_promo_end',
            DB::Raw("md5(concat(rooms.id, '-', date_format(curdate(), '%Y%m%d'))) as _token"),
        )
            ->leftjoin('properties', 'rooms.property_id', '=', 'properties.id')
            ->leftJoin('orders', function ($join) {
                $join->on('rooms.order_id', '=', 'orders.id')
                    ->where('rooms.is_booked', '=', '1')
                    ->whereIn('orders.status_order', ['1', '2'])
                    ->where('orders.checkout_date', '>=', Carbon::now()->toDateString());
            })
            ->leftjoin('users', 'orders.user_id', '=', 'users.id')
            ->leftJoin('promo', function ($join) {
                $join->on('rooms.promo_id', '=', 'promo.id')
                    ->where(DB::raw('DATE(start_date)'), '<=', date('Y-m-d'))
                    ->where(DB::raw('DATE(end_date)'), '>=', date('Y-m-d'));
            })
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

    public function storeRoom($request)
    {
        DB::beginTransaction();
        try {
            if (perbandinganPromoWithPrice(preg_replace("/[^0-9]/", "", $request->price), $request->promo_id) == false) {
                DB::rollBack();

                return throw new Exception('simpan data gagal, karena harga promo lebih besar dari pada harga kamar.');
            }

            $room_id = Room::insertGetId(
                [
                    'property_id' => $request->property_id,
                    'promo_id'    => !empty($request->promo_id) ? $request->promo_id : null,
                    'room_name'   => $request->room_name,
                    'price'       => preg_replace("/[^0-9]/", "", $request->price),
                    'description' => $request->description,
                    'created_by'  => \Auth::user()->id,
                    'created_at'  => Carbon::now(),
                ],
            );

            if ($room_id) {
                $jmlFile = count($request->file());
                for ($i = 0; $i < $jmlFile - 1; $i++) {
                    try {
                        $namaFile = 'unit_images' . $i;
                        $upload   = new Upload();
                        if (file_exists($request->file($namaFile))) {
                            $disk = $this->path . '/' . $upload->upload($request->file($namaFile), $this->path);
                            if ($disk) {
                                RoomImages::create(
                                    [
                                        'room_id'      => $room_id,
                                        'image_path'   => $disk,
                                        'is_thumbnail' => $request->radio == $i ? '1' : '0',
                                        'created_by'   => \Auth::user()->id,
                                    ],
                                );
                            }
                        }
                    } catch (\Throwable $th) {
                        DB::rollBack();

                        return throw new Exception($th->getMessage());
                    }
                }
            }
            DB::commit();

            return $room_id;
        } catch (Throwable $t) {
            DB::rollBack();

            return throw new Exception($t->getMessage());
        }
    }

    public function updateProperty($request, $token)
    {
        DB::beginTransaction();
        try {
            if (perbandinganPromoWithPrice(preg_replace("/[^0-9]/", "", $request->price), $request->promo_id) == false) {
                DB::rollBack();

                return throw new Exception('simpan data gagal, karena harga promo lebih besar dari pada harga kamar.');
            }
            $getRoom = Room::token($token);
            $update  = $getRoom->update(
                [
                    'property_id' => $request->property_id,
                    'promo_id'    => !empty($request->promo_id) ? $request->promo_id : null,
                    'room_name'   => $request->room_name,
                    'price'       => preg_replace("/[^0-9]/", "", $request->price),
                    'description' => $request->description,
                    'updated_by'  => \Auth::user()->id,
                ],
            );

            $datas = $getRoom->first();

            if (isset($request->delete_images)) {
                foreach ($request->delete_images as $key => $value) {
                    $getDataImages = RoomImages::where('id', $value)->first();
                    if ($getDataImages) {
                        Storage::delete($getDataImages->image_path);
                        RoomImages::where('id', $value)->delete();
                    }
                }
            }

            $jmlFile = count($request->file());
            if ($jmlFile > 0) {
                for ($i = 0; $i < $jmlFile - 1; $i++) {
                    try {
                        $namaFile = 'unit_images' . $i;
                        if (file_exists($request->file($namaFile))) {
                            $upload = new Upload();
                            $disk   = $this->path . '/' . $upload->upload($request->file($namaFile), $this->path);
                            if ($disk) {
                                if ($request->radio == $i) {
                                    RoomImages::where('room_id', $datas->id)->update(['is_thumbnail' => '0']);
                                }
                                RoomImages::create(
                                    [
                                        'room_id'      => $datas->id,
                                        'is_thumbnail' => $request->radio == $i ? '1' : '0',
                                        'image_path'   => $disk,
                                        'updated_by'   => \Auth::user()->id,
                                    ],
                                );
                            }
                        }
                    } catch (\Throwable $th) {
                        DB::rollBack();

                        return throw new Exception($th->getMessage());
                    }
                }
            }

            $is_thumbnail = explode("old-", $request->radio);
            if (count($is_thumbnail) > 1) {
                //jika -old maka data lama dan edit by id
                RoomImages::where('room_id', $datas->id)->update(['is_thumbnail' => '0']);
                RoomImages::where('id', $is_thumbnail[1])->update(['is_thumbnail' => '1']);
            }

            DB::commit();

            return $update;
        } catch (Throwable $t) {
            DB::rollBack();

            return throw new Exception($t->getMessage());
        }
    }

    public function deleteRoom($token)
    {
        DB::beginTransaction();
        try {
            $dataToken = Room::token($token);
            $data      = $dataToken->first();
            if (!$data) {
                $delete = [
                    'status'  => false,
                    'message' => __('Data tidak ditemukan'),
                ];
            } else {
                $getDataImages = $data->images()->get();
                if (count($getDataImages) > 0) {
                    foreach ($getDataImages as $key => $value) {
                        Storage::delete($value->image_path);
                        RoomImages::where('id', $value->id)->delete();
                    }
                }

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

    public function getOptionRoom($search = null, $id = null)
    {
        $cur_date = Carbon::now()->toDateString();
        $datas    = Room::select('rooms.id', 'rooms.room_name')
            ->leftJoin('orders', 'rooms.order_id', '=', 'orders.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->when(
                auth()->user()->hasRole('Admin Property'),
                function ($query) {
                    return $query->where('rooms.property_id', auth()->user()->property_id);
                },
            )
            ->where('orders.is_terminated', '=', DB::Raw("'0'"))
            ->whereIn('orders.status_order', ['1', '2'])
            ->where('orders.checkout_date', '>=', $cur_date)
            ->whereNull('rooms.deleted_at');

        if ($search != null) {
            $datas = $datas->where('rooms.room_name', 'like', "%{$search}%");
        }
        if ($id != null) {
            $datas = $datas->where('rooms.id', '=', "{$id}");
        }
        $datas = $datas->orderBy('rooms.room_name', 'asc');

        return $datas->get();
    }
}

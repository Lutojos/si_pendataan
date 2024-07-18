<?php

namespace App\Http\Repositories;

use App\Library\Upload;
use App\Models\HelpCenter;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

/**
 * UserRepositories.
 */
class KaryawanRepositories extends NotificationRepositories
{
    /**
     * __construct.
     */
    public $path = 'help_center_images';

    public function __construct()
    {
    }

    /**
     * getTask.
     *
     * @param mixed $request
     * @return array
     */
    public function getTask($request)
    {
        try {
            $HelpCenter = new HelpCenter();
            $filter     = $request->filter; //date string format dd/mm/yyyy - dd/mm/yyyy
            $date_range = $filter;
            //if date range null
            if (!$date_range) {
                $date_range = date('d/m/Y') . ' - ' . date('d/m/Y');
            }
            // Memisahkan tanggal awal dan akhir
            list($start_date, $end_date) = explode(" - ", $date_range);
            $user                        = auth()->user()->id;
            // Mengubah format tanggal awal dan akhir
            $start_date = date('Y-m-d', strtotime(str_replace('/', '-', $start_date)));
            $end_date   = date('Y-m-d', strtotime(str_replace('/', '-', $end_date)));

            $HelpCenter = $HelpCenter->whereRaw(DB::raw("DATE(task_date) BETWEEN '$start_date' AND '$end_date'"));

            $status     = [0 => 'Pending', 1 => 'Todo', 2 => 'Batal', 3 => 'Selesai'];
            $HelpCenter = $HelpCenter->select(DB::raw('count(id) as total'), 'task_status')
                ->where('assign_to', $user)
                ->groupBy('task_status')
                // ->withTrashed()
                ->get();
            $data = [];
            //loop status
            foreach ($status as $key => $value) {
                $data[$key]['id']    = $key;
                $data[$key]['name']  = $value;
                $data[$key]['total'] = 0;
                foreach ($HelpCenter as $key2 => $value2) {
                    if ($value2->task_status == $key) {
                        //if trashed status = Batal
                        if ($value2->trashed()) {
                            $data[$key]['name'] = 'Batal';
                        }

                        $data[$key]['total'] = $value2->total;
                    }
                }
            }

            return $data;
        } catch (\Throwable $th) {
            //throw $th;
            return _400($th->getMessage());
        }
    }

    /**
     * getJadwal.
     *
     * @param mixed $request
     * @return array
     */
    public function getJadwal($request)
    {
        $jadwal      = new HelpCenter();
        $status      = [1, 2];
        $status_name = [
            0 => 'Pending',
            1 => 'Todo',
            2 => 'Batal',
            3 => 'Selesai',
        ];
        $user       = auth()->user()->id;
        $filter     = $request->filter; //date string format dd/mm/yyyy - dd/mm/yyyy
        $date_range = $filter;
        if (!$date_range) {
            $date_range = date('d/m/Y') . ' - ' . date('d/m/Y');
        }
        // Memisahkan tanggal awal dan akhir
        list($start_date, $end_date) = explode(" - ", $date_range);

        // Mengubah format tanggal awal dan akhir
        $start_date = date('Y-m-d', strtotime(str_replace('/', '-', $start_date)));
        $end_date   = date('Y-m-d', strtotime(str_replace('/', '-', $end_date)));

        $jadwal = $jadwal->whereRaw(DB::raw("DATE(task_date) BETWEEN '$start_date' AND '$end_date'"))
            ->where('assign_to', $user)
            ->whereIn('task_status', ['0', '1']) //pending dan todo
            ->orderBy('task_date', 'desc');

        $data = [];
        foreach ($jadwal->get() as $key => $value) {
            $data[$key]['_token']           = md5($value->id . '-' . date('Ymd'));
            $data[$key]['task_id']          = $value->task_id;
            $data[$key]['task_name']        = $value->tasks->task_name;
            $data[$key]['task_date']        = $value->task_date;
            $data[$key]['notes']            = $value->notes;
            $data[$key]['req_id']           = $value->requested_by;
            $data[$key]['req_name']         = $value->requester->name ?? "-";
            $data[$key]['assign_id']        = $value->assign_to;
            $data[$key]['assign_name']      = $value->assignee->name ?? "-";
            $data[$key]['room_id']          = $value->room_id;
            $data[$key]['room_name']        = $value->room->room_name ?? '-';
            $data[$key]['task_status_id']   = $value->task_status;
            $data[$key]['task_status_name'] = $status_name[$value->task_status];
            $data[$key]['request_date']     = $value->created_at;
            $data[$key]['updated_at']       = $value->updated_at;
            //created_at,images
            $data[$key]['images'] = [];
            foreach ($value->images as $key2 => $value2) {
                $data[$key]['images'][$key2]['id'] = $value2->id;

                $data[$key]['images'][$key2]['url'] = $value2->getImage();
            }

            // $image = $value->images()->get();

            // foreach ($image as $k => $val) {
            //     $data['image'][$key]['id']  = $val->id;
            //     $data['image'][$key]['url'] = $val->getImage();
            // }
        }

        return array_values($data);
    }

    //get task by status
    /**
     * getTaskByStatus.
     *
     * @param mixed $request
     * @return array
     */
    public function getTaskByStatus($request)
    {
        $status         = [0 => 'Pending', 1 => 'Todo', 2 => 'Batal', 3 => 'Selesai'];
        $user           = auth()->user()->id;
        $filter         = $request->filter; //date string format dd/mm/yyyy - dd/mm/yyyy
        $date_range     = $filter;
        $status_request = $request->status;
        if (!$date_range) {
            $date_range = date('d/m/Y') . ' - ' . date('d/m/Y');
        }
        // Memisahkan tanggal awal dan akhir
        list($start_date, $end_date) = explode(" - ", $date_range);

        // Mengubah format tanggal awal dan akhir
        $start_date = date('Y-m-d', strtotime(str_replace('/', '-', $start_date)));
        $end_date   = date('Y-m-d', strtotime(str_replace('/', '-', $end_date)));

        $jadwal = new HelpCenter();
        $jadwal = $jadwal->whereRaw(DB::raw("DATE(task_date) BETWEEN '$start_date' AND '$end_date'"));
        $jadwal = $jadwal->where('task_status', $status_request);
        $jadwal = $jadwal->where('assign_to', $user)->orderBy('task_date', 'desc');
        $data   = [];
        //loop
        foreach ($jadwal->get() as $key => $value) {
            $data[$key]['_token']         = md5($value->id . '-' . date('Ymd'));
            $data[$key]['task_id']        = $value->task_id;
            $data[$key]['task_name']      = $value->tasks->task_name;
            $data[$key]['task_date']      = $value->task_date;
            $data[$key]['notes']          = $value->notes;
            $data[$key]['req_id']         = $value->requested_by;
            $data[$key]['req_name']       = $value->requester->name ?? "-";
            $data[$key]['assign_id']      = $value->assign_to;
            $data[$key]['assign_name']    = $value->assignee->name ?? "-";
            $data[$key]['room_id']        = $value->room_id;
            $data[$key]['room_name']      = $value->room->room_name ?? '-';
            $data[$key]['task_status_id'] = $value->task_status;
            $data[$key]['request_date']   = $value->created_at;
            $data[$key]['updated_at']     = $value->updated_at;
            $data[$key]['finish_date']    = $value->finish_date;
            $data[$key]['cancel_date']    = $value->cancel_date;
            //images
            $data[$key]['images'] = [];
            foreach ($value->images as $key2 => $value2) {
                $data[$key]['images'][$key2]['id'] = $value2->id;

                $data[$key]['images'][$key2]['url'] = $value2->getImage();
            }
        }

        return $data;
    }

    /**
     * getDetailTask.
     *
     * @param mixed $token
     * @return object
     */
    public function getDetailTask($token)
    {
        $HelpCenter  = new HelpCenter();
        $HelpCenter  = $HelpCenter->token($token)->first();
        $status_name = [
            0 => 'Pending',
            1 => 'Todo',
            2 => 'Batal',
            3 => 'Selesai',
        ];
        $data = [];
        if ($HelpCenter) {
            $data['id']               = $HelpCenter->id;
            $data['task_id']          = $HelpCenter->task_id;
            $data['category_id']      = $HelpCenter->tasks->category_id;
            $data['category']         = $HelpCenter->tasks->category->category_name;
            $data['task_name']        = $HelpCenter->tasks->task_name;
            $data['task_date']        = $HelpCenter->task_date;
            $data['notes']            = $HelpCenter->notes;
            $data['req_id']           = $HelpCenter->requested_by;
            $data['req_name']         = $HelpCenter->requester->name ?? "-";
            $data['assign_id']        = $HelpCenter->assign_to;
            $data['assign_name']      = $HelpCenter->assignee->name ?? "-";
            $data['assign_date']      = $HelpCenter->assign_date;
            $data['cancel_date']      = $HelpCenter->cancel_date;
            $data['finish_date']      = $HelpCenter->finish_date;
            $data['room_id']          = $HelpCenter->room_id;
            $data['room_name']        = $HelpCenter->room->room_name ?? '-';
            $data['task_status_id']   = $HelpCenter->task_status;
            $data['task_status_name'] = $status_name[$HelpCenter->task_status];
            $data['created_at']       = $HelpCenter->created_at;
            $data['updated_at']       = $HelpCenter->updated_at;
        }
        $inventory = new Inventory();
        $inventory = $inventory->where('item_qty', '>', 0)->where('property_id', auth()->user()->property_id)->get();
        //inventory task inventories
        $data['inventory_use'] = $HelpCenter->inventories()->select([
            'inventory_id',
            DB::raw('sum(qty) as qty'),
        ])
            ->with(['inventory' => function ($query) {
                $query->select(['id', 'item_name']);
            }])

            ->groupBy('inventory_id')->get();
        $data['inventory_list'] = [];
        //hanlde if $inventory is null
        if (count($inventory) > 0) {
            foreach ($inventory as $key) {
                $data['inventory_list'][$key->id]['id']    = $key->id;
                $data['inventory_list'][$key->id]['name']  = $key->item_name;
                $data['inventory_list'][$key->id]['stock'] = $key->item_qty;
                $data['inventory_list'][$key->id]['use']   = 0;
                foreach ($data['inventory_use'] as $key2) {
                    if ($key->id == $key2->inventory_id) {
                        $data['inventory_list'][$key->id]['use'] = $key2->qty;
                    }
                }
            }
            $data['inventory_list'] = array_values($data['inventory_list']);
        }

        $image = $HelpCenter->images()->get();

        foreach ($image as $key => $value) {
            $data['image'][$key]['id']  = $value->id;
            $data['image'][$key]['url'] = $value->getImage();
        }
        //if image empty
        if (!isset($data['image'])) {
            $data['image'] = [];
        }

        return $data;
    }

    public function store($request, $token)
    {
        DB::beginTransaction();
        try {
            $HelpCenter = new HelpCenter();
            $HelpCenter = $HelpCenter->token($token)->first();
            if (!$HelpCenter) {
                return [
                    'status'  => false,
                    'message' => 'Token tidak ditemukan',
                ];
            }

            $inventoryStock = new Inventory();
            $images         = $request->images;
            $upload         = new Upload();

            $HelpCenter->task_status = '3';
            $HelpCenter->finish_date = date('Y-m-d H:i:s');
            $HelpCenter->notes       = $request->notes;
            $HelpCenter->save();
            $inventory = [];
            if ($request->inventory_use) {
                $inv = $inventoryStock->where('item_qty', '>', 0)->where('property_id', auth()->user()->property_id)->count();
                //inventoryStock
                if ($inv == 0) {
                    return [
                        'status'  => false,
                        'message' => 'Stok barang habis, / tidak ada barang yang tersedia',
                    ];
                }
                // $inventory = json_decode("[$request->inventory_use]", true);
                $inventory = $request->inventory_use; //json_decode($request->inventory_use, true);
            }

            //insert inventory
            if (count($inventory) > 0) {
                foreach ($inventory as $key => $value) {
                    $ivData = [
                        'inventory_id' => $value['inventory_id'],
                        'qty'          => $value['qty'],
                    ];
                    $HelpCenter->inventories()->create($ivData);
                    ///update stock
                    $inventoryStock           = $inventoryStock->find($value['inventory_id']);
                    $inventoryStock->item_qty = $inventoryStock->item_qty - $value['qty'];
                    $inventoryStock->save();
                    //add attach
                    $inventoryStock->histories()->create([
                        'inventory_id' => $value['inventory_id'],
                        'noted'        => 'Pemakaian Barang untuk Room ' . $HelpCenter->room->room_name,
                        'qty'          => $value['qty'],
                    ]);
                }
            }
            //if images empty

            //insert image
            if (count($images) > 0) {
                foreach ($images as $key => $value) {
                    //if empty value
                    if (empty($value)) {
                        return [
                            'status'  => false,
                            'message' => 'Gambar tidak boleh kosong, minimal 1 gambar',
                        ];
                    }
                    //max image 2Mb
                    if ($value->getSize() > 2000000) {
                        return [
                            'status'  => false,
                            'message' => 'Ukuran file terlalu besar max 2Mb',
                        ];
                    }
                    $fileName = $upload->upload($value, $this->path);
                    $HelpCenter->images()->create([
                        'image_path' => $this->path . '/' . $fileName,
                    ]);
                }
            };

            $email         = $HelpCenter->requester->email ?? false;
            $emailKaryawan = $HelpCenter->assignee->email ?? false;
            if ($email) {
                $this->finishMaintenance($email);
            }
            if ($emailKaryawan) {
                $this->finishMaintenance($emailKaryawan);
            }
            DB::commit();

            return [
                'status'  => true,
                'message' => 'Success',

            ];
        } catch (\Exception $e) {
            DB::rollback();

            return [
                'status'  => false,
                'message' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ];
        }
    }
}

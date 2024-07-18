<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class KecamatanPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create([
            'name'         => 'list kecamatan',
            'capabilities' => 'Read',
            'module_name'  => 'Kecamatan',
        ]);
        Permission::create([
            'name'         => 'create kecamatan',
            'capabilities' => 'Create',
            'module_name'  => 'Kecamatan',
        ]);
        Permission::create([
            'name'         => 'edit kecamatan',
            'capabilities' => 'Edit',
            'module_name'  => 'Kecamatan',
        ]);
        Permission::create([
            'name'         => 'delete kecamatan',
            'capabilities' => 'Delete',
            'module_name'  => 'Kecamatan',
        ]);
    }
}

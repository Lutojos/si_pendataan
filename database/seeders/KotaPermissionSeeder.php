<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class KotaPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create([
            'name'         => 'list kota',
            'capabilities' => 'Read',
            'module_name'  => 'Kota',
        ]);
        Permission::create([
            'name'         => 'create kota',
            'capabilities' => 'Create',
            'module_name'  => 'Kota',
        ]);
        Permission::create([
            'name'         => 'edit kota',
            'capabilities' => 'Edit',
            'module_name'  => 'Kota',
        ]);
        Permission::create([
            'name'         => 'delete kota',
            'capabilities' => 'Delete',
            'module_name'  => 'Kota',
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class ProvinsiPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create([
            'name'         => 'list provinsi',
            'capabilities' => 'Read',
            'module_name'  => 'Provinsi',
        ]);
        Permission::create([
            'name'         => 'create provinsi',
            'capabilities' => 'Create',
            'module_name'  => 'Provinsi',
        ]);
        Permission::create([
            'name'         => 'edit provinsi',
            'capabilities' => 'Edit',
            'module_name'  => 'Provinsi',
        ]);
        Permission::create([
            'name'         => 'delete provinsi',
            'capabilities' => 'Delete',
            'module_name'  => 'Provinsi',
        ]);
    }
}

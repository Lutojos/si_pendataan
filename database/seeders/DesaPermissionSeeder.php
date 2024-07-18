<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class DesaPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create([
            'name'         => 'list desa',
            'capabilities' => 'Read',
            'module_name'  => 'Desa',
        ]);
        Permission::create([
            'name'         => 'create desa',
            'capabilities' => 'Create',
            'module_name'  => 'Desa',
        ]);
        Permission::create([
            'name'         => 'edit desa',
            'capabilities' => 'Edit',
            'module_name'  => 'Desa',
        ]);
        Permission::create([
            'name'         => 'delete desa',
            'capabilities' => 'Delete',
            'module_name'  => 'Desa',
        ]);
    }
}

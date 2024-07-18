<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create([
            'name'         => 'list role',
            'capabilities' => 'Read',
            'module_name'  => 'Role',
        ]);
        Permission::create([
            'name'         => 'create role',
            'capabilities' => 'Create',
            'module_name'  => 'Role',
        ]);
        Permission::create([
            'name'         => 'edit role',
            'capabilities' => 'Edit',
            'module_name'  => 'Role',
        ]);
        Permission::create([
            'name'         => 'delete role',
            'capabilities' => 'Delete',
            'module_name'  => 'Role',
        ]);
        Permission::create([
            'name'         => 'assign permission',
            'capabilities' => 'Assign Permission',
            'module_name'  => 'Role',
        ]);
    }
}

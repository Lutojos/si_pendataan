<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class AnggotaPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create([
            'name'         => 'list anggota',
            'capabilities' => 'Read',
            'module_name'  => 'Anggota',
        ]);
        Permission::create([
            'name'         => 'create anggota',
            'capabilities' => 'Create',
            'module_name'  => 'Anggota',
        ]);
        Permission::create([
            'name'         => 'edit anggota',
            'capabilities' => 'Edit',
            'module_name'  => 'Anggota',
        ]);
        Permission::create([
            'name'         => 'delete anggota',
            'capabilities' => 'Delete',
            'module_name'  => 'Anggota',
        ]);
    }
}

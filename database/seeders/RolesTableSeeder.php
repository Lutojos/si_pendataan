<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superadmin = Role::create(['name' => 'Superadmin']);
        $superadmin->givePermissionTo(Permission::all());

        $karyawan = Role::create(['name' => 'Karyawan']);
        $karyawan->givePermissionTo([
            'list users',
            'create users',
            'edit users',
        ]);
    }
}

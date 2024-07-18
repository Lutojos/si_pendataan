<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PromoPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create([
            'name'         => 'list promo',
            'capabilities' => 'Read',
            'module_name'  => 'Promo',
        ]);
        Permission::create([
            'name'         => 'create promo',
            'capabilities' => 'Create',
            'module_name'  => 'Promo',
        ]);
        Permission::create([
            'name'         => 'edit promo',
            'capabilities' => 'Edit',
            'module_name'  => 'Promo',
        ]);
        Permission::create([
            'name'         => 'delete promo',
            'capabilities' => 'Delete',
            'module_name'  => 'Promo',
        ]);
    }
}

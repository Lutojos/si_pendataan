<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            //permisssion seeder
            RolePermissionSeeder::class,
            UserPermission::class,
            RolesTableSeeder::class,
            UsersTableSeeder::class,
            ProvinsiPermissionSeeder::class,
            KotaPermissionSeeder::class,
            DesaPermissionSeeder::class,
           
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class UserPermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permisssion = [
            [
                'name'         => 'list users',
                'capabilities' => 'Read',
                'module_name'  => 'Users',
            ], [
                'name'         => 'delete users',
                'capabilities' => 'Delete',
                'module_name'  => 'Users',
            ],
            [
                'name'         => 'create users',
                'capabilities' => 'Create',
                'module_name'  => 'Users',
            ],
            [
                'name'         => 'edit users',
                'capabilities' => 'Edit',
                'module_name'  => 'Users',
            ],

        ];

        foreach ($permisssion as $key => $value) {
            Permission::create($value);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name'     => 'Administrator',
            'email'    => 'adminangrepak@mail.com',
            'password' => Hash::make('Qwer1234!'),
            'role_id'  => 1,
        ]);

        $user->assignRole('Superadmin');

        $karyawan = User::create([
            'name'     => 'Staff',
            'email'    => 'staffangrepak@mail.com',
            'password' => Hash::make('Qwer1234!'),
            'role_id'  => 4,
        ]);

        $karyawan->assignRole('Karyawan');
    }
}

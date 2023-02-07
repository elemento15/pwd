<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (User::count() == 0) {
            User::create([
                'name'  => 'Luis Lomeli',
                'email' => 'luism.lomeli@gmail.com',
                'password' => Hash::make('sysadministrator.'),
            ]);

            Profile::create([
                'email' => 'luism.lomeli@gmail.com',
            ]);
        }
    }
}

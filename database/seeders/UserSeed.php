<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'John Doe',
                'email' => 'johndoe@mail.com',
                'password' => Hash::make('senha@123'),
            ],
            [
                'name' => 'Jane Doe',
                'email' => 'janedoe@mail.com',
                'password' => Hash::make('senha@123'),
            ],

        ]);
    }
}

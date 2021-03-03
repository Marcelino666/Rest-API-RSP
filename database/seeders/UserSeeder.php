<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
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
                'email' => 'admin@rsp.id',
                'password' => bcrypt('Refactory!2021'),
                'photo' => 'admin.img',
                'created_at' => '2021-02-18 00:00:00',
            ],
            [
                'email' => 'laravelproject12345@gmail.com',
                'password' => bcrypt('12345678'),
                'photo' => 'laravel.img',
                'created_at' => '2021-02-18 10:20:45',
            ]
        ]);
    }
}

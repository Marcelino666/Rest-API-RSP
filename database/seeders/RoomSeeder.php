<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Str;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rooms')->insert([
            [
                'room_name' => 'Exclusive',
                'room_capacity' => '10',
                'photo' => 'exclusive.img',
                'created_at' => '2021-02-18',
            ],
            [
                'room_name' => 'Business',
                'room_capacity' => '20',
                'photo' => 'business.img',
                'created_at' => '2021-02-18',
            ],
            [
                'room_name' => 'Economy',
                'room_capacity' => '30',
                'photo' => 'economy.img',
                'created_at' => '2021-02-18',
            ],
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bookings')->insert([
            [
                'user_id' => '1',
                'room_id' => '2',
                'total_person' => '1',
                'booking_time' => '2021-02-18-10:20:45',
                'noted' => 'Okey',
                'check_in_time' => '2021-02-18-10:20:45',
                'check_out_time' => '2021-02-18-11:20:45',
                'created_at' => '2021-02-18-10:20:45',
            ],
            [
                'user_id' => '2',
                'room_id' => '3',
                'total_person' => '1',
                'booking_time' => '2021-02-20-10:20:45',
                'noted' => 'Okey',
                'check_in_time' => '2021-02-20-10:20:45',
                'check_out_time' => '2021-02-20-11:20:45',
                'created_at' => '2021-02-18-20:20:45',
            ]

        ]);
    }
}

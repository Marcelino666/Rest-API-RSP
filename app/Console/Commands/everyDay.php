<?php

namespace App\Console\Commands;

use App\Mail\ReminderMail;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class everyDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminder Booking';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $timezone = date('Y-m-d', time());

        $bookings = DB::table('bookings')->get();

        foreach($bookings as $booking)
        {
            $dateBooking = date('Y-m-d', strtotime($booking->booking_time));

            if($timezone == $dateBooking)
            {
                $user = User::where('id', $booking->user_id)->firstOrFail();

                $temp = Booking::where('id', $booking->id)->firstOrFail();

                Mail::to($user->email)->send(new ReminderMail($temp));
            }
        }
    }
}

<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CheckInMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $booking;
    public $room;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Booking $booking, Room $room, User $user)
    {
        $this->user = $user;
        $this->booking = $booking;
        $this->room = $room;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.checkin');
    }
}

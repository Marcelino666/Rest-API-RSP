<?php

namespace App\Http\Controllers;

use App\Mail\CheckInMail;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Database\QueryException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class CheckInController extends Controller
{
    //middleware
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request -> validate([
            'booking_id' => ['required','numeric'],
            'check_in_time' => ['required'],
        ]);

        try{
            
            $user_id = auth::id();
            
            $user = User::where('id', $user_id)->firstOrFail();
            
            $result = Booking::where('id', $request->booking_id)->firstOrFail();
            
            $dateBooking = date('Y-m-d', strtotime($result->booking_time));
            $dateCheckIn = date('Y-m-d', strtotime($request->check_in_time));
            
            if($result->user_id != $user_id)
            {
                return response()->json([
                    'message' => 'Sorry this is not your booking',
                ]);
            }
            else
            {
                if($result->check_in_time != null)
                {
                    return response()->json([
                        'message' => 'Check in time has been filled',
                    ]);
                }
                else
                {
                    if($dateBooking != $dateCheckIn)
                    {
                        return response()->json([
                            'message' => 'Check in date must be the same as the booking date',
                        ]);
                    } 
                    elseif($request->check_in_time < $result->booking_time)
                    {
                        return response()->json([
                            'message' => 'Check in time cannot be before booking time',
                        ]);
                    }
                    else 
                    {
                        $result->update([
                            'check_in_time' => $request->check_in_time,
                        ]);
                            
                        $room = Room::where('id', $result->room_id)->firstOrFail();
            
                        Mail::to($user->email)->send(new CheckInMail($result, $room, $user));
            
                        $response = [
                            'message' => 'Check in was created successfully',
                            'data' => $result
                        ];
            
                        return response()->json($response, Response::HTTP_CREATED);
                    }
                }
            }
        } catch(QueryException $e){
            return response()->json([
                'message' => "Failed " . $e->errorInfo
            ]);
        }
    }
}

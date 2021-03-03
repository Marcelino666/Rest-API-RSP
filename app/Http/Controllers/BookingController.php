<?php

namespace App\Http\Controllers;

use App\Http\Middleware\Role;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingMail;
use App\Models\User;

class BookingController extends Controller
{
    //middleware
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = Auth::id();
        if($user_id == 1)
        {
            $booking = Booking::get();
            $response = [
                'message' => 'List Booking',
                'data' => $booking
            ];
            
            return response()->json($response, Response::HTTP_OK);
        }
        else
        {
            $booking = Booking::get()->where('user_id','==',$user_id);

            $response = [
                'message' => 'List Booking',
                'data' => $booking
            ];
            
            return response()->json($response, Response::HTTP_OK);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request -> validate([
            'room_id' => ['required','numeric'],
            'total_person' => ['required','numeric'],
            'booking_time' => ['required'],
        ]);

        try{

            $room = Room::where('id', $request->room_id)->firstOrFail();

            if($request->total_person > $room->room_capacity)
            {
                return response()->json([
                    'message' => 'The total number of people should not be more than the room capacity',
                ]);
            }else{
                $user_id = auth::id();

                $user = User::where('id', $user_id)->firstOrFail();

                $booking = Booking::create([
                    'user_id' => $user_id,
                    'room_id' => request('room_id'),
                    'total_person' => request('total_person'),
                    'booking_time' => request('booking_time'),
                    'noted' => request('noted'),
                    ]);
    
                $room->update([
                    'room_capacity' => $room->room_capacity - $request->total_person
                ]); 
                
                Mail::to($user->email)->send(new BookingMail($booking, $room, $user));
    
                $response = [
                    'message' => 'Booking was successfully created',
                    'data' => $booking
                ];
    
                return response()->json($response, Response::HTTP_CREATED);

            }
        } catch(QueryException $e){
            return response()->json([
                'message' => "Failed " . $e->errorInfo
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $user_id = Auth::id();
            $booking = Booking::where('id', $id)->firstOrFail();
            if($booking->user_id == $user_id)
            {
                $response = [
                    'message' => 'List Booking',
                    'data' => $booking
                ];
                
                return response()->json($response, Response::HTTP_OK);
            }
        }catch (\Exception $e) {
            //tidak ada file
            return abort(404);
        }catch(QueryException $e){
            return response()->json([
                'message' => "Failed " . $e->errorInfo
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $user_id = Auth::id();
            $booking = Booking::where('id', $id)->firstOrFail();
            $room = Room::where('id', $request->room_id)->firstOrFail();
            if($booking->user_id == $user_id)
            {
                if($request->room_id != $booking->room_id)
                {
                    if($request->total_person > $room->room_capacity)
                    {
                        return response()->json([
                            'message' => 'The total number of people should not be more than the room capacity',
                        ]);
                    }
                    else
                    {
                        $oldroom = Room::where('id', $booking->room_id)->firstOrFail();
                        $oldroom->update([
                            'room_capacity' => $oldroom->room_capacity + $booking->total_person
                        ]); 

                        $room->update([
                            'room_capacity' => $room->room_capacity - $request->total_person
                        ]); 

                        $booking->update([
                            'room_id' => $request->room_id,
                            'total_person' => $request->total_person,
                            'booking_time' => $request->booking_time,
                            'noted' => $request->noted,
                        ]);

                        $response = [
                            'message' => 'Booking was successfully updated',
                            'data' => $booking
                        ];
            
                        return response()->json($response, Response::HTTP_CREATED);
                    }
                }
                else
                {
                    $tempRoom = $room->room_capacity + $booking->total_person;
                    
                    if($request->total_person > $tempRoom)
                    {
                        return response()->json([
                            'message' => 'The total number of people should not be more than the room capacity',
                        ]);
                    }
                    else{
                        $room->update([
                            'room_capacity' => $tempRoom - $request->total_person
                        ]); 

                        $booking->update([
                            'room_id' => $request->room_id,
                            'total_person' => $request->total_person,
                            'booking_time' => $request->booking_time,
                            'noted' => $request->noted,
                        ]);

                        $response = [
                            'message' => 'Booking was successfully updated',
                            'data' => $booking
                        ];
            
                        return response()->json($response, Response::HTTP_CREATED);
                    }
                }

            }
        }catch(QueryException $e){
            return response()->json([
                'message' => "Failed " . $e->errorInfo
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $user_id = Auth::id();
            $booking = Booking::where('id', $id)->firstOrFail();
            if($booking->user_id == $user_id)
            {
                $room = Room::where('id', $booking->room_id)->firstOrFail();
                
                $room->update([
                    'room_capacity' => $room->room_capacity + $booking->total_person
                ]); 

                $booking->delete();

                $response = [
                    'message' => 'Booking Deleted'
                ];

                return response()->json($response, Response::HTTP_OK);
            }
        } catch(QueryException $e){
            return response()->json([
                'message' => "Failed " . $e->errorInfo
            ]);
        }
    }
}

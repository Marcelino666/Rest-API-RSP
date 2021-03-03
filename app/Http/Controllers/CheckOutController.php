<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckOutController extends Controller
{
    //Middleware
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
            'check_out_time' => ['required'],
        ]);

        try{
            $user_id = Auth::id();
            
            $result = Booking::where('id', $request->booking_id)->firstOrFail();

            if($result->user_id != $user_id)
            {
                return response()->json([
                    'message' => 'Sorry this is not your booking',
                ]);
            }
            else
            {

                if($result->check_in_time == null)
                {
                    return response()->json([
                        'message' => 'Not checked in yet',
                    ]);
                }
                elseif($result->check_out_time != null)
                {
                    return response()->json([
                        'message' => 'Check out already filled',
                    ]);
                }
                else
                {
                    if($request->check_out_time < $result->check_in_time)
                    {
                        return response()->json([
                            'message' => 'Check out time cannot be before check in time',
                        ]);
                    }
                    else
                    {
                        $result->update([
                            'check_out_time' => $request->check_out_time,
                        ]);
            
                        $response = [
                            'message' => 'Check out was created successfully',
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

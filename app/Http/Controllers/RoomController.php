<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    //Middleware
    // public function __construct()
    // {
    //     $this->middleware('auth.role: 1');
    //     $this->middleware('auth:api');
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $room = Room::get()->where('room_capacity', '!=', 0);

        $response = [
            'message' => 'List Rooms',
            'data' => $room
        ];

        return response()->json($response, Response::HTTP_OK);
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
            'room_name' => ['required','unique:rooms,room_name'],
            'room_capacity' => ['required','numeric'],
            'photo' => ['required','image']
        ]);

        try{
            $file = $request->file('photo');
            $path = Storage::disk('dropbox')->put('rooms', $file);

            $room = Room::create([
                'room_name' => request('room_name'),
                'room_capacity' => request('room_capacity'),
                'photo' => $path
            ]);

            $response = [
                'message' => 'The room was successfully created',
                'data' => $room
            ];

            return response()->json($response, Response::HTTP_CREATED);
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
        $result = Room::where('id', $id)->firstOrFail();
        $path = Storage::disk('dropbox')->url($result->photo);
       
        $response = [
            'message' => 'Detail of Room resource',
            'data' => $result,
            'photo-url' => $path
        ];
        return response()->json($response, Response::HTTP_OK);
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
        $result = Room::where('id', $id)->firstOrFail();

        $validator = Validator::make($request->all(), [
            // 'room_name' => ['required'],
            'room_capacity' => ['numeric'],
            'photo' => ['image']
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        try{
            $file = $request->file('photo');
            Storage::disk('dropbox')->delete($result->photo);
            $path = Storage::disk('dropbox')->put('rooms', $file);

            $result->update([
                'room_name' => $request->room_name, 
                'room_capacity' => $request->room_capacity,
                'photo' => $path
            ]);

            $response = [
                'message' => 'Room Updated',
                'data' => $result
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch(QueryException $e){
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
        $result = Room::where('id', $id)->firstOrFail();

        try{
            Storage::disk('dropbox')->delete($result->photo);
            $result->delete();

            $response = [
                'message' => 'Room Deleted'
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch(QueryException $e){
            return response()->json([
                'message' => "Failed " . $e->errorInfo
            ]);
        }
    }
}

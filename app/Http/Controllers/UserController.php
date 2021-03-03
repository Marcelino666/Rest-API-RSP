<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    //Middleware
    public function __construct()
    {
        // $this->middleware('auth.role:1', ['only' => ['blockUser']]);
        $this->middleware('auth:api');
        // $this->dropbox = Storage::disk('dropbox')->getDriver()->getAdapter()->getClient();
    }

    // public function blockUser()
    // {
    //     return 'This is an admin route.';
    // }

    // public function profile()
    // {
    //     return 'This route is for all users.';
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = Auth::id();

        if($user_id==1)
        {
            $user = User::get();
            $response = [
                'message' => 'List Users',
                'data' => $user
            ];
    
            return response()->json($response, Response::HTTP_OK);
        }
        else
        {
            $user = User::get()->where('id','==',$user_id)->first();
            //ambil link url photo
            $path = Storage::disk('dropbox')->url($user->photo);
            //menentukan temporary path local
            $tempPath = tempnam(sys_get_temp_dir(), $path);
            //copy file ke temporary file
            copy($path, $tempPath);
            $response = [
                'message' => 'Detail of User resource',
                'data' => $user,
                'photo-url' => $path,
                'photo-temporary-local-path' => $tempPath
            ];

            return response()->json($response, Response::HTTP_OK);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
        
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
        
    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        try{
            $user_id = Auth::id();
            $result = User::where('id', $user->id)->firstOrFail();

            if($result->id == $user_id)
            {
                //ambil link url photo
                $path = Storage::disk('dropbox')->url($result->photo);
                //menentukan temporary path local
                $tempPath = tempnam(sys_get_temp_dir(), $path);
                //copy file ke temporary file
                copy($path, $tempPath);
                $response = [
                    'message' => 'Detail of User resource',
                    'data' => $result,
                    'photo-url' => $path,
                    'photo-temporary-local-path' => $tempPath
                ];
    
                return response()->json($response, Response::HTTP_OK);
            }

            
            // return response()->file($tempPath);

            //find data

            // //mengambil url
            // $link = $this->dropbox->listSharedLinks($result->photo);
            // //menyimpan hasil explode dari url diatas
            // $raw = explode("?", $link[0]['url']);
            // //menyimpan data raw dari url
            // $path = $raw[0] . '?raw=1';
            // //menentukan temporary path local
            // $tempPath = tempnam(sys_get_temp_dir(), $path);
            // //copy file ke temporary file
            // copy($path, $tempPath);

	        // // //menampilkan berkas
	        // return response()->file($tempPath);




        }catch (\Exception $e) {
            //tidak ada file
            return abort(404);
        }catch(QueryException $e){
            return response()->json([
                'message' => "Failed " . $e->errorInfo
            ]);
        }

        // $path = $result->photo;
        // return '<img src="' .$path. '" alt="">';
        // return Storage::disk('dropbox')->download($path);
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    // public function edit(User $user)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {

        $validator = Validator::make($request->all(), [
            'photo' => ['image']
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try{
            $user_id = Auth::id();
            $result = User::where('id', $user->id)->firstOrFail();
            if($result->id == $user_id)
            {
                $file = $request->file('photo');
            //    $extension = $file->getClientOriginalExtension();
            //    $filename = time() . '.' . $extension;
            //    $file->move('images', $filename);
                Storage::disk('dropbox')->delete($result->photo);

                $path = Storage::disk('dropbox')->put('images', $file);
                $result->update([
                    'email' => $request->email, 
                    'password' => bcrypt($request->password),
                    'photo' => $path
                ]);

                $response = [
                    'message' => 'User Updated',
                    'data' => $result
                ];

                return response()->json($response, Response::HTTP_OK);
            }
        } catch(QueryException $e){
            return response()->json([
                'message' => "Failed " . $e->errorInfo
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        try{
            $user_id = Auth::id();
            $result = User::where('id', $user->id)->firstOrFail();

            if($result->id == $user_id)
            {
                Storage::disk('dropbox')->delete($result->photo);
                $result->delete();
                $response = [
                    'message' => 'User Deleted'
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

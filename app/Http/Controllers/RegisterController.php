<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Spatie\Dropbox\Client;
use Illuminate\Http\File;
use Guzzle\Http\Exception\ClientErrorResponseException;


class RegisterController extends Controller
{

    public function __construct()
    {
        $this->dropbox = Storage::disk('dropbox')->getDriver()->getAdapter()->getClient();
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required','unique:users,email'],
            'password'=> ['required'],
            'photo' => ['required']
        ]);
        
        if($validator->fails())
        {
            return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
            
        try{
                

            // $user = User::create([
            //     'email' => $request->email, 
            //     'password' => bcrypt($request->password),
            //     'photo' => $request->photo
            // ]);

            $file = $request->file('photo');

            // foreach ($files as $file) {
            // $fileExtension = $file->getClientOriginalExtension();
            // $newName = uniqid() . '.' . $fileExtension;

            //store to dropbox
            $path = Storage::disk('dropbox')->put('images', $file);
            // $this->dropbox->createSharedLinkWithSettings($path);

            $user = new User;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            // $user->photo = $request->file('photo')->store('image');
            // $user->photo = $request->file('photo');
            // $user->photo = $request->file('photo');
            $user->photo = $path;
            $user->save();

            

            $response = [
                'message' => 'Thanks, you are registered',
                'data' => $user
            ];

            return response()->json($response, Response::HTTP_CREATED);
                // }
            
            // $path =  Storage::putFile('public', $request->file('photo'));
            // $path = $request->file('photo')->store('');
            // $path = $request->file('photo');

            // 


            

        } catch(QueryException $e){
            return response()->json([
                'message' => "Failed " . $e->errorInfo
            ]);
        } catch (\Exception $e) {
            // return response()->json([ "Message : " . "Failed " . $e->errorInfo ]);
            return response()->json([
                'message' => "Failed " . $e->errorInfo
            ]);
        } 
    }
}

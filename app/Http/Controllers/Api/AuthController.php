<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

   public function registerPro(Request $request)
   {

       try {
           $params = $request->all();
           $email = $params['email'];
           $telephone = $params['telephone'];

           if(!isset($params['role_id']) || empty($params['role_id'])){
               return response(['error' => 'Role id can not be empty'], 400);
           }

           $validEmail = filter_var( $email, FILTER_VALIDATE_EMAIL );
           if(!$validEmail){
               return response(['error' => 'The email is not valid'], 400);
           }

           $user= User::where('email', $email)->first();
           if (!is_null($user)) {
               return response(['error' => 'The email is already exist'], 404);
           }

           $user= User::where('telephone', $telephone)->first();
           if (!is_null($user)) {
               return response(['error' => 'The telephone is already exist'], 404);
           }

           if(!$request->has('avatar')){
                return response('please upload the photo',400);
           }

           $validatedData = $request->validate([
               'role_id'=>'required',
               'firstName' => 'required|max:55',
               'lastName' => 'required|max:55',
               'telephone' => 'required|max:55|unique:users',
               'email' => 'email|required|unique:users',
               'password' => 'required|confirmed',
               'avatar'=>'image|mimes:jpg,jpeg,bmp,svg,png|max:5000'
           ]);


           $avatarUploaded = $request->file('avatar');
           $avatarName = $validatedData['email'] . '.' .time() . $avatarUploaded->getClientOriginalExtension();
           $avatarPath = public_path('/images/');
           $avatarUploaded->move($avatarPath, $avatarName);
           $validatedData['avatar'] = '/image/' . $avatarName;

           $validatedData['password'] = bcrypt($validatedData['password']);

           $user = User::create($validatedData);
           $accessToken = $user->createToken('authToken')->accessToken;


           $data = array('name' => $validatedData['lastName']);
           $emailId = $validatedData['email'];

           Mail::send('email.welcomePro', $data,
               function ($message) use($emailId) {
               $message->from('info@tiggidoo.com', 'Bienvenue au Tiggidoo');
               $message->to($emailId);
               $message->subject('Bienvenue au Tiggidoo');
             }
           );


           return response(['user' => $user, 'access_token' => $accessToken],200);

       } catch (\Exception $e) {
           return response(['error' => $e->getMessage()], 400);
       }
   }

   public function login(Request $request)
   {

       try {
           $loginData = $request->validate([
               'email' => 'required',
               'password' => 'required'
           ]);
           if (!auth()->attempt($loginData)) {
               return response(['message' => 'Invalid credentials']);
           }
           $accessToken = auth()->user()->createToken('authToken')->accessToken;

           return response(['user' => auth()->user(), 'access_token' => $accessToken], 200);

       } catch (\Exception $e) {
           return response(['error' => $e->getMessage()], 400);
       }

   }
}

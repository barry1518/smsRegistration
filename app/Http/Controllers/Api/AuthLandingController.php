<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Email;

class AuthLandingController extends Controller
{
    protected $emailService;

    public function __construct(Email $emailService) {
        $this->emailService = $emailService;
    }

    public function registerPro(Request $request)
    {

        try {
            $params = $request->all();
            $email = $params['email'];
            $telephone = $params['telephone'];
            $lag = $params['lag'];

            $validatedData = $request->validate([
                'firstName' => 'required|max:55',
                'lastName' => 'required|max:55',
                'telephone' => 'required|max:55',
                'email' => 'email|required',
                'lag' =>  'required|max:55'
            ]);

            $validEmail = filter_var( $email, FILTER_VALIDATE_EMAIL );
            if(!$validEmail){
                return response(['error' => 'The email is not valid'], 400);
            }

            $user = User :: select('*')
                ->where('email', '=', $email)
                ->where('role_id', '=', config('role.landing_pro'))
                ->first();

            if (!is_null($user)) {
                return response(['error' => 'The email is already exist'], 404);
            }

            $user = User :: select('*')
                ->where('telephone', '=', $telephone)
                ->where('role_id', '=', config('role.landing_pro'))
                ->first();

            if (!is_null($user)) {
                return response(['error' => 'The telephone is already exist'], 404);
            }

            $validatedData['role_id'] = config('role.landing_pro');
            if(!$request->has('password')){
                $validatedData['password'] = config('role.landing_pwd');
                $validatedData['password'] = bcrypt($validatedData['password']);
            }

            $user = User::create($validatedData);
            $accessToken = $user->createToken('authToken')->accessToken;

            if($lag == 'fr') {
                $mailData = [
                    'emailId'            =>  $validatedData['email'],
                    'messageFromEmail'   => 'info@tiggidoo.com',
                    'messageFromMessage' => 'Bienvenue chez Tiggidoo',
                    'messageSubject'     => 'Bienvenue chez Tiggidoo',
                    'templateName'       => 'email.bienvenuPro',
                    'templateData'       => ['name' => ucfirst($validatedData['firstName'])],
                ];
            }

            if($lag == 'En') {
                $mailData = [
                    'emailId'            =>  $validatedData['email'],
                    'messageFromEmail'   => 'info@tiggidoo.com',
                    'messageFromMessage' => 'Welcome to Tiggidoo',
                    'messageSubject'     => 'First Contact Tiggidoo',
                    'templateName'       => 'email.welcomePro',
                    'templateData'       => ['name' => ucfirst($validatedData['firstName'])],
                ];
            }

            $this->emailService->sendEmail($mailData);

            return response(['user' => $user, 'access_token' => $accessToken],200);

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }

    public function registerUser(Request $request)
    {

        try {
            $params = $request->all();
            $email = $params['email'];
            $lag = $params['lag'];

            $validEmail = filter_var( $email, FILTER_VALIDATE_EMAIL );
            if(!$validEmail){
                return response(['error' => 'The email is not valid'], 400);
            }

            $user = User :: select('*')
                     ->where('email', '=', $email)
                     ->where('role_id', '=',config('role.landing_user'))
                     ->first();

            if (!is_null($user)) {
                return response(['error' => 'The email is already exist'], 404);
            }

            $validatedData = $request->validate([
                'firstName' => 'required|max:55',
                'email' => 'email|required'
            ]);

            $validatedData['role_id'] = config('role.landing_user');
            $validatedData['lastName'] = 'anonyme';
            $validatedData['telephone'] = '000-000-0000';
            if(!$request->has('password')){
                $validatedData['password'] = config('role.landing_pwd');
                $validatedData['password'] = bcrypt($validatedData['password']);
            }

            $user = User::create($validatedData);
            $accessToken = $user->createToken('authToken')->accessToken;

            if($lag == 'fr') {
                $mailData = [
                    'emailId'            =>  $validatedData['email'],
                    'messageFromEmail'   => 'info@tiggidoo.com',
                    'messageFromMessage' => 'Bienvenue chez Tiggidoo',
                    'messageSubject'     => 'Bienvenue chez Tiggidoo',
                    'templateName'       => 'email.bienvenuUts',
                    'templateData'       => ['name' => ucfirst($validatedData['firstName'])],
                ];
            }

            if($lag == 'En') {
                $mailData = [
                    'emailId'            =>  $validatedData['email'],
                    'messageFromEmail'   => 'info@tiggidoo.com',
                    'messageFromMessage' => 'Welcome to Tiggidoo',
                    'messageSubject'     => 'First contact Tiggidoo',
                    'templateName'       => 'email.welcomeUser',
                    'templateData'       => ['name' => ucfirst($validatedData['firstName'])],
                ];
            }

            $this->emailService->sendEmail($mailData);
            return response(['user' => $user, 'access_token' => $accessToken],200);

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }

}

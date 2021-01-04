<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendEmailController extends Controller
{
    public function mail(Request $request){

        try {
            $params = $request->all();
            $emailId = $params['email_id'];

            $validEmail = filter_var( $emailId, FILTER_VALIDATE_EMAIL );
            if(!$validEmail){
                return response(['error' => 'The email is not valid'], 400);
            }

            Mail::send('email.welcomeUser',
                [
                    'name' => "zhiqiang"
                ],
                function ($message) use($emailId) {
                    $message->from('info@tiggidoo.com', 'Bienvenue chez Tiggidoo');
                    $message->to($emailId);
                    $message->subject('Bienvenue chez Tiggidoo');
                }
            );

            return response('success', 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()], 400);
        }



    }
}

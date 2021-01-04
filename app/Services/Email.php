<?php
namespace App\Services;

use Illuminate\Support\Facades\Mail;

class Email
{
    public function sendEmail($mailData){
        Mail::send($mailData['templateName'], $mailData['templateData'],
            function ($message) use($mailData) {
                $message->from($mailData['messageFromEmail'], $mailData['messageFromMessage']);
                $message->to($mailData['emailId']);
                $message->subject($mailData['messageSubject']);
            }
        );
    }
}

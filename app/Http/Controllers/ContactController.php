<?php

namespace App\Http\Controllers;
Use App\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function sendMail(Request $request)
    {
       
        // $data = $request->all();

        // $data = array(
        //     'firstName' => $request->firstName,
        //      'email'=>$request->email,
        //       'contact'=>$request->contact,
        //      'message'=>$request->message
        // );

        // Appsettings data starts
        $team= AppSetting::where('id','=',999)->get()[0];
   
        $primaryEmail =  $team->primaryEmail;
        $secondaryEmail =  $team->secondaryEmail;
        
         // Appsettings data ends

          $firstName  = $request->firstName;
          $email= $request->email;
          $contact= $request->contact;
          $messagedata= $request->message;
        

        Mail::send('emails.mail', ['firstName' => $firstName, 'email' => $email, 'contact' => $contact, 'messagedata' => $messagedata], function($message) use($primaryEmail, $secondaryEmail) {
            $message->to($primaryEmail, 'Nandu')
                    ->subject('Askumbau testing')
                    ->bcc($secondaryEmail, 'Nandeesh');
            $message->from('donotreply@askumbau.com','donotreply@askumbau.com');
        });
        return response()->json(['message' => 'Request completed']);
    }
}

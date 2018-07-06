<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function sendMail(Request $request)
    {
        $data = $request->all();
   
        Mail::send('emails.mail', $data, function($message) {
            $message->to('nandeesh@bixbytessolutions.com', 'Nandu')
                    ->subject('Askumbau testing');
            $message->from('donotreply@askumbau.com','donotreply@askumbau.com');
        });
    }
}

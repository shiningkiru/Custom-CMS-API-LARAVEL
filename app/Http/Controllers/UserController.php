<?php

namespace App\Http\Controllers;

use App\User;
use Password;
use Validator;
use App\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function createUser(Request $request){
        $userId=$request->id;
        $validatedData = Validator::make($request->all(),[
            'fullName' => 'required|max:191',
            'email' => 'required|email|unique:users,email,'.$userId,
            'roles' => 'required|in:admin,editer,viewe,super_admin,user,client,staff',
        ]);//password_confirmation required
        if ($validatedData->fails()) {
            return response()->json($validatedData->errors(),400);
        }
        if($userId == null){
            $user=new User();
            $validatedData = Validator::make($request->all(),[
                'password' => 'required|min:6|confirmed',//|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/|confirmed',
            ]);
            if ($validatedData->fails()) {
                return response()->json($validatedData->errors(),400);
            }
        }else{
            $validatedData = Validator::make($request->all(),[
                'id'=>'exists:users,id'
            ]);//password_confirmation required
            if ($validatedData->fails()) {
                return response()->json($validatedData->errors(),400);
            }
            $user=User::find($userId);
        }
        $user->name=$request->fullName;
        $user->email=$request->email;
        if($userId == null)
            $user->password=bcrypt($request->password);
        $user->roles=$request->roles;
        $user->save();
        return response()->json('SUCCESSFULLY UPDATED',200);
    }

    public function getUser(){
        return User::all()->toArray(); 
    }

    public function sendResetLinks(Request $request){
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $error_message = "Your email address was not found.";
            return response()->json(['success' => false, 'email'=> $error_message], 401);
        }
        $hashToken=(explode("@",$request->email)[0]).uniqid();
        $token = ResetPassword::where('email','=',$request->email)->where('created_at','>',Carbon::now()->subHours(2))->first();
        if($token instanceof ResetPassword){
           // return response()->json("Link Already Sent",400);
        }

        $token = ResetPassword::where('email','=',$request->email)->first();
        if(!($token)){
            $token=new ResetPassword();
            $token->email=$request->email;
        }
        $token->token=$hashToken;
        $token->created_at=Carbon::now();
        $token->save();

        
        try {
            $data['token']=$hashToken;

            Mail::send('emails.forgot', $data, function($message) use ($request) {
                $message->to($request->email, 'Nandu')
                        ->subject('Forgot password reset link');
                $message->from('donotreply@askumbau.com','donotreply@askumbau.com');
            });
            return response()->json('A reset email has been sent! Please check your email.',200);
        } catch (\Exception $e) {
            //Return with error
            $error_message = $e->getMessage();
            return response()->json(['success' => false, 'error' => $error_message], 401);
        }
        return response()->json([
            'success' => true, 'data'=> 'A reset email has been sent! Please check your email.'
        ],200);
    }

    public function resetPassword(Request $request){
        $token = $request->token;
        $validatedData = Validator::make($request->all(),[
            'password' => 'required|min:6|confirmed',
        ]);
        if ($validatedData->fails()) {
            return response()->json($validatedData->errors(),400);
        }
        $password=$request->password;
        $token = ResetPassword::where('token','=',$request->token)->where('created_at','>',Carbon::now()->subHours(2))->first();
        if(!$token){
            return response()->json("Password expired",400);
        }
        $user = User::where('email', $token->email)->first();
        if (!$user) {
            $error_message = "Your email address was not found.";
            return response()->json(['success' => false, 'error' => $error_message], 401);
        }
        $user->password=bcrypt($password);
        $user->save();
        return response()->json("Successfully updated",200);
    }
}

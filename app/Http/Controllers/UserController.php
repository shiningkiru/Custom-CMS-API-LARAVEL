<?php

namespace App\Http\Controllers;

use App\User;
use Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function createUser(Request $request){
        $userId=$request->id;
        $validatedData = Validator::make($request->all(),[
            'id'=>'exists:users,id',
            'fullName' => 'required|max:191',
            'email' => 'required|email|unique:users,email,'.$userId,
            'password' => 'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/|confirmed',
            'roles' => 'required|in:admin,editer,viewe,super_admin,user,client,staff',
        ]);//password_confirmation required
        if ($validatedData->fails()) {
            return response()->json($validatedData->errors(),400);
        }
        if($userId == null){
            $user=new User();
        }else{
            $user=User::find($userId);
        }
        $user->name=$request->fullName;
        $user->email=$request->email;
        $user->password=bcrypt($request->password);
        $user->roles=$request->roles;
        $user->save();
        return response()->json('SUCCESSFULLY UPDATED',200);
    }
}

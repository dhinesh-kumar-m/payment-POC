<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserMaster;
use Illuminate\Support\Facades\Hash;
use Validator;

class UserController extends Controller
{
    public function signup_user(Request $request){
    	try{

	    	$rules = [
		        'email' 		=> 'required|unique:user_master|max:255',
		        'password'		=> 'required',
		    ];
		    $data = $request->input('data');
		    $validator = Validator::make($data,$rules);
		    if($validator->fails()){
				return $this->errorResponse($validator->messages(),201);
		    }

	    	$first_name			= isset($data["first_name"]) ? $data["first_name"]: "";
	    	$last_name			= isset($data["last_name"]) ? $data["last_name"]: "";
	    	$email				= $data["email"];
	    	$password 			= $data["password"];
	    	$mobile_number 		= isset($data["mobile_number"]) ? $data["mobile_number"]: "";

		    $user               = new UserMaster;
	        $user->first_name   = $first_name;
	        $user->last_name 	= $last_name;
	        $user->email        = $email;
	        $user->password     = Hash::make($password);
	        $user->mobile_number= $mobile_number;
	        $user->save();

	        $successMessage = 'Successfully created';
			return $this->successResponse($user, $successMessage, 200);
		}catch(Exception $e){
			$errorMessage = 'Something Wrong';
			return $this->errorResponse($errorMessage, 400);
		}
    }
}

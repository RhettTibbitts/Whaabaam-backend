<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserApiToken extends Model
{
	public $timestamps = false;

	public static function saveUserApiToken($user_id = null){ 
		$token = str_random(40);
	    $user_token = UserApiToken::where('user_id',$user_id)->first();
		if(empty($user_token)){
			$user_token 		 	= new UserApiToken;
			$user_token->user_id 	= $user_id;
		} 

		$user_token->token   	= $token;
		$user_token->created_at	= date('Y-m-d H:i:s');
		if($user_token->save()){
			return $user_token->token;
		} else{
			return false;
		}
	}
}

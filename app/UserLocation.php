<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
	protected $fillable = [
		'user_id','lat','lng','address'
	];

	public function userInfo(){
		return $this->belongsTo('App\User','user_id','id')->select('id');
	}

	public static function getLastLocationTimestamp($user_id){
		return Self::where('user_id',$user_id)->orderBy('id','desc')->value('created_at');
	}
}

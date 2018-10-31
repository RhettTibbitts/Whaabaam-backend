<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
	protected $fillable = [
	];
	
	public static function saveDevice($request,$user_id){

        if(!empty(@$request->device_fcm_token)){ 
            $usr_device = UserDevice::where('user_id',$user_id)->first();
            if(!empty($usr_device)){
                $usr_device->token	     = $request->device_fcm_token;
                $usr_device->device_type = $request->device_type;
            } else{
                $usr_device              = new UserDevice;
                $usr_device->user_id     = $user_id;
                $usr_device->device_type = $request->device_type;
                $usr_device->token  	 = $request->device_fcm_token;
            }

            if($usr_device->save()){
                return true;
            } else{
                return false;
            }
        } else{
            return false;
        }
    }

    public static function getDevice($user_id = null){
        return UserDevice::select('device_type','token')->where('user_id',$user_id)->first();
    }

}

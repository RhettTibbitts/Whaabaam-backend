<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Notification extends Model
{
	public $timestamps = false;

    protected $fillable = ['id','user_id','event_id','event_type','message','created_at','user_id'];

    public function ConcernedUser(){
        return $this->belongsTo('App\User','profile_user_id','id')->select('id','first_name','last_name','image','email','quickblox_id');
    }

	public static function getNotifications($user_id=null){

        $msg1_req   = 'Wants To connect';   //F
        $msg2_close = 'Is close by';        //C

        return Notification::select('id','profile_user_id','event_id','event_type','created_at',
                        DB::raw('(CASE WHEN event_type = "F" THEN "'.$msg1_req.'" ELSE "'.$msg2_close.'" END) AS message')
                    )
                    ->with('ConcernedUser')
                    ->has('ConcernedUser')
                    ->where('user_id',$user_id)
                    ->orderBy('notifications.id','desc')
                    ->paginate(PAGINATE_LIMIT)
                    ->toArray();
    }

    public static function saveNotif($to_user_id, $from_user_id, $event_id=null,$event_type=null){ 
    	
 		$notif                      = new Notification;   	
        $notif->user_id             = $to_user_id;           //for whom notif. is saved (whom notif. will be shown)
 		$notif->profile_user_id 	= $from_user_id;   	     //about whom notif. is saved (about which notif. will be shown)
 		$notif->event_id 	        = $event_id; 	         //id of friend_requests or capture_ users table	
        $notif->event_type          = $event_type;           //F = Friend request, C = close user notif.     
 		// $notif->message 	        = $msg;   	
 		$notif->created_at          = date('Y-m-d H:i:s');   	
 		if($notif->save()){
 			return true;
 		} else{
 			return false;
 		}
    }

}

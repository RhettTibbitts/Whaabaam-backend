<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\UserLocation;
use App\CapturedUser;
use App\Admin;
use App\CaptureFilter;
use App\Notification;
use Carbon\Carbon;

// use App\Traits\GeoCodeLocation;
use App\Traits\PushNotification;

class CronController extends Controller
{
	// use GeoCodeLocation;
	use PushNotification;
	
	public function capture_users(){ //save captured users

		$current_time = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
		// $current_time = Carbon::createFromFormat('Y-m-d H:i:s', date('2018-07-25 16:03:00'));
		$x = (int)ALIVE_CHECK_TIME; //check last x minutes, if user has made any location entry in between. usually = 6 or 7 (mint)

		$users = $this->_get_active_users($current_time,$x);

		foreach($users as $user){

			$close_usrs = $this->_get_close_users($user,$current_time);
			// echo '<pre>selected user id = '; print_r($user); print_r($close_usrs); //die;  //mk

			$notify_user_ids = [];
			foreach($close_usrs as $close_usr_id => $close_usr){

				// echo '<pre>o'; print_r($close_usr); //die;
				$validate = $this->_validate_interval($close_usr,$x); 
				// $validate = true; //just for testing
				if($validate == true){

					//save captured user 
					$saved = $this->_save_captured_user($user,$close_usr_id,$close_usr);
					
					//if new capture user has been saved then send push notif. to user
					if($saved['status'] == true){
						if($saved['action'] == 'add'){						//send notif. only if it is new record 
							if(isset($close_usr['0']['user_info']['id'])){  
								$notify_user_ids[$close_usr_id] = $saved['capture_save_id'];
							}
						}
					}
				}
			}

			//send notification to current user about captured users
			if(!empty($notify_user_ids)){

				//validate if the user's notification preference 
				//check is notif. preference fields match
				$notify_user_ids = $this->is_notif_pref_match($user,$notify_user_ids);

				// when push notifiction is send then send all notification at once to a user
				// but when saving into notification save notification individually
				$this->_send_notifs($user,$notify_user_ids);
			}
		}	

		return response()->json([
			'status' => 200,
			'message' => 'done'
		]);
	}

	function is_notif_pref_match($user,$notify_user_ids){

		//get user pref. fields
		$cap_filters = CaptureFilter::getCaptureFilterFields($user['capture_filter_ids']);

		$filtered_notify_user_ids = [];
		if(!empty($cap_filters)){

			foreach($notify_user_ids as $notify_user_id => $cap_save_id){					//for all notif. rec. user
				$notif_usr_query = User::where('id',$notify_user_id);

				foreach($cap_filters as $col_name){							//if its notif. matches with prefer.
					$notif_usr_query->where($col_name,$user[$col_name]);
				}
				
				$usr_id = $notif_usr_query->value('id');
				if(!empty($usr_id)){
					$filtered_notify_user_ids[$usr_id] = $cap_save_id;
				}

			}		
		}
		return $filtered_notify_user_ids;
	}

	function _send_notifs($user,$notify_user_ids) { 
			
		//send push notification to the user that a new user has been captured for him
		if(!empty(@$user['user_device']['token'])) {
			$device_id   = $user['user_device']['token'];
			$device_type = $user['user_device']['device_type'];

			$notify_users_count = count($notify_user_ids);

			//get first user id
			$first_usr = array_slice($notify_user_ids,0,1);
			$first_user_id = $first_usr[0];
			$usr1_name = User::getUserName($first_user_id);

			if($notify_users_count == 1){

				$txt = $usr1_name.' has been  added to your close contact.';

			} else if($notify_users_count == 2){

				//get second user id
				$first_usr = array_slice($notify_user_ids,0,1);
				$secnd_user_id = $first_usr[0];
				$usr2_name = User::getUserName($secnd_user_id);

				$txt = $usr1_name.' and '.$usr2_name.' has been added to your close contact.';
			
			} else if($notify_users_count > 2){

				$rem_usr_count = $notify_users_count - 1;				
				$txt = $usr1_name.' and '.$rem_usr_count.' other has been added to your close contact.';
			}

			// e.g. Rahul Gupta and Mohit Kumar has been added to your close contact.
			// Rahul Gupta and 2 other has been added to your close contact.

			$message = [
				'title' => 'New close contact',
				'body' => $txt,
                'event_type' => 'CLOSE_CONTACT',
			];
			$this->notifyAndroid($device_id,$message);		
			
			/*if($device_type == 'A'){ //android
				$this->notifyAndroid($device_id,$message);		
			} else{
				$this->notifyIos($device_id,$message);		
			}*/
		}

		//save notification for each individual captured users
		foreach($notify_user_ids as $n_user_id => $capture_save_id){
			Notification::saveNotif($user['id'], $n_user_id, $capture_save_id, 'C');				
		}
		// old when saving only one notif. in db for all cap. users
		// Notification::saveNotif($v,'C','');				
	}

	function _get_active_users($current_time = null, $x = 0){

		//get only those users who updated their location in between last x minutes
		$user_last_min_update = $current_time->subMinute($x); //time after which user's entry must exist in db
		// echo $user_last_min_update; die;
		$users = User::select('id','capture_time_period','capture_filter_ids',  'city_id','state_id','occupation','education','high_school','college','alma_matter','likes','military_id','religion_id','relationship_id')
						->where('status',1)
						->whereHas('userLocation', function($query) use ($user_last_min_update){
							$query->where('created_at','>',$user_last_min_update);
						})
						->with(['userLocation' => function($query) use ($user_last_min_update){
							$query->where('created_at','>',$user_last_min_update);
						}])
						->with('userDevice')
						->orderBy('id','asc')
						->get()->toArray();
		return $users;
	}
		
	function _get_close_users($user = null, $current_time = null){

		$capture_time 	= $user['capture_time_period'];
		$lat 			= $user['user_location']['lat'];
		$lng 			= $user['user_location']['lng'];
		$radius 		= Admin::getCaptureDistance();
		$start_time   = $current_time->subMinute($capture_time);
		// echo '$start_time '.$start_time; echo '$user '.$user['id'];
		// $radius = 7264;
		// $radius = (int)$radius + 1;
		// echo $radius; die;
		//getting all the close users to the current users
		$close_usrs_query = UserLocation::where('user_id','<>',$user['id'])
							->select('id','user_id','created_at','lat','lng','address')
							/*->selectRaw('( 6371 * acos( cos( radians(?) ) *
	                               cos( radians( lat ) )
	                               * cos( radians( lng ) - radians(?)
	                               ) + sin( radians(?) ) *
	                               sin( radians( lat ) ) )
	                             ) AS distance', [$lat, $lng, $lat])*/

							->selectRaw('convert(( 6371000 * acos( cos( radians(?) ) *
                               cos( radians( lat ) )
                               * cos( radians( lng ) - radians(?)
                               ) + sin( radians(?) ) *
                               sin( radians( lat ) ) )
                             ),UNSIGNED) AS distance', [$lat, $lng, $lat])

                			->havingRaw("distance < ?", [$radius])
                			->where('created_at','>',$start_time);

	    //implementing notif. preference start
		//Note: It will include userinfo array with the query result, which will be further used to decide that if push notification should be send to that user or not.

		//if captured user's has the same selected fields like:occupation, as of the  current user 

		$capture_columns = $this->_get_capture_columns($user['capture_filter_ids']);
		if(!empty($capture_columns)){
			$close_usrs_query = $close_usrs_query->with(['userInfo' => function($query) use ($capture_columns,$user){
										foreach($capture_columns as $col_name){
										 	$query->where($col_name,$user[$col_name]);
										}
									} ]);
		}
	    //implementing notif. preference end

		$close_usrs = $close_usrs_query->orderBy('id','desc')
							->get()
							->groupBy('user_id')
							->toArray();
							
		return $close_usrs;
	}

	function _get_capture_columns($capture_ids){
		if(!empty($capture_ids)){
			$capture_ids = explode(',',$capture_ids);
			return CaptureFilter::whereIn('id',$capture_ids)->pluck('column_name')->toArray();
		} else{
			return array();
		}
	}

	function _validate_interval($loc_timings=array(),$interval=null){
		
		$pre_timing = '';
		$valid = true;

		foreach($loc_timings as $loc){

			if(empty($pre_timing)) {
				$pre_timing = $loc['created_at'];
			} else{
				
				$pre_time   = Carbon::createFromFormat('Y-m-d H:i:s', $pre_timing);
				$created_at = Carbon::createFromFormat('Y-m-d H:i:s', $loc['created_at']);

				$mint_diff = $pre_time->diffInMinutes($created_at);

				if($mint_diff > 7){ 
					$valid = false;
					break;
				} else{ 
					$pre_timing = $loc['created_at'];
				}
			}
		}
		return $valid;
	}

	function _save_captured_user($user=null,$capture_user_id=null,$capture_usr=null){

		$user_id  = $user['id']; 
		//before saving check is record already exist for today, if yes then update the record's updated at timestamp
		$cap_user = CapturedUser::where('user_id',$user_id)
						->where('capture_user_id',$capture_user_id)
						->where('updated_at','LIKE',date('Y-m-d').'%')
						->first();
		
		// $address = $this->getLocationFromLatLong($capture_usr['0']['lat'],$capture_usr['0']['lng']);
		if(!empty($cap_user)){ //record already exist update its update time

			$cap_user->updated_at = date('Y-m-d H:i:s');
			$cap_user->lat 		  = $capture_usr['0']['lat'];
			$cap_user->lng 		  = $capture_usr['0']['lng'];
			$cap_user->address 	  = ($capture_usr['0']['address']) ? $capture_usr['0']['address'] : '';
			if($cap_user->save()){ 			
				return [
					'status' => true,
					'action' => 'update',
					'capture_save_id' => $cap_user->id
				];
			} else{
				return [
					'status' => false
				];
			}

		} else{
			
			//save captured users for current user
			$cap_user 					= new CapturedUser;
			$cap_user->user_id 			= $user_id;
			$cap_user->capture_user_id 	= $capture_user_id;
			$cap_user->lat 				= $capture_usr['0']['lat'];
			$cap_user->lng 				= $capture_usr['0']['lng'];
			$cap_user->address 	  		= ($capture_usr['0']['address']) ? $capture_usr['0']['address'] : '';
			if($cap_user->save()){ 			
				return [
					'status' => true,
					'action' => 'add',
					'capture_save_id' => $cap_user->id
				];
			} else{
				return [
					'status' => false
				];
			}
		}
	}

	/* testing ios notification
		$device_id = '3ef141dcf41019fbac1ca10c7398ad84d61375d7e3e039c107ec3f00ab9cbcae';
		$txt = 'hi';
			$message = [
				'title' => 'New close contact',
				'body' => $txt,
                'event_type' => 'CLOSE_CONTACT',
			];
			// if($device_type == 'A'){ //android
			// 	$this->notifyAndroid($device_id,$message);		
			// } else{
				$this->notifyIos($device_id,$message);		
			// }
			echo 'ok'; die;*/

		//testing device id
		/*$device_id = '7fa36d6991795a5c60dd9da2504223c9ff5d0007c12df6fc7f90621f4fa117fd';
		$data = [
                'title' => 'Whabaam',
                'event_type' => 'FRIEND_REQ',
                'body'  => 'You have a new request'
            ];
		$this->notifyIos($device_id, $data);*/

		//testing notif. start
		/*$message = [
			'title' => 'welcome4',
			'body' => 'abc'
		];

		$device_id = 'f-2WX7PQaUE:APA91bEo94kQT0wBIAtNnf7AKHaskrAWxK3-msGLy17dud2pBKPee1QQd4ZmkGRGWmRABy7WGhLCYJzauxnrwEzujhtdRC8RkY3dHImRKw873FayElo8vBTrnvNNfOGyXIASzqfVRLSVITN-RHFRuo4lVY3j_G9NXQ';
		$this->notifyAndroid($device_id,$message);		
		die;*/
		//notif end
		// $a=	$this->getLocationFromLatLong(30.9108,75.8793);
		// echo $a; die;

		// $current_time = Carbon::createFromFormat('Y-m-d H:i:s', '2018-07-24 17:05:00'); //just for testing purpose a given date
		//$x = 120; //for testing only

		/*$n = new \App\UserNote;
		$n->user_id = 1;
		$n->target_user_id = 1;
		$n->note = 'cron test';
		$n->save();*/
}
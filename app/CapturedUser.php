<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class CapturedUser extends Model
{
    protected $fillable = ['user_id','capture_user_id','lat','lng','address'];

    public function userInfo(){
    	return $this->belongsTo('App\User','capture_user_id','id')->select('id','first_name','last_name','image','quickblox_id','email','last_name_access'); //,'education'
    }

    function scopeWithName($query, $name){
        $names = explode(" ",$name);
        return User::where(function($query) use ($names){
            $query->whereIn('first_name', $names);
            $query->orWhere(function($query) use ($names){
                $query->whereIn('last_name', $names);
            });
        });
    }

	/*function scopeWithName($query, $name){
        $names = explode(" ",$name);
        return User::where(function($query) use ($names){
            $query->whereIn('first_name', $names);
            $query->orWhere(function($query) use ($names){
                $query->whereIn('last_name', $names);
            });
        });
    }*/

    public static function getCapturedUsers($request){

    	//captured users  are those who are stored into the db though the cron job. the users who spent a specific time in some other person range, then that user will be saved as captured users for the user.
    	
    	//$date  	 = (!empty(@$request->date)) ? $request->date : date('Y-m-d');
    	
    	//if no date selected then don't filter otherwise filter acc to date
    	$date  	 = (!empty(@$request->date)) ? $request->date : '';
		
		$filters = (!empty(@$request->filters)) ? array_filter($request->filters) : array(); //remove empty values
		
		if (($key = array_search('hide_friends', $filters)) !== false) {
		    unset($filters[$key]);
			$hide_friends = true;
		}

		if (($key = array_search('hide_strangers', $filters)) !== false) {
		    unset($filters[$key]);
			$hide_strangers = true;
		}
	
		//possible values are:
		//$filters = ['city_id','state_id','occupation','education','high_school','college','alma_matter','relationship_id'];
		$search  = (!empty(@$request->search)) ? $request->search : '';

		$cap_usrs_query = CapturedUser::select('id','user_id','capture_user_id','updated_at','lat','lng','address')
								->where('user_id',$request->user_id);
		
		if(!empty($date)){ //set by default date to today. but now we need to show all
			$cap_usrs_query = $cap_usrs_query->where('updated_at','like',$date.'%');
		}

		//get current user info
		$user = User::select('id','city_id','state_id','occupation','education','high_school','college','alma_matter','relationship_id','political_id','religion_id','military_id','likes','friend_ids')
					->where('id',$request->user_id)
					->first()->toArray();
		$my_friend_ids =  explode(',',$user['friend_ids']);

		//manage hide_friends & hide_strangers
		if(@$hide_friends == true){ 
			$cap_usrs_query = $cap_usrs_query->whereNotIn('capture_user_id',$my_friend_ids);
		}
		//manage hide_friends & hide_strangers - end


		if(@$hide_strangers == true){ 
			$cap_usrs_query = $cap_usrs_query->whereIn('capture_user_id',$my_friend_ids);
		}

		if(!empty($filters)){
		

			$cap_usrs_query = $cap_usrs_query
								->with(['userInfo' => function($query) use ($filters,$user,$request){

									foreach($filters as $col_name){
									 	$query->where($col_name,$user[$col_name]);
									}

									$query->when($request->search, function($when_query) use ($request){
			                            return $when_query->where( function($query) use ($request){
			                                $query->whereRaw("concat(first_name, ' ', last_name) like '%".$request->search."%' ");
			                                // $query->orWhereRaw("concat(first_name, ' ', last_name) like '%".$request->search."%' ");
			                            });
			                        });
								}])
								->whereHas('userInfo', function($query) use ($filters,$user,$request){
									foreach($filters as $col_name){
									 	$query->where($col_name,$user[$col_name]);
									}

									$query->when($request->search, function($when_query) use ($request){
			                            return $when_query->where( function($query) use ($request){
			                                $query->whereRaw("concat(first_name, ' ', last_name) like '%".$request->search."%' ");
			                                // $query->orWhereRaw("concat(first_name, ' ', last_name) like '%".$request->search."%' ");
			                            });
			                        });
								});

								// ->selectRaw('select status from friend_requests where id = 1 AS mk');
								// , [$lat, $lng, $lat])

								// ->selectRaw('convert(( 6371000 * acos( cos( radians(?) ) *
	       //                         cos( radians( lat ) )
	       //                         * cos( radians( lng ) - radians(?)
	       //                         ) + sin( radians(?) ) *
	       //                         sin( radians( lat ) ) )
	       //                       ),UNSIGNED) AS distance', [$lat, $lng, $lat])

								/*>with(['userInfo' => function($query) use ($filters,$user,$request){
									foreach($filters as $col_name){
									 	$query->where($col_name,$user[$col_name]);
									 	
									 	$query->when($request->search, function($when_query) use ($request){

				                            return $when_query->where( function($query) use ($request){
				                                $query->orWhereRaw("concat(first_name, ' ', last_name) like '%".$request->search."%' ");
				                                // $query->orWhereRaw("concat(first_name, ' ', last_name) like '%".$request->search."%' ");
				                            });
				                        });
									}
								}])
								->whereHas('userInfo', function($query) use ($filters,$user,$request){
									foreach($filters as $col_name){

									 	$query->where($col_name,$user[$col_name]);

									 	$query->when($request->search, function($when_query) use ($request){

				                            return $when_query->where( function($query) use ($request){
				                                $query->orWhereRaw("concat(first_name, ' ', last_name) like '%".$request->search."%' ");
				                                // $query->orWhereRaw("concat(first_name, ' ', last_name) like '%".$request->search."%' ");
				                            });
				                        });
									}
								});*/
		} else{
			if(!empty($search)){
				$cap_usrs_query = $cap_usrs_query
									->with(['userInfo' => function($query) use ($search){
										$query->whereRaw("concat(first_name, ' ', last_name) like '%".$search."%' ");
									}])
									->whereHas('userInfo', function($query) use ($search){
										$query->whereRaw("concat(first_name, ' ', last_name) like '%".$search."%' ");
									});
			} else{
				$cap_usrs_query = $cap_usrs_query->with('userInfo');
			}
		}
		$captured_usrs = $cap_usrs_query->orderBy('id','desc')->paginate(10)->toArray();

		//determing if the searched user is friendor not
		foreach($captured_usrs['data'] as $key => $value){
   			$friend_status = FriendRequest::checkFriendReqStatus($value['user_id'],$value['capture_user_id']);
			$captured_usrs['data'][$key]['req_status'] = $friend_status;

			//manage last name access
            if($friend_status != 'FRIEND'){
                if($captured_usrs['data'][$key]['user_info']['last_name_access'] == 0){
                    $captured_usrs['data'][$key]['user_info']['last_name'] = '';
                }
            }
		}
		return $captured_usrs;
    }

}
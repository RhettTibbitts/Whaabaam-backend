<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Http\Request;

class FriendRequest extends Model
{
	public $timestamps = false;
    
    public function family(){
        return $this->hasOne('App\FamilyMember','friend_req_id','id')->whereNull('deleted_at')
                    ->select('friend_req_id','family_relation_id','other_relation_detail');
    }

    public function receiverUser(){
        return $this->belongsTo('App\User','rec_user_id','id')->select('id','first_name','last_name','quickblox_id','email','image');
    }

    public function senderUser(){
        return $this->belongsTo('App\User','req_user_id','id')->select('id','first_name','last_name','quickblox_id','email','image');
    }

    public static function getFriends($user_id, $request=null, $show_all = false){

        $query = FriendRequest::select(
                            'friend_requests.id as friend_request_id',
                            DB::raw('ifnull(req_u.id,rec_u.id) as friend_user_id'),
                            DB::raw('ifnull(req_u.first_name,rec_u.first_name) as first_name'),
                            DB::raw('ifnull(req_u.last_name,rec_u.last_name) as last_name'),
                            DB::raw('ifnull(req_u.quickblox_id,rec_u.quickblox_id) as quickblox_id'),
                            DB::raw('ifnull(req_u.email,rec_u.email) as email'),
                            DB::raw('ifnull(req_u.image,rec_u.image) as image'),
                            DB::raw('"'.asset(USER_PROFILE_MAIN_IMG_PATH).'" AS image_path'),
                            DB::raw('"'.asset(USER_PROFILE_MAIN_IMG_PATH.'thumb/').'" AS thumb_path'),
                            
                            DB::raw('ifnull(req_l.address,rec_l.address) as address'),
                            // DB::raw('ifnull(req_l.id,rec_l.id) as loc_id'),
                            DB::raw('ifnull(req_l.updated_at,rec_l.updated_at) as time')
                            // 'rec_l.*','req_l.*'
                        )
                        ->where('friend_requests.unfriend_at',null)
                        ->where('friend_requests.status','=','A')

                        ->where( function($query) use ($user_id){
                            $query->orWhere('friend_requests.req_user_id',$user_id);
                            $query->orWhere('friend_requests.rec_user_id',$user_id);
                        })
                        //join only if req or rec user if isnot equal to user id, to get friend info
                        ->leftJoin('users as req_u', function($join) use ($user_id) {
                            $join->on('req_u.id','=','friend_requests.req_user_id');
                            $join->where('req_u.id','<>',$user_id);
                            $join->where('req_u.deleted_at',null);
                            $join->where('req_u.status',1);
                        })
                        ->leftJoin('users as rec_u', function($join) use ($user_id) {
                            $join->on('rec_u.id','=','friend_requests.rec_user_id');
                            $join->where('rec_u.id','<>',$user_id);
                            $join->where('rec_u.deleted_at',null);
                            $join->where('rec_u.status',1);
                        })

                        //join only if req or rec user if isnot equal to user id, 
                        //get friend location info
                        ->leftJoin('user_locations as req_l', function($join) use ($user_id) {
                            $join->on('req_l.user_id','=','req_u.id');
                            $join->on('req_l.id','=', DB::raw('(SELECT id FROM user_locations WHERE user_locations.user_id = req_u.id ORDER BY user_locations.id desc LIMIT 1 )') );
                            $join->where('req_l.user_id','<>',$user_id);
                        })
                        ->leftJoin('user_locations as rec_l', function($join) use ($user_id) {
                            $join->on('rec_l.user_id','=','rec_u.id');
                            $join->on('rec_l.id','=', DB::raw('(SELECT id FROM user_locations WHERE user_locations.user_id = rec_u.id ORDER BY user_locations.id desc LIMIT 1 )') );
                            $join->where('rec_l.user_id','<>',$user_id);
                        })

                        // ->whereRaw('friend_request_id = 7')
                        //->where('friend_request_id',7)
                        //search first and last name
                        ->when($request->filter_name, function($when_query) use ($request){

                            return $when_query->where( function($query) use ($request){
                                $query->orWhereRaw("concat(req_u.first_name, ' ', req_u.last_name) like '%".$request->filter_name."%' ");
                                $query->orWhereRaw("concat(rec_u.first_name, ' ', rec_u.last_name) like '%".$request->filter_name."%' ");
                            });
                        })

                        //ensuring that both users are active
                        ->join('users as req_ui', function($join) use ($user_id) {
                            $join->on('req_ui.id','=','friend_requests.req_user_id');
                            $join->where('req_ui.deleted_at',null);
                            $join->where('req_ui.status',1);
                        })
                        ->join('users as rec_ui', function($join) use ($user_id) {
                            $join->on('rec_ui.id','=','friend_requests.rec_user_id');
                            $join->where('rec_ui.deleted_at',null);
                            $join->where('rec_ui.status',1);
                        })

                        ->orderBy('friend_requests.id','desc');
            
        if($show_all == false){
            $friend_reqs = $query->paginate(PAGINATE_LIMIT);
        } else{
            $friend_reqs = $query->get()->toArray();;
        }

        return $friend_reqs;
        // Note about this function:
        // in every req there is my user id  and sender or receiver id
        // I need to get data of other users only not 
        // user_id is my id

        // if(req.req_user_id != user_id){ 
        //     then use req_user_id to join with user table 
        // }
        // similarly, make join with rec table only if that rec id is not my id
        // if(req.rec_user_id != user_id){ 
        //     then use rec_user_id to join with user table 
        // }

        // now at a time only one join will get its data, but the thing is: both joins keys are diff.
        // i need same key from both join data. for this i used ifnull  
    }

    public static function getFriendsToAddInFamily($request){
        $user_id = $request->user_id; 

        $friends = FriendRequest::select(
                            'friend_requests.id','friend_requests.id as friend_request_id',
                            DB::raw('ifnull(req_u.id,rec_u.id) as friend_user_id'),
                            DB::raw('ifnull(req_u.first_name,rec_u.first_name) as first_name'),
                            DB::raw('ifnull(req_u.last_name,rec_u.last_name) as last_name'),
                            DB::raw('ifnull(req_u.image,rec_u.image) as image'),
                            DB::raw('"'.asset(USER_PROFILE_MAIN_IMG_PATH).'" AS image_path')
                            // DB::raw('ifnull(req_l.address,rec_l.address) as address'),
                            // DB::raw('ifnull(req_l.id,rec_l.id) as loc_id'),
                            // DB::raw('ifnull(req_l.updated_at,rec_l.updated_at) as time')
                        )
                        ->where('friend_requests.unfriend_at',null)
                        ->where('friend_requests.status','=','A')

                        ->where( function($query) use ($user_id){
                            $query->orWhere('friend_requests.req_user_id',$user_id);
                            $query->orWhere('friend_requests.rec_user_id',$user_id);
                        })
                        //join only if req or rec user if isnot equal to user id, to get friend info
                        ->leftJoin('users as req_u', function($join) use ($user_id) {
                            $join->on('req_u.id','=','friend_requests.req_user_id');
                            $join->where('req_u.id','<>',$user_id);
                            $join->where('req_u.deleted_at',null);
                            $join->where('req_u.status',1);
                        })
                        ->leftJoin('users as rec_u', function($join) use ($user_id) {
                            $join->on('rec_u.id','=','friend_requests.rec_user_id');
                            $join->where('rec_u.id','<>',$user_id);
                            $join->where('rec_u.deleted_at',null);
                            $join->where('rec_u.status',1);
                        })
                        ->doesntHave('family')

                        //ensuring that both users are active
                        ->join('users as req_ui', function($join) use ($user_id) {
                            $join->on('req_ui.id','=','friend_requests.req_user_id');
                            $join->where('req_ui.deleted_at',null);
                            $join->where('req_ui.status',1);
                        })
                        ->join('users as rec_ui', function($join) use ($user_id) {
                            $join->on('rec_ui.id','=','friend_requests.rec_user_id');
                            $join->where('rec_ui.deleted_at',null);
                            $join->where('rec_ui.status',1);
                        })

                        //join only if req or rec user if isnot equal to user id, 
                        //get friend location info
                        /*->leftJoin('user_locations as req_l', function($join) use ($user_id) {
                            $join->on('req_l.user_id','=','req_u.id');
                            $join->on('req_l.id','=', DB::raw('(SELECT id FROM user_locations WHERE user_locations.user_id = req_u.id ORDER BY user_locations.id desc LIMIT 1 )') );
                            $join->where('req_l.user_id','<>',$user_id);
                        })
                        ->leftJoin('user_locations as rec_l', function($join) use ($user_id) {
                            $join->on('rec_l.user_id','=','rec_u.id');
                            $join->on('rec_l.id','=', DB::raw('(SELECT id FROM user_locations WHERE user_locations.user_id = rec_u.id ORDER BY user_locations.id desc LIMIT 1 )') );
                            $join->where('rec_l.user_id','<>',$user_id);
                        })*/

                        ->orderBy('friend_requests.id','desc')
                        ->paginate(PAGINATE_LIMIT);
        return $friends;

        /*Note: Show only those friends who has been not added to family already*/
    }

    public static function checkFriendship($user1_id,$user2_id){
        
        //search for any req. in which 
        //if they will be friend then this wil return friend req id
        return FriendRequest::orWhere( function($query) use($user1_id,$user2_id){
                            $query->where('req_user_id',$user1_id);
                            $query->where('rec_user_id',$user2_id);
                            $query->where('status','A');
                            $query->where('unfriend_at',NULL);
                        })
                        ->orWhere( function($query) use($user1_id,$user2_id){
                            $query->where('req_user_id',$user2_id);
                            $query->where('rec_user_id',$user1_id);
                            $query->where('status','A');
                            $query->where('unfriend_at',NULL);
                        })
                        ->orderBy('id','desc')
                        ->value('id');
    }

    public static function checkFriendReqStatus($user1_id,$user2_id){  //used in showing captured users list
        
        //check if user has already sent a request to the same user, or that request has been approved
        $frn_req = FriendRequest::getLastFriendRequest($user1_id,$user2_id);
        
        $status_txt = '';
        if(empty($frn_req)){                //if no friend req found
            $status_txt = 'NEW_USER';       //no req has been made
        } else{                             //if no friend req found then what is the status of request

            if($frn_req->unfriend_at == null){

                if($frn_req->status == 'P'){

                    if($frn_req->req_user_id == $user1_id){
                        $status_txt = 'REQ_SENT';
                    } else{
                        $status_txt = 'REQ_RECEIVED';
                    }

                }elseif($frn_req->status == 'R'){
                    $status_txt = 'NEW_USER'; //REQ_REJECTED

                } elseif($frn_req->status == 'A'){
                    $status_txt = 'FRIEND';
                } 
            } else{
                $status_txt = 'NEW_USER';
            }        
        }
        return $status_txt; //NEW_USER, REQ_SENT, FRIEND
    }

    public static function validateNewFriendRequest($request){

        //check if user has already sent a request to the same user, or that request has been approved
        $existing_req = FriendRequest::getLastFriendRequest($request->user_id,$request->receiver_user_id);

        $status_txt = '';
        if(!empty($existing_req)){
            if($existing_req->req_user_id == $request->user_id){    //if req was done by our side
                
                if($existing_req->status == 'P'){
                    $status_txt = 'req already sent';
                } else if($existing_req->status == 'A'){

                    if($existing_req->unfriend_at == null){
                        $status_txt = 'frnd already';
                    } else{
                        $status_txt = 'ok good';
                    }
                } else if($existing_req->status == 'R'){ //if prev req was rejected, then you can send another request
                    $status_txt = 'ok good';
                }

            } else{                                                    //if req was done by other
                if($existing_req->status == 'P'){
                    $status_txt = 'frnd req already got';

                } else if($existing_req->status == 'A'){

                    if($existing_req->unfriend_at == null){
                        $status_txt = 'frnd already';
                    } else{
                        $status_txt = 'ok good';
                    }

                }if($existing_req->status == 'R'){ //if prev req was rejected, then you can send another request
                    $status_txt = 'ok good';
                }
            }
        } else{
            $status_txt = 'ok good';
        }
        return $status_txt;
    }

    public static function addNewRequest($request){
        $req                 = new FriendRequest;
        $req->req_user_id    = $request->user_id;
        $req->rec_user_id    = $request->receiver_user_id;
        $req->status         = 'P';                     //pending status whn req. created
        $req->req_created_at = date('Y-m-d H:i:s');            
        $req->req_updated_at = date('Y-m-d H:i:s'); 

        if($req->save()){
            return $req->id;
        } else{
            return false;
        }
    }

    static function getLastFriendRequest($user1_id=null,$user2_id=null){

        return FriendRequest::select('*')
                            ->orWhere( function($query) use($user1_id,$user2_id){
                                $query->where('req_user_id',$user1_id);
                                $query->where('rec_user_id',$user2_id);
                            })
                            ->orWhere( function($query) use($user1_id,$user2_id){
                                $query->where('req_user_id',$user2_id);
                                $query->where('rec_user_id',$user1_id);
                            })
                            ->orderBy('id','desc')
                            ->first();
    }

    public static function getMutualFriends($user1_id,$user2_id){
        
        //get friends of first user
        $request = new Request;
        $usr1_frnds = Self::getFriends($user1_id,$request, true);
        // echo '<pre>'; print_r($usr1_frnds); die;
        $usr1_frnds_ids = array_map( function ($v){ return $v['friend_user_id']; }, $usr1_frnds);

        //get friends of first user
        $usr2_frnds = Self::getFriends($user2_id,$request, true);
        $usr2_frnds_ids = array_map( function ($v){ return $v['friend_user_id']; }, $usr2_frnds);

        $all_users = array_merge($usr1_frnds_ids, $usr2_frnds_ids);
        $count_repeated_user = array_count_values($all_users);

        $mutual_friends_ids = [];
        foreach($count_repeated_user as $key =>  $v){
            if($v > 1){
                $mutual_friends_ids[] = $key;
            }
        }
        
        /*$mut_frnds = User::whereIn('id',$mutual_friends_ids)->select('first_name','last_name','id as friend_user_id','image',
                        DB::raw('"'.asset(USER_PROFILE_MAIN_IMG_PATH).'" AS image_path') )
                        ->paginate(PAGINATE_LIMIT);*/

        $mut_frnds = User::whereIn('id',$mutual_friends_ids)->select('first_name','last_name','id as friend_user_id','image')->paginate(PAGINATE_LIMIT);;
        $mut_frnds_count = User::whereIn('id',$mutual_friends_ids)->count();
        $mut_frnds->count = $mut_frnds_count;

        return $mut_frnds;
    }

    public static function updateUserFriends($action,$user_id,$friend_id){    //store my friends string in user table
        $user = User::select('id','friend_ids')->where('id',$user_id)->first();
        
        $friend_ids_str = $user->friend_ids;

        $friend_ids_arr = (!empty($friend_ids_str)) ? explode(',',$friend_ids_str) : array();

        if($action == 'add'){
            array_push($friend_ids_arr,$friend_id);
        } else{ //remove

            if( ($key = array_search($friend_id,$friend_ids_arr)) !== false){
                unset($friend_ids_arr[$key]);
            }
        }

        array_filter($friend_ids_arr);
        array_unique($friend_ids_arr);
        sort($friend_ids_arr);

        $new_friend_ids_str = implode(',',$friend_ids_arr);

        $user->friend_ids = $new_friend_ids_str;
        $user->save();
        return true;
    }

    /*public static function getFriends($user_id, $request, $paginate = false){
        $query = FriendRequest::select(
                            'id as friend_request_id','req_user_id','rec_user_id'
                        )
                        ->where( function($query) use ($user_id){
                            $query->orWhere('req_user_id',$user_id);
                            $query->orWhere('rec_user_id',$user_id);
                        })
                        ->where('unfriend_at',null)
                        ->where('status','=','A')
                        ->with('receiverUser')
                        ->has('receiverUser')
                        ->with('receiverUser.userLocation')
                        ->with('senderUser')
                        ->has('senderUser')
                        ->with('senderUser.userLocation')
                        ->orderBy('id','desc');
        if($paginate == true){
            $friend_reqs = $query->paginate(PAGINATE_LIMIT);
            foreach($friend_reqs as $key => $value){
                if($value['req_user_id'] == $user_id){
                    $friend_reqs[$key]['user'] = $friend_reqs[$key]['receiverUser'];
                } else{
                    $friend_reqs[$key]['user'] = $friend_reqs[$key]['senderUser'];
                }
                unset($friend_reqs[$key]['receiverUser']);
                unset($friend_reqs[$key]['senderUser']); 
            }

        } else{
            $friend_reqs = $query->get()->toArray();

            //setting sender or receiver in a single key
            foreach($friend_reqs as $key => $value){
                if($value['req_user_id'] == $user_id){
                    $friend_reqs[$key]['user'] = $friend_reqs[$key]['receiver_user'];
                } else{
                    $friend_reqs[$key]['user'] = $friend_reqs[$key]['sender_user'];
                }
                unset($friend_reqs[$key]['receiver_user']);
                unset($friend_reqs[$key]['sender_user']);    
            }
        }
        return $friend_reqs;
    }*/


}
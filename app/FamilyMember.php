<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FamilyMember extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function userInfo(){
        return $this->belongsTo('App\User','user_id','id')->select('id','first_name','last_name','image');
    }

    public function anotherUserInfo(){
        return $this->belongsTo('App\User','another_user_id','id')->select('id','first_name','last_name','image');
    }

    public function relation(){
        return $this->belongsTo('App\FamilyRelation','family_relation_id','id')->select('id','name');
    }

    public function ValidFriendReq(){ //used to get friend req info if it is still valid
        return $this->belongsTo('App\FriendRequest','friend_req_id','id')
                    ->select('id')
                    ->where('status','A')
                    ->whereNull('unfriend_at');
    }

    public static function validateNewFamilyMember($request){ 
    	//step .1 - check that another user is not already present in our family list. 
    	//step .2 - check another user should be friend of first user 
    	$exists = Self::isFamilyMemberExists($request->user_id,$request->another_user_id); //step.1

    	if($exists == true){
    		return array(
                'status'  => false,
                'message' => 'family already exists'
            );
    	} else{

    		$friend_req_id = FriendRequest::checkFriendship($request->user_id,$request->another_user_id);
    		if(!empty($friend_req_id)) {
    			return array(
                    'status'        => true,
                    'friend_req_id' => $friend_req_id
                );
    		} else{
    			return array(
                    'status'  => false,
                    'message' => 'not friends'
                );
    		}
    	}
    }

    public static function add($request,$friend_req_id){ 

    	$family 							= new FamilyMember;
        $family->friend_req_id              = $friend_req_id;
		$family->user_id  					= $request->user_id;
		$family->another_user_id  			= $request->another_user_id;
		$family->family_relation_id   		= $request->family_relation_id;
		if($family->family_relation_id == 8){ //if other relation
			$family->other_relation_detail  = $request->other_relation_detail;
		}
		if($family->save()){
			return true;
		} else{ 
			return false;
		}
    }

    public static function isFamilyMemberExists($user1_id,$user2_id){

	    return	Self::orWhere( function($query) use($user1_id, $user2_id){
    			$query->where('user_id',$user1_id);
    			$query->where('another_user_id',$user2_id);
    		})
    		->orWhere( function($query)  use($user1_id, $user2_id){
    			$query->where('user_id',$user1_id);
    			$query->where('another_user_id',$user2_id);
    		})
    		->exists();
    }

    public static function MyFamilyMembers($user_id){

        $friend_reqs = Self::select('id','friend_req_id','user_id','another_user_id','family_relation_id','other_relation_detail')
                        ->where( function($query) use ($user_id){
                            $query->orWhere('user_id',$user_id);
                            $query->orWhere('another_user_id',$user_id);
                        })
                        ->whereNull('deleted_at')
                        ->with('relation')
                        ->has('relation')
                        ->with('userInfo')
                        ->has('userInfo')
                        ->with('anotherUserInfo')
                        ->has('anotherUserInfo')
                        ->has('ValidFriendReq')         //checking if friend has been not unfriended
                        ->orderBy('id','desc')
                        ->paginate(PAGINATE_LIMIT)
                        ->toArray();
        
        //making same key for both user info and another user info 
        foreach($friend_reqs['data'] as $key => $value){ //get method
            if($value['user_id'] == $user_id){
                $friend_reqs['data'][$key]['user_info'] = $value['another_user_info'];
            }
            unset($friend_reqs['data'][$key]['another_user_info']);
        }
        return $friend_reqs;

        // Note: only those family members should be shown to the users, if they are friend of that user
    }

    public static function MyFamilyMembersCount($user_id){

        return Self::select('id','friend_req_id','user_id','another_user_id','family_relation_id','other_relation_detail')
                        ->where( function($query) use ($user_id){
                            $query->orWhere('user_id',$user_id);
                            $query->orWhere('another_user_id',$user_id);
                        })
                        ->whereNull('deleted_at')
                        ->with('relation')
                        ->has('relation')
                        ->with(['userInfo' => function($query) use ($user_id){
                            // $query->where('id','<>',$user_id);
                        }])
                        ->whereHas('userInfo', function($query) use ($user_id){
                            // $query->where('id','<>',$user_id);
                        })
                        ->with(['anotherUserInfo' => function($query) use ($user_id){
                            // $query->where('id','<>',$user_id);
                        }])
                        ->whereHas('userInfo', function($query) use ($user_id){
                            // $query->where('id','<>',$user_id);
                        })
                        ->has('ValidFriendReq')         //checking if friend has been not unfriended
                        ->count();

                        // ->orderBy('id','desc')
                        // ->get()
                        // ->toArray();
        
        //making same key for both user info and another user info 
        /*foreach($friend_reqs['data'] as $key => $value){ //get method
            if($value['user_id'] == $user_id){
                $friend_reqs['data'][$key]['user_info'] = $value['another_user_info'];
            }
            unset($friend_reqs['data'][$key]['another_user_info']);
        }
        return $friend_reqs;*/

        // Note: only those family members should be shown to the users, if they are friend of that user
    }

}
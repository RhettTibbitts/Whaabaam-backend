<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use Illuminate\Validation\Rule;

use App\State;
use App\User;
use App\Admin;
use App\Religion;
use App\City;
use App\Military;
use App\Political;
use App\Relationship;
use App\CaptureFilter;
use App\UserImage;
use App\FriendRequest;
use App\FamilyMember;
use App\Traits\QuickBlox;

class ProfileController extends Controller
{
    use QuickBlox;
	
    public function view_my_profile(Request $request){ //view my profile
		
        $validator = Validator::make($request->input(),[
            'user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ]
        ],[
            'user_id.exists' => __('messages.user err')
        ]);

        if ($validator->fails()) {
              return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        } else{
            $user = User::getUser($request->user_id);
			if(!empty($user)){
				$states 			= State::getStates();
                $cities             = City::getCities($user->state_id);
				$from_cities 		= City::getCities($user->from_state_id);
				$militaries			= Military::getMilitaries();
				$politicals			= Political::getPoliticals();
				$religions			= Religion::getReligions();
				$relationships		= Relationship::getRelationships();

	  		 	return response()->json([
			 			'status' => 200,
			 			'message'=> __('messages.My profile detail'),
			 			'data'	 => array(
			 				'user' 			=> $user,
			 				'states' 		=> $states,
                            'cities'        => $cities,
			 				'from_cities'   => $from_cities,
			 				'militaries' 	=> $militaries,
			 				'politicals' 	=> $politicals,
			 				'religions' 	=> $religions,
			 				'relationships' => $relationships,
			 			)
			 		]);				
			}else{
	  		 	return response()->json([
		 			'status' => 400,
                    'message'=> __('messages.user err') 
		 		]);				
			}
		}
	}

	public function edit(Request $request){
	
    	$validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'last_name_access' => 'required',
            'email' => 'required|email|unique:users,email,'.$request->user_id,
            'email_access' => 'required',
            // 'phone' => 'nullable',
            // 'phone_access' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'city_id_access' => 'required',
            'from_state_id' => 'nullable',
            'from_city_id' => 'nullable',
            'from_city_id_access' => 'nullable',
            'occupation' => 'required',
            'occupation_access' => 'required',
            'work_website' => 'required|max:255',
            'work_website_access' => 'required',
            'education' => 'required|max:255',
            'education_access' => 'required',
            'high_school' => 'required|max:255',
            'high_school_access' => 'required',
            'college' => 'required|max:255',
            'college_access' => 'required',
            'alma_matter' => 'required|max:255',
            'alma_matter_access' => 'required',
            'likes' => 'required|max:255',
            'likes_access' => 'required',
            'military_id' => 'required',
            'military_id_access' => 'required',
            'political_id' => 'required',
            'political_id_access' => 'required',
            'religion_id' => 'required',
            'religion_id_access' => 'required',
            'relationship_id' => 'required',
            'relationship_id_access' => 'required',
            'family_access' => 'required',
            'fb_link' => 'nullable',
            'fb_link_access' => 'nullable',
            'insta_link' => 'nullable',
            'insta_link_access' => 'nullable',
            'twit_link' => 'nullable',
            'twit_link_access' => 'nullable',
            'linked_in_link' => 'nullable',
            'linked_in_link_access' => 'nullable',
            'resume' => 'nullable|mimes:pdf,doc,docx',
            // 'image.0' => 'nullable|mimes:jpg,jpeg,png',
            // 'image.1' => 'nullable|mimes:jpg,jpeg,png',
            // 'image.2' => 'nullable|mimes:jpg,jpeg,png'
            // 'capture_distance' => 'required',
        ],[
            'state_id.required' => 'State is required',
            'city_id.required' => 'City is required',
            'military_id.required' => 'Military is required',
            'political_id.required' => 'Political is required',
            'religion_id.required' => 'Religion is required',
            'relationship_id.required' => 'Relationship is required',
        	// 'image.0.mimes' => 'The image must be a file of type: jpg, jpeg, png.',
        	// 'image.1.mimes' => 'The image must be a file of type: jpg, jpeg, png.',
        	// 'image.2.mimes' => 'The image must be a file of type: jpg, jpeg, png.'
        ]);
        
        if ($validator->fails()) {
  		 	return response()->json([
	 			'status' => 400,
	 			'message'=>$validator->errors()->first()
	 		]);
        } else{

			$user = User::editUser($request, $request->user_id);
			if($user == true){

                $this->QuickBloxUserUpdate($request->user_id);                    

	  		 	return response()->json([
		 			'status' => 200,
		 			'message'=> __('messages.profile update succ'),
		 		]);				
			}else{
	  		 	return response()->json([
		 			'status' => 400,
                    'message'=> __('messages.user err') 
		 		]);				
			}

		}	
	}	

    //Notification preference management started
	public function show_capture_options(Request $request){
		$validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
        ],[
            'user_id.exists' => __('messages.user err') 
        ]);
        if ($validator->fails()) {
         	return response()->json([
	 			'status' => 400,
	 			'message'=>$validator->errors()->first()
	 		]);
        } else{		
        	$filter_opts = CaptureFilter::getCaptureFilters();

        	$user = User::select('capture_filter_ids','capture_time_period')
									->where('id',$request->user_id)
									->first();
            $seleced_filter_ids = explode(',',$user->capture_filter_ids);

            foreach($filter_opts as $key => $value){
                if(in_array($value['id'],$seleced_filter_ids)){
                    $filter_opts[$key]['selected'] = true;
                } else{
                    $filter_opts[$key]['selected'] = false;
                }
            }

         	return response()->json([
	 			'status' => (!empty($filter_opts)) ? 200 :400,
	 			'message'=> (!empty($filter_opts)) ? 'Capture Filter options':'No option available',
	 			'data'	 => $filter_opts,
	 			'capture_time_period'   => $user->capture_time_period
	 		]);				
        }
	}

	public function edit_capture_options(Request $request){
		$validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'capture_filter_ids' => 'nullable',
            'capture_time_period' => 'nullable'
        ],[
            'user_id.exists' => __('messages.user err') 
        ]);
        if ($validator->fails()) {
         	return response()->json([
		 			'status' => 400,
         			'message'=>$validator->errors()->first()
		 		]);
        } else{		
        	$updated = User::where('id',$request->user_id)->update([
	        	'capture_filter_ids'  => $request->capture_filter_ids,
	        	'capture_time_period' => $request->capture_time_period						
			]);

         	return response()->json([
	 			'status' => ($updated) ? 200 : 400,
	 			'message'=> ($updated) ? 'Capture options updated successfully' : COMMON_ERR,
	 		]);				
        }
	}
    //Notification preference management ended

    public function view_another_profile(Request $request){ //view another user profile
        $validator = Validator::make($request->input(),[
            'user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ],
            'profile_user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ]
        ],[
            'user_id.exists' => __('messages.user err')
        ]);

        if ($validator->fails()) {
              return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        } else{

            if($request->user_id == $request->profile_user_id){
                $is_friend = true;
                $friend_status = 'FRIEND';
            } else{
                $friend_status = FriendRequest::checkFriendReqStatus($request->user_id,$request->profile_user_id);
                if($friend_status == 'FRIEND'){
                    $is_friend = true;
                } else{
                    $is_friend = false;
                }
                // $is_friend = FriendRequest::checkFriendship($request->user_id,$request->profile_user_id);
                // $is_friend = (!empty($is_friend)) ? true : false;
            }

            $user = User::getAnotherUser($request->profile_user_id,$is_friend);
            
            if(!empty($user)){

                $user['is_friend']  = $is_friend;
                $user['req_status'] = $friend_status;

                //adding user mutual friends
                $mutual_friends = FriendRequest::getMutualFriends($request->user_id,$request->profile_user_id);
                // echo '<pre>'; print_r($mutual_friends->name); die;

                $user['mutual_friends']['data'] = $mutual_friends->items();
                $user['mutual_friends']['current_page'] = $mutual_friends->currentPage();
                $user['mutual_friends']['per_page']     = $mutual_friends->perPage();
                $user['mutual_friends']['last_page']    = $mutual_friends->lastPage();
                $user['mutual_friends']['total']        = $mutual_friends->count;

                if($is_friend == true){

                    //adding user family members
                    $family = FamilyMember::MyFamilyMembers($request->profile_user_id);
                    $user['family']['data']         = $family['data'];
                    $user['family']['current_page'] = $family['current_page'];
                    $user['family']['per_page']     = $family['per_page'];
                    $user['family']['last_page']    = $family['last_page'];
                    $user['family']['total']        = FamilyMember::MyFamilyMembersCount($request->profile_user_id);
                    
                } else{
                    $family_access = User::getFamilyAccess('id',$request->profile_user_id);
                    if($family_access == 1){
                        $user['family'] = FamilyMember::MyFamilyMembers($request->profile_user_id);
                        $user['family']['total'] = FamilyMember::MyFamilyMembersCount($request->profile_user_id);

                    } else{
                        $user['family']['data']         = [];
                        $user['family']['current_page'] = 1;
                        $user['family']['per_page']     = PAGINATE_LIMIT;
                        $user['family']['last_page']    = 1;
                        $user['family']['total']        = 0;
                    }
                }

                return response()->json([
                    'status' => 200,
                    'message'=> __('messages.profile detail'),
                    'data'   => $user
                ]);
            } else{
                return response()->json([
                    'status' => 400,
                    'message'=> __('messages.user err'),
                ]);
            }             
        }
    }

    public function get_cities(Request $request){
        $validator = Validator::make($request->input(),[
            'state_id' => 'required'
        ],[
            'state_id.required' => __('messages.state req err')
        ]);

        if ($validator->fails()) {
              return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        } else{        
            $cities  = City::getCities($request->state_id);
            return response()->json([
                'status' => 200,
                'message'=> (!empty($cities)) ? __('messages.city list') : __('messages.no cities found'),
                'data'   => $cities
            ]);
        }
    }

    public function send_error_email(Request $request) {
        $validator = Validator::make($request->input(),[
             'from_user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1)->where('deleted_at',null);
                })
            ],
            'to_user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1)->where('deleted_at',null);
                })
            ],
            'message' => 'required'
        ]);

        if ($validator->fails()) {
              return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        } else{        
            $sent  = User::sendErrorReportEmail($request);
            
            return response()->json([
                'status' => 200,
                'message'=> (!empty($sent)) ? __('messages.err report sent succ') : __('messages.COMMON_ERR'),
            ]);
        }   
    }

}
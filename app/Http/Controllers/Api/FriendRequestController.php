<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use Illuminate\Validation\Rule;
use App\Traits\PushNotification;
use App\User;
use App\FriendRequest;
use App\Notification;

class FriendRequestController extends Controller
{
    use PushNotification;

    public function friends(Request $request){ //view all friends
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

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        } else{
            $friends = FriendRequest::getFriends($request->user_id,$request);
            return response()->json([
                'status' => 200,
                'message'      => ($friends->count() > 0) ? __('messages.friends') : __('messages.no friend found'),
                'data'         => (!($friends->isEmpty())) ? $friends->items() : [],
                'current_page' => $friends->currentPage(),
                'per_page'     => $friends->perPage(),
                'last_page'    => $friends->lastPage()
            ]);
        }
    }

    public function send_request(Request $request){
        $validator = Validator::make($request->input(),[
            'user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ],
            'receiver_user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ]
        ],[
            'user_id.exists' => __('messages.user err'),
            'receiver_user_id.exists' => __('messages.rec user err')
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        } else{
            
            $resp = FriendRequest::validateNewFriendRequest($request);

            if(!empty($resp)) {
                if($resp == 'ok good'){

                    //create new friend request
                    $frind_req_id = FriendRequest::addNewRequest($request);
                    if(!empty($frind_req_id)) {

                        //save notif.
                        Notification::saveNotif($request->receiver_user_id,$request->user_id,$frind_req_id,'F');  

                        //send push notif.
                        $txt = __('messages.push msg: new frnd req');
                        $this->PushNotify($request->receiver_user_id,$request->user_id,$frind_req_id, 'F', $txt);

                        //mk test start
                            /*$url = 'http://localhost/whabaam/api/send_push';
                            $ch  = curl_init();
                            //$a = file_get_contents($url, true);
                            $headers = array(
                                'Authorization: key=qwe',
                                'Content-Type: application/json'
                            );
                            $msg = 'mk';
                            // Set the url, number of POST vars, POST data
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                            // Disabling SSL Certificate support temporarly
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($msg));

                            // Execute post
                            $result = curl_exec($ch);
                            if ($result === FALSE) {
                                //die('Curl failed: ' . curl_error($ch));
                            }
                            // Close connection
                            curl_close($ch);
                            print_r($result); die;*/
                        //mk end

                        return response()->json([
                            'status' => 200,
                            'message'=> __('messages.frnd req sent succ'),
                        ]);
                    } else{
                        return response()->json([
                            'status' => 400,
                            'message'=>__('messages.COMMON_ERR')
                        ]);
                    }
                } else{
                    return response()->json([
                        'status' => 400,
                        'message'=>__('messages.'.$resp)
                    ]);
                }
            } else{
                return response()->json([
                    'status' => 400,
                    'message'=>__('messages.COMMON_ERR')
                ]);
            }
        }
    }

    public function response_request(Request $request){ //by request receiver

        $user_id = ($request->user_id) ?? null;
        $validator = Validator::make($request->input(),[
            'user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ],
            'another_user_id' => [
                'required'
            ],
            'action' => [
                'required', 
                Rule::in(['A','R'])     //A = Accept, R = Reject
            ]
        ],[
            'user_id.exists' => __('messages.user err'),
            'receiver_user_id.exists' => __('messages.rec user err')
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        } else{
            $frnd_req = FriendRequest::getLastFriendRequest($request->user_id,$request->another_user_id);

            //$frnd_req = FriendRequest::where('rec_user_id',$request->user_id)->where('id',$request->friend_req_id)->first();
             if(!empty($frnd_req)) {
                
                if($frnd_req->status == 'P'){

                    $frnd_req->status         = $request->action;  //A or R         
                    $frnd_req->req_updated_at = date('Y-m-d H:i:s'); 
                    
                    if($frnd_req->save()){

                        if($frnd_req->status == 'A'){
                            
                            //updating friends string of both users
                            FriendRequest::updateUserFriends('add',$request->user_id,$request->another_user_id);
                            FriendRequest::updateUserFriends('add',$request->another_user_id,$request->user_id);

                            $msg =  __('messages.frnd req accept succ');

                        } else{
                            $msg =  __('messages.frnd req reject succ');
                        }

                        return response()->json([
                            'status' => 200,
                            'message'=> $msg,
                        ]);
                    } else{
                        return response()->json([
                            'status' => 400,
                            'message'=>__('messages.COMMON_ERR')
                        ]);
                    }

                } else if($frnd_req->status == 'A'){

                    if( ($frnd_req->is_self_cancelled == 0) && ($frnd_req->unfriend_by == null) && ($frnd_req->unfriend_at == null) ){
                        return response()->json([
                            'status' => 400,
                            'message'=>__('messages.req already accepted')
                        ]);                    
                    } else{ //if cancelled or unfriend
                        return response()->json([
                            'status' => 400,
                            'message'=>__('messages.req not found')
                        ]);                    
                    }
                } else{ //rejected
                    return response()->json([
                        'status' => 400,
                        'message'=>__('messages.req already rejected')
                    ]);                    
                }

            } else{
                return response()->json([
                    'status' => 400,
                    'message'=>__('messages.req not found')
                ]);
            }
        }
    }

    public function cancel_request(Request $request){ //by requester

        $user_id = ($request->user_id) ?? null;
        $validator = Validator::make($request->input(),[
            'user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ],
            'friend_user_id' => [
                'required'
            ]
        ],[
            'user_id.exists' => __('messages.user err'),
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        } else{
            // $frnd_req = FriendRequest::where('req_user_id',$request->user_id)->where('id',$request->friend_req_id)->first();
            $frnd_req = FriendRequest::getLastFriendRequest($request->user_id,$request->friend_user_id);

            if(!empty($frnd_req)) {
                
                if($frnd_req->status == 'P'){

                    $frnd_req->status            = 'R';
                    $frnd_req->is_self_cancelled = 1;
                    $frnd_req->req_updated_at    = date('Y-m-d H:i:s'); 
                    
                    if($frnd_req->save()){

                        return response()->json([
                            'status' => 200,
                            'message'=> __('messages.req cancel succ')
                        ]);
                    } else{
                        return response()->json([
                            'status' => 400,
                            'message'=>__('messages.COMMON_ERR')
                        ]);
                    }
                } else if($frnd_req->status == 'A'){
                    return response()->json([
                        'status' => 400,
                        'message'=>__('messages.req cancel already accepted err')
                    ]);                    
                } else{ //rejected
                    return response()->json([
                        'status' => 400,
                        'message'=>__('messages.req cancel already rejected err')
                    ]);                    
                }

            } else{
                return response()->json([
                    'status' => 400,
                    'message'=>__('messages.req not found')
                ]);
            }
        }
    }

    public function unfriend(Request $request){ //by both requester or sender

        $validator = Validator::make($request->input(),[
            'user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ],
            'friend_user_id' => [
                'nullable',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ],
            'friend_quickblox_id' => [
                'nullable',
                 Rule::exists('users','quickblox_id')->where(function($query){
                    $query->where('status',1);
                })
            ]
        ],[
            'user_id.exists' => __('messages.user err'),
        ]);


        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        } else{
            if(!empty(@$request->friend_user_id)){
                $frnd_req = FriendRequest::getLastFriendRequest($request->user_id,$request->friend_user_id);                
            } else{
                if(!empty(@$request->friend_quickblox_id)){
                    $friend_user_id = User::getUserByQuickbloxId($request->friend_quickblox_id);
                    $frnd_req = FriendRequest::getLastFriendRequest($request->user_id,$friend_user_id);                
                
                } else{
                    return response()->json([
                        'status' => 400,
                        'message' => 'Either friend user id or friend quickblox id is required'
                    ]);
                }
            }

            if(!empty($frnd_req)) {
                if($frnd_req->status == 'A' ) { //validating: friend can be unfriend only if request was accepted

                    if($frnd_req->req_user_id == $request->user_id){
                        $unfriender = 'S';
                    } else if($frnd_req->rec_user_id == $request->user_id) {
                        $unfriender = 'R';
                    }else{
                        return response()->json([
                            'status' => 400,
                            'message'=>__('messages.friend not found')
                        ]);
                    }
                    $frnd_req->unfriend_by = $unfriender;
                    $frnd_req->unfriend_at = date('Y-m-d H:i:s'); 
                    
                    if($frnd_req->save()){

                        //updating friends string of both users
                        FriendRequest::updateUserFriends('remove',$request->user_id,$request->friend_user_id);
                        FriendRequest::updateUserFriends('remove',$request->friend_user_id,$request->user_id);
                        
                        return response()->json([
                            'status' => 200,
                            'message'=> __('messages.unfriend succ')
                        ]);
                    } else{
                        return response()->json([
                            'status' => 400,
                            'message'=>__('messages.COMMON_ERR')
                        ]);
                    }
                } else{ 
                    return response()->json([
                        'status' => 400,
                        'message'=>__('messages.COMMON_ERR')
                    ]);                    
                }
            } else{ 
                return response()->json([
                    'status' => 400,
                    'message'=>__('messages.friend not found')
                ]);
            } 
        }
    }

    public function mutual_friends(Request $request){ 
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

            $mutual_friends = FriendRequest::getMutualFriends($request->user_id,$request->profile_user_id);
            return response()->json([
                'status' => 200,
                'message'      => ($mutual_friends->count() > 0) ? __('messages.mututal friends') : __('messages.no mututal friends'),
                'data'         => (!($mutual_friends->isEmpty())) ? $mutual_friends->items() : [],
                'current_page' => $mutual_friends->currentPage(),
                'per_page'     => $mutual_friends->perPage(),
                'last_page'    => $mutual_friends->lastPage()
            ]);             
        }
    }

    public function check_is_friend(Request $request){
        $validator = Validator::make($request->input(),[
            'user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ],
            'quickblox_id' => 'required'
        ],[
            'user_id.exists' => __('messages.user err')
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        } else{

            $another_user_id = User::getUserByQuickbloxId($request->quickblox_id);
            if(!empty($another_user_id)){
                $status = FriendRequest::checkFriendReqStatus($request->user_id,$another_user_id);
                
                if($status == 'FRIEND') {
                    return response()->json([
                        'status' => 200,
                        'friend' => 'YES',
                        'message' => __('messages.available for chat')
                    ]);
                } else{
                    return response()->json([
                        'status' => 400,
                        'friend' => 'NO',
                        'message' => __('messages.you can not chat user not in connection')
                    ]);
                }


            } else{
                return response()->json([
                    'status' => 400,
                    'message' => __('messages.user err')
                ]); 
            }
        }
    }
    
}
<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use Illuminate\Validation\Rule;
use App\Traits\PushNotification;

use App\FriendRequest;
use App\Notification;

class NotificationController extends Controller
{
    use PushNotification;
    
    public function index(Request $request){
	    
    	$validator = Validator::make($request->input(),[
    		'user_id' => [
    			'required',
    			Rule::exists('users','id')->where(function($query){
    				$query->where('status',1);
    			})
    		]
    	]);

    	if($validator->fails()) {
    		return response()->json([
    			'status'  => 400,
    			'message' => $validator->errors()->first() 
    		]);
    	}
	    
	    $notifs = Notification::getNotifications($request->user_id);
        /*foreach($notifs['data'] as $key => $value){
            if($value['event_type'] == 'C'){ //close by
                $res = FriendRequest::checkFriendReqStatus($request->user_id,$value['profile_user_id']);
           
            } else{ //is friend req

                $status = FriendRequest::where('id',$value['event_id'])->value('status');
                if($status == 'P'){
                    $status_txt = 'REQ_SENT';
                }elseif($status == 'R'){
                    $status_txt = 'REQ_REJECTED';
                    // $status_txt = 'NEW_USER'; //REQ_REJECTED
                } elseif($status == 'A'){
                    $status_txt = 'FRIEND';
                } else{                         //if req not found
                    $status_txt = 'NEW_USER';
                }
                $res = $status_txt;
            }
            $notifs['data'][$key]['req_status'] = $res;
        }*/
        
        /*foreach($notifs['data'] as $key => $value){
            $res = FriendRequest::checkFriendReqStatus($request->user_id,$value['profile_user_id']);
            $notifs['data'][$key]['req_status'] = $res;
        }*/

        return response()->json([
            'status'       => 200,
            'message'      => (count($notifs) > 0) ? __('messages.notifs') : __('messages.no notifs'),
            'data'         => $notifs['data'],
            'current_page' => $notifs['current_page'],
            'per_page'     => $notifs['per_page'],
            'last_page'    => $notifs['last_page']
        ]);
    }

    public function send_push(Request $request){
        $txt = __('messages.push msg: new frnd req');
        $this->PushNotify(1,14,1, 'F', $txt);
    }

}

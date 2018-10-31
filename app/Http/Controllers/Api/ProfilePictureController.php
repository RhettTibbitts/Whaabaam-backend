<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use Illuminate\Validation\Rule;
use App\UserImage;
use App\Traits\QuickBlox;

class ProfilePictureController extends Controller
{
    use QuickBlox;
    
    public function upload_profile_pic(Request $request){ //both main & m=normal user image
        
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required|exists:users,id',
            'image'     => 'required|mimes:jpg,jpeg,png',
            'image_type'=> [
                'required',
                Rule::in(['1','0']) //0 = normal, 1 = main
            ]
        ],[
            'user_id.exists' => __('messages.user err'),
            'image.mimes'    => __('messages.img ext err')
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message'=>$validator->errors()->first()
            ]);
        } else{ 
            
            $resp = UserImage::saveUserImage($request,$request->user_id);  //echo $resp; die;
            if($resp['status'] === true){

                //updating user's quickblox image
                if($request->image_type == '1'){ //main image
                    $this->QuickBloxUserUpdate($request->user_id);                    
                }

                return response()->json([
                    'status' => 200,
                    'message'=> __('messages.profile pic upload succ'),
                    'data'   => $resp['data']
                ]);             
            } else{ 
                if($resp['status'] == 'max_limit'){
                    $msg = __('messages.max img count exceed');                   
                } elseif($resp['status'] == 'ext_err'){
                    $msg = __('messages.img ext err');                   
                } else{
                    $msg = __('messages.COMMON_ERR');                   
                }

                return response()->json([
                    'status' => 400,
                    'message'=> $msg
                ]);                             
            }
        }
    }

    public function delete_profile_pic(Request $request){ //delete user normal pic

        $user_id = $request->user_id ?? null;
		$validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'user_image_id' => [ //checking is image is of same user
                'required',
                Rule::exists('user_images','id')->where(function($query) use ($user_id){
                    $query->where('user_id',$user_id);
                } )
            ]
        ],[
            'user_id.exists' => __('messages.user err'),
            'user_image_id.exists' => __('messages.user img exist err'),

        ]);
        if ($validator->fails()) {
         	return response()->json([
	 			'status' => 400,
     			'message'=>$validator->errors()->first()
	 		]);
        } else{	
            $deleted = UserImage::deleteUserImage($request->user_image_id);
    		if($deleted === true){

	         	return response()->json([
		 			'status' => 200,
		 			'message'=> __('messages.profile pic del succ'),
		 		]);				
    		} else{
	         	return response()->json([
		 			'status' => 400,
		 			'message'=> __('messages.COMMON_ERR')
		 		]);				        		
    		}
        }
	}

    public function delete_profile_main_pic(Request $request){ //delete user main pic
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
            
            $deleted = UserImage::deleteUserMainImage($request->user_id);
            if($deleted){

                $this->QuickBloxUserUpdate($request->user_id);                    

                return response()->json([
                    'status' => 200,
                    'message'=> __('messages.profile pic del succ'),
                ]);             
            } else{
                return response()->json([
                    'status' => 400,
                    'message'=> __('messages.COMMON_ERR')
                ]);                             
            }     
        }
    }

    public function set_profile_main_pic(Request $request){ 
        
        $user_id = $request->user_id ?? null;
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'user_image_id' => [ //checking is image is of same user
                'required',
                Rule::exists('user_images','id')->where(function($query) use ($user_id){
                    $query->where('user_id',$user_id);
                } )
            ]
        ],[
            'user_id.exists'        => __('messages.user err'),
            'user_image_id.exists'  => __('messages.user img exist err'),
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message'=>$validator->errors()->first()
            ]);
        } else{ 

            $resp = UserImage::setUserMainImage($request);
            if($resp){

                $this->QuickBloxUserUpdate($request->user_id);                    
                
                return response()->json([
                    'status' => 200,
                    'message'=> __('messages.profile pic set succ')
                ]);             
            } else{
                return response()->json([
                    'status' => 400,
                    'message'=> __('messages.COMMON_ERR')
                ]);                             
            }
        }
    }

    public function delete_resume(Request $request){ //delete resume
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
            
            $deleted = UserImage::deleteResume($request->user_id);
            if($deleted){ 
                return response()->json([
                    'status' => 200,
                    'message'=> __('messages.resume del succ'),
                ]);             
            } else{
                return response()->json([
                    'status' => 400,
                    'message'=> __('messages.COMMON_ERR')
                ]);                             
            }     
        }
    }

}
<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use Illuminate\Validation\Rule;

use App\UserNote;

class UserNoteController extends Controller
{
	public function index(Request $request){ 
		
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
            ],
        ],[
            'user_id.exists' => __('messages.user err'),
            'profile_user_id.exists' => __('messages.user err')
        ]);
        if ($validator->fails()) {
              return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        } else{
           
            $notes = UserNote::getUserNotes($request);
  		 	return response()->json([
	 			'status'       => 200,
	 			'message'      => ($notes->count() > 0) ? __('messages.user notes') : __('messages.no user note'),
                'data'         => $notes->items(),
                'current_page' => $notes->currentPage(),
                'per_page'     => $notes->perPage(),
                'last_page'    => $notes->lastPage(),
	 		]);				
		}
	}

    public function add(Request $request){
        
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
            ],
            'note' => [
                'required',
                'max:6000'
            ]
        ],[
            'user_id.exists' => __('messages.user err'),
            'profile_user_id.exists' => __('messages.user err')
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message'=>$validator->errors()->first()
            ]);
        } else{

            $user = UserNote::addUserNote($request);
            if($user == true){
                return response()->json([
                    'status' => 200,
                    'message'=> __('messages.note add succ'),
                ]);             
            }else{
                return response()->json([
                    'status' => 400,
                    'message'=> __('messages.COMMON_ERR') 
                ]);             
            }
        }   
    }

    public function edit(Request $request){
        $user_id = (isset($request->user_id)) ? $request->user_id : '';

        $validator = Validator::make($request->input(),[
            'user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ],
            'note' => [
                'required',
                'max:6000'
            ],
            'user_note_id' => [
                'required',
                Rule::exists('user_notes','id')->where(function($query) use ($user_id){
                    $query->where('user_id',$user_id);
                })
            ]
        ],[
            'user_id.exists' => __('messages.user err'),
            'user_note_id.exists' => __('messages.note not found')
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message'=>$validator->errors()->first()
            ]);
        } else{

            $user = UserNote::editUserNote($request);
            if($user == true){
                return response()->json([
                    'status' => 200,
                    'message'=> __('messages.note update succ'),
                ]);             
            }else{
                return response()->json([
                    'status' => 400,
                    'message'=> __('messages.COMMON_ERR') 
                ]);             
            }
        }   
    }

    public function delete(Request $request){
        $user_id = (isset($request->user_id)) ? $request->user_id : '';

        $validator = Validator::make($request->input(),[
            'user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ],
            'user_note_id' => [
                'required',
                Rule::exists('user_notes','id')->where(function($query) use ($user_id){
                    $query->where('user_id',$user_id);
                })
            ]
        ],[
            'user_id.exists' => __('messages.user err'),
            'user_note_id.exists' => __('messages.note not found')
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message'=>$validator->errors()->first()
            ]);
        } else{

            $user = UserNote::deleteUserNote($request);
            if($user == true){
                return response()->json([
                    'status' => 200,
                    'message'=> __('messages.note del succ'),
                ]);             
            }else{
                return response()->json([
                    'status' => 400,
                    'message'=> __('messages.COMMON_ERR') 
                ]);             
            }
        }   
    }

}
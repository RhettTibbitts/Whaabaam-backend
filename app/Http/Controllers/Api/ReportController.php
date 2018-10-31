<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Auth; 
use App\UserReport, App\User; 

class ReportController extends Controller 
{
    public function add(Request $request) {        

        $validator = Validator::make($request->input(), [ 
            'user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ],
            'to_user_id' => [
                'nullable',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ],
            'to_quickblox_id' => [
                'nullable',
                Rule::exists('users','quickblox_id')->where(function($query){
                    $query->where('status',1);
                })
            ]
        ],[
            'to_user_id.exists'   => __('messages.another user err'),
            'to_user_id.required' => __('messages.another user err'),
            'user_id.exists'   => __('messages.user err'),
            'user_id.required' => __('messages.user err'),
        ]);

        if ($validator->fails()) { 
            return response()->json([
                'status' => 400,
                'message'=> $validator->errors()->first() 
            ]);
        }else{
           
            if( (empty(@$request->to_user_id)) && (empty(@$request->to_quickblox_id)) ){
                return response()->json([
                    'status' => 400,
                    'message' => 'Either to user id or quickblox id is required'
                ]);

            } else{
                if(!empty(@$request->to_user_id)) {
                    $to_user_id = $request->to_user_id;
                } else{
                    $to_user_id = User::getUserByQuickbloxId($request->to_quickblox_id);
                }

                $updated = UserReport::add($request->user_id,$to_user_id);         
                if($updated){
                    return response()->json(['status'=>200,'message'=>trans('messages.report succ') ]); 
                } else{
                    return response()->json(['status'=>400,'message'=>trans('messages.common err')]); 
                }
            }

        } 
    } 

}

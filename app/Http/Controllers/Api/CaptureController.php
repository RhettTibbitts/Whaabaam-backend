<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use Illuminate\Validation\Rule;

use App\User;
use App\UserLocation;
use App\CapturedUser;
use Carbon\Carbon;

class CaptureController extends Controller
{
    public function index(Request $request){ //show captured users 
        $validator = Validator::make($request->input(),[
            'user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ],
            'date' => 'nullable|date_format:"Y-m-d"',
            'filters' => [
                'nullable',
                'array',
                Rule::in(['city_id','state_id','occupation','education','high_school','college','alma_matter','relationship_id','political_id','religion_id','military_id','likes','hide_friends','hide_strangers'])
            ],
            'search' => 'nullable'
        ],[
            'user_id.exists' => __('messages.user err')
        ]);

        if($validator->fails()){

            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        } else{
            $users = CapturedUser::getCapturedUsers($request); 
            return response()->json([
                'status' => 200,
                'message'      => (count($users) > 0) ? __('messages.close users') : __('messages.no close user'),
                'data'         => (!(empty($users))) ? $users['data'] : [],
                'current_page' => $users['current_page'],
                'per_page'     => $users['per_page'],
                'last_page'    => $users['last_page']
            ]);
        }
    } 

    public function save_location(Request $request){
        $validator = Validator::make($request->input(),[
            'user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1)->where('deleted_at',null);
                })
            ],
            'lat' => 'required',
            'lng' => 'required',
            'address' => 'required'
        ],[
            'user_id.exists' => __('messages.user err')
        ]);

        if($validator->fails()){

            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        } else{

            $last_loc_time = UserLocation::getLastLocationTimestamp($request->user_id);
            $last_loc_time = Carbon::parse($last_loc_time);
            $min_diff = $last_loc_time->diffInMInutes();
            
            if($min_diff >= 4){

                $loc_id = UserLocation::create($request->input())->id;
                if(!empty($loc_id)){
        
                    return response()->json([
                        'status' => 200,
                        'message'=> __('messages.location save success')
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
                    'message'=>__('messages.location interval err')
                ]);
            }
        }
    }

}
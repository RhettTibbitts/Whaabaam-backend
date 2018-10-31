<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use Illuminate\Validation\Rule;

use App\User;

class SearchController extends Controller
{
    public function index(Request $request){ //show captured users 
        $validator = Validator::make($request->input(),[
            'user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
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
            $users = User::searchUsers($request->user_id,$request); 

            $data = (isset($users['data'])) ? $users['data'] : [];

            // echo '<pre>'; print_r($users); die;
            return response()->json([
                'status'       => ($users['total'] > 0) ? 200 : 400,
                'message'      => ($users['total'] > 0) ? __('messages.users') : __('messages.no user'),
                'data'         => $data,
                'current_page' => $users['current_page'],
                'per_page'     => $users['per_page'],
                'last_page'    => $users['last_page']
            ]);

        }
    } 

}
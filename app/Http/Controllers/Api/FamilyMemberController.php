<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use Illuminate\Validation\Rule;

use App\FamilyRelation;
use App\FamilyMember;
use App\FriendRequest;

class FamilyMemberController extends Controller
{
    public function show_relations(){
        $relations = FamilyRelation::getFamilyRelations();

        return response()->json([
            'status'    => 200,
            'message'   => (!empty($relations)) ? __('messages.relations list') : __('messages.no relation found'),
            'data'      => $relations
        ]);
    }

    public function add(Request $request){
        $validator = Validator::make($request->input(),[
            'user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ],
            'another_user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ],
            'family_relation_id' => [
                'required',
                Rule::exists('family_relations','id')
            ],
            'other_relation_detail' => [
                'required_if:family_relation_id,==,8'
            ]
        ],[
            'user_id.exists'                => __('messages.user err'),
            'another_user_id.exists'        => __('messages.rec user err'),
            'family_relation_id.required'   => __('messages.family rel is req'),
            'other_relation_detail.required_if' => __('messages.other rel is req'),
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        } else{
            $resp = FamilyMember::validateNewFamilyMember($request);
            // echo '<pre>'; print_r($resp); 
            if($resp['status'] === true){

                $added = FamilyMember::add($request,$resp['friend_req_id']);

                if($added == true){
                    return response()->json([
                        'status' => 200,
                        'message'=> __('messages.family add succ'),
                    ]);
                } else{
                    return response()->json([
                        'status' => 400,
                        'message'=>__('messages.COMMON_ERR')
                    ]);
                }
            } else{
                if($resp['message'] === 'family already exists'){ 
                    $msg = __('messages.family already added');

                } else if($resp['message'] === 'not friends'){ 
                    $msg = __('messages.family add err: user is not a friend');
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

    public function delete(Request $request){
        $validator = Validator::make($request->input(),[
            'family_member_id' => [
                'required',
                Rule::exists('family_members','id')->where(function($query){
                    $query->where('deleted_at',null);
                })
            ],
            'user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ]
        ],[
            'family_member_id.exists'   => __('messages.family member err'),
            'user_id.exists'            => __('messages.user err')
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        } else{     
            $fam_mem = FamilyMember::where('id',$request->family_member_id)->first();

            if($fam_mem->user_id == $request->user_id){
                $fam_mem->deleted_by = 1;

            } else if($fam_mem->another_user_id == $request->user_id){
                $fam_mem->deleted_by = 2;

            } else{  
                return response()->json([
                    'status' => 400,
                    'message'=> __('messages.COMMON_ERR')
                ]); 
            }
            $fam_mem->deleted_at = date('Y-m-d H:i:s');
            
            if($fam_mem->update()){ 
                return response()->json([
                    'status' => 200,
                    'message'=> __('messages.family member del succ')
                ]);
            } else{
                return response()->json([
                    'status' => 400,
                    'message'=> __('messages.COMMON_ERR')
                ]); 
            }

        }  
    }

    public function show_friends_to_add_in_family(Request $request){

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

            $friends = FriendRequest::getFriendsToAddInFamily($request); //echo '<pre>'; print_r($friends); die;
            return response()->json([
                'status'       => 200,
                'message'      => ($friends->count() > 0) ? __('messages.friends') : __('messages.no friend found'),
                'data'         => (!($friends->isEmpty())) ? $friends->items() : [],
                'current_page' => $friends->currentPage(),
                'per_page'     => $friends->perPage(),
                'last_page'    => $friends->lastPage()
            ]);
        }
    }

    public function my_family_members(Request $request){ 

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
            
            $members = FamilyMember::MyFamilyMembers($request->user_id);
            return response()->json([
                'status'    => 200,
                'message'   => (!empty($members)) ? __('messages.family members') : __('messages.no family member found'),
                'data'         => (!(empty($members))) ? $members['data'] : [],
                'current_page' => $members['current_page'],
                'per_page'     => $members['per_page'],
                'last_page'    => $members['last_page']
            ]);
        }       
    }

    public function another_user_family_members(Request $request){ 

        $validator = Validator::make($request->input(),[
            'user_id' => [
                'required',
                Rule::exists('users','id')->where(function($query){
                    $query->where('status',1);
                })
            ],
            'another_user_id' => [
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
            
            $members = FamilyMember::MyFamilyMembers($request->user_id);
            return response()->json([
                'status'    => 200,
                'message'   => (!empty($members)) ? __('messages.family members') : __('messages.no family member found'),
                'data'         => (!(empty($members))) ? $members['data'] : [],
                'current_page' => $members['current_page'],
                'per_page'     => $members['per_page'],
                'last_page'    => $members['last_page']
            ]);
        }       
    }

}
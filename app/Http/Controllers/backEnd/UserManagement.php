<?php

namespace App\Http\Controllers\backEnd;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUser;

use App\User;
use App\Military;
use App\Political;
use App\Religion;
use App\Relationship;
use App\State;
use App\City;
use App\UserImage;
use Hash;
use App\Traits\QuickBlox;

class UserManagement extends Controller
{
	use QuickBlox;

	public function index(Request $request){

		$users = User::select('id','first_name','last_name','email','status')->orderBy('id','desc')->get();
		
		return view('backEnd.user.index', compact('users'));
	}

	public function add(StoreUser $request){

		if($request->isMethod('post')){
		
			$user = User::addUser($request);
			if($user != false){
				
				$quick_resp = $this->QuickBloxRegistration($user);

				return redirect('/admin/users')->with('success','User has been saved successfully');
			} else{
				return redirect()->back()->with('error',COMMON_ERR);
			}
		}

		$states 			= State::getStates();
		$cities 			= array();
		$from_cities		= array();
		$militaries			= Military::getMilitaries();
		$politicals			= Political::getPoliticals();
		$religions			= Religion::getReligions();
		$relationships		= Relationship::getRelationships();
	
		return view('backEnd.user.form', compact('countries','states','cities','from_cities','militaries','politicals','religions','relationships'));
	}

	public function edit(StoreUser $request,$user_id = null){

		
		if($request->isMethod('post')){
		
			$user = User::editUser($request, $user_id);
			if($user != false){
                $this->QuickBloxUserUpdate($user_id);

				return redirect('/admin/users')->with('success','User has been edited successfully');
			} else{
				return redirect()->back()->with('error',COMMON_ERR);
			}
		}

		$user = User::with('images')->where('id',$user_id)->first();		
		if(!empty($user)){
			$states 			= State::getStates();
			$cities 			= City::getCities($user->state_id);
			$from_cities 		= City::getCities($user->from_state_id);
			$militaries			= Military::getMilitaries();
			$politicals			= Political::getPoliticals();
			$religions			= Religion::getReligions();
			$relationships		= Relationship::getRelationships();
			
			return view('backEnd.user.form', compact('user','militaries','politicals','religions','relationships','states','cities','from_cities'));
		} else{
			return redirect()->back()->with('error','User not found');
		}
	}

	public function delete(Request $request, $user_id = null){
		$deleted = User::where('id',$user_id)->delete();
		if($deleted){
			return redirect('admin/users')->with('success','User has been deleted successfully');
		} else{
			return redirect()->back()->with('error',COMMON_ERR);
		}
	}

	public function delete_image($usr_img_id = null){
		$img = UserImage::find($usr_img_id);
		if(!empty($img)){
			$user_id = $img->user_id;
			$deleted = UserImage::deleteUserImage($usr_img_id);
			if($deleted == true){
				return redirect('admin/user/edit/'.$user_id)->with('success','Image has been deleted successfully');
			} else{
				return redirect()->back()->with('error',COMMON_ERR);
			}
		} else{
			return redirect()->back()->with('error',COMMON_ERR);
		}
	}

	public function delete_resume($user_id = null){
		$deleted = UserImage::deleteResume($user_id);
     	if($deleted == true){
			return redirect('admin/user/edit/'.$user_id)->with('success','Resume has been deleted successfully');
		} else{
			return redirect()->back()->with('error',COMMON_ERR);
		}
	}

	public function delete_main_image($user_id = null){
		$deleted = UserImage::deleteUserMainImage($user_id);
     	if($deleted == true){
            $this->QuickBloxUserUpdate($user_id);

			return redirect('admin/user/edit/'.$user_id)->with('success','Main image has been deleted successfully');
		} else{
			return redirect()->back()->with('error',COMMON_ERR);
		}
	}
}
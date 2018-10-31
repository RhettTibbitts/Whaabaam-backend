<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Session;
use Hash;
use Validator;
use Illuminate\Validation\Rule;

use App\User;
use App\UserApiToken;
use App\UserDevice;
use Carbon\Carbon;
use App\Traits\QuickBlox;
use App\Jobs\CloseByUsersCron;

class AuthController extends Controller
{
	use QuickBlox;
	public function login(Request $request){
		
		//testing quickblox
		// $user = User::getUserByEmail($request->email);
		// $this->QuickBloxRegistration($user);
		// die;
		//testing end

    	// echo $this->generateRegisteredUserQuickBloxPassword(210); die;


		// echo base64_encode('ABCD1234'); die;
        // $d = $this->QuickBloxUserUpdate(2);
        //  echo '<pre>'; print_r($d); die;


		if($request->isMethod('post')) {

			$validator = Validator::make($request->all(), [
				'email' => [
					'required',
					'email',
					Rule::exists('users')->where(function($query){
						$query->where('status',1);
					})
				],
				'password' 			=> 'required',
				'device_type' 		=> 'nullable',
				'device_fcm_token' 	=> 'nullable'
	        ],[
	        	'email.exists' 		=> __('messages.user err')
	        ]);

	        if ($validator->fails()) {
	  		 	return response()->json([
		 			'status' => 400,
		 			'message'=> $validator->errors()->first()
		 		]);
	        } else{
						
				$user = User::getUserByEmail($request->email);
	        	if($user->status == 1){
					if(Hash::check($request->password,$user->password)){
						$user_id = $user->id;

						//save device fcm token - used for push notifications
						UserDevice::saveDevice($request,$user_id);
						
						//save api token - used for checking is user loggged in from the same device
						$token = UserApiToken::saveUserApiToken($user_id);					

						//register in quick blox
						if(empty($user->quickblox_id)){
							$res = $this->QuickBloxRegistration($user);
							if(@$res['status'] === true){
								$user->quickblox_id = @$res['quickblox_id'];
							}
						}

						if(!empty($token)){

							return array(
								'status' 	=> 200,
								'message' 	=> __('messages.login success'),
								'data' 		=> $user,
								'token'	    => $token,
								'max_user_pics' => MAX_USER_PICS
							);
						} else{
							return array(
								'status' 	=> 400,
								'message' 	=> __('messages.COMMON_ERR')
							);					
						}
					} else{ 
						return array(
							'status' 	=> 400,
							'message' 	=> __('messages.incorrect pass err')
						);
					}
				} else{
					return array(
						'status' 	=> 400,
						'message' 	=> __('messages.account inactive')
					);
				}
			}
		}
	}

	public function logout(Request $request){
		$validator = Validator::make($request->all(), [
            'user_id' => [
            	'required',
        		Rule::exists('users','id')->where(function($query){
					$query->where('status',1);
				})
        	]
        ]);
        if ($validator->fails()) {
  		 	return response()->json([
	 			'status' => 400,
	 			'message'=>$validator->errors()->first()
	 		]);
        } else{

			UserApiToken::where('user_id',$request->user_id)->update(['token' => '']);
			UserDevice::where('user_id',$request->user_id)->update(['token' => '']);
			return array(
				'status' 	=> 200,
				'message' 	=> __('messages.logout success')
			);
        }
	}

	public function signup(Request $request){

		$validator = Validator::make($request->all(), [
            'first_name' 		=> 'required',
			'last_name' 		=> 'required',
			'email' 			=> 'required|email|unique:users',
			'password' 			=> 'required',
			'device_type' 		=> 'nullable',
			'device_fcm_token' 	=> 'nullable'
        ]);
        if ($validator->fails()) {
  		 	return response()->json([
		 			'status' => 400,
		 			'message'=>$validator->errors()->first()
		 		]);
        } else{
        	$user = new User;

        	/*//register in quick blox - before register in our system
			$quick_resp = $this->QuickBloxRegistration($request);
			if($quick_resp['status'] === true){
				$user->quickblox_id = $quick_resp['quickblox_id'];
			} else{
				return array(
					'status' 	=> 400,
					'message' 	=> __('messages.COMMON_ERR')
				);
			}*/

        	$user->first_name = $request->first_name;
        	$user->last_name = $request->last_name;
        	$user->email = $request->email;
        	$user->password = Hash::make($request->password);
        	$user->capture_time_period = LOCATION_UPDATE_DEFAULT_INTERVAL;

        	if($user->save()){

        		User::sendRegisterationEmail($user->id);

        		//login response start
        		//save device fcm token
				UserDevice::saveDevice($request,$user->id);
				
				//save api token
				$token = UserApiToken::saveUserApiToken($user->id);					
				
				//register in quick blox - before register in our system
				$quick_resp = $this->QuickBloxRegistration($user);
				
			 	// $this->dispatch(new CloseByUsersCron()); //coment it, because now cron job is running after every 1 mint
			
				//$cron = file_get_contents('http://whaabaam.com/backend/api/cron/capture-users');

				// Dispatch Job 
				// $this->dispatch((new CloseByUsersCron()) ->onQueue('process-data-queue') ->delay(5));

				if(!empty($token)){

					$user = User::getUser($user->id);

					return array(
						'status' 	=> 200,
			 			'message'=> __('messages.register success'),
						'data' 		=> $user,
						'token'	    => $token,
						'max_user_pics' => MAX_USER_PICS,
						// 'cron' => $cron
					);
				} else{
					return array(
						'status' 	=> 400,
						'message' 	=> __('messages.COMMON_ERR')
					);					
				}
        		//login response end
      
        	} else{
				return array(
					'status' 	=> 400,
					'message' 	=> __('messages.COMMON_ERR')
				);					
			}
		}
	}

	public function change_password(Request $request){
		
		$validator = Validator::make($request->all(),[
			"user_id" => 'required',
			"current_password" => 'required|min:5',
			"new_password" => 'required'
		]);

		if($validator->fails()){
  		 	return response()->json([
	 			'status' => 400,
	 			'message'=>$validator->errors()->first()
	 		]);
		} else{
			$user = User::where('id',$request->user_id)->select('id','password')->first();
			if(!empty($user)){
				if(Hash::check($request->current_password,$user->password)){

					$user->password = Hash::make($request->new_password);
					if($user->save()){
			  		 	return response()->json([
				 			'status' => 200,
				 			'message'=> __('messages.pass change succ')
				 		]);	
					} else{
			  		 	return response()->json([
				 			'status' => 400,
				 			'message'=> __('messages.COMMON_ERR')
				 		]);									
					}

				} else{
		  		 	return response()->json([
			 			'status' => 400,
			 			'message'=> __('messages.cur pass incorrect') 
			 		]);									
				}	
			}else{
	  		 	return response()->json([
		 			'status' => 400,
		 			'message'=> __('messages.user err') 
		 		]);				
			}
		}
	}

	public function forgot_password(Request $request){ //send email

		$validator = Validator::make($request->all(),[
			'email' => [
				'required',
				Rule::exists('users')->where(function ($query) {                      
		            $query->where('status', 1);                  
		        })
			]
		],[
			'email.exists' => __('messages.email not exist err')
		]);

		if($validator->fails()) {
			return response()->json([
	 			'status' => 400,
	 			'message'=>$validator->errors()->first()
	 		]);
		} else{
			$email_sent = User::sendForgotPassEmail($request->email);
			
       		return response()->json([
	 			'status' => 200,
	 			'message'=> __('messages.forgot pass email success')
	 		]);					
		}
	}
	
	public function forgot_password_verify(Request $request){ //check otp

		$validator = Validator::make($request->all(),[
			'email' => [
				'required',
				Rule::exists('users')->where(function ($query) {                      
		            $query->where('status', 1);                  
		        })
			],
			'verify_code' => 'required'
		],[
			'email.exists' => __('messages.email not exist err')
		]);

		if($validator->fails()) {
			return response()->json([
	 			'status' => 400,
	 			'message'=>$validator->errors()->first()
	 		]);
		} else{
			$user = User::select('id','verify_code_created_at')
						->where('email',$request->email)
						->where('verify_code',$request->verify_code)
						->first();
			if(!empty($user)){
	
				$cu = date('Y-m-d H:i:s');
				$to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $cu);
				$from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $user->verify_code_created_at);
				$diff_in_minutes = $to->diffInMinutes($from);
				if($diff_in_minutes <= 15){

					$user->security_code = str_random(10);
					if($user->save()){
						return response()->json([
				 			'status' => 200,
				 			'message'=> __('messages.otp verify succ'),
				 			'security_code' => $user->security_code
				 		]);
					} else{
						return response()->json([
				 			'status' => 400,
				 			'message'=> __('messages.COMMON_ERR')
				 		]);
					}

				} else{
					return response()->json([
			 			'status' => 400,
			 			'message'=>__('messages.otp expired')
			 		]);					
				}
			} else{
				return response()->json([
		 			'status' => 400,
		 			'message'=>__('messages.otp invalid')
		 		]);
			}
		}
	}

	public function forgot_password_set_password(Request $request){ //set new password
		$email = $request->email ?? null;
		$validator = Validator::make($request->all(),[
			'email' => [
				'required',
				Rule::exists('users')->where(function($query){
					$query->where('status',1);
				})
			],
			'security_code' => [
				'required',
				Rule::exists('users')->where(function($query) use ($email) {
					$query->where('email',$email);
				})
			],
			'password' => 'required'
		],[
			'email.exists' => __('messages.email not exist err'),
			'security_code.exists' => __('messages.COMMON_ERR') //if security code not correct
		]);

		if($validator->fails()) {
			return response()->json([
	 			'status' => 400,
	 			'message'=>$validator->errors()->first()
	 		]);
		} else{ 
			//set new password and reset the forget pass verify code fields
			$updated = User::where('email',$request->email)
						->where('security_code',$request->security_code)
						->update([
							'password' => Hash::make($request->password),
							'verify_code' => '',
							'verify_code_created_at' => null,
							'security_code' => ''
						]);

			if(!empty($updated)){
	
				return response()->json([
		 			'status' => 200,
		 			'message'=> __('messages.password set success'),
		 		]);
			} else{
				return response()->json([
		 			'status' => 400,
		 			'message'=>__('messages.otp try again err')
		 		]);
			} 
    	}
	}

}
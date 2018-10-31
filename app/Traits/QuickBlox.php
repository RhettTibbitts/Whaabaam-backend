<?php
namespace App\Traits;
use App\User;

trait QuickBlox{
	
    function generateRegisteredUserQuickBloxPassword($user_id){
        $constant_pass = 'WXYZ1234';
        return base64_encode($user_id.$constant_pass);
    }

    public function QuickBloxRegistration($user){ 
        $resp = $this->getQuickBloxSessionToken();
        if($resp['status'] == true){
            $token = $resp['token']; 
        } else{
            return array(
                'status' => false,
                'message' => ''             
            );
        }

        //$url = 'https://api.quickblox.com/session.json';
        $url = 'https://api.quickblox.com/users.json';

        $data = array(
            'user' => array(
                'email'     => $user->email,
                'password'  => $this->generateRegisteredUserQuickBloxPassword($user->id),
                'full_name' => $user->first_name.' '.$user->last_name,
                'phone'     => $user->phone,
                'login'     => $user->id,
                // 'external_user_id' => $user->id,    //no more use
            )
        );
        //pass should be min 8 characters //we use email as pass

        $headers = array(
            'Content-Type:application/json',
            'QuickBlox-REST-API-Version: 0.1.0',
            'QB-Token: '.$token
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $result = curl_exec($ch);
        if($result == false){
            die('quickblox register req failed: '.curl_error($ch));
        }
        curl_close($ch);
        $result = json_decode($result,true);

        if(isset($result['user'])) {
            User::where('id',$result['user']['login'])->update(['quickblox_id' => $result['user']['id'] ]);

            return array(
                'status' => true,
                'quickblox_id' => $result['user']['id']             
            );
        } else{
            return array(
                'status' => false,
                'message' => 'register curl error'
            );
        } 
    }

    public function getQuickBloxSessionToken(){ //Application session token (read access)

        $application_id = QUICKBLOX_APP_ID;
        $auth_key       = QUICKBLOX_AUTH_KEY;
        $authSecret     = QUICKBLOX_AUTH_SECRET;
        $nonce          = rand();
        $timestamp      = time();

        // Build post body
        $body = array(
                'application_id'=> $application_id,
                'auth_key'      => $auth_key,
                'nonce'         => $nonce,
                'timestamp'     => $timestamp
           );
        
        $built_query        = urldecode(http_build_query($body));
        $signature          = hash_hmac('sha1', $built_query , $authSecret);
        $body['signature']  = $signature; 
        $post_body          = http_build_query($body);

        $url = 'https://api.quickblox.com/session.json';

        $headers = array(
            'Content-Type:application/json',
            'QuickBlox-REST-API-Version: 0.1.0',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);

        $result = curl_exec($ch);
        if($result == false){
            // die('quickblox token req failed: '.curl_error($ch));
        }
        curl_close($ch);
        $result = json_decode($result,true);
        if(isset($result['session'])) {
            return array(
                'status' => true,
                'token'  => $result['session']['token']
            );
        } else{
            return array(
                'status' => false,
            );
        } 
    }

    public function QuickBloxUserUpdate($user_id){

        $user = User::select('id','email','first_name','last_name','image','quickblox_id')->where('id',$user_id)->first();
        if(!empty($user)){
            $resp = $this->getQuickBloxUserSessionToken($user->id); 

            if($resp['status'] == true){
                $token = $resp['token']; 

                $url = 'https://api.quickblox.com/users/'.$user->quickblox_id.'.json';
                $data = array(
                    'user' => array(
                        'custom_data' => $user->image['thumb'],
                        'full_name' => $user->first_name.' '.$user->last_name,

                        // 'password'  => $this->generateRegisteredUserQuickBloxPassword($user->id),
                        // 'old_password' => $user->email
                    )
                );
         
                $headers = array(
                    'Content-Type:application/json',
                    'QuickBlox-REST-API-Version: 0.1.0',
                    'QB-Token: '.$token
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                // curl_setopt($ch, CURLOPT_POST, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                $result = curl_exec($ch);
                if($result == false){
                    die('quickblox edit req failed: '.curl_error($ch));
                }
                curl_close($ch);
                $result = json_decode($result,true);
                // echo '<pre>'; print_r($result); die;
                
                ///////////////////////////////////////////////
                // $admin = \App\Admin::find(1);
                // $old_img = $admin->test;
                
                // $admin->test = $old_img.",'".$user->id."'";
                // $admin->save();

                ///////////////////////////////////////////////                

                if(isset($result['user'])) {
                   
                    return array(
                        'status' => true,
                        'message' => 'updated successfully'
                        // 'quickblox_id' => $result['user']['id']             
                    );
                } else{
                    return array(
                        'status' => false,
                        'message' => $result
                    );
                } 

            } else{
                return array(
                    'status' => false,
                    'message' => 'generate session token err',             
                );
            }
        } else{ 
            return array(
                'status' => false,
                'message' => 'user not found'
            );            
        }
    }

    public function getQuickBloxUserSessionToken($user_id){ //Application session token (read+right access)

        $application_id = QUICKBLOX_APP_ID;
        $auth_key       = QUICKBLOX_AUTH_KEY;
        $authSecret     = QUICKBLOX_AUTH_SECRET;
        $nonce          = rand();
        $timestamp      = time();

        // Build post body
        $body = array(
                'application_id'=> $application_id,
                'auth_key'      => $auth_key,
                'nonce'         => $nonce,
                'timestamp'     => $timestamp,
                'user'          => [
                    'login'     => $user_id,
                    'password'  => $this->generateRegisteredUserQuickBloxPassword($user_id),
                ]
           );

        $built_query        = urldecode(http_build_query($body));
        $signature          = hash_hmac('sha1', $built_query , $authSecret);
        $body['signature']  = $signature; 
        $post_body          = http_build_query($body);

        $url = 'https://api.quickblox.com/session.json';

        $headers = array(
            'Content-Type:application/json',
            'QuickBlox-REST-API-Version: 0.1.0',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);

        $result = curl_exec($ch);
        if($result == false){
            // die('quickblox token req failed: '.curl_error($ch));
        }
        curl_close($ch);
        $result = json_decode($result,true);
        //echo '<pre>'; print_r($result); //die;

        if(isset($result['session'])) {
            return array(
                'status' => true,
                'token'  => $result['session']['token']
            );
        } else{
            return array(
                'status' => false,
                'message'  => $result
            );
        } 
    }

}
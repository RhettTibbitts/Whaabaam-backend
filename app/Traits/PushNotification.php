<?php
namespace App\Traits;
use App\UserDevice;
use App\Notification;

trait PushNotification{
    
    public function PushNotify($to_user_id,$from_user_id,$event_id,$event_type,$msg_txt){
       
        $device = UserDevice::getDevice($to_user_id);
      

        if(!empty(@$device->token)) {    
            $token       = $device->token;
            $device_type = $device->device_type;
            
            $msg_txt = '';
            //set message checking event type
            if($event_type == 'F'){ //new friend req
                $msg_title  = 'New friend connection';
                $msg_txt    = $msg_txt;
                $type_txt   = 'FRIEND_REQ';
            } else{
                return false;
            }

            $message = [
                'title' => $msg_title,
                'body'  => $msg_txt,
                'event_type' => $type_txt,
            ];
            $this->notifyAndroid($token,$message);      
            /*if($device_type == 'A'){  
            } else{ 
                $this->notifyIos($token,$message);      
            }*/
        }

        //save notification
        //Notification::saveNotif($user['id'],$txt,'C');  
        Notification::saveNotif($to_user_id, $from_user_id, $event_id, $event_type);   
        // return response()->json(['resp'=> true]);   
    }

    public function notifyAndroid($device_id, $message) { //For Android 
        $url = 'https://fcm.googleapis.com/fcm/send';
        /*$msg = array(
            'registration_ids'  =>  $device_id,
            'data'              =>  $message,
        );*/
        $msg = array(
            'to'            =>  $device_id,
            'notification'  =>  $message,
            'data'          =>  $message
        );
        // prx($message); die;  

        $headers = array(
            'Authorization: key=' . FCM_API_KEY,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($msg));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            //die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        //echo '<pre>'; print_r($result);die;
        return  $result;
    }

    public function notifyIos($device_id, $data){
        return false;    

        // Put your device token here (without spaces):
        $deviceToken = $device_id;
        // Put your private key's passphrase here:
        $passphrase = "";
        // Put your alert message here:
        $message = "Message";

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', '1'.PEM_FILE_PATH);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        // Open a connection to the APNS server
        $fp = stream_socket_client(IOS_APNS_SERVER, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        // $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        //ssl://gateway.sandbox.push.apple.com:2195
        //ssl://gateway.push.apple.com:2195
        // $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        //api.sandbox.push.apple.com:443
        
        if (!$fp) {
            //exit("Failed to connect: $err $errstr" . PHP_EOL);
            return false;
        }
        //echo 'Connected to APNS' . PHP_EOL;

        // Create the payload body
        $body['aps'] = array( 
            'alert' => $data,
            'sound' => 'default'
        );

        /*$body['aps'] = array( 
            'alert' => array(
                'title' => 'Whabaam',
                'event_type' => 'FRIEND_REQ',
                'body'  => 'You have a new request',
             ),
            'sound' => 'default'
        );*/
        //Note: 'event_type' => 'FRIEND_REQ', //'CLOSE_CONTACT'

        // Encode the payload as JSON
        $payload = json_encode($body);
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        //pr($deviceToken); 
        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));
        fclose($fp);
         // echo '<pre>'; print_r($result); //die;
        
        if (!$result) {
             // echo 'Message not delivered' . PHP_EOL;
            return true;
        } else {
             // echo 'Message successfully delivered' . PHP_EOL;
            return false;
        }

        // Close the connection to the server
        //DIE;
    }
}
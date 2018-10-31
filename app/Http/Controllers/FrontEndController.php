<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\ContactUs;
use Validator;
use Session;
use App\Page; 

use App\Traits\QuickBlox;

class FrontEndController extends Controller
{
	use QuickBlox;

	public function contact_us(Request $request){
		$validator = Validator::make($request->input(),[
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required',
            'message' => 'required',
		]);

        if($validator->fails()){
			return redirect()->back()->with('error',$validator->errors()->first());
        } else{
        	// $to_email = 'xicomtest123456@gmail.com';
        	$to_email = env('CONTACT_US_MAIL');
        	if(!empty($to_email)){
	        	$company_name = 'WhaaBaam';
           
	            $email_format = Email::getEmail('contact_us');
	            $content = $email_format->content;
	            $subject = $email_format->subject;

	            $content = str_replace('{{$name}}', $request->name, $content);
	            $content = str_replace('{{$email}}', $request->email, $content);
	            $content = str_replace('{{$subject}}', $request->subject, $content);
	            $content = str_replace('{{$message}}', $request->message, $content);

	           	Mail::send('emails.contact_us',
	                ['content' => $content], 
                    function($message) use ($to_email,$company_name,$subject){
                        $message->to($to_email,$company_name)->subject($subject);
                    }
	            );

	       		return redirect('http://whaabaam.com/contact_us?resp=success')->with('success', 'Contact us message submitted successfully');
			}			
			
			// return redirect('http://localhost/whabaam/contact_us?resp=success')->with('success', 'Contact us message submitted successfully');
			// return redirect()->back()->with('success', 'Contact us message submitted successfully');
			/*$saved = ContactUs::add($request);
			if($saved == true){
				return redirect()->back()->with('success', 'Contact us message submitted successfully');
			} else{
				return redirect()->back()->with('error', __('messages.COMMON_ERR'));
			}*/
        }

	}

	public function terms_conditions(Request $request) {        
        $content = Page::where('slug','terms-conditions')->value('content'); 
          // echo '<pre>'; print_r($content); die;   
        return view('frontEnd.cms.terms_conditions', compact('content'));
    }

    /*public function privacy_policy(Request $request) {        
        $page = Page::select('heading','content')->where('slug','privacy-policy')->first();         
        return view('frontEnd.cms.privacy_policy', compact('page'));
    }*/

	public function test(Request $request){
		//update user friend's
        $u = \App\User::select('id')->get();
		// echo '<pre>'; print_r($u); die;   

        foreach ($u as $key => $value) {
        	$friends = \App\FriendRequest::getFriends($value->id,$request,true);
			
	        $friends_ids = [];
        	foreach ($friends as $key1 => $frnd) {

				$friends_ids[] = $frnd['friend_user_id'];
        	}
			// echo '<pre>'; print_r($friends_ids); die;   

	        array_unique($friends_ids);
	        sort($friends_ids);
			// echo '<pre>'; print_r($friends_ids['friend_user_id']); die;   

	        $friend_ids_str = implode(',',$friends_ids);

	        \App\User::Where('id',$value->id)->update([ 'friend_ids' =>  $friend_ids_str ]);
        }
        echo 'done'; die;
	}

	/*public function test(){
		//manually update user quickblox credentials
		$done_ids = ['2','209','210','211','212','213','214','215','216','217','218','219','220','221','222','223','224','225','226','227','228','229','230','231','232','233','234','235','209','236','210','237','211','238','212','239','213','240','214','241','215','242','216','243','217','244','218','245','219','246','220','247','221','248','222','249','223','250','224','251','225','252','226','253','227','254','228','255','229','256','230','231','232','233','234','235','236','237','238','239','240','241','242','243','244','245','246','247','248','249','250','251','252','253','254','255','256',  '257','258','259','260','261','262','263','264','265','266','267','268','269','270','271','272','273','274','275','276','277','278','279','280','281','257','282','258','283','259','284','260','285','261','286','262','287','263','288','264','289','265','290','266','291','267','292','268','293','269','294','270','295','271','296','272','297','273','298','274','299','275','300','276','301','277','302','278','303','279','304','280','305','281','282','283','284','285','286','287','288','289','290','291','292','293','294','295','296','297','298','299','300','301','302','303','304','305',  '306','307','308','309','310','311','312','313','314','315','316','317','318','319','320','321','322','323','324'];


		echo '<pre>'; print_r($done_ids); die;   


        $u = \App\User::whereNotIn('id',$done_ids)
        			->get();
		// echo '<pre>'; print_r($u); die;   

        foreach ($u as $key => $value) {
        	$this->QuickBloxUserUpdate($value->id);
        	
        }
        echo 'done'; die;
	}*/

	/*public function get_signature_for_quickblox(){ // get_signature
		$application_id = 72672;
		$auth_key 		= "vJMB-BxvStDACPx";
		$authSecret 	= "XGxR7gcbeVekQWt";

		$nonce = rand();
		echo "<br>nonce: " . $nonce;

		$timestamp = time();
		echo "<br>timestamp: " . $timestamp ."<br>";

		$stringForSignature = "application_id=".$application_id."&auth_key=".$auth_key."&nonce=".$nonce."&timestamp=".$timestamp;
		echo $stringForSignature."<br>";

		$signature = hash_hmac( 'sha1', $stringForSignature , $authSecret);
		echo $signature.'<br>';

		$lastInp = "application_id=".$application_id."&auth_key=".$auth_key."&nonce=".$nonce."&timestamp=".$timestamp."&signature=".$signature;
		echo '$lastInp = '.$lastInp;
    }*/
}

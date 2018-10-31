<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Mail;
use Hash;
use Auth, DB;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'id','first_name', 'last_name','last_name_access', 'email','email_access','phone','phone_access','state_id','city_id','city_id_access','from_state_id','from_city_id','from_city_id_access','occupation','occupation_access','work_website','work_website_access','education','education_access','high_school','high_school_access','college','college_access','alma_matter','alma_matter_access','likes','likes_access','military_id','military_id_access','political_id','political_id_access','family_access','religion_id','religion_id_access','relationship_id','relationship_id_access','password','resume','image','quickblox_id','fb_link','fb_link_access','insta_link','insta_link_access','twit_link','twit_link_access','linked_in_link','linked_in_link_access','capture_time_period','status'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = ['deleted_at'];

    public function getFirstNameAttribute($value){ //Accessors to capitalize name
        return ucfirst($value);
    }

    public function getImageAttribute($value){ //accessor
        if(!empty($value)){
            return array(
                'org'   => asset(USER_PROFILE_MAIN_IMG_PATH.$value),
                'thumb' => asset(USER_PROFILE_MAIN_IMG_PATH.'thumb/'.$value)
            );
        } else{
            return array(
                'org'   => '',
                'thumb' => ''
            );
        }
        /*if(!empty($value)){
            return asset(USER_PROFILE_MAIN_IMG_PATH.$value);
            //USER_PROFILE_MAIN_THUMB_PATH
        }*/
    }

    public function getResumeAttribute($value){ //accessor
        if(!empty($value)){
            return asset(USER_RESUME_PATH.$value);
        }
    }

    public function getActualAttribute($field){ //to get only image name
        return $this->attributes[$field];
    }

    public function images(){
        return $this->hasMany('App\UserImage','user_id','id')->select('id','user_id','name');
    }

    public function military(){
        return $this->belongsTo('App\Military','military_id','id')->select('id','name');
    }

    public function political(){
        return $this->belongsTo('App\Political','political_id','id')->select('id','name');
    }

    public function relationship(){
        return $this->belongsTo('App\Relationship','relationship_id','id')->select('id','name');
    }

    public function religion(){
        return $this->belongsTo('App\Religion','religion_id','id')->select('id','name');
    }

    public function state(){
        return $this->belongsTo('App\State','state_id','id')->select('id','name');
    }

    public function city(){
        return $this->belongsTo('App\City','city_id','id')->select('id','name');
    }
    
    public function fromState(){
        return $this->belongsTo('App\State','from_state_id','id')->select('id','name');
    }

    public function fromCity(){
        return $this->belongsTo('App\City','from_city_id','id')->select('id','name');
    }

    public function userLocation(){
        return $this->hasOne('App\UserLocation','user_id','id')
                    ->select('id','user_id','lat','lng','created_at')
                    ->orderBy('id','desc');
    }

    public function userDevice(){
        return $this->hasOne('App\UserDevice','user_id','id')
                    ->select('id','user_id','token','device_type')
                    ->orderBy('id','desc');
    }

    //functions starts
    public static function getUserName($user_id){
        $user = User::select('first_name','last_name')->find($user_id);
        if(!empty($user)){
            return $user['first_name'].' '.$user['last_name'];
        } else{
            return '';
        }
    } 

    public static function addUser($request){

        $data               = $request->all();
        $data['password']   = Hash::make($data['password']);
        $data['first_name'] = trim($data['first_name']);
        $data['last_name']  = trim($data['last_name']);

        /*if(isset($data['multi_images'])) {  //for admin
            unset($data['multi_images']);
        }*/
        $user_id = User::create($data)->id;

        if($user_id){
            //image upload
            // UserImage::saveUserImage($request,$user_id);

            //save upload 
            UserImage::_saveResume($request,$user_id);

            //save main image 
            UserImage::saveUserMainImageByAdmin($request,$user_id);

            //save multi images 
            UserImage::saveUserMultiImagesByAdmin($request,$user_id);

            Self::sendRegisterationEmail($user_id);
            return true;
        } else{
            return false;
        }
    }

    public static function editUser($request,$user_id = null){
 
        $data = $request->all();
        if(!empty($data['password'])){
            $data['password'] = Hash::make($data['password']);
        } else{
            unset($data['password']);
        }
        unset($data['_token']);
        unset($data['password_confirmation']);
        
        //removing image format from direclty saving
        unset($data['image']);
        unset($data['resume']);
        if(isset($data['multi_images'])) {
            unset($data['multi_images']);
        }

        unset($data['email']);
        unset($data['user_id']); //unset in case of api
        $data['first_name'] = trim($data['first_name']);
        $data['last_name_access']  = $data['last_name_access'];
        
        if(isset($data['capture_time_period'])){ //for admin
            $data['capture_time_period'] = (int)$data['capture_time_period'];
        }

        $data['is_profile_updated'] = 1;
        $updated = User::where('id',$user_id)->update($data);

        if($updated){ 

            //if admin login, then save all images
            if(Auth::guard('admin')->check()){
                //image upload
                UserImage::saveUserMainImageByAdmin($request,$user_id); 
                UserImage::saveUserMultiImagesByAdmin($request,$user_id); 
            }

            //resume upload 
            UserImage::_saveResume($request,$user_id);

            return true;
        } else{
            return false;
        }
    }

    public static function getUser($user_id = null){  //my profile
        $user = User::select('id','first_name','last_name','last_name_access','email','email_access','phone','phone_access','state_id','city_id','city_id_access','from_state_id','from_city_id','from_city_id_access','occupation','occupation_access','work_website','work_website_access','education','education_access','high_school','high_school_access','college','college_access','alma_matter','alma_matter_access','likes','likes_access','military_id','military_id_access','political_id','political_id_access','family_access','religion_id','religion_id_access','relationship_id','relationship_id_access','resume','image','quickblox_id','fb_link','fb_link_access','insta_link','insta_link_access','twit_link','twit_link_access','linked_in_link','linked_in_link_access','capture_time_period','status')
                        ->with('images')
                        ->where('id',$user_id)
                        ->first();
        return $user;
    }

    public static function getAnotherUser($user_id,$is_friend = false){ //view other user profile 
        $user = User::select('id','first_name','last_name','last_name_access','email','email_access','phone','phone_access','state_id','city_id','city_id_access','from_state_id','from_city_id','from_city_id_access','occupation','occupation_access','work_website','work_website_access','education','education_access','high_school','high_school_access','college','college_access','alma_matter','alma_matter_access','likes','likes_access','military_id','military_id_access','political_id','political_id_access','family_access','religion_id','religion_id_access','relationship_id','relationship_id_access','resume','image','quickblox_id','fb_link','fb_link_access','insta_link','insta_link_access','twit_link','twit_link_access','linked_in_link','linked_in_link_access')
                        ->where('id',$user_id)
                        ->where('status',1)
                        ->with('images')
                        ->with('military')
                        ->with('political')
                        ->with('relationship')
                        ->with('religion')
                        ->with('state')
                        ->with('city')
                        ->with('fromState')
                        ->with('fromCity')
                        ->first();
        if(!empty($user)){
            $user = $user->toArray();

            if($is_friend !== true){    //if another user is not my friend then hide private details from him

                if($user['last_name_access'] == 0){
                    unset($user['last_name']);
                }
                if($user['email_access'] == 0){
                    unset($user['email']);
                }
                if($user['phone_access'] == 0){
                    unset($user['phone']);
                }
                if($user['city_id_access'] == 0){
                    unset($user['state_id']);
                    unset($user['city_id']);
                }
                if($user['from_city_id_access'] == 0){
                    unset($user['from_state_id']);
                    unset($user['from_city_id']);
                }
                if($user['occupation_access'] == 0){
                    unset($user['occupation']);
                    unset($user['resume']);
                }
                if($user['work_website_access'] == 0){
                    unset($user['work_website']);
                }
                if($user['education_access'] === 0){
                    unset($user['education']);
                }
                if($user['high_school_access'] == 0){
                    unset($user['high_school']);
                }
                if($user['college_access'] == 0){
                    unset($user['college']);
                }
                if($user['alma_matter_access'] == 0){
                    unset($user['alma_matter']);
                }
                if($user['likes_access'] == 0){
                    unset($user['likes']);
                }
                if($user['military_id_access'] == 0){
                    unset($user['military']);
                }
                if($user['political_id_access'] == 0){
                    unset($user['political']);
                }
                if($user['religion_id_access'] == 0){
                    unset($user['religion']);
                }
                if($user['relationship_id_access'] == 0){
                    unset($user['relationship']);
                }
                if($user['city_id_access'] == 0){
                    unset($user['state']);
                    unset($user['city']);
                }
                if($user['fb_link_access'] == 0){
                    unset($user['fb_link']);
                }
                if($user['insta_link_access'] == 0){
                    unset($user['insta_link']);
                }
                if($user['twit_link_access'] == 0){
                    unset($user['twit_link']);
                }
                if($user['linked_in_link_access'] == 0){
                    unset($user['linked_in_link']);
                }
            }
            unset($user['last_name_access']);
            unset($user['email_access']);
            unset($user['phone_access']);
            unset($user['city_id_access']);
            unset($user['occupation_access']);
            unset($user['work_website_access']);
            unset($user['education_access']);
            unset($user['high_school_access']);
            unset($user['college_access']);
            unset($user['alma_matter_access']);
            unset($user['likes_access']);
            unset($user['military_id_access']);
            unset($user['political_id_access']);
            unset($user['religion_id_access']);
            unset($user['relationship_id_access']);

            unset($user['military_id']);
            unset($user['political_id']);
            unset($user['religion_id']);
            unset($user['relationship_id']);
            unset($user['city_id_access']);
            unset($user['state_id']);
            unset($user['city_id']);
            unset($user['fb_link_access']);
            unset($user['insta_link_access']);
            unset($user['twit_link_access']);
            unset($user['linked_in_link_access']);
            unset($user['family_access']);
            unset($user['from_city_id_access']);
            unset($user['from_state_id']);
            unset($user['from_city_id']);
            
            return $user;
        } else{
            return false;
        }
    }

    public static function getUserByEmail($email = null){ 
        $user = User::select('id','first_name', 'last_name','last_name_access','email','email_access','phone','phone_access','state_id','city_id','city_id_access','from_state_id','from_city_id','from_city_id_access','occupation','occupation_access','resume','work_website','work_website_access','education','education_access','high_school','high_school_access','college','college_access','alma_matter','alma_matter_access','likes','likes_access','military_id','military_id_access','political_id','political_id_access','family_access','religion_id','religion_id_access','relationship_id','relationship_id_access','image','password','quickblox_id','fb_link','fb_link_access','insta_link','insta_link_access','twit_link','twit_link_access','linked_in_link','linked_in_link_access','capture_time_period','status','is_profile_updated')
                        ->with('images')
                        ->where('email',$email)
                        ->first();
        return $user;
    }

    public static function getUserByQuickbloxId($quickblox_id = null){ 
        return User::where('quickblox_id',$quickblox_id)->where('status',1)->value('id');
    }
    
    public static function getFamilyAccess($user_id = null){ 
        return User::where('id',$user_id)->value('family_access');
    }

    public static function searchUsers($user_id,$request){

        $users = User::select('id','first_name','last_name','last_name_access','email','image','phone','quickblox_id'
                        // DB::raw('(CASE WHEN users.last_name_access = 1 THEN users.last_name ELSE "" END) AS last_name')
                    )    
                    ->when($request->search, function($when_query) use ($request){
                            return $when_query->where( function($query) use ($request){
                                // $query->whereRaw("concat(first_name, ' ', last_name) like '%".$request->search."%' ");
                                $query->orWhereRaw("concat(first_name, ' ', last_name) like '%".$request->search."%' ");
                                $query->orWhere('phone','like', '%'.$request->search.'%');
                            });
                        })
                    ->where('id','!=',$user_id)
                    ->paginate(10)->toArray();
        //determing if the searched user is friendor not
        // echo '<pre>'; print_r($users); die;
        
        $user2 = $users;
        if($user2['total'] > 0){
            unset($user2['data']);

            foreach($users['data'] as $key => $value){
                $friend_status = FriendRequest::checkFriendReqStatus($user_id,$value['id']);
                // $users['data'][$key]['req_status'] = $friend_status;

                //done in order to reuse structure of close users in app
                $user2['data'][$key]['user_info']  = $users['data'][$key];
                $user2['data'][$key]['req_status'] = $friend_status;

                //manage last name access
                if($friend_status != 'FRIEND'){
                    if($user2['data'][$key]['user_info']['last_name_access'] == 0){
                        $user2['data'][$key]['user_info']['last_name'] = '';
                    }
                }
            }
        }

        return $user2;
        // $users =
    }

    public static function sendRegisterationEmail($user_id){
        $user = User::select('id','first_name','last_name','email')->where('id',$user_id)->first();
        if(!empty(@$user->email)){

            $to_email = $user->email;
            $to_email = 'xicomtest123456@gmail.com';
            $company_name = PROJECT_NAME;

            $email_format = Email::getEmail('registeraton');
            $content = $email_format->content;
            $subject = $email_format->subject;

            $content = str_replace('{{$first_name}}', $user->first_name, $content);
            $content = str_replace('{{$last_name}}', $user->last_name, $content);
        
            if (!filter_var($to_email, FILTER_VALIDATE_EMAIL) === false) {   
                Mail::send('emails.registration_email_to_user',
                    ['content' => $content], 
                    function($message) use ($to_email,$company_name,$subject){
                        $message->to($to_email,$company_name)->subject($subject);
                    }
                );
            } 
        }   
    }

    public static function sendForgotPassEmail($email=null){
        $user = User::select('id','first_name','last_name','email')->where('email',$email)->first();

        $rand = rand(111111,999999);
        $user->verify_code = $rand;
        $user->verify_code_created_at = date('Y-m-d H:i:s');
        if($user->save()){

            $company_name = PROJECT_NAME;
            $to_email = $user->email;
            
            $email_format = Email::getEmail('forgot_password');
            $content = $email_format->content;
            $subject = $email_format->subject;

            $content = str_replace('{{$first_name}}', $user->first_name, $content);
            $content = str_replace('{{$last_name}}', $user->last_name, $content);
            $content = str_replace('{{$email}}', $user->email, $content);
            $content = str_replace('{{$verify_code}}', $user->verify_code, $content);

            if (!filter_var($to_email, FILTER_VALIDATE_EMAIL) === false) { 
                Mail::send('emails.forgot_pass_email_to_user',
                    ['content' => $content], 
                    function($message) use ($to_email,$company_name,$subject){
                        $message->to($to_email,$company_name)->subject($subject);
                    }
                );
                return true;
            } 
        }   
    }
 
    public static function sendErrorReportEmail($request){

        $from_user = User::select('id','first_name','last_name')->where('id',$request->from_user_id)->first();
        $from_user_name = $from_user->first_name.' '.$from_user->last_name;

        $to_user = User::select('id','first_name','last_name')->where('id',$request->to_user_id)->first();
        $to_user_name = $to_user->first_name.' '.$to_user->last_name;

        $to_email = env('ERR_REPORT_MAIL');
        if(!empty($to_email)) {

            $company_name = PROJECT_NAME;
            $email_format = Email::getEmail('error_report');
            $content = $email_format->content;
            $subject = $email_format->subject;

            $content = str_replace('{{$from_user_name}}', $from_user_name, $content);
            $content = str_replace('{{$to_user_name}}', $to_user_name, $content);
            $content = str_replace('{{$message}}', $request->message, $content);

            if (!filter_var($to_email, FILTER_VALIDATE_EMAIL) === false) { 
                Mail::send('emails.error_report',
                    ['content' => $content], 
                    function($message) use ($to_email,$company_name,$subject){
                        $message->to($to_email,$company_name)->subject($subject);
                    }
                );
                return true;
            } 
        }   
    }
    
}
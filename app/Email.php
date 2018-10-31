<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    public function getNameAttribute($value){ //Accessors to capitalize name
        return ucfirst($value);
    }

    public static function getEmails(){
	    return Email::select('id','name')->get()->toArray();
    }

    public static function getEmail($slag){
	    return Email::select('subject','content')->where('slag',$slag)->first();
    }

    public static function editEmail($request){
	    $email = Email::find($request->email_id);
	    $email->subject = $request->subject;
	    $email->content = $request->content;
	    if($email->save()){
	    	return true;
	    } else{
	    	return false;
	    }
    }

}

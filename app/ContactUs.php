<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactUs extends Model
{
	public $table = 'contact_us';
    // use SoftDeletes;
    public static function add($request){

	    $contact_us 		= new ContactUs;
	    $contact_us->name 	= $request->name;
	    $contact_us->email 	= $request->email;
	    $contact_us->subject= $request->subject;
	    $contact_us->message= $request->message;

	    if($contact_us->save()){
	    	return true;
	    } else{
	    	return false;
	    }
    }

}

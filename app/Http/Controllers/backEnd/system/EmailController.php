<?php

namespace App\Http\Controllers\backEnd\system;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEmail;
use App\Email;

class EmailController extends Controller
{
	public function index(Request $request){
		$emails = Email::getEmails();
		return view('backEnd.system.email.index', compact('emails'));
	}

	public function edit(StoreEmail $request,$email_id = null){

		if($request->isMethod('post')){
			$edited = Email::editEmail($request, $email_id);
			
			if($edited == true){
				return redirect('/admin/emails')->with('success','Email has been edited successfully');
			} else{
				return redirect()->back()->with('error',COMMON_ERR);
			}
		}

		$email = Email::where('id',$email_id)->first();		
		if(!empty($email)){
			return view('backEnd.system.email.form', compact('email'));
		} else{
			return redirect()->back()->with('error','Email not found');
		}
	}

}
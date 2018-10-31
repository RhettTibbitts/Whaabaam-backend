<?php

namespace App\Http\Controllers\backEnd\system;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreReligion;
use App\Religion;

class ReligionController extends Controller
{
	public function index(Request $request){
		$religions = Religion::getReligions();
		return view('backEnd.system.religion.index', compact('religions'));
	}

	public function add(StoreReligion $request){

		if($request->isMethod('post')){
		
			$added = Religion::addReligion($request);
			if($added == true){
				return redirect('/admin/religions')->with('success','Religion has been saved successfully');
			} else{
				return redirect()->back()->with('error',COMMON_ERR);
			}
		}
		return view('backEnd.system.religion.form', compact('religion'));
	}

	public function edit(StoreReligion $request,$religion_id = null){

		if($request->isMethod('post')){
			$edited = Religion::editReligion($request, $religion_id);
			
			if($edited == true){
				return redirect('/admin/religions')->with('success','Religion has been edited successfully');
			} else{
				return redirect()->back()->with('error',COMMON_ERR);
			}
		}

		$religion = Religion::where('id',$religion_id)->first();		
		if(!empty($religion)){
			return view('backEnd.system.religion.form', compact('religion'));
		} else{
			return redirect()->back()->with('error','Religion not found');
		}
	}

	public function delete($religion_id = null){
		$deleted = Religion::deleteReligion($religion_id);
		if($deleted){
			return redirect('admin/religions')->with('success','Religion has been deleted successfully');
		} else{
			return redirect()->back()->with('error',COMMON_ERR);
		}
	}

}
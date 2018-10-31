<?php

namespace App\Http\Controllers\backEnd\system;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMilitary;
use App\Military;

class MilitaryController extends Controller
{
	public function index(Request $request){
		$militaries = Military::getMilitaries();
		return view('backEnd.system.military.index', compact('militaries'));
	}

	public function add(StoreMilitary $request){

		if($request->isMethod('post')){
		
			$added = Military::addMilitary($request);
			if($added == true){
				return redirect('/admin/militaries')->with('success','Military has been saved successfully');
			} else{
				return redirect()->back()->with('error',COMMON_ERR);
			}
		}
		return view('backEnd.system.military.form', compact('military'));
	}

	public function edit(StoreMilitary $request,$military_id = null){

		if($request->isMethod('post')){
			$edited = Military::editMilitary($request, $military_id);
			
			if($edited == true){
				return redirect('/admin/militaries')->with('success','Military has been edited successfully');
			} else{
				return redirect()->back()->with('error',COMMON_ERR);
			}
		}

		$military = Military::where('id',$military_id)->first();		
		if(!empty($military)){
			return view('backEnd.system.military.form', compact('military'));
		} else{
			return redirect()->back()->with('error','Military not found');
		}
	}

	public function delete($military_id = null){
		$deleted = Military::deleteMilitary($military_id);
		if($deleted){
			return redirect('admin/militaries')->with('success','Military has been deleted successfully');
		} else{
			return redirect()->back()->with('error',COMMON_ERR);
		}
	}

}
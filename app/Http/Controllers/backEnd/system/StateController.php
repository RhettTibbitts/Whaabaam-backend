<?php

namespace App\Http\Controllers\backEnd\system;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreState;
use App\State;

class StateController extends Controller
{
	public function index(Request $request){

		$states = State::getStates();
		return view('backEnd.system.state.index', compact('states'));
	}

	public function add(StoreState $request){

		if($request->isMethod('post')){
		
			$state = State::add($request);
			if($state != false){
				return redirect('/admin/states')->with('success','State has been saved successfully');
			} else{
				return redirect()->back()->with('error',COMMON_ERR);
			}
		}
		return view('backEnd.system.state.form', compact('countries','states','cities','militaries','politicals','religions','relationships'));
	}

	public function edit(StoreState $request,$state_id = null){

		if($request->isMethod('post')){
		
			$state = State::edit($request, $state_id);
			if($state != false){
				return redirect('/admin/states')->with('success','State has been edited successfully');
			} else{
				return redirect()->back()->with('error',COMMON_ERR);
			}
		}

		$state = State::where('id',$state_id)->first();		
		if(!empty($state)){
			return view('backEnd.system.state.form', compact('state','militaries','politicals','religions','relationships','states','cities'));
		} else{
			return redirect()->back()->with('error','State not found');
		}
	}

	public function delete(Request $request, $state_id = null){
		$deleted = State::where('id',$state_id)->delete();
		if($deleted){
			return redirect('admin/states')->with('success','State has been deleted successfully');
		} else{
			return redirect()->back()->with('error',COMMON_ERR);
		}
	}

}
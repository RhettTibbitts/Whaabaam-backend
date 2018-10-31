<?php

namespace App\Http\Controllers\backEnd\system;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCity;
use App\City;

class CityController extends Controller
{
	public function index(Request $request,$state_id){

		$cities = City::getCities($state_id);
		return view('backEnd.system.city.index', compact('cities','state_id'));
	}

	public function add(StoreCity $request,$state_id){

		if($request->isMethod('post')){
			
			$city = City::add($request);
			if($city != false){
				return redirect('/admin/cities/'.$request->state_id)->with('success','City has been saved successfully');
			} else{
				return redirect()->back()->with('error',COMMON_ERR);
			}
		}
		return view('backEnd.system.city.form', compact('city','state_id'));
	}

	public function edit(StoreCity $request,$city_id = null){

		if($request->isMethod('post')){
			$city = City::edit($request, $city_id);
			
			if($city != false){
				return redirect('/admin/cities/'.$request->state_id)->with('success','City has been edited successfully');
			} else{
				return redirect()->back()->with('error',COMMON_ERR);
			}
		}

		$city = City::where('id',$city_id)->first();		
		$state_id = $city->state_id;
		if(!empty($city)){
			return view('backEnd.system.city.form', compact('city','state_id'));
		} else{
			return redirect()->back()->with('error','City not found');
		}
	}

	public function delete($city_id = null){
		$city = City::find($city_id);
	    $state_id = $city->state_id;

		if($city->delete()){
			return redirect('admin/cities/'.$state_id)->with('success','City has been deleted successfully');
		} else{
			return redirect()->back()->with('error',COMMON_ERR);
		}
	}

}
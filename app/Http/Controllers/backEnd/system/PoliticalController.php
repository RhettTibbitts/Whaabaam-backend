<?php

namespace App\Http\Controllers\backEnd\system;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StorePolitical;
use App\Political;

class PoliticalController extends Controller
{
	public function index(Request $request){
		$politicals = Political::getPoliticals();
		return view('backEnd.system.political.index', compact('politicals'));
	}

	public function add(StorePolitical $request){

		if($request->isMethod('post')){
		
			$added = Political::addPolitical($request);
			if($added == true){
				return redirect('/admin/politicals')->with('success','Political has been saved successfully');
			} else{
				return redirect()->back()->with('error',COMMON_ERR);
			}
		}
		return view('backEnd.system.political.form', compact('political'));
	}

	public function edit(StorePolitical $request,$political_id = null){

		if($request->isMethod('post')){
			$edited = Political::editPolitical($request, $political_id);
			
			if($edited == true){
				return redirect('/admin/politicals')->with('success','Political has been edited successfully');
			} else{
				return redirect()->back()->with('error',COMMON_ERR);
			}
		}

		$political = Political::where('id',$political_id)->first();		
		if(!empty($political)){
			return view('backEnd.system.political.form', compact('political'));
		} else{
			return redirect()->back()->with('error','Political not found');
		}
	}

	public function delete($political_id = null){
		$deleted = Political::deletePolitical($political_id);
		if($deleted){
			return redirect('admin/politicals')->with('success','Political has been deleted successfully');
		} else{
			return redirect()->back()->with('error',COMMON_ERR);
		}
	}

}
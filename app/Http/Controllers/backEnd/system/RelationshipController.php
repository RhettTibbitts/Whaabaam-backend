<?php

namespace App\Http\Controllers\backEnd\system;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRelationship;
use App\Relationship;

class RelationshipController extends Controller
{
	public function index(Request $request){
		$relationships = Relationship::getRelationships();
		return view('backEnd.system.relationship.index', compact('relationships'));
	}

	public function add(StoreRelationship $request){

		if($request->isMethod('post')){
		
			$added = Relationship::addRelationship($request);
			if($added == true){
				return redirect('/admin/relationships')->with('success','Relationship has been saved successfully');
			} else{
				return redirect()->back()->with('error',COMMON_ERR);
			}
		}
		return view('backEnd.system.relationship.form', compact('relationship'));
	}

	public function edit(StoreRelationship $request,$relationship_id = null){

		if($request->isMethod('post')){
			$edited = Relationship::editRelationship($request, $relationship_id);
			
			if($edited == true){
				return redirect('/admin/relationships')->with('success','Relationship has been edited successfully');
			} else{
				return redirect()->back()->with('error',COMMON_ERR);
			}
		}

		$relationship = Relationship::where('id',$relationship_id)->first();		
		if(!empty($relationship)){
			return view('backEnd.system.relationship.form', compact('relationship'));
		} else{
			return redirect()->back()->with('error','Relationship not found');
		}
	}

	public function delete($relationship_id = null){
		$deleted = Relationship::deleteRelationship($relationship_id);
		if($deleted){
			return redirect('admin/relationships')->with('success','Relationship has been deleted successfully');
		} else{
			return redirect()->back()->with('error',COMMON_ERR);
		}
	}

}
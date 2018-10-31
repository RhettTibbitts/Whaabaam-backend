<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Relationship extends Model
{
    use SoftDeletes;

    public static function getRelationships(){
	    return Relationship::select('id','name')->orderBy('sort_order','asc')->orderBy('name','asc')->get()->toArray();
    }

    public static function addRelationship($request){
	    $relationship = new Relationship;
	    $relationship->name = $request->name;
	    if($relationship->save()){
	    	return true;
	    } else{
	    	return false;
	    }
    }

    public static function editRelationship($request){
	    $relationship = Relationship::find($request->relationship_id);
	    $relationship->name = $request->name;
	    if($relationship->save()){
	    	return true;
	    } else{
	    	return false;
	    }
    }

    public static function deleteRelationship($relationship_id){
	    $relationship = Relationship::destroy($relationship_id);
	    if($relationship){
	    	return true;
	    } else{
	    	return false;
	    }
    }

}

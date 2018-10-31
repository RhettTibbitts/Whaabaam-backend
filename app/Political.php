<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Political extends Model
{
    use SoftDeletes;

    public static function getPoliticals(){
	    return Political::select('id','name')->orderBy('sort_order','asc')->orderBy('name','asc')->get()->toArray();
    }

    public static function addPolitical($request){
	    $political = new Political;
	    $political->name = $request->name;
	    if($political->save()){
	    	return true;
	    } else{
	    	return false;
	    }
    }

    public static function editPolitical($request){
	    $political = Political::find($request->political_id);
	    $political->name = $request->name;
	    if($political->save()){
	    	return true;
	    } else{
	    	return false;
	    }
    }

    public static function deletePolitical($political_id){
	    $political = Political::destroy($political_id);
	    if($political){
	    	return true;
	    } else{
	    	return false;
	    }
    }

}

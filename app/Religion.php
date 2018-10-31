<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Religion extends Model
{
    use SoftDeletes;

    public static function getReligions(){
	    return Religion::select('id','name')->orderBy('sort_order','asc')->orderBy('name','asc')->get()->toArray();
    }

    public static function addReligion($request){
	    $religion = new Religion;
	    $religion->name = $request->name;
	    if($religion->save()){
	    	return true;
	    } else{
	    	return false;
	    }
    }

    public static function editReligion($request){
	    $religion = Religion::find($request->religion_id);
	    $religion->name = $request->name;
	    if($religion->save()){
	    	return true;
	    } else{
	    	return false;
	    }
    }

    public static function deleteReligion($religion_id){
	    $religion = Religion::destroy($religion_id);
	    if($religion){
	    	return true;
	    } else{
	    	return false;
	    }
    }

}

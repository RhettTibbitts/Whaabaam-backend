<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Military extends Model
{
    use SoftDeletes;

    public static function getMilitaries(){
	    return Military::select('id','name')->orderBy('sort_order','asc')->orderBy('id','asc')->get()->toArray();
    }

    public static function addMilitary($request){
	    $military = new Military;
	    $military->name = $request->name;
	    if($military->save()){
	    	return true;
	    } else{
	    	return false;
	    }
    }

    public static function editMilitary($request){
	    $military = Military::find($request->military_id);
	    $military->name = $request->name;
	    if($military->save()){
	    	return true;
	    } else{
	    	return false;
	    }
    }

    public static function deleteMilitary($military_id){
	    $military = Military::destroy($military_id);
	    if($military){
	    	return true;
	    } else{
	    	return false;
	    }
    }

}

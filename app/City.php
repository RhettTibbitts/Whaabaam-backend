<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use SoftDeletes;
	public $timestamps = false;

	public function getNameAttribute($value){
		return ucfirst($value);
	}

    public static function getCities($state_id = null){
		return City::where('state_id',$state_id)->orderBy('name','asc')->get()->toArray();
    }

    public static function add($request){
	    $city 		 	= new City;
	    $city->name 	= $request->name;
	    $city->state_id	= $request->state_id;
	    if($city->save()){
	    	return true;
	    } else{
	    	return false;
	    }
    }

    public static function edit($request){
	    $city		= City::find($request->city_id);
	    $city->name = $request->name;
	    if($city->save()){
	    	return true;
	    } else{
	    	return false;
	    }
    }

}
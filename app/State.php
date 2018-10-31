<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use SoftDeletes;
	public $timestamps = false;
	
    public static function getStates(){
	    //229 - uae , 101 = india, us = 231
	    return State::select('id','name')->where('country_id',231)->orderBy('name','asc')->get()->toArray();
    }

    public static function add($request){
	    //229 - uae , 101 = india, us = 231
	    $state 		 		= new State;
	    $state->name 		= $request->name;
	    $state->country_id 	= 231;
	    if($state->save()){
	    	return true;
	    } else{
	    	return false;
	    }
    }

    public static function edit($request){
	    //229 - uae , 101 = india, us = 231
	    $state 		 		= State::find($request->state_id);
	    $state->name 		= $request->name;
	    if($state->save()){
	    	return true;
	    } else{
	    	return false;
	    }
    }

}

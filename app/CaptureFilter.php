<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CaptureFilter extends Model
{
    public static function getCaptureFilters(){
	    return CaptureFilter::select('id','name')->get()->toArray();
    }

    public static function getCaptureFilterFields($capture_filter_ids = array()){
	    
	    if(!empty($capture_filter_ids)){
	    	$capture_filter_ids_arr = explode(',',$capture_filter_ids);
	    } else{
	    	$capture_filter_ids_arr = [];
	    }
	    return CaptureFilter::whereIn('id',$capture_filter_ids_arr)
	    		->pluck('column_name')->toArray();
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    protected $fillable = ['id','from_user_id','to_user_id','report_id','created_at'];

    public function fromUser(){
        return $this->belongsTo('App\User','from_user_id','id')->select('id','name');
    }

    public function toUser(){
        return $this->belongsTo('App\User','to_user_id','id')->select('id','name');
    }

    public static function add($from_user_id,$to_user_id){
        return UserReport::create([
	        	'from_user_id' => $from_user_id,
	        	'to_user_id'   => $to_user_id
	        ])->id;
    }  

    public static function getReports($request, $paginate = false){
        return UserReport::with('fromUser')->has('fromUser')
        				->with('toUser')->has('toUser')
        				->orderBy('created_at' , 'desc')
                        ->get();
		
	}

}
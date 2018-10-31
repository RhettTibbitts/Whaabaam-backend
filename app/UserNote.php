<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserNote extends Model
{
	protected $fillable = [
	];

    public static function getUserNotes($request){ //echo $request->profile_user_id; die;
        return  UserNote::select('id','note','created_at')
                    ->where('user_id',$request->user_id)
                    ->where('target_user_id',$request->profile_user_id)
                    ->paginate(15);
    }
    
    public static function addUserNote($request){
        $note             = new UserNote;
        $note->user_id    = $request->user_id;
        $note->target_user_id  = $request->profile_user_id;
        $note->note       = $request->note;

        if($note->save()){
            return true;
        } else{
            return false;
        }
    }

	public static function editUserNote($request){
        $usr_note = UserNote::where('id',$request->user_note_id)->first();
        if(!empty($usr_note)){
            $usr_note->note  	= $request->note;
        }

        if($usr_note->save()){
            return true;
        } else{
            return false;
        }
    }

    public static function deleteUserNote($request){
        $delete = UserNote::where('id',$request->user_note_id)->delete();
        if($delete){
            return true;
        } else{
            return false;
        }
    }

}
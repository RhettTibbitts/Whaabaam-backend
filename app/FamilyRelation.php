<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class FamilyRelation extends Model
{
	protected $table = 'family_relations';
    
    public static function getFamilyRelations(){
        return FamilyRelation::select('id','name')->get()->toArray();
    }

}
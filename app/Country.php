<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public static function getList(){
		return Country::get()->toArray();
    }

}

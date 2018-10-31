<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function generateImageName(){
        $rand = rand('11111','99999');
        $new_file_name = time().$rand;
        return $new_file_name;
    }

	

	// $src="1494684586337H.jpg";
	// $dest="new.jpg";
	// $desired_width="200";
	// make_thumb($src, $dest, $desired_width);

}

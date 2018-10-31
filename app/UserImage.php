<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Image;
use Auth;

class UserImage extends Model
{
    public function getNameAttribute($value){ //accessor
    	if(!empty($value)){
    		// return asset(USER_PROFILE_IMG_PATH.$value);
            return array(
                'org'   => asset(USER_PROFILE_IMG_PATH.$value),
                'thumb' => asset(USER_PROFILE_IMG_PATH.'thumb/'.$value)
            );
    	} else{
            return array(
                'org'   => '',
                'thumb' => ''
            );
        }
    }

    public function getActualAttribute($field){
        return $this->attributes[$field];
    }

    public static function saveUserImage($request,$user_id){ //for api // upload single image either main image or other profile image
        if($request->file('image')){
            $resp = [];
            if($request->image_type == '1'){    //main image
                $store_path = base_path(USER_PROFILE_MAIN_IMG_PATH);
            } else{                             //normal image

                $res = self::_checkMaxLimit($user_id); 
                if($res == 'err'){ 
                    return array(
                        'status' => 'max_limit',
                    );
                } 
                $store_path = base_path(USER_PROFILE_IMG_PATH); 
            }

            $file       = $request->file('image'); 
            $file_name  = $file->getClientOriginalName();
            $file_ext   = strtolower($file->getClientOriginalExtension());
            $allow_exts = ['jpg','png','jpeg'];

            if(in_array($file_ext,$allow_exts)) {
                $new_file = self::generateImageName().'.'.$file_ext;

                //new code
                /*$img = Image::make($file->getRealPath());
                $img->orientate();
                $img->resize(512, 512, function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->save($store_path.'thumb/'.$new_file);*/

                Self::makeThumb($file->getRealPath(), $store_path, $new_file);
                //new code end

                if($file->move($store_path, $new_file)) { 

                    //saving img thumbnail
                    // Self::makeThumb($store_path, $new_file, $file_ext);

                    if($request->image_type == '1'){ //main image

                        //deleting old image
                        $user = User::select('id','image')->where('id',$user_id)->first();
                        $old_img = $user->getActualAttribute('image');
                        Self::_removeDirectoryImg($old_img,1);   

                        $user->image = $new_file;
                        if($user->save()){ 
                            return array(
                                'status' => true,
                                'data' => array(
                                    'image' => $user->image
                                ) 
                            );
                        } else{ 
                            return array(
                                'status' => false,
                            );
                        }

                    } else{ //image is normal image
                        $img           = new UserImage;
                        $img->user_id  = $user_id;
                        $img->name     = $new_file;
                        if($img->save()){ 
                            return array(
                                'status' => true,
                                'data' => array(
                                    'id' => $img->id,
                                    'image' => $img->name,
                                ) 
                            );
                        } else{ 
                            return array(
                                'status' => false,
                            );
                        }
                    }
                }
            } else{
                return array(
                    'status' => 'ext_err',
                );
            }
        } 
    }

    static function _checkMaxLimit($user_id){
        //count already images
        $old_img_count = UserImage::where('user_id',$user_id)->count();

        $max_img_limit = MAX_USER_PICS;
        $remaining_limit = $max_img_limit - $old_img_count;
        if($remaining_limit > 0){
            return 'ok';
        } else{
            return 'err';
        }
    }

    public static function deleteUserImage($usr_img_id = null){
        $usr_img = UserImage::where('id',$usr_img_id)->first(); 
        if(!empty($usr_img)) {
            
            $old_img = $usr_img->getActualAttribute('name');
            Self::_removeDirectoryImg($old_img,0);                      
     
            if($usr_img->delete()){
                 return true;
            } else{
                return false;
            }
        } else{
            return true;
        }
    }

    public static function deleteUserMainImage($user_id = null){
        //deleting old image
        $user = User::select('id','image')->where('id',$user_id)->first();
        $old_img = $user->getActualAttribute('image');
        Self::_removeDirectoryImg($old_img,1);   

        $user->image = '';
        if($user->save()){
            return true;
        } else{
            return false;
        }        	
    }

    public static function setUserMainImage($request){
		$usr_img = UserImage::select('name')->where('id',$request->user_image_id)->first();
        if(!empty($usr_img)){
            
            $img = $usr_img->getActualAttribute('name');
            if(!empty($img)){
                $source_path = Self::_getImgPath(0);
                $target_path = Self::_getImgPath(1);
                
                if(copy($source_path.$img,$target_path.$img)){
                    $deleted = Self::deleteUserMainImage($request->user_id);

                    User::where('id',$request->user_id)->update([
                        'image' => $img
                    ]);
                    return true;
                }else{
                	return false;
                } 
            }else{
            	return false;
            }
        }else{
        	return false;
        }
    }

    private static function _removeDirectoryImg($img_name,$img_type){
        if(!empty($img_name)){
            $path  = Self::_getImgPath($img_type);
            $img   = $path.$img_name;
            if(file_exists($img)) {
                unlink($img);
            }

            //remove thumbnail
            $img   = $path.'thumb/'.$img_name;
            if(file_exists($img)) {
                unlink($img);
            }
        } 
        return true;
    }

    public static function _saveResume($request,$user_id){ //upload resume with edit profile
        if($request->file('resume')){
            $store_path = base_path(USER_RESUME_PATH); 
            $resp = [];

            $file       = $request->file('resume');
            $file_name  = $file->getClientOriginalName();
            $file_ext   = strtolower($file->getClientOriginalExtension());
            $allow_exts = ['doc','docx','pdf'];

            if(in_array($file_ext,$allow_exts)) {
                $new_file = self::generateImageName().'.'.$file_ext;
                
                if($file->move($store_path, $new_file)) { 

                    //deleting old resume
                    $user = User::select('id','resume')->where('id',$user_id)->first();
                    $old_img = $user->getActualAttribute('resume'); 
                    Self::_removeDirectoryResume($old_img);   

                    //saving in resume in db
                    $user->resume = $new_file;
                    if($user->save()){ 
                        return array(
                            'status' => true,
                        );
                    } else{ 
                        return array(
                            'status' => false,
                        );
                    }
                }
            } else{
                return array(
                    'status' => 'ext_err',
                );
            }
        } 
    }

    private static function _removeDirectoryResume($file_name){
        if(!empty($file_name)){
            $path  = base_path(USER_RESUME_PATH);
            $file   = $path.$file_name; 
            if(file_exists($file)) {
                unlink($file);
            }
        } 
        return true;
    }

    public static function deleteResume($user_id = null){
        //deleting old resume
        $user = User::select('id','resume')->where('id',$user_id)->first();
        $old_img = $user->getActualAttribute('resume');
        Self::_removeDirectoryResume($old_img);   

        $user->resume = '';
        if($user->save()){
            return true;
        } else{
            return false;
        }           
    }

    public static function _getImgPath($img_type = null){
        if($img_type == '1'){       //1= main image
            return base_path(USER_PROFILE_MAIN_IMG_PATH);
        } else{                     //0= normal image
            return base_path(USER_PROFILE_IMG_PATH);
        }
    }

    public static function saveUserMainImageByAdmin($request,$user_id){ //for website
        if($request->file('image')){
            $file       = $request->file('image');
            $file_name  = $file->getClientOriginalName();
            $file_ext   = strtolower($file->getClientOriginalExtension());
            $allow_ext  = ['jpg','png','jpeg'];

            $store_path = Self::_getImgPath(1);

            if(in_array($file_ext,$allow_ext)) {
                $new_file_name = self::generateImageName().'.'.$file_ext;
                
                Self::makeThumb($file->getRealPath(), $store_path, $new_file_name);

                if($file->move($store_path, $new_file_name)) { 

                    $del = Self::deleteUserMainImage($user_id);

                    $user = User::where('id',$user_id)->first();
                    $user->image = $new_file_name;
                    if($user->save()){

                        //saving img thumbnail
                        // Self::makeThumb($store_path, $new_file_name, $file_ext);
                    }
                }
            }
            return  true;
        }
    }

    public static function saveUserMultiImagesByAdmin($request,$user_id){ //for website
                // echo '<pre>'; print_r($_FILES); 
                // echo '<pre>'; print_r($request->file('multi_images'));  die;

        if($request->file('multi_images')){
            foreach($request->file('multi_images') as $key => $value){ 
                $file       = $request->file('multi_images')[$key];
                $file_name  = $file->getClientOriginalName(); 
                // echo '<pre>'; print_r($file_name);  die;
                $file_ext   = strtolower($file->getClientOriginalExtension());
                $allow_ext  = ['jpg','png','jpeg'];
                $store_path = Self::_getImgPath(0);

                if(in_array($file_ext,$allow_ext)) {
                    $new_file_name = self::generateImageName().'.'.$file_ext;

                    Self::makeThumb($file->getRealPath(), $store_path, $new_file_name);

                    if($file->move($store_path, $new_file_name)) { 
                        $img           = new UserImage;
                        $img->user_id  = $user_id;
                        $img->name     = $new_file_name;
                        if($img->save()) {

                            //saving img thumbnail
                            // Self::makeThumb($store_path, $new_file_name, $file_ext);
                        }

                    }
                } //echo '1'; die;
            }
            return  true;
        }
    }

    public static function makeThumb($file, $store_path,$new_file) {

        $img = Image::make($file);
                $img->orientate();
                $img->resize(512, 512, function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->save($store_path.'thumb/'.$new_file);
    }   

    /* public static function _saveEditProfileImages($request,$user_id){ //for website
        if($request->file('image')){
            foreach($request->file('image') as $key => $value){
                $file       = $request->file('image')[$key];
                $file_name  = $file->getClientOriginalName();
                $file_ext   = $file->getClientOriginalExtension();
                $allow_ext  = ['jpg','png','jpeg'];

                if(in_array($file_ext,$allow_ext)) {
                    $new_file_name = self::generateImageName().'.'.$file_ext;
                    if($file->move(base_path(USER_PROFILE_IMG_PATH), $new_file_name)) { 
                        $img           = new UserImage;
                        $img->user_id  = $user_id;
                        $img->name     = $new_file_name;
                        $img->save();
                    }
                }
            }
            return  true;
        }
    }*/
    
    /*static function makeThumb($src_path, $img_name,$file_ext, $desired_width = 512) {

        $src = $src_path.$img_name;
        $dest = $src_path.'thumb/'.$img_name;
        // $size = getimagesize($src);        print_r($size); die;
 
        // read the source image 
        if($file_ext == 'jpg'){
            $source_image = imagecreatefromjpeg($src);
        } elseif($file_ext == 'jpeg'){
            $source_image = imagecreatefromjpeg($src);
        } elseif($file_ext == 'png'){
            $source_image = imagecreatefrompng($src);
        } else{
            return false;
        }

        $exif = exif_read_data($src);
        if(!empty($exif['Orientation'])) {
            switch($exif['Orientation']) {
            case 8:
                $source_image = imagerotate($source_image,90,0);
                break;
            case 3:
                $source_image = imagerotate($source_image,180,0);
                break;
            case 6:
                $source_image = imagerotate($source_image,-90,0);
                break;
            } 
        }

        $width = imagesx($source_image);
        $height = imagesy($source_image);

        // find the "desired height" of this thumbnail, relative to the desired width  
        $desired_height = floor($height * ($desired_width / $width));

        // create a new, "virtual" image 
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

        // copy source image at a resized size 
        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);


        // create the physical thumbnail image to its destination 
        if($file_ext == 'jpg'){
            $resp = imagejpeg($virtual_image, $dest);
        } elseif($file_ext == 'jpeg'){
            $resp = imagejpeg($virtual_image, $dest);
        } elseif($file_ext == 'png'){
            $resp = imagepng($virtual_image, $dest);
        } else{
            $resp = false;
        }
        return $resp;
    }*/
}

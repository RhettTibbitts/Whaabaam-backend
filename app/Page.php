<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages';

    public static function getContent($slug){
    	return Page::where('slug',$slug)->value('content');
    } 

    public static function getPages(){
    	return Page::get();
    } 

    public static function editPage($request,$page_id){
    	$page = Page::where('id',$page_id)->first();
    	$page->content = $request->content;
    	if($page->save()){
    		return true;
    	} else{
    		return false;    		
    	}
    } 

}
	
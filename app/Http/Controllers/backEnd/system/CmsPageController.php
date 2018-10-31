<?php
namespace App\Http\Controllers\backEnd\system;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Page;

class CmsPageController extends Controller
{
    public function __construct()
    {                
        // $this->middleware('guest');
    }
    
    public function index() {
        $pages = Page::getPages();        
        return view('backEnd.system.cms_page.index', compact('pages'));
    }
    
    public function edit(Request $request,$page_id) {
		
		if($request->isMethod('post')){
        
	        $edited = Page::editPage($request,$page_id);        
	        if($edited){
				return redirect('admin/pages')->with('success','Page has been edited successfully');
	        } else{
                return redirect()->back()->with('error',COMMON_ERR);
	        }
	    } else{
	        $page = Page::find($page_id);        
        	return view('backEnd.system.cms_page.form', compact('page'));
	    }
    }

}
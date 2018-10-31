<?php

namespace App\Http\Controllers\backEnd;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserReport;
use App\UserReport;

class ReportController extends Controller
{
	public function index(Request $request){
		$reports = UserReport::getReports($request,true);
		return view('backEnd.system.military.index', compact('reports'));
	}

	public function view($report_id = null){

		/*if($request->isMethod('post')){
			$edited = UserReport::editUserReport($request, $report_id);
			
			if($edited == true){
				return redirect('/admin/reports')->with('success','UserReport has been edited successfully');
			} else{
				return redirect()->back()->with('error',COMMON_ERR);
			}
		}*/

		$military = UserReport::where('id',$report_id)->first();		
		if(!empty($military)){
			return view('backEnd.system.military.form', compact('military'));
		} else{
			return redirect()->back()->with('error','UserReport not found');
		}
	}

	public function delete($report_id = null){
		$deleted = UserReport::deleteUserReport($report_id);
		if($deleted){
			return redirect('admin/reports')->with('success','UserReport has been deleted successfully');
		} else{
			return redirect()->back()->with('error',COMMON_ERR);
		}
	}

}
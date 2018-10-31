<?php

namespace App\Http\Controllers\backEnd;
use App\Http\Controllers\Controller;
use Auth;
use App\User, App\Military, App\Political, App\Relationship, App\Religion, App\State, App\City;

class DashboardController extends Controller
{
	public function index(){
		if (!Auth::guard('admin')->check()) {
            return redirect('/admin/login');
        }
        $users_count = User::where('status',1)->count();
        $militaries_count = Military::count();
        $politicals_count = Political::count();
        $relationships_count = Relationship::count();
        $religions_count = Religion::count();
        $staties_count = State::count();
        $cities_count = City::count();

		return view('backEnd.dashboard', compact('users_count','militaries_count','politicals_count','relationships_count','religions_count','staties_count','cities_count'));
	}


}

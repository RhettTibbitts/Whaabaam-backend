<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\State;
use App\City;

class LocationController extends Controller
{
	public function get_states($country_id = null){
		$states = State::getStates();

		$options = '<option value="">Select State</option>';
		foreach($states as $state){
			$options .= '<option value="'.$state['id'].'" >'. ucfirst($state['name']).'</option>';
		}
		echo $options; die;
	}

	public function get_cities($state_id = null){
		$cities = City::getCities($state_id);

		$options = '<option value="">Select City</option>';
		foreach($cities as $city){
			$options .= '<option value="'.$city['id'].'">'.ucfirst($city['name']).'</option>';
		}
		echo $options; 
	}



}

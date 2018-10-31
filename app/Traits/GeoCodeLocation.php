<?php
namespace App\Traits;

trait GeoCodeLocation{
	
	public function getLocationFromLatLong($lat=null,$long=null){
        
        //8.407168
        //Samrala Chowk/Coordinates 30.9108° N, 75.8793° E
        $address        = "";
        $city           = "";
        $state          = "";
        $country        = "";

        $geoLocation    = array();
        $URL            = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$long.'&sensor=false';
        $data           = file_get_contents($URL);
        $geoAry         = json_decode($data,true);

        if(!empty($geoAry)) {
            for($i=0;$i<count($geoAry['results']);$i++){
                if($geoAry['results'][$i]['types'][0]=='sublocality_level_1'){
                    
                    $address = (isset($geoAry['results'][$i]['address_components'][0]['long_name'])) ? $geoAry['results'][$i]['address_components'][0]['long_name'] : '';

                    $city    = (isset($geoAry['results'][$i]['address_components'][1]['long_name'])) ? $geoAry['results'][$i]['address_components'][1]['long_name'] : '';

                    $state   = (isset($geoAry['results'][$i]['address_components'][3]['long_name'])) ? $geoAry['results'][$i]['address_components'][3]['long_name'] : '';
                    $country = (isset($geoAry['results'][$i]['address_components'][4]['long_name'])) ? $geoAry['results'][$i]['address_components'][4]['long_name'] : '';
                    break;
                }else{
                    $address = (isset($geoAry['results'][0]['address_components'][2]['long_name'])) ? $geoAry['results'][0]['address_components'][2]['long_name'] : '';
                    $city    = (isset($geoAry['results'][0]['address_components'][3]['long_name'])) ? $geoAry['results'][0]['address_components'][3]['long_name'] : '';
                    $state   = (isset($geoAry['results'][0]['address_components'][5]['long_name'])) ? $geoAry['results'][0]['address_components'][5]['long_name'] : '';
                    $country = (isset($geoAry['results'][0]['address_components'][6]['long_name'])) ? $geoAry['results'][0]['address_components'][6]['long_name'] : '';
                }
            }   
        }

        $geoLocation = array(
            'address'=>$address,
            'city'=>$city,
            'state'=>$state,
            'country'=>$country
        );
        
        $geoLocation = implode(', ', $geoLocation);
        return $geoLocation;
        //echo '<pre>'; print_r($geoAry); die;
    }

    function validateLatLong($lat, $long) {
        return preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?),[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $lat.','.$long);
    }
}
<?php

function getAllLocations() {
        $result = getAll('locations');   
	echo $result;
}

function getLocationsWithCountry(){
    $sql = "SELECT locations.id, locations.city, locations.country_id, countries.country_name  FROM locations, countries where locations.country_id=countries.id";
    
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);  
        //$stmt->bindParam("id", $id);
        $stmt->execute();			
        $location = $stmt->fetchAll(PDO::FETCH_OBJ);		
        $db = null;
        if(!empty($location)){
                echo '{"type":"success","location": ' . json_encode($location) . '}'; 
        }else{
                echo '{"type":"error","message":"No record found"}'; 
        }
    } catch(PDOException $e) {
            return '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}
function getLocation($id) {
        $result = findById($id,'locations');
        echo $result;	
}

function getAllActiveLocation() {    
           
	//$sql = "SELECT locations.id, locations.name, lacation_category_map.category_id from locations LEFT JOIN lacation_category_map ON locations.id = lacation_category_map.location_id";
	
	$sql = "SELECT locations.id, locations.city, locations.country_id  FROM locations where locations.is_active=1";
	
	
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		//$stmt->bindParam("id", $id);
		$stmt->execute();			
		$location = $stmt->fetchAll(PDO::FETCH_OBJ);		
		$db = null;
		if(!empty($location)){
		        echo '{"type":"success","location": ' . json_encode($location) . '}'; 
		}else{
		        echo '{"type":"error","message":"No record found"}'; 
		}
	} catch(PDOException $e) {
		return '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function addLocation() {	
	
	$request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	//print_r($body);exit;
	if(isset($body->id)){
	        unset($body->id);
	}
	$unique_field = array();
	$unique_field['city']=$body->city;
        $unique_field['country_id'] = $body->country_id;
	$rowcount = rowCountByCondition($unique_field,'locations');
	//echo $rowcount;exit;
	if($rowcount==0){
	   $allinfo['save_data'] = $body;
	   
	    //$allinfo['unique_data'] = $unique_field;
	  $location_details  = add(json_encode($allinfo),'locations');
          //var_dump($location_details);
          //exit;
	  if(!empty($location_details)){
	    /* $user_details = add(json_encode($allinfo),'coupons');
	    $user_details = json_decode($user_details);*/
	
	    $result = '{"type":"success","message":"Added Succesfully"}'; 
	  }
	  else{
	       $result = '{"type":"error","message":"Not Added"}'; 
	  }
	}
	else{
	    $result = '{"type":"error","message":"Already exists"}'; 
	}
	echo $result;
	
}

function updateLocation($id) {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$coupon = json_decode($body);
	if(isset($coupon->id)){
	        unset($coupon->id);
	}	
	$allinfo['save_data'] = $coupon;
        $allinfo['country_id'] = $coupon->country_id;
	$location_details = edit(json_encode($allinfo),'locations',$id);
	if(!empty($location_details)){
	    $result = '{"type":"success","message":"Changed Succesfully"}'; 
	}
	else{
	    $result = '{"type":"error","message":"Not Changed"}'; 
	}
	echo $result;
	
}

function deleteLocation($id) {
       $result =  delete('locations',$id);
       echo $result;	
}

function getCountries()
{
    $result = getAll('countries');   
    echo $result;
}

/**/



?>

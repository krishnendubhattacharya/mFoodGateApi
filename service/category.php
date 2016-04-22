<?php
function getAllCats() {
        $result = getAll('category');
	echo $result;
}
function getCat($id) {
        $result = findById($id,'category');
        echo $result;	
}

function getAllActiveCats() {        
	$sql = "SELECT * FROM category WHERE is_active=1 ORDER BY seq ASC";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		//$stmt->bindParam("id", $id);
		$stmt->execute();			
		$category = $stmt->fetchAll(PDO::FETCH_OBJ); 
		$db = null;
		if(!empty($category)){
		        echo '{"type":"success","category": ' . json_encode($category) . '}'; 
		}else{
		        echo '{"type":"error","message":"No record found"}'; 
		}
	} catch(PDOException $e) {
		return '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function addCat() {	
	
	$request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	//print_r($body);exit;
	if(isset($body->id)){
	        unset($body->id);
	}
	$unique_field = array();
	$unique_field['name']=$body->name;
	$rowcount = rowCount(json_encode($unique_field),'category');
	//echo $rowcount;exit;
	if($rowcount==0){
	   $allinfo['save_data'] = $body;
	   
	    //$allinfo['unique_data'] = $unique_field;
	  $cat_details  = add(json_encode($allinfo),'category');
	  if(!empty($cat_details)){
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

function updateCat($id) {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$coupon = json_decode($body);
	if(isset($coupon->id)){
	        unset($coupon->id);
	}	
	$allinfo['save_data'] = $coupon;
	$coupon_details = edit(json_encode($allinfo),'category',$id);
	if(!empty($coupon_details)){
	    $result = '{"type":"success","message":"Changed Succesfully"}'; 
	}
	else{
	    $result = '{"type":"error","message":"Not Changed"}'; 
	}
	echo $result;
	
}

function deleteCat($id) {
       $result =  delete('category',$id);
       echo $result;	
}


/**/



?>

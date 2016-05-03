<?php
function getAllCats() {
        $result = json_decode(getAll('category'));
        if(!empty($result->category))
        {
            $result->category = array_map(function($t){
                if(!empty($t->icon))
                    $t->icon_url = SITEURL.'category_icons/'.$t->icon;
                return $t;
            }, $result->category);
        }
	echo json_encode($result);
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
                        $category = array_map(function($t){
                            if(!empty($t->icon))
                                $t->icon_url = SITEURL.'category_icons/'.$t->icon;
                            return $t;
                        },$category);
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
	   if(!empty($body->icon))
           {
               $body->icon->filename = time().$body->icon->filename;
               file_put_contents('category_icons/'.$body->icon->filename, base64_decode($body->icon->base64)); 
               $body->icon = $body->icon->filename;
           }
               
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
        if(!empty($coupon->icon) && isset($coupon->icon->filename))
        {
            $coupon->icon->filename = time().$coupon->icon->filename;
            file_put_contents('category_icons/'.$coupon->icon->filename, base64_decode($coupon->icon->base64)); 
            $coupon->icon = $coupon->icon->filename;
        }
        else
        {
            unset($coupon->icon);
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

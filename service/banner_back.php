<?php  
function getAllBanner() {
   
	$sql = "SELECT *,B.id as id  FROM banners as B,users as M ,restaurants as R where B.merchant_id=M.id and B.restaurant_id=R.id";
    
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);  
        //$stmt->bindParam("id", $id);
        $stmt->execute();			
        $location = $stmt->fetchAll(PDO::FETCH_OBJ);		
        $db = null;
        if(!empty($location)){
                $location = array_map(function($t){
                    if(!empty($t->image))
                        $t->image_url = SITEURL."banner_images/".$t->image;
                        
                   return $t;
                },$location);
                $result = '{"type":"success","banner": ' . json_encode($location) . '}'; 
        }else{
                $result = '{"type":"error","message":"No record found"}'; 
        }
    } catch(PDOException $e) {
            $result = '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
	 echo $result;
}

function getActiveBanner() {
   
	$sql = "SELECT * from banners where banners.is_active=1 and DATE(banners.start_date) <= CURDATE() and DATE(banners.end_date)>= CURDATE()";
    
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);  
        //$stmt->bindParam("id", $id);
        $stmt->execute();			
        $banners = $stmt->fetchAll(PDO::FETCH_OBJ);
        $count = $stmt->rowCount();		
        $db = null;
        if(!empty($banners)){
                /*$banners = array_map(function($t){
                    if(!empty($t->image))
                        $t->image_url = SITEURL."banner_images/".$t->image;
                        
                   return $t;
                },$banners);*/
                
		
		for($i=0;$i<$count;$i++){
		        $img = SITEURL."banner_images/".$banners[$i]->image;
                        $banners[$i]->image_url = $img; 
		}
                $result = '{"type":"success","banner": ' . json_encode($banners) . '}'; 
        }else{
                $result = '{"type":"error","message":"No record found"}'; 
        }
    } catch(PDOException $e) {
            $result = '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
	 echo $result;
}

function getActiveBannerOther($id) {
   
	$sql = "SELECT * from banners where banners.id !=:id banners.is_active=1 and DATE(banners.start_date) <= CURDATE() and DATE(banners.end_date)>= CURDATE()";
    
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);  
        $stmt->bindParam("id", $id);
        $stmt->execute();			
        $banners = $stmt->fetchAll(PDO::FETCH_OBJ);
        $count = $stmt->rowCount();		
        $db = null;
        if(!empty($banners)){
                /*$banners = array_map(function($t){
                    if(!empty($t->image))
                        $t->image_url = SITEURL."banner_images/".$t->image;
                        
                   return $t;
                },$banners);*/
                
		
		for($i=0;$i<$count;$i++){
		        $img = SITEURL."banner_images/".$banners[$i]->image;
                        $banners[$i]->image_url = $img; 
		}
                $result = '{"type":"success","banner": ' . json_encode($banners) . '}'; 
        }else{
                $result = '{"type":"error","message":"No record found"}'; 
        }
    } catch(PDOException $e) {
            $result = '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
	 echo $result;
}

function getActiveBannerOther($id) {
   
	$sql = "SELECT * from banners where banners.id !=:id banners.is_active=1 and DATE(banners.start_date) <= CURDATE() and DATE(banners.end_date)>= CURDATE()";
    
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);  
        $stmt->bindParam("id", $id);
        $stmt->execute();			
        $banners = $stmt->fetchAll(PDO::FETCH_OBJ);
        $count = $stmt->rowCount();		
        $db = null;
        if(!empty($banners)){
                /*$banners = array_map(function($t){
                    if(!empty($t->image))
                        $t->image_url = SITEURL."banner_images/".$t->image;
                        
                   return $t;
                },$banners);*/
                
		
		for($i=0;$i<$count;$i++){
		        $img = SITEURL."banner_images/".$banners[$i]->image;
                        $banners[$i]->image_url = $img; 
		}
                $result = '{"type":"success","banner": ' . json_encode($banners) . '}'; 
        }else{
                $result = '{"type":"error","message":"No record found"}'; 
        }
    } catch(PDOException $e) {
            $result = '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
	 echo $result;
}

function addBanner() {	
	
	$request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	//print_r($body);exit;
	if(isset($body->id)){
	        unset($body->id);
	}
        
	//unset($body->is_active); 
	if(!empty($body->image))
	{
		file_put_contents('./banner_images/'.$body->image->filename,  base64_decode($body->image->base64));
		$body->image = $body->image->filename;
	}
	$body->created_on = date('Y-m-d H:i:s');
	//$body->is_active = 1;
	$body->number_of_click = 0;
//unset($body->image);
	$allinfo['save_data'] = $body;
	   
	//$allinfo['unique_data'] = $unique_field;
	$location_details  = add(json_encode($allinfo),'banners');
	if(!empty($location_details)){
		/* $user_details = add(json_encode($allinfo),'coupons');
		$user_details = json_decode($user_details);*/
		$banner_details = json_decode($location_details);
		$result = '{"type":"success","message":"Added Succesfully"}'; 
	}
	else{
		$result = '{"type":"error","message":"Not Added"}'; 
	}
	
	echo $result;
}

function getBannerDetails($id)
{
    $rarray = array();
    $restaurant = findByIdArray($id,'banners');
    if(!empty($restaurant))
    {
        $rarray['restaurant'] = $restaurant;
        
        if(!empty($restaurant['image']))
        {
            $rarray['restaurant']['image_url'] = SITEURL."banner_images/".$restaurant['image'];
        }
        $result = json_encode(array('type'=>"success",'data'=>$rarray));
    }
    else
    {
        $result = '{type:"error",message:"Not Added"}'; 
    }
    echo($result);
    exit;
}

function updateBanner($id) {	
	
	$request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	//print_r($body);exit;
        $id = $body->id;
	if(isset($body->id)){
	        unset($body->id);
	}
        
        if(isset($body->image_url))
            unset($body->image_url);
         
        if(!empty($body->image))
        {
            file_put_contents('./banner_images/'.$body->image->filename,  base64_decode($body->image->base64));
            $body->image = $body->image->filename;
        }
        else {
            unset($body->image);
        }
	//unset($body->image);
           $allinfo['save_data'] = $body;
           
           $location_details = edit(json_encode($allinfo),'banners',$id);
           
          if(!empty($location_details)){
              $restaurant_details = json_decode($location_details);
              //var_dump($location_details);
              //exit;
              

            $result = '{"type":"success","message":"Updated Succesfully"}'; 
          }
          else{
               $result = '{"type":"error","message":"Not Updated. Please try again."}'; 
          }
	
	echo $result;
	
}

function deleteBanner($id) {
       $result =  delete('banners',$id);
       echo $result;	
}


?>

<?php  
function getBannersClicked($banid,$userid=null)
{
    $rarray = array();
    $update_qry = "UPDATE banners set number_of_click = number_of_click+1 where id=$banid";
    if(updateByQuery($update_qry))
    {
        if(!empty($userid))
        {
            $add_details = findByIdArray($banid,'banners');
            $already_exists_query = "SELECT * from point_details where type='B' and parent_id=".$add_details['id']." and user_id=".$userid;
            $if_exist = findByQuery($already_exists_query);
            echo '<pre>';
            //var_dump($add_details);
            var_dump($if_exist);
            //var_dump($add_details);
            if(!empty($add_details) && ($add_details['target_click']>=$add_details['number_of_click']) && empty($if_exist))
            {
                $temp = array();
                $temp['user_id'] = $userid;
                $temp['points'] = $add_details['mpoint_get_per_click'];
                $temp['point_id'] = $add_details['mpoint_name'];
                $temp['merchant_id'] = $add_details['merchant_id'];
                $temp['source'] = "earn from banner click";
                $temp['type'] = 'B';
                $temp['parent_id'] = $add_details['id'];
                $temp['date'] = $add_details['start_date'];
                $temp['expire_date'] = $add_details['end_date'];
                $temp['redeemed_points'] = 0;
                $temp['remaining_points'] = $add_details['mpoint_get_per_click'];
                $t = add(json_encode(array('save_data' => $temp)),'points');
                
                $temp = array();
                $temp['user_id'] = $userid;
                $temp['points'] = $add_details['mpoint_get_per_click'];
                $temp['point_id'] = $add_details['mpoint_name'];
                $temp['merchant_id'] = $add_details['merchant_id'];
                $temp['source'] = "earn from banner click";
                $temp['type'] = 'B';
                $temp['parent_id'] = $add_details['id'];
                $temp['date'] = $add_details['start_date'];
                $temp['expire_date'] = $add_details['end_date'];
                $temp['redeemed_points'] = 0;
                $temp['remaining_points'] = $add_details['mpoint_get_per_click'];
                $temp['transaction_type'] = 0;
                $t = add(json_encode(array('save_data' => $temp)),'point_details');
                
                $rarray = array('type' => 'success', 'message' => 'Congratulation. You have got '.$add_details['mpoint_get_per_click'].' m-points.');
            }
            else {
            //echo 'helllo';
                //$rarray = array('type' => 'error', 'message' => 'Banners not found.');
            } 
        }
        else
        {
            $rarray = array('type' => 'success', 'message' => 'Banners clicked successfully.');
        }
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'Internal error. Please try again later.');
    }
    echo json_encode($rarray);
}

function getAllBanner() {
   
	$sql = "SELECT *,B.id as id,R.title as res_title,B.title as title  FROM banners as B,users as M ,restaurants as R where B.merchant_id=M.id and B.restaurant_id=R.id";
    
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
   
	$sql = "SELECT * from banners where banners.is_active=1 and DATE(banners.start_date) <= CURDATE() and DATE(banners.end_date)>= CURDATE() order by banners.created_on DESC";
    
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
   
	$sql = "SELECT * from banners where banners.is_active=1 and DATE(banners.start_date) <= CURDATE() and DATE(banners.end_date)>= CURDATE()";
    
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

function getHotBanner() {
   
	$sql = "SELECT * from banners where banners.is_active=1 and DATE(banners.start_date) <= CURDATE() and DATE(banners.end_date)>= CURDATE() order by banners.number_of_click DESC";
    
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

function getUpdateBannerOther($id) {
   
	$sql = "SELECT * from banners where banners.id !=:id and banners.is_active=1 and DATE(banners.start_date) <= CURDATE() and DATE(banners.end_date)>= CURDATE() order by banners.created_on DESC";
    
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
	
	if(isset($body->outlet_id))
        {
            $outlets = $body->outlet_id;
        }
	unset($body->outlet_id);
        
	//unset($body->is_active); 
	if(!empty($body->image))
	{
	        $body->image->filename = time().$body->image->filename;
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
		
                foreach($outlets as $outlet)
                {
                    $temp = array();
                    $temp['banner_id'] = $banner_details->id;
                    $temp['outlet_id']   = $outlet->id;
                    $data = add(json_encode(array('save_data' => $temp)),'banners_outlet_map');
                    //echo $data;
                }
		
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
        $rarray['banner'] = $restaurant;
        $rest = findByIdArray($restaurant['restaurant_id'],'restaurants');
		$merchant = findByIdArray($restaurant['merchant_id'],'users');
		$rarray['restaurant'] = $rest;
		$rarray['merchant'] = $merchant;
        if(!empty($restaurant['image']))
        {
            $rarray['banner']['image_url'] = SITEURL."banner_images/".$restaurant['image'];
        }
        $outlets = findByConditionArray(array('banner_id' => $id),'banners_outlet_map');
        $result = json_encode(array('type'=>"success",'data'=>$rarray,'outlets'=>$outlets));
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
	
	if(isset($body->outlet_id))
        {
            $outlets = $body->outlet_id;
        }
	unset($body->outlet_id);
        
        if(isset($body->image_url))
            unset($body->image_url);
         
        if(!empty($body->image))
        {
                $body->image->filename = time().$body->image->filename;
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
              
              deleteAll('banners_outlet_map',array('banner_id' => $id));
              foreach($outlets as $outlet)
                {
                    $temp = array();
                    $temp['banner_id'] = $id;
                    $temp['outlet_id']   = $outlet->id;
                    $data = add(json_encode(array('save_data' => $temp)),'banners_outlet_map');
                    //echo $data;
                }
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

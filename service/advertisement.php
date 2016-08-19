<?php  
function getAdsClicked($adid,$userid=null)
{
    $rarray = array();
    $update_qry = "UPDATE advertisements set number_of_click = number_of_click+1 where id=$adid";
    if(updateByQuery($update_qry))
    {
        if(!empty($userid))
        {
            $add_details = findByIdArray($adid,'advertisements');
            $already_exists_query = "SELECT * from point_details where type='A' and parent_id=".$add_details['id']." and user_id=".$userid;
            $if_exist = findByQuery($already_exists_query);
            if(!empty($add_details) && ($add_details['target_click']>=$add_details['number_of_click']) && empty($if_exist))
            {
                $temp = array();
                $temp['user_id'] = $userid;
                $temp['points'] = $add_details['mpoint_get_per_click'];
                $temp['point_id'] = $add_details['mpoint_name'];
                $temp['merchant_id'] = $add_details['merchant_id'];
                //$temp['advertise_id'] = $add_details['id'];
                $temp['source'] = "earn from adv click";
                $temp['date'] = $add_details['start_date'];
                $temp['expire_date'] = $add_details['end_date'];
                $temp['redeemed_points'] = 0;
                $temp['type'] = 'A';
                $temp['parent_id'] = $add_details['id'];
                $temp['remaining_points'] = $add_details['mpoint_get_per_click'];
                $t = add(json_encode(array('save_data' => $temp)),'points');
                
                $temp = array();
                $temp['user_id'] = $userid;
                $temp['points'] = $add_details['mpoint_get_per_click'];
                $temp['point_id'] = $add_details['mpoint_name'];
                $temp['merchant_id'] = $add_details['merchant_id'];
                //$temp['advertise_id'] = $add_details['id'];
                $temp['source'] = "earn from adv click";
                $temp['date'] = $add_details['start_date'];
                $temp['expire_date'] = $add_details['end_date'];
                $temp['redeemed_points'] = 0;
                $temp['type'] = 'A';
                $temp['parent_id'] = $add_details['id'];
                $temp['remaining_points'] = $add_details['mpoint_get_per_click'];
                $temp['transaction_type'] = 0;
                $t = add(json_encode(array('save_data' => $temp)),'point_details');
                $rarray = array('type' => 'success', 'message' => 'Congratulation. You have got '.$add_details['mpoint_get_per_click'].' m-points.');
            }
            else {
                //$rarray = array('type' => 'error', 'message' => 'Advertisement not found.');
            } 
        }
        else
        {
            $rarray = array('type' => 'success', 'message' => 'Advertisement clicked successfully.');
        }
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'Internal error. Please try again later.');
    }
    echo json_encode($rarray);
}

function getActiveAdsByLocation($location_id)
{
    $rarray = array();
    $locations = findByConditionArray(array('location_id' => $location_id),'advertisement_location_map');
    if(!empty($locations))
    {
        $location_ids = array_column($locations,'advertisement_id');
        $query = "select * from advertisements where DATE(start_date)<=CURDATE() and DATE(end_date)>=CURDATE() and is_active=1 and id in(".implode(",",$location_ids).") order by advertisements.created_on DESC";
        $result = findByQuery($query);
        if(!empty($result))
        {
            $result = array_map(function($t){
                if(!empty($t['image']))
                    $t['image_url'] = SITEURL."advertisement_images/".$t['image'];
                    $t['expire_date'] = date('d M,Y H:i:s', strtotime($t['end_date']));
                return $t;
            },$result);
            $rarray = array('type' => 'success', 'ads' => $result);
        }
        else
        {
            $rarray = array('type' => 'error', 'message' => 'No ads found.');
        }
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'No ads found.');
    }
    echo json_encode($rarray);
}

function getHotAds()
{
    $rarray = array();
    
    

        $query = "select * from advertisements where DATE(start_date)<=CURDATE() and DATE(end_date)>=CURDATE() and is_active=1 order by advertisements.number_of_click DESC";
        $result = findByQuery($query);
        if(!empty($result))
        {
            $result = array_map(function($t){
                if(!empty($t['image']))
                    $t['image_url'] = SITEURL."advertisement_images/".$t['image'];
                    $t['expire_date'] = date('d M,Y H:i:s', strtotime($t['end_date']));
                return $t;
            },$result);
            $rarray = array('type' => 'success', 'ads' => $result);
        }
        else
        {
            $rarray = array('type' => 'error', 'message' => 'No ads found.');
        }
    
    
    echo json_encode($rarray);
}
    
function getAllAds() {
   
	$sql = "SELECT *,B.id as id,B.title as title,R.title as res_title  FROM advertisements as B,users as M ,restaurants as R where B.merchant_id=M.id and B.restaurant_id=R.id";
    
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
                        $t->image_url = SITEURL."advertisement_images/".$t->image;
                        
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

function getAllAdsLocation() {
   
	$sql = "SELECT * FROM advertisement_locations";
    
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);  
        //$stmt->bindParam("id", $id);
        $stmt->execute();			
        $alladslocation = $stmt->fetchAll(PDO::FETCH_OBJ);		
        $db = null;
        if(!empty($alladslocation)){                
                $result = '{"type":"success","location": ' . json_encode($alladslocation) . '}'; 
        }else{
                $result = '{"type":"error","message":"No record found"}'; 
        }
    } catch(PDOException $e) {
            $result = '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
	 echo $result;
}

function addAds() {	
	
	$request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	//print_r($body);exit;
	if(isset($body->id)){
	        unset($body->id);
	}
        
        if(isset($body->location_id))
        {
            $locations = $body->location_id;
        }
	unset($body->location_id); 
        
        $outlets = array();
        if(isset($body->outlet_id))
        {
            $outlets = $body->outlet_id;
        }
	unset($body->outlet_id); 
        
	if(!empty($body->image))
	{
	        $body->image->filename = time().$body->image->filename;
		file_put_contents('./advertisement_images/'.$body->image->filename,  base64_decode($body->image->base64));
		$body->image = $body->image->filename;
	}
	$body->created_on = date('Y-m-d H:i:s');
	//$body->is_active = 1;
	$body->number_of_click = 0;
        $body->start_date = $body->start_date." 23:59:59";
        $body->end_date = $body->end_date." 23:59:59";
//unset($body->image);
	$allinfo['save_data'] = $body;
	   
	//$allinfo['unique_data'] = $unique_field;
	$location_details  = add(json_encode($allinfo),'advertisements');
	if(!empty($location_details)){
		/* $user_details = add(json_encode($allinfo),'coupons');
		$user_details = json_decode($user_details);*/
		$banner_details = json_decode($location_details);
		
		foreach($locations as $location)
                {
                      $temp = array();
                      $temp['advertisement_id'] = $banner_details->id;
                      $temp['location_id']   = $location->id;
                      $data = add(json_encode(array('save_data' => $temp)),'advertisement_location_map');
                      //echo $data;
                }

                foreach($outlets as $outlet)
                {
                      $temp = array();
                      $temp['advertisement_id'] = $banner_details->id;
                      $temp['outlet_id']   = $outlet->id;
                      $data = add(json_encode(array('save_data' => $temp)),'advertisement_outlet_map');
                      //echo $data;
                }
		$result = '{"type":"success","message":"Added Succesfully"}'; 
	}
	else{
		$result = '{"type":"error","message":"Not Added"}'; 
	}
	
	echo $result;
}

function getAdsDetails($id)
{
    $rarray = array();
    $restaurant = findByIdArray($id,'advertisements');
    if(!empty($restaurant))
    {
        $rarray['restaurant'] = $restaurant;
        
        if(!empty($restaurant['image']))
        {
            $rarray['restaurant']['image_url'] = SITEURL."advertisement_images/".$restaurant['image'];
        }
        $rarray['locations'] = findByConditionArray(array('advertisement_id' => $id),'advertisement_location_map');
        $rarray['outlets'] = findByConditionArray(array('advertisement_id' => $id),'advertisement_outlet_map');
        $rarray['merchant'] = findByIdArray($restaurant['merchant_id'],'users');
        $result = json_encode(array('type'=>"success",'data'=>$rarray));
    }
    else
    {
        $result = '{type:"error",message:"Not Added"}'; 
    }
    echo($result);
    exit;
}

function updateAds($id) {	
	
	$request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	//print_r($body);exit;
        $id = $body->id;
	if(isset($body->id)){
	        unset($body->id);
	}
	
	if(isset($body->location_id))
        {
            $locations = $body->location_id;
        }
        unset($body->location_id);
        
        $outlets = array();
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
            file_put_contents('./advertisement_images/'.$body->image->filename,  base64_decode($body->image->base64));
            $body->image = $body->image->filename;
        }
        else {
            unset($body->image);
        }
	//unset($body->image);
        $body->start_date = $body->start_date." 23:59:59";
        $body->end_date = $body->end_date." 23:59:59";
           $allinfo['save_data'] = $body;
           
           $location_details = edit(json_encode($allinfo),'advertisements',$id);
           
          if(!empty($location_details)){
              $restaurant_details = json_decode($location_details);
              //var_dump($location_details);
              //exit;
              
              deleteAll('advertisement_location_map',array('advertisement_id' => $id));
              foreach($locations as $location)
              {
                    $temp = array();
                    $temp['advertisement_id'] = $id;
                    $temp['location_id']   = $location->id;
                    $data = add(json_encode(array('save_data' => $temp)),'advertisement_location_map');
                    //echo $data;
              }
              deleteAll('advertisement_outlet_map',array('advertisement_id' => $id));
              
               foreach($outlets as $outlet)
                {
                      $temp = array();
                      $temp['advertisement_id'] = $id;
                      $temp['outlet_id']   = $outlet->id;
                      $data = add(json_encode(array('save_data' => $temp)),'advertisement_outlet_map');
                      //echo $data;
                }
              

            $result = '{"type":"success","message":"Updated Succesfully"}'; 
          }
          else{
               $result = '{"type":"error","message":"Not Updated. Please try again."}'; 
          }
	
	echo $result;
	
}

function deleteAds($id) {
       $result =  delete('advertisements',$id);
       echo $result;	
}

function getUpdateAdvOther($id) {
   
	$sql = "SELECT * from advertisements where advertisements.id !=:id and advertisements.is_active=1 and DATE(advertisements.start_date) <= CURDATE() and DATE(advertisements.end_date)>= CURDATE() order by advertisements.created_on DESC";
    
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
		        $img = SITEURL."advertisement_images/".$banners[$i]->image;
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

function getActiveAdvOther($id) {
   
	$sql = "SELECT * from advertisements where advertisements.is_active=1 and DATE(advertisements.start_date) <= CURDATE() and DATE(advertisements.end_date)>= CURDATE()";
    
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
		        $img = SITEURL."advertisement_images/".$banners[$i]->image;
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

?>

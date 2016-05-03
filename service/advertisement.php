<?php  
function getActiveAdsByLocation($location_id)
{
    $rarray = array();
    $locations = findByConditionArray(array('location_id' => $location_id),'advertisement_location_map');
    if(!empty($locations))
    {
        $location_ids = array_column($locations,'advertisement_id');
        $query = "select * from advertisements where DATE(start_date)<=CURDATE() and DATE(end_date)>=CURDATE() and is_active=1 and id in(".implode(",",$location_ids).")";
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


?>

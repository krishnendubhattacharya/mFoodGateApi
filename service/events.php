<?php
function addEvent() 
    {
        $request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
        //print_r($body);
        //exit;
        
        /*if($body->eventdata->is_active)
        {
            $body->eventdata->is_active = 1;
        }
        else
        {
            $body->eventdata->is_active = 0;
        }*/
        $body->eventdata->created_on = date('Y-m-d H:i:s');
        if(!empty($body->eventdata->from_date))
        {
            $body->eventdata->from_date = date('Y-m-d',strtotime($body->eventdata->from_date));
        }
        
        if(!empty($body->eventdata->to_date))
        {
            $body->eventdata->to_date = date('Y-m-d',strtotime($body->eventdata->to_date));
        }
        
        
       
        $allinfo['save_data'] = $body->eventdata;
        //print_r($allinfo);

        //$allinfo['unique_data'] = $unique_field;
	$cat_details  = json_decode(add(json_encode($allinfo),'events'));
        //var_dump($cat_details);
        //exit;
        if(!empty($cat_details)){	
            /*if(!empty($body->typedata))
            {
                foreach($body->typedata as $typedata)
                {
                    $temp['save_data'] = array('event_id' => $cat_details->id,'image' => $typedata->image);
                    add(json_encode($temp['save_data']),'event_images');
                }
            }*/
		
            if(!empty($body->typedata))
            {
                foreach($body->typedata as $typedata)
                {
                    //print_r($typedata);
                    $temp['save_data'] = array('event_id' => $cat_details->id,'category_id' => $typedata->id);
                    $s = add(json_encode($temp),'event_category_map');
                    //var_dump
                }
            }
            if(!empty($body->areadata))
            {
                foreach($body->areadata as $areadata)
                {
                    $temp['save_data'] = array('event_id' => $cat_details->id,'location_id' => $areadata->id);
                    add(json_encode($temp),'event_location_map');
                }
            }
	    $result = '{"type":"success","message":"Added Succesfully"}'; 
	  }
	  else{
	       $result = '{"type":"error","message":"Not Added"}'; 
	  }
          echo $result;
          exit;
    }
    
    
function updateEvent($id) {
	$request = Slim::getInstance()->request();
	
	$body = $request->getBody();
	$body = json_decode($body);
       
        $user_id = $id;
		
        if(!empty($body->eventdata->from_date))
        {
            $body->eventdata->from_date = date('Y-m-d',strtotime($body->eventdata->from_date));
        }
        
        if(!empty($body->eventdata->to_date))
        {
            $body->eventdata->to_date = date('Y-m-d',strtotime($body->eventdata->to_date));
        }
        
        if(isset($body->eventdata->id))
            unset($body->eventdata->id);
        
        //print_r($body);
        //exit;
        if(empty($body->eventdata->image))
            unset($body->eventdata->image);
        
        if(isset($body->eventdata->imageurl))
            unset($body->eventdata->imageurl);
        
	$allinfo['save_data'] = $body->eventdata;
        //print_r($allinfo);
        //exit;
	$result = edit(json_encode($allinfo),'events',$id);
        if($result)
        {
            deleteAll('event_location_map',array('event_id' => $id));
            if(!empty($body->areadata))
            {
                foreach($body->areadata as $areadata)
                {
                    $temp['save_data'] = array('event_id' => $id,'location_id' => $areadata->id);
                    add(json_encode($temp),'event_location_map');
                }
            }
            
            deleteAll('event_category_map',array('event_id' => $id));
            if(!empty($body->typedata))
            {
                foreach($body->typedata as $typedata)
                {
                    //print_r($typedata);
                    $temp['save_data'] = array('event_id' => $id,'category_id' => $typedata->id);
                    $s = add(json_encode($temp),'event_category_map');
                    //var_dump
                }
            }
            
            
            
        }
	/*if(!empty($user_details)){
	        $result = '{"type":"success","message":"Updated Succesfully"}'; 
	}else{
	        $result = '{"type":"error","message":"Try Again"}'; 
	}*/
	$result = '{"type":"success","message":"Updated Succesfully"}';
	echo $result;
	
}
    
function getEventsByUser($user_id)  
    {
        $rarray = array();
        try
        {
            $sql = "SELECT * FROM events WHERE user_id=:user_id ORDER BY id DESC";
            $db = getConnection();
            $stmt = $db->prepare($sql); 
            $stmt->bindParam("user_id", $user_id);
            $stmt->execute();
            $points = $stmt->fetchAll(PDO::FETCH_OBJ);
            
			$count = $stmt->rowCount();
		
			for($i=0;$i<$count;$i++){
				$created_on = date('m/d/Y', strtotime($points[$i]->created_on));
				$points[$i]->created_on = $created_on;
				$from_date = date('m/d/Y', strtotime($points[$i]->from_date));
				$points[$i]->from_date = $from_date;
				$to_date = date('m/d/Y', strtotime($points[$i]->to_date));
				$points[$i]->to_date = $to_date;
                                if(!empty($points[$i]->image))
                                    $points[$i]->image_url = SITEURL.'event_images/'.$points[$i]->image;
                                    
                                if($points[$i]->status == 'O')
                                    $points[$i]->status = 'Open';
                                else if($points[$i]->status == 'E')
                                    $points[$i]->status = 'Expired';
                                else
                                    $points[$i]->status = 'Completed';
                                $points[$i]->categories = json_decode(findByCondition(array('event_id'=>$points[$i]->id),'event_category_map'));
                                $points[$i]->locations = json_decode(findByCondition(array('event_id'=>$points[$i]->id),'event_location_map'));
			}	
			/*if(!empty($points))
            {
                $points = array_map(function($t,$k){
                    $t->sl = $k+1;
                    //$t->type = ($t->type=='C'?'Credit':'Debit');
                                    $t->date = date('m/d/Y',  strtotime($t->date));
                    $t->expire_date = date('m/d/Y',  strtotime($t->expire_date));
                    return $t;
                }, $points,  array_keys($points));
            }*/
            $rarray = array('status' => 'success','data' => $points);
        }
        catch(PDOException $e) {
            $rarray = array('status' => 'error','error' => array('text' => $e->getMessage()));
        } 
        echo json_encode($rarray);
        exit;
    }

function eventFilesUpload()
{
    if(!empty($_FILES['files']['tmp_name']['0']))
    {
        move_uploaded_file($_FILES['files']['tmp_name']['0'], 'event_images/'.$_FILES['files']['name']['0']);
        echo json_encode($_FILES['files']['name']['0']);
        exit;
    }
}

function getEvenDetails($id) 
{
    $event_details = findById($id,'events');
    $event_details = json_decode($event_details);
    if(!empty($event_details))
    {
        $event_details->from_date = date('m/d/Y', strtotime($event_details->from_date));
        $event_details->to_date = date('m/d/Y', strtotime($event_details->to_date));
        if(!empty($event_details->image))
            $event_details->image = SITEURL.'event_images/'.$event_details->image;
        else
            $event_details->image = SITEURL.'event_images/default.png';
        
        if($event_details->status == 'O')
            $event_details->status = 'Open';
        else if($event_details->status == 'E')
            $event_details->status = 'Expired';
        else
            $event_details->status = 'Completed';
        
        $event_details->user_details = findByIdArray($event_details->user_id,'users');
        $event_details->locations = array();
        $locations = findByConditionArray(array('event_id' => $id),'event_location_map');
        foreach($locations as $cat)
        {
            $event_details->locations[] = findByIdArray($cat['location_id'],'locations');
        }
        
        $event_details->categories = array();
        $category = findByConditionArray(array('event_id' => $id),'event_category_map');
        foreach($category as $cat)
        {
            $event_details->categories[] = findByIdArray($cat['category_id'],'category');
        }
        
        echo json_encode(array('status' => 'success','data' =>$event_details));
    }
    else
    {
        echo json_encode(array('status' => 'error','message' => 'No data found'));
    }
    //echo $event_details;
    exit;
}

function getImagesByEvent($id)
{
    $condition = array('event_id' => $id);
    $result = findByCondition($condition,'event_images');
    $result = json_decode($result);
    if(!empty($result))
    {
        $result = array_map(function($t){
            $t->image_url = SITEURL.'event_images/'.$t->image;
            return $t;
        },$result);
        echo json_encode(array('status' => 'success','data' =>$result));
    }
    else
    {
        echo json_encode(array('status' => 'error','message' => 'No data found'));
    }
}

function addEventImage()
{
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $allinfo['save_data'] = $body;
	   
    //$allinfo['unique_data'] = $unique_field;
    $location_details  = add(json_encode($allinfo),'event_images');
    //var_dump($location_details);
    //exit;
    if(!empty($location_details)){
      /* $user_details = add(json_encode($allinfo),'coupons');
      $user_details = json_decode($user_details);*/

      $result = '{type:"success",message:"Added Succesfully"}'; 
    }
    else{
         $result = '{type:"error",message:"Not Added"}'; 
    }
    echo $result;
    exit;
}

function getActiveEvents()
{
    $rarray = array();
    try
    {
        $sql = "SELECT * FROM events WHERE status='O' ORDER BY id DESC";
        $db = getConnection();
        $stmt = $db->prepare($sql); 
        //$stmt->bindParam("user_id", $user_id);
        $stmt->execute();
        $points = $stmt->fetchAll(PDO::FETCH_OBJ);

                    $count = $stmt->rowCount();

                    for($i=0;$i<$count;$i++){
                            $created_on = date('m/d/Y', strtotime($points[$i]->created_on));
                            $points[$i]->created_on = $created_on;
                            $from_date = date('m/d/Y', strtotime($points[$i]->from_date));
                            $points[$i]->from_date = $from_date;
                            $to_date = date('m/d/Y', strtotime($points[$i]->to_date));
                            $points[$i]->to_date = $to_date;
                            if(!empty($points[$i]->image))
                                $points[$i]->image_url = SITEURL.'event_images/'.$points[$i]->image;

                            if($points[$i]->status == 'O')
                                $points[$i]->status = 'Open';
                            else if($points[$i]->status == 'E')
                                $points[$i]->status = 'Expired';
                            else
                                $points[$i]->status = 'Completed';
                            $points[$i]->categories = json_decode(findByCondition(array('event_id'=>$points[$i]->id),'event_category_map'));
                            $points[$i]->locations = json_decode(findByCondition(array('event_id'=>$points[$i]->id),'event_location_map'));
                    }	
                    /*if(!empty($points))
        {
            $points = array_map(function($t,$k){
                $t->sl = $k+1;
                //$t->type = ($t->type=='C'?'Credit':'Debit');
                                $t->date = date('m/d/Y',  strtotime($t->date));
                $t->expire_date = date('m/d/Y',  strtotime($t->expire_date));
                return $t;
            }, $points,  array_keys($points));
        }*/
        $rarray = array('status' => 'success','data' => $points);
    }
    catch(PDOException $e) {
        $rarray = array('status' => 'error','error' => array('text' => $e->getMessage()));
    } 
    echo json_encode($rarray);
    exit;
}

//function 

/*function getMerchantsRelatedEvents($merchant_id)
{
    $rarray = array();
    if($merchant_id)
    {
        $merchant_details = findByIdArray($merchant_id,'users');
        if(!empty($merchant_details))
        {
            
        }
        else
        {
            $rarray = array('type' => 'error', 'message' => 'Merchant not found.');
        }
    }
    echo json_encode($rarray);
}*/
?>
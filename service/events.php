<?php
function addEvent() 
    {
        $request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
        $event_location_id = array();
        $event_category_id = array();
        $users = array();
        $from_user_id = $body->eventdata->user_id;
        $from_user_detail = findByConditionArray(array('id' => $from_user_id),'users');
        $from_user_name = $from_user_detail[0]['first_name'].' '.$from_user_detail[0]['last_name'];
        //echo $from_user_name;
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
            $body->eventdata->from_date = date('Y-m-d H:i:s',strtotime($body->eventdata->from_date));
        }
        
        if(!empty($body->eventdata->to_date))
        {
            $body->eventdata->to_date = date('Y-m-d H:i:s',strtotime($body->eventdata->to_date));
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
                    $event_category_id[]=$typedata->id;
                    $s = add(json_encode($temp),'event_category_map');
                    //var_dump
                }
            }
            if(!empty($body->areadata))
            {
                foreach($body->areadata as $areadata)
                {
                    $temp['save_data'] = array('event_id' => $cat_details->id,'location_id' => $areadata->id);
                    $event_location_id[]=$areadata->id;
                    add(json_encode($temp),'event_location_map');
                }
            }
            
            $sql = "SELECT * FROM merchant_location_map WHERE location_id in(".implode(',',$event_location_id).")";
            //$stm = $db->prepare($sql);
            //$stm->execute($outlet_locations);
            $data = findByQuery($sql); 
            if(!empty($data))
            {
                $users = array_column($data, 'user_id');
            }

            //$in  = str_repeat('?,', count($outlet_categories) - 1) . '?';
            $sql = "SELECT * FROM merchant_category_map WHERE category_id in(".implode(',',$event_category_id).")";
            //$stm = $db->prepare($sql);
            //$stm->execute($outlet_categories);
            $cat_data = findByQuery($sql);
            //print_r($events);
            if(!empty($cat_data))
            {
                $cat_users = array_column($cat_data, 'user_id');
                if(!empty($cat_users))
                {
                    foreach($cat_users as $ta)
                    {
                        $users[] = $ta;
                    }
                }
            }
            $users = array_unique($users);
            if(!empty($users))
                {
                    
                        //$event_in  = str_repeat('?,', count($events) - 1) . '?';
                        $sql = "SELECT * FROM users WHERE user_type_id=3 and id in(".implode(',',$users).")";
                        //$db = getConnection();
                        //$stmt = $db->prepare($sql); 
                        //$stmt->bindParam("user_id", $user_id);
                       // $stmt->execute($events);
                        $user_details = findByQuery($sql);
                        //print_r($user_details);
			//print_r($points);
			//exit;

                                    $count = count($user_details);

                                    foreach($user_details as $user_dtl){
                                        $email = $user_dtl['email'];
                                        $from = ADMINEMAIL;
                                        $to = $email;
                                        $subject ='Event Offer';
                                        $body ='<html><body><p>Dear User,</p>

                                                <p>'.$from_user_name.' has posted an event.Please login for bid<br />
                                                
                                                <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">If we can help you with anything in the meantime just let us know by e-mailing&nbsp;</span>'.$from.'<br />
                                                <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Thanks again for signing up with the site</span><span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">!&nbsp;</span></p>

                                                <p>Thanks,<br />
                                                mFood&nbsp;Team</p>

                                                <p>&nbsp;</p></body></html>
                                                ';
                                        //echo $body;
                                        //echo $email;
                                                //$body = "Hello";
                                        sendMail($to,$subject,$body);
                                        
                                    }
                }
            //print_r($users);
            
            //exit;
            
	    $result = '{"type":"success","message":"Added Successfully"}'; 
	  }
	  else{
	       $result = '{"type":"error","message":"Not Added"}'; 
	  }
          echo $result;
          exit;
    }
    
function addTemplate() 
    {
        $request = Slim::getInstance()->request();
	   $body = json_decode($request->getBody());
        $from_user_id = $body->eventdata->user_id;
        $from_user_detail = findByConditionArray(array('id' => $from_user_id),'users');
        $from_user_name = $from_user_detail[0]['first_name'].' '.$from_user_detail[0]['last_name'];
        //echo $from_user_name;
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
        
        
        
       
        $allinfo['save_data'] = $body->eventdata;
      echo '<pre>';  print_r($allinfo);

        //$allinfo['unique_data'] = $unique_field;
	$cat_details  = json_decode(add(json_encode($allinfo),'event_templates'));
        //var_dump($cat_details);
        //exit;
        if(!empty($cat_details)){	
         $result = '{"type":"success","message":"Added Successfully"}'; 
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
            $body->eventdata->from_date = date('Y-m-d H:i:s',strtotime($body->eventdata->from_date));
        }
        
        if(!empty($body->eventdata->to_date))
        {
            $body->eventdata->to_date = date('Y-m-d H:i:s',strtotime($body->eventdata->to_date));
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

function updateTemplate($id) {
	$request = Slim::getInstance()->request();
	
	$body = $request->getBody();
	$body = json_decode($body);
       
        $user_id = $id;
		
                
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
	$result = edit(json_encode($allinfo),'event_templates',$id);
     
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
				$from_date = date('m/d/Y h:i a', strtotime($points[$i]->from_date));
				$points[$i]->from_date = $from_date;
				$from_time = date('h:i a', strtotime($points[$i]->from_date));
				$points[$i]->from_time = $from_time;
				
				$to_date = date('m/d/Y h:i a', strtotime($points[$i]->to_date));
				$points[$i]->to_date = $to_date;
				$to_time = date('h:i a', strtotime($points[$i]->to_date));
				$points[$i]->to_time = $to_time;
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
    
function getTemplateByUser($user_id)  
    {
        $rarray = array();
        try
        {
            $sql = "SELECT * FROM event_templates WHERE user_id=:user_id ORDER BY id DESC";
            $db = getConnection();
            $stmt = $db->prepare($sql); 
            $stmt->bindParam("user_id", $user_id);
            $stmt->execute();
            $points = $stmt->fetchAll(PDO::FETCH_OBJ);
            
			$count = $stmt->rowCount();
		
			for($i=0;$i<$count;$i++){
				$created_on = date('m/d/Y', strtotime($points[$i]->created_on));
				$points[$i]->created_on = $created_on;
				
				
                                if(!empty($points[$i]->image))
                                    $points[$i]->image_url = SITEURL.'template_images/'.$points[$i]->image;
                                    
                                if($points[$i]->is_active == '1')
                                    $points[$i]->status = 'Active';
                                else if($points[$i]->is_active == '0')
                                    $points[$i]->status = 'Inactive';
                                
                                
			}	
			
            $rarray = array('status' => 'success','data' => $points);
        }
        catch(PDOException $e) {
            $rarray = array('status' => 'error','error' => array('text' => $e->getMessage()));
        } 
        echo json_encode($rarray);
        exit;
    }    
    
function getEventsDealByUser($user_id)  
    {
        $status = 'C';
        $rarray = array();
        try
        {
            $sql = "SELECT * FROM events WHERE user_id=:user_id and status=:status ORDER BY id DESC";
            $db = getConnection();
            $stmt = $db->prepare($sql); 
            $stmt->bindParam("user_id", $user_id);
            $stmt->bindParam("status", $status);
            $stmt->execute();
            $points = $stmt->fetchAll(PDO::FETCH_OBJ);
            
			$count = $stmt->rowCount();
		
			for($i=0;$i<$count;$i++){
				$created_on = date('m/d/Y', strtotime($points[$i]->created_on));
				$points[$i]->created_on = $created_on;
				$from_date = date('m/d/Y h:i a', strtotime($points[$i]->from_date));
				$points[$i]->from_date = $from_date;
				$from_time = date('h:i a', strtotime($points[$i]->from_date));
				$points[$i]->from_time = $from_time;
				
				$to_date = date('m/d/Y h:i a', strtotime($points[$i]->to_date));
				$points[$i]->to_date = $to_date;
				$to_time = date('h:i a', strtotime($points[$i]->to_date));
				$points[$i]->to_time = $to_time;
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

function templateFilesUpload()
{
    if(!empty($_FILES['files']['tmp_name']['0']))
    {
          /*$length = 6;
          $chars =  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.'0123456789';
		$str = '';
		$max = strlen($chars) - 1;
		for ($i=0; $i < $length; $i++)
			$str .= $chars[rand(0, $max)];
			
        $filename = $_FILES['files']['name']['0'];
        $filename = explode('.',$filename);
        $imgname = $filename[0].$str.'.'.$filename[1];
        $_FILES['files']['name']['0'] = $imgname;*/
        move_uploaded_file($_FILES['files']['tmp_name']['0'], 'template_images/'.$_FILES['files']['name']['0']);
        echo json_encode($_FILES['files']['name']['0']);
        exit;
    }
}

function getEventTemplate($id,$eid) 
{
    $event_details = findById($id,'event_templates');
    $event_details = json_decode($event_details);
    $mul_image=array();
    if(!empty($event_details))
    {
        $event_details->created_on = date('m-d-Y h:i:s a ', strtotime($event_details->created_on));
        if(!empty($event_details->image))
        {
            $event_details->image = SITEURL.'template_images/'.$event_details->image;
            $mul_image[]['image'] = $event_details->image;
        }
        else
        {    
        	  $event_details->image = SITEURL.'template_images/default.png';
            $mul_image[] = $event_details->image;
        }    
         $condition = array('event_template_id' => $id);
    	    $result = findByCondition($condition,'event_template_images');
         $result = json_decode($result);
	    //echo '<pre>';print_r($result);
	    if(!empty($result))
	    {
	    		foreach($result as $key=>$val){
	    			$mul_image[] = SITEURL.'template_images/'.$val->image;
			}
	    }
	    $event_details->mulimg = $mul_image;
	    
	    
	    ////////////////////Event Fetch////////////
		$eve = findById($eid,'events');
		$eve = json_decode($eve);
		$eve->startDate = date('m-d-Y', strtotime($eve->from_date));
		$eve->endDate = date('m-d-Y', strtotime($eve->to_date));
		$eve->startTime  = date('H:i a', strtotime($eve->from_date ));
		$eve->endTime  = date('H:i a', strtotime($eve->to_date ));
		$eve->name  = $eve->title;
		$uid  = $eve->user_id;
		////////User Fetch//////////
		$usr = findById($uid,'users');
		$usr = json_decode($usr);
		$eve->username = $usr->first_name.' '.$usr->last_name;
		
		$event_details->content = str_replace(array('<Member_Name>','<Event_Name>','<Event_Start_Date>','<Event_Start_Time>','<Event_End_Date>','<Event_End_Time>'),array($eve->username,$eve->name,$eve->startDate,$eve->startTime,$eve->endDate,$eve->endTime),$event_details->content);
        
    
	    
	    
         echo json_encode(array('status' => 'success','data' =>$event_details));
    }
    else
    {
        echo json_encode(array('status' => 'error','message' => 'No data found'));
    }
    //echo $event_details;
    exit;
}

function getIsBidEvent($id,$uid) 
{
    $eveBid = findByConditionArray(array('event_id'=>$id,'user_id'=>$uid),'event_bids');
    //echo 'Event_id:'.$points[$i]['id'].'| UserId:'.$merchant_id.'<br>';
    //echo '<pre>';print_r($eveBid);
    if(!empty($eveBid))
    {
    		$isBid = true;
    }else{
    		$isBid = false;
    }
    echo json_encode(array('status' => 'success','data' =>$isBid));
    exit;
}    

function getEvenDetails($id) 
{
    $event_details = findById($id,'events');
    $event_details = json_decode($event_details);
    $mul_image=array();
    
    if(!empty($event_details))
    {
        $event_details->from_date = date('m-d-Y h:i:s a ', strtotime($event_details->from_date));
        $event_details->to_date = date('m-d-Y h:i:s a', strtotime($event_details->to_date));
        $event_details->offer_from_date  = date('m-d-Y', strtotime($event_details->offer_from_date ));
        $event_details->offer_to_date  = date('m-d-Y', strtotime($event_details->offer_to_date ));
        //$event_details->mulimg = array();
        if(!empty($event_details->image))
        {
            $event_details->image = SITEURL.'event_images/'.$event_details->image;
            $mul_image[]['image'] = $event_details->image;
        }
        else
        {    
        	  $event_details->image = SITEURL.'event_images/default.png';
            $mul_image[]['image'] = $event_details->image;
        }    
        
        if($event_details->status == 'O')
            $event_details->status = 'Open';
        else if($event_details->status == 'E')
            $event_details->status = 'Expired';
        else
            $event_details->status = 'Completed';
        
         $condition = array('event_id' => $id);
	    $result = findByCondition($condition,'event_images');
	    
	    $result = json_decode($result);
	    //echo '<pre>';print_r($result);
	    if(!empty($result))
	    {
	    		foreach($result as $key=>$val){
	    			$mul_image[]['image'] = SITEURL.'event_images/'.$val->image;
			}
		   
	    }
	    $event_details->mulimg = $mul_image;
	    
	            
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

function getImagesByEventTemplate($id)
{
    $condition = array('event_template_id' => $id);
    $result = findByCondition($condition,'event_template_images');
    $result = json_decode($result);
    if(!empty($result))
    {
        $result = array_map(function($t){
            $t->image_url = SITEURL.'template_images/'.$t->image;
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

      $result = '{"type":"success","message":"Added Succesfully"}'; 
    }
    else{
         $result = '{type:"error",message:"Not Added"}'; 
    }
    echo $result;
    exit;
}

function addEventTemplateImage()
{
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $allinfo['save_data'] = $body;
	   
    //$allinfo['unique_data'] = $unique_field;
    $location_details  = add(json_encode($allinfo),'event_template_images');
    //var_dump($location_details);
    //exit;
    if(!empty($location_details)){
      /* $user_details = add(json_encode($allinfo),'coupons');
      $user_details = json_decode($user_details);*/

      $result = '{"type":"success","message":"Added Succesfully"}'; 
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
    $lastdate = date('Y-m-d');
    try
    {
        $sql = "SELECT * FROM events WHERE status='O' and DATE(offer_to_date) >=:lastdate and is_active=1 ORDER BY from_date ASC";
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("lastdate", $lastdate); 
        //$stmt->bindParam("user_id", $user_id);
        $stmt->execute();
        $points = $stmt->fetchAll(PDO::FETCH_OBJ);

                    $count = $stmt->rowCount();

                    for($i=0;$i<$count;$i++){
                            $created_on = date('m/d/Y', strtotime($points[$i]->created_on));
                            $points[$i]->created_on = $created_on;
                            $from_date = date('m/d/Y h:i a', strtotime($points[$i]->from_date));
                            $points[$i]->from_date = $from_date;
							$points[$i]->expire_date = date('d M, Y H:i:s', strtotime($points[$i]->offer_to_date));
                            $to_date = date('m/d/Y h:i a', strtotime($points[$i]->to_date));
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
							foreach($points[$i]->locations as $k=>$loc)
							{
								$location = findByIdArray($loc->location_id,'locations');
								$points[$i]->locations[$k]->location = $location['city'];
							}
                                                        
                                                        foreach($points[$i]->categories as $k=>$cat)
							{
								$category = findByIdArray($cat->category_id,'category');
								$points[$i]->categories[$k]->category = $category['name'];
							}
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

function getMerchantsRelatedEvents($id)
{
    $rarray = array();
    $events = array();
    $loc_events = array();
    $cat_events = array();
    if($id)
    {
        $merchant_id = $id;
        $merchant_details = findByIdArray($merchant_id,'users');
        //print_r($merchant_details);
        $outlet_categories = array();
        if(!empty($merchant_details))
        {
            $merchant_outlets = findByConditionArray(array('user_id' => $merchant_id),'outlets');
            if(!empty($merchant_outlets))   
            { 
                $outlet_ids = array_column($merchant_outlets, 'id');
                $outlet_locations = array_column($merchant_outlets, 'location_id');
                foreach($outlet_ids as $oid)
                {
                    $categories = findByConditionArray(array('outlet_id' => $oid),'outlet_category_map');
                    foreach($categories as $cat)
                    {
                        array_push($outlet_categories,$cat['category_id']);
                    }
                   // $outlet_categories.push
                }
                //$categories = findByConditionArray(array('outlet_id' => 'IN (1,2)'),'outlet_category_map');
                //$outlet_categories = array_unique(array_column($categories, 'category_id'));
                //$db = getConnection(); 
                //$in  = str_repeat('?,', count($outlet_locations) - 1) . '?';
                $sql = "SELECT * FROM event_location_map WHERE location_id in(".implode(',',$outlet_locations).")";
                //$stm = $db->prepare($sql);
                //$stm->execute($outlet_locations);
                $data = findByQuery($sql); 
                if(!empty($data))
                {
                    $events = array_column($data, 'event_id');
                }
                
                //$in  = str_repeat('?,', count($outlet_categories) - 1) . '?';
                $sql = "SELECT * FROM event_category_map WHERE category_id in(".implode(',',$outlet_categories).")";
                //$stm = $db->prepare($sql);
                //$stm->execute($outlet_categories);
                $cat_data = findByQuery($sql);
                //print_r($events);
                if(!empty($cat_data))
                {
                    $cat_events = array_column($cat_data, 'event_id');
                    if(!empty($cat_events))
                    {
                        foreach($cat_events as $ta)
                        {
                            $events[] = $ta;
                        }
                    }
                }
                //print_r($loc_events);
                //print_r($cat_events); 
                //array_push($events, $loc_events, $cat_events);
                
                $events = array_values(array_unique($events));
			//print_r($events);
                if(!empty($events))
                {
                    $tdt = date('Y-m-d H:i:s');
                        //$event_in  = str_repeat('?,', count($events) - 1) . '?';
                        $sql = "SELECT * FROM events WHERE id in(".implode(',',$events).") and offer_to_date > '".$tdt."' and status Not in('C','E')";
                        //$db = getConnection();
                        //$stmt = $db->prepare($sql); 
                        //$stmt->bindParam("user_id", $user_id);
                       // $stmt->execute($events);
                        $points = findByQuery($sql);
			//print_r($points);
			//exit;

                                    $count = count($points);

                                    for($i=0;$i<$count;$i++){
                                            $eveBid = findByConditionArray(array('event_id'=>$points[$i]['id'],'user_id'=>$merchant_id),'event_bids');
                                            //echo 'Event_id:'.$points[$i]['id'].'| UserId:'.$merchant_id.'<br>';
                                            //echo '<pre>';print_r($eveBid);
                                            if(!empty($eveBid))
                                            {
                                            		$points[$i]['is_bid'] = true;
                                            }else{
                                            		$points[$i]['is_bid'] = false;
                                            }
                                            
                                            $created_on = date('m/d/Y', strtotime($points[$i]['created_on']));
                                            $points[$i]['created_on'] = $created_on;
                                            
                                            $from_date = date('m/d/Y', strtotime($points[$i]['offer_from_date']));
                                            $points[$i]['offer_from_date'] = $from_date;
                                            
                                            $to_date = date('m/d/Y', strtotime($points[$i]['offer_to_date']));
                                            $points[$i]['offer_to_date'] = $to_date;
                                            
                                            $event_date = date('m/d/Y', strtotime($points[$i]['from_date']));
                                            $points[$i]['event_date'] = $event_date;
                                            
                                            $event_start_time = date('h:i A', strtotime($points[$i]['from_date']));
                                            $points[$i]['event_start_time'] = $event_start_time;
                                            
                                            $event_end_time = date('h:i A', strtotime($points[$i]['to_date']));
                                            $points[$i]['event_end_time'] = $event_end_time;
                                            if(!empty($points[$i]['image']))
                                                $points[$i]['image_url'] = SITEURL.'event_images/'.$points[$i]['image'];

                                            if($points[$i]['status'] == 'O')
                                            {
                                                if($points[$i]['is_bid']){
                                                	$points[$i]['status'] = 'Bid';
                                                }
                                                else{
                                                	$points[$i]['status'] = 'Open';
                                                }
                                            }else if($points[$i]['status'] == 'E')
                                            {    $points[$i]['status'] = 'Expired'; }
                                            else
                                            {    $points[$i]['status'] = 'Completed'; }
                                            $points[$i]['categories'] = json_decode(findByCondition(array('event_id'=>$points[$i]['id']),'event_category_map'));
                                            $points[$i]['locations'] = json_decode(findByCondition(array('event_id'=>$points[$i]['id']),'event_location_map'));
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
                    //}
                    
                }
                else
                {
                    $rarray = array('type' => 'error', 'message' => 'No events found.');
                }
               
            }
            else
            {
                $rarray = array('type' => 'error', 'message' => 'No outlet found');
            }
        }
        else
        {
            $rarray = array('type' => 'error', 'message' => 'Merchant not found.');
        }
    }
    echo json_encode($rarray);
}


function getMyEvents($id)
{
    $rarray = array();
    $events = array();
    $loc_events = array();
    $cat_events = array();
    if($id)
    {
        $sql = "SELECT events.id, events.title, events.created_on, events.offer_from_date, events.offer_to_date, events.from_date, events.to_date, events.status, events.image, event_bids.price  FROM events, event_bids  WHERE event_bids.event_id = events.id and event_bids.user_id='".$id."'";
        
                        //$event_in  = str_repeat('?,', count($events) - 1) . '?';
                        //$sql = "SELECT * FROM events WHERE id in(".implode(',',$events).")";
                        
                        //$db = getConnection();
                        //$stmt = $db->prepare($sql); 
                        //$stmt->bindParam("user_id", $user_id);
                       // $stmt->execute($events);
                        $points = findByQuery($sql);
			//print_r($points);
			//exit;

                                    $count = count($points);

                                    for($i=0;$i<$count;$i++){
                                            $created_on = date('m/d/Y', strtotime($points[$i]['created_on']));
                                            $points[$i]['created_on'] = $created_on;
                                            
                                            $from_date = date('m/d/Y', strtotime($points[$i]['offer_from_date']));
                                            $points[$i]['offer_from_date'] = $from_date;
                                            
                                            $to_date = date('m/d/Y', strtotime($points[$i]['offer_to_date']));
                                            $points[$i]['offer_to_date'] = $to_date;
                                            
                                            $event_date = date('m/d/Y', strtotime($points[$i]['from_date']));
                                            $points[$i]['event_date'] = $event_date;
                                            
                                            $event_start_time = date('h:i A', strtotime($points[$i]['from_date']));
                                            $points[$i]['event_start_time'] = $event_start_time;
                                            
                                            $event_end_time = date('h:i A', strtotime($points[$i]['to_date']));
                                            $points[$i]['event_end_time'] = $event_end_time;
                                            
                                            $points[$i]['price'] = number_format($points[$i]['price'],2,'.',',');
                                            if(!empty($points[$i]['image']))
                                                $points[$i]['image_url'] = SITEURL.'event_images/'.$points[$i]['image'];

                                            if($points[$i]['status'] == 'O')
                                                $points[$i]['status'] = 'Open';
                                            else if($points[$i]['status'] == 'E')
                                                $points[$i]['status'] = 'Expired';
                                            else
                                                $points[$i]['status'] = 'Completed';
                                            
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
                    //}
                    
                
                
               
            
            
        
    }
    echo json_encode($rarray);
}

function deleteEventImage($id) {
       $result =  delete('event_images',$id);
       echo $result;	
}
function deleteEventTemplateImage($id) {
       $result =  delete('event_template_images',$id);
       echo $result;	
}
?>

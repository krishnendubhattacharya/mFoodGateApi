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
            if(!empty($body->typedata))
            {
                foreach($body->typedata as $typedata)
                {
                    $temp['save_data'] = array('event_id' => $cat_details->id,'category_id' => $typedata->id);
                    add(json_encode($temp['save_data']),'event_category_map');
                }
            }
            
            if(!empty($body->areadata))
            {
                foreach($body->areadata as $areadata)
                {
                    $temp['save_data'] = array('event_id' => $cat_details->id,'category_id' => $areadata->id);
                    add(json_encode($temp['save_data']),'event_location_map');
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
?>
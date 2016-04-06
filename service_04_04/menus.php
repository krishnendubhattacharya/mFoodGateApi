<?php
    function addMenu()
    {
        $request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
        
        if($body->status)
        {
            $body->status = 1;
        }
        else
        {
            $body->status = 0;
        }
       
        $allinfo['save_data'] = $body;
        //print_r($allinfo);

        //$allinfo['unique_data'] = $unique_field;
	$cat_details  = add(json_encode($allinfo),'menus');
        //echo $cat_details;
        //exit;
        if(!empty($cat_details)){	
	    $result = '{"type":"success","message":"Added Succesfully"}'; 
	  }
	  else{
	       $result = '{"type":"error","message":"Not Added"}'; 
	  }
          echo $result;
          exit;
    }
?>
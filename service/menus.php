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
    
    function getMenuByUser($user_id)
    {
        $rarray = array();
        try
        {
            $sql = "SELECT * FROM menus WHERE user_id=:user_id ORDER BY id DESC";
            $db = getConnection();
            $stmt = $db->prepare($sql); 
            $stmt->bindParam("user_id", $user_id);
            $stmt->execute();
            $points = $stmt->fetchAll(PDO::FETCH_OBJ);
            if(!empty($points))
            {
                $points = array_map(function($t,$k){
                    $t->sl = $k+1;
                    if(!empty($t->image))
                    {
                        $t->imageurl = SITEURL.'menu_images/'.$t->image;
                    }
                    if(!empty($t->status))
                    {
                        $t->status = 'Active';
                    }
                    else {
                        $t->status = 'Inactive';
                    }
                    //$t->type = ($t->type=='C'?'Credit':'Debit');
                    //$t->date = date('m/d/Y',  strtotime($t->date));
                    //$t->expire_date = date('m/d/Y',  strtotime($t->expire_date));
                    return $t;
                }, $points,  array_keys($points));
            }
            $rarray = array('status' => 'success','data' => $points);
        }
        catch(PDOException $e) {
            $rarray = array('status' => 'error','error' => array('text' => $e->getMessage()));
        } 
        echo json_encode($rarray);
        exit;
    }
    
    function menuFileUpload()
    {
        //print_r($_FILES);
        move_uploaded_file($_FILES['files']['tmp_name']['0'], 'menu_images/'.$_FILES['files']['name']['0']);
        echo json_encode($_FILES['files']['name']['0']);
        exit;
    }
    
    function updateMenu() {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$coupon = json_decode($body);
        $id = $coupon->id;
	if(isset($coupon->id)){
	        unset($coupon->id);
	}	
        if(isset($coupon->imageurl))
        {
            unset($coupon->imageurl);
        }
	$allinfo['save_data'] = $coupon;
	$coupon_details = edit(json_encode($allinfo),'menus',$id);
	if(!empty($coupon_details)){
	    $result = '{"type":"success","message":"Changed Succesfully"}'; 
	}
	else{
	    $result = '{"type":"error","message":"Not Changed"}'; 
	}
	echo $result;
	
}

/**/
function deleteMenu($id) {
       $result =  delete('menus',$id);
       echo $result;	
}
?>
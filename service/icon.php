<?php
function addIcon() {	
	
	$request = Slim::getInstance()->request();
        $body = json_decode($request->getBody());
   
    $icon = array();
    
		$body->created_date = date('Y-m-d H:i:s');
		if(!empty($body->image)){
                        file_put_contents('./icon_images/'.$body->image->filename,  base64_decode($body->image->base64));
                        $body->image = $body->image->filename;
                }else{
                        $body->image = "";
                }
                unset($body->image_url);
		$allinfo['save_data'] = $body;
                
		$icon = add(json_encode($allinfo),'icon');
		if(!empty($icon)){
		    $icon = json_decode($icon);
		    $offers = json_encode($icon);
		    $result = '{"type":"success","message":"Added Successfully","offers":'.$offers.' }'; 
		}
		else{
		    $result = '{"type":"error","message":"Try Again!"}'; 
		}
	    
	echo  $result;
	
}

function updateIcon() {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
       
	$body = json_decode($body);
         
        unset($body->created_date);
        if(!empty($body->image))
        {
            file_put_contents('./icon_images/'.$body->image->filename,  base64_decode($body->image->base64));
            $body->image = $body->image->filename;
        }
        else
        {
            unset($body->image);
        }
        $id = $body->id;
        unset($body->image_url);
	if(isset($body->id)){
	    unset($body->id);
	}	
	$allinfo['save_data'] = $body;
	$coupon_details = edit(json_encode($allinfo),'icon',$id);
        
	if(!empty($coupon_details)){
	    $result = '{"type":"success","message":"Changed Succesfully"}'; 
	}
	else{
	    $result = '{"type":"error","message":"Not Changed"}'; 
	}
	echo $result;
	
}


function getIconList() {     

        $is_active = 1;  
	    $sql = "SELECT * FROM icon order by id ASC";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);	
		
		
		$stmt->execute();
		$restaurants = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    $restaurants[$i]->created_date = date('m-d-Y',strtotime($restaurants[$i]->created_date));
		    if(empty($restaurants[$i]->image)){
                            $img = $site_path.'icon_images/setting.png';
                            $restaurants[$i]->image = "";
                            $restaurants[$i]->imagee = "setting.png";
                            $restaurants[$i]->image_url = $img;
                        }
                        else{                            
	                        $img = $site_path."icon_images/".$restaurants[$i]->image;
	                        $restaurants[$i]->image = $img; 
	                        $restaurants[$i]->imagee = $restaurants[$i]->image;
	                        $restaurants[$i]->image_url = $img;                           
                        }
		    
		}
		$db = null;
		$restaurants = json_encode($restaurants);
	    $result = '{"type":"success","icon":'.$restaurants.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
        exit;
}


function getIconDetail($id) {     

        $is_active = 1;  
	    $sql = "SELECT * FROM icon where icon.id=:id";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);	
		
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$restaurants = $stmt->fetchObject();  
		$count = $stmt->rowCount();
		//$restaurants->date = date('m-d-Y',strtotime($restaurants->date));
		
		    
		    if(empty($restaurants->image)){
                            $img = $site_path.'icon_images/setting.png';
                            $restaurants->image = "";
                            $restaurants->image_url = $img;
                        }
                        else{                            
	                        $img = $site_path."icon_images/".$restaurants->image;
	                        $restaurants->image = $img; 
	                        $restaurants->image_url = $img;                           
                        }
		    
		
		$db = null;
		$restaurants = json_encode($restaurants);
	    $result = '{"type":"success","icon":'.$restaurants.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}




/**/
        function deleteIcon($id) {
               $result =  delete('icon',$id);
               echo $result;	
        }


    
        function iconFileUpload()
        {
                //print_r($_FILES);.
                move_uploaded_file($_FILES['files']['tmp_name']['0'], 'icon_images/'.$_FILES['files']['name']['0']);
                echo json_encode($_FILES['files']['name']['0']);
                exit;
        }


    
?>

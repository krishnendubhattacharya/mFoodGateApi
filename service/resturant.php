<?php
function getFeaturedResturantHome() {     

        $is_active = 1;  
	    $sql = "SELECT restaurants.id,restaurants.title,restaurants.logo FROM restaurants where restaurants.is_featured=1 order by rand() limit 3";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);	
		
		
		$stmt->execute();
		$restaurants = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    
		    if(empty($restaurants[$i]->logo)){
                            $img = $site_path.'restaurant_images/default.jpg';
                            $restaurants[$i]->logo = $img;
                        }
                        else{                            
	                        $img = $site_path."restaurant_images/".$restaurants[$i]->logo;
	                        $restaurants[$i]->logo = $img;                            
                        }
		}
		$db = null;
		$restaurants = json_encode($restaurants);
	    $result = '{"type":"success","restaurants":'.$restaurants.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getResturantByCategory($cid) {     

        $is_active = 1;  
	    $sql = "SELECT restaurants.id,restaurants.title,restaurants.logo FROM restaurants where restaurants.category_id=:cid order by restaurants.title DESC";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);	
		$stmt->bindParam("cid", $cid);
		
		$stmt->execute();
		$restaurants = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    
		    if(empty($restaurants[$i]->logo)){
                            $img = $site_path.'restaurant_images/default.jpg';
                            $restaurants[$i]->logo = $img;
                        }
                        else{                            
	                        $img = $site_path."restaurant_images/".$restaurants[$i]->logo;
	                        $restaurants[$i]->logo = $img;                            
                        }
		    
		}
		$db = null;
		$restaurants = json_encode($restaurants);
	    $result = '{"type":"success","restaurants":'.$restaurants.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getAllRestaurantWithUser() {
     $sql = "SELECT *,R.id as id  FROM restaurants as R,users as U where R.user_id=U.id";
    
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);  
        //$stmt->bindParam("id", $id);
        $stmt->execute();			
        $location = $stmt->fetchAll(PDO::FETCH_OBJ);		
        $db = null;
        if(!empty($location)){
                $location = array_map(function($t){
                    if(!empty($t->logo))
                        $t->logo_url = SITEURL."restaurant_images/".$t->logo;
                        
                   return $t;
                },$location);
                echo '{"type":"success","restaurant": ' . json_encode($location) . '}'; 
        }else{
                echo '{"type":"error","message":"No record found"}'; 
        }
    } catch(PDOException $e) {
            return '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}
/**/
function addResturant() {	
	
	$request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	//print_r($body);exit;
	if(isset($body->id)){
	        unset($body->id);
	}
        if(isset($body->category_id))
        {
            $categories = $body->category_id;
        }
        unset($body->category_id);
        //unset($body->is_active); 
        if(!empty($body->logo))
        {
            file_put_contents('./restaurant_images/'.$body->logo->filename,  base64_decode($body->logo->base64));
            $body->logo = $body->logo->filename;
        }
	unset($body->image);
           $allinfo['save_data'] = $body;
           
            //$allinfo['unique_data'] = $unique_field;
          $location_details  = add(json_encode($allinfo),'restaurants');
          if(!empty($location_details)){
            /* $user_details = add(json_encode($allinfo),'coupons');
            $user_details = json_decode($user_details);*/
              $restaurant_details = json_decode($location_details);
              
              foreach($categories as $category)
              {
                    $temp = array();
                    $temp['restaurant_id'] = $restaurant_details->id;
                    $temp['category_id']   = $category->id;
                    $data = add(json_encode(array('save_data' => $temp)),'resturant_category_map');
                    //echo $data;
              }

            $result = '{"type":"success","message":"Added Succesfully"}'; 
          }
          else{
               $result = '{"type":"error","message":"Not Added"}'; 
          }
	
	echo $result;
	
}

function restaurantLogoUpload()
{
    //print_r($_FILES);
    move_uploaded_file($_FILES['files']['tmp_name']['0'], 'restaurant_images/'.$_FILES['files']['name']['0']);
    echo json_encode($_FILES['files']['name']['0']);
    exit;
}

function getRestaurantDetails($id)
{
    $rarray = array();
    $restaurant = findByIdArray($id,'restaurants');
    if(!empty($restaurant))
    {
        $rarray['restaurant'] = $restaurant;
        if(!empty($restaurant['user_id']))
        {
            $rarray['user'] = findByIdArray($restaurant['user_id'],'users');
            $rarray['categories'] = findByConditionArray(array('restaurant_id' => $id),'resturant_category_map');
        }
        if(!empty($restaurant['logo']))
        {
            $rarray['restaurant']['logo_url'] = SITEURL."restaurant_images/".$restaurant['logo'];
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

function updateResturant($id) {	
	
	$request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	//print_r($body);exit;
        $id = $body->id;
	if(isset($body->id)){
	        unset($body->id);
	}
        if(isset($body->category_id))
        {
            $categories = $body->category_id;
        }
        if(isset($body->image_url))
            unset($body->image_url);
        unset($body->category_id);
        //unset($body->is_active); 
        if(!empty($body->logo))
        {
            file_put_contents('./restaurant_images/'.$body->logo->filename,  base64_decode($body->logo->base64));
            $body->logo = $body->logo->filename;
        }
        else {
            unset($body->logo);
        }
	unset($body->image);
           $allinfo['save_data'] = $body;
           
            //$allinfo['unique_data'] = $unique_field;
           $location_details = edit(json_encode($allinfo),'restaurants',$id);
           
          //$location_details  = add(json_encode($allinfo),'restaurants');
          if(!empty($location_details)){
            /* $user_details = add(json_encode($allinfo),'coupons');
            $user_details = json_decode($user_details);*/
              $restaurant_details = json_decode($location_details);
              //var_dump($location_details);
              //exit;
              deleteAll('resturant_category_map',array('restaurant_id' => $id));
              foreach($categories as $category)
              {
                    $temp = array();
                    $temp['restaurant_id'] = $restaurant_details->id;
                    $temp['category_id']   = $category->id;
                    $data = add(json_encode(array('save_data' => $temp)),'resturant_category_map');
                    //echo $data;
              }

            $result = '{"type":"success","message":"Updated Succesfully"}'; 
          }
          else{
               $result = '{"type":"error","message":"Not Added"}'; 
          }
	
	echo $result;
	
}

/**/
function deleteResturant($id) {
       $result =  delete('restaurants',$id);
       echo $result;	
}

?>

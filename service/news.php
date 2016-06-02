<?php
function getActiveNewsByRestaurant($res_id)
{
    $rarray = array('type' => 'error','message' => 'No news found.');
    $news = findByConditionArray(array('restaurant_id' => $res_id),'news_restaurant_map');
    $news_id = array_column($news, 'news_id');
    if(!empty($news_id))
    {
        $query = "SELECT * FROM news WHERE id in(".implode(',', $news_id).") and is_active=1";
        $details = findByQuery($query);
        if(!empty($details))
        {
            $details = array_map(function($t){
                $t['image_url'] = SITEURL.'news_images/'.$t['image'];
                return $t;
            },$details);
            $rarray = array('type' => 'success','data' => $details);
        }
    }
    echo json_encode($rarray);
}

function addNews() {	
	
	$request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
   
    $news = array();
    
		$body->date = date('Y-m-d H:i:s');
                file_put_contents('./news_images/'.$body->image->filename,  base64_decode($body->image->base64));
                $body->image = $body->image->filename;
                unset($body->image_url);
		$allinfo['save_data'] = $body;
                
		$news = add(json_encode($allinfo),'news');
		if(!empty($news)){
		    $news = json_decode($news);
		    $offers = json_encode($news);
		    $result = '{"type":"success","message":"Added Successfully","offers":'.$offers.' }'; 
		}
		else{
		    $result = '{"type":"error","message":"Try Again!"}'; 
		}
	    
	echo  $result;
	
}

function updateNews() {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
       
	$body = json_decode($body);
         
        unset($body->date);
        if(!empty($body->image))
        {
            file_put_contents('./news_images/'.$body->image->filename,  base64_decode($body->image->base64));
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
	$coupon_details = edit(json_encode($allinfo),'news',$id);
        
	if(!empty($coupon_details)){
	    $result = '{"type":"success","message":"Changed Succesfully"}'; 
	}
	else{
	    $result = '{"type":"error","message":"Not Changed"}'; 
	}
	echo $result;
	
}
/*
 * function addNews() {	
	
	$request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
   
    $news = array();
    
		$body->date = date('Y-m-d H:i:s');
		$body->is_active = 1;
		$allinfo['save_data'] = $body;
		$news = add(json_encode($allinfo),'news');
		if(!empty($news)){
		    $news = json_decode($news);
		    $dd = date('d M, Y',strtotime($news->created_on));
		    $news->created_on = $dd;
		    $offers = json_encode($news);
		    $result = '{"type":"success","message":"Added Successfully","offers":'.$offers.' }'; 
		}
		else{
		    $result = '{"type":"error","message":"Try Again!"}'; 
		}
	    
	echo  $result;
	
}
 */

function getNewsList() {     

        $is_active = 1;  
	    $sql = "SELECT news.id,news.title,news.description,news.image,news.views,news.is_banner,news.date,news.is_active FROM news order by id DESC";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);	
		
		
		$stmt->execute();
		$restaurants = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    $restaurants[$i]->date = date('m-d-Y',strtotime($restaurants[$i]->date));
		    if(empty($restaurants[$i]->image)){
                            $img = $site_path.'news_images/default.jpg';
                            $restaurants[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."news_images/".$restaurants[$i]->image;
	                        $restaurants[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		$restaurants = json_encode($restaurants);
	    $result = '{"type":"success","news":'.$restaurants.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
        exit;
}

function getNewsListAll() {     

        $is_active = 1;  
	    $sql = "SELECT news.id,news.title,news.description,news.image,news.views,news.is_banner,news.date FROM news where news.is_active=1 order by id DESC";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);	
		
		
		$stmt->execute();
		$restaurants = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    $restaurants[$i]->date = date('m-d-Y',strtotime($restaurants[$i]->date));
		    if(empty($restaurants[$i]->image)){
                            $img = $site_path.'news_images/default.jpg';
                            $restaurants[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."news_images/".$restaurants[$i]->image;
	                        $restaurants[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		$restaurants = json_encode($restaurants);
	    $result = '{"type":"success","news":'.$restaurants.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getNewsLatest() {     

        $is_active = 1;  
	    $sql = "SELECT news.id,news.title,news.description,news.image,news.views,news.is_banner,news.date FROM news where news.is_active=1 order by date DESC";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);	
		
		
		$stmt->execute();
		$restaurants = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    $restaurants[$i]->date = date('m-d-Y',strtotime($restaurants[$i]->date));
		    if(empty($restaurants[$i]->image)){
                            $img = $site_path.'news_images/default.jpg';
                            $restaurants[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."news_images/".$restaurants[$i]->image;
	                        $restaurants[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		$restaurants = json_encode($restaurants);
	    $result = '{"type":"success","latest":'.$restaurants.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getNewsMostViews() {     

        $is_active = 1;  
	    $sql = "SELECT news.id,news.title,news.description,news.image,news.views,news.is_banner,news.date FROM news where news.is_active=1 order by views DESC";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);	
		
		
		$stmt->execute();
		$restaurants = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    $restaurants[$i]->date = date('m-d-Y',strtotime($restaurants[$i]->date));
		    if(empty($restaurants[$i]->image)){
                            $img = $site_path.'news_images/default.jpg';
                            $restaurants[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."news_images/".$restaurants[$i]->image;
	                        $restaurants[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		$restaurants = json_encode($restaurants);
	    $result = '{"type":"success","mostviews":'.$restaurants.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getNewsBanner() {     

        $is_active = 1;  
	    $sql = "SELECT news.id,news.title,news.description,news.image,news.views,news.is_banner,news.date FROM news where news.is_active=1 and news.is_banner=1";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);	
		
		
		$stmt->execute();
		$restaurants = $stmt->fetchObject();  
		$count = $stmt->rowCount();
		$restaurants->date = date('m-d-Y',strtotime($restaurants->date));
		
		    
		    if(empty($restaurants->image)){
                            $img = $site_path.'news_images/default.jpg';
                            $restaurants->image = $img;
                        }
                        else{                            
	                        $img = $site_path."news_images/".$restaurants->image;
	                        $restaurants->image = $img;                            
                        }
		    
		
		$db = null;
		$restaurants = json_encode($restaurants);
	    $result = '{"type":"success","banner":'.$restaurants.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getNewsDetails($id) {     

        $is_active = 1;  
	    $sql = "SELECT * FROM news where news.id=:id";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);	
		
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$restaurants = $stmt->fetchObject();  
		$count = $stmt->rowCount();
		$restaurants->date = date('m-d-Y',strtotime($restaurants->date));
		
		    
		    if(empty($restaurants->image)){
                            $img = $site_path.'news_images/default.jpg';
                            $restaurants->image = $img;
                        }
                        else{                            
	                        $img = $site_path."news_images/".$restaurants->image;
	                        $restaurants->image = $img;                            
                        }
		    
		
		$db = null;
		$restaurants = json_encode($restaurants);
	    $result = '{"type":"success","banner":'.$restaurants.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}


function getNewsDetail($id) { 

        $sql = "SELECT news.views FROM news where news.id=:id";
        $db = getConnection();
	$stmt = $db->prepare($sql);
	$stmt->bindParam("id", $id);
	
	$stmt->execute();
	$news_details = $stmt->fetchObject();
	$news_views = $news_details->views;
            
        $arr = array();
	$arr['views'] = $news_views+1;	
	$allinfo['save_data'] = $arr;
	$old_owner_details = edit(json_encode($allinfo),'news',$id);
        $is_active = 1;  
	    $sql = "SELECT news.id,news.title,news.description,news.image,news.views,news.is_banner,news.date FROM news where news.id=:id";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id", $id);	
		
		
		$stmt->execute();
		$restaurants = $stmt->fetchObject();  
		$count = $stmt->rowCount();
		
		//for($i=0;$i<$count;$i++){
		    $restaurants->date = date('m-d-Y',strtotime($restaurants->date));
		    if(empty($restaurants->image)){
                            $img = $site_path.'news_images/default.jpg';
                            $restaurants->image = $img;
                        }
                        else{                            
	                        $img = $site_path."news_images/".$restaurants->image;
	                        $restaurants->image = $img;                            
                        }
		    
		//}
		$db = null;
		$restaurants = json_encode($restaurants);
	    $result = '{"type":"success","news":'.$restaurants.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

/**/
function deleteNews($id) {
       $result =  delete('news',$id);
       echo $result;	
}

function addMerchantNews() 
    {
        $request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
        
        $restaurants = array();
        if(!empty($body->restaurants_ids))
        {
            $restaurants = $body->restaurants_ids;
            unset($body->restaurants_ids);
        }
        
        if($body->featured_event)
        {
            $body->featured_event = 1;
        }
        else
        {
            $body->featured_event = 0;
        }
        
        if($body->special_event)
        {
            $body->special_event = 1;
        }
        else
        {
            $body->special_event = 0;
        }
        
        if($body->post_to_public)
        {
            $body->post_to_public = 1;
        }
        else
        {
            $body->post_to_public = 0;
        }
        
        if($body->is_active)
        {
            $body->is_active = 1;
        }
        else
        {
            $body->is_active = 0;
        }
        $body->date = date('Y-m-d H:i:s');
        $body->published_date = date('Y-m-d',  strtotime($body->published_date));
       
        $allinfo['save_data'] = $body;
        //print_r($allinfo);

        //$allinfo['unique_data'] = $unique_field;
	$cat_details  = add(json_encode($allinfo),'news');
        //echo $cat_details;
        //exit;
        if(!empty($cat_details)){	
            $ret_details = json_decode($cat_details);
            if(!empty($restaurants) && !empty($ret_details->id))
            {
                foreach($restaurants as $restaurant)
                {
                    $temp = array();
                    $temp['news_id'] = $ret_details->id;
                    $temp['restaurant_id'] = $restaurant;
                    $s =  add(json_encode(array('save_data' =>$temp)),'news_restaurant_map');
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
    
    function newsFileUpload()
    {
        //print_r($_FILES);.
        move_uploaded_file($_FILES['files']['tmp_name']['0'], 'news_images/'.$_FILES['files']['name']['0']);
        echo json_encode($_FILES['files']['name']['0']);
        exit;
    }


    function getNewsByMerchant($id)
    {
        $rarray = array();
        $details = findByConditionArray(array('user_id' => $id),'news');
        if(!empty($details))
        {
            $details = array_map(function($t){
                if(!empty($t['image']))
                {
                    $t['imageurl'] = SITEURL.'news_images/'.$t['image'];
                }
                $t['published_date'] = date('m/d/Y',strtotime($t['published_date']));
                $restaurants = findByConditionArray(array('news_id' => $t['id']),'news_restaurant_map');
                if(!empty($restaurants))
                {
                    $t['restaurants_ids'] = array_map(function($s){
                        $s['id'] = $s['restaurant_id'];
                        return $s;
                    }, $restaurants);
                           $t['restaurants_ids'] = array_column($restaurants, 'restaurant_id');
                }
                else
                {
                    $t['restaurants_ids'] = array();
                }
                
                return $t;
            }, $details);
            $rarray = array("type" => "success","data" => $details);
        }
        else {
            $rarray = array("type" => "error","data" => array(),"message" => "Sorry no news found.");
        }
        echo json_encode($rarray);
        exit;
    }
    
    function updateMerchantNews() 
    {
        $request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
        
        $restaurants = array();
        if(!empty($body->restaurants_ids))
        {
            $restaurants = $body->restaurants_ids;
            unset($body->restaurants_ids);
        }
        
        $id = $body->id;
        if(isset($body->id))
        {
            unset($body->id);
        }
        if($body->featured_event)
        {
            $body->featured_event = 1;
        }
        else
        {
            $body->featured_event = 0;
        }
        
        if($body->special_event)
        {
            $body->special_event = 1;
        }
        else
        {
            $body->special_event = 0;
        }
        
        if($body->post_to_public)
        {
            $body->post_to_public = 1;
        }
        else
        {
            $body->post_to_public = 0;
        }
        
       
        $body->date = date('Y-m-d H:i:s');
        $body->published_date = date('Y-m-d',  strtotime($body->published_date));
        if(isset($body->imageurl))
        {
            unset($body->imageurl);
        }
        
        $allinfo['save_data'] = $body;
        //print_r($allinfo);

        //$allinfo['unique_data'] = $unique_field;
	$cat_details  = edit(json_encode($allinfo),'news',$id);
        //echo $cat_details;
        //exit;
        if(!empty($cat_details)){	
            $ret_details = json_decode($cat_details);
            deleteAll('news_restaurant_map' , array('news_id' =>$ret_details->id));
            if(!empty($restaurants) && !empty($ret_details->id))
            {
                foreach($restaurants as $restaurant)
                {
                    $temp = array();
                    $temp['news_id'] = $ret_details->id;
                    $temp['restaurant_id'] = $restaurant;
                    $s =  add(json_encode(array('save_data' =>$temp)),'news_restaurant_map');
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
?>

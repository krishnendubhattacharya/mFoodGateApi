<?php
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


?>

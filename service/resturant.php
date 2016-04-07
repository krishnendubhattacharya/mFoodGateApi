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



?>

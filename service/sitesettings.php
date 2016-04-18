<?php
function getSiteSetting() {	
	
	$is_active = 1;  
	$sql = "SELECT *,site_settings.id,site_settings.facebook_url,site_settings.twitter_url,site_settings.youtube_url,site_settings.pininterest_url,site_settings.google_store_link,site_settings.app_store_link FROM site_settings where site_settings.id=1";
	
	//echo $sql;exit;
	$site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);	
		$stmt->bindParam("page_header", $page_header);
		
		$stmt->execute();
		$content = $stmt->fetchObject();  
		$count = $stmt->rowCount();
		
		
		    
		
		$db = null;
		$content = json_encode($content);
	    $result = '{"type":"success","site":'.$content.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
	
}

function updateSiteSettings()
{
    $request = Slim::getInstance()->request();

    $body = $request->getBody();
    $user = json_decode($body);

    $user->id = 1;
    unset($user->id);

    $allinfo['save_data'] = $user;

    $result = edit(json_encode($allinfo),'site_settings',1);
    
    $result = '{"type":"success","message":"Updated Succesfully"}';
    echo $result;
}

/**/



?>

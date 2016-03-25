<?php
function getContent($page_header) {	
	
	$is_active = 1;  
	$sql = "SELECT contents.id,contents.title,contents.content FROM contents where contents.page_header=:page_header";
	
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
	    $result = '{"type":"success","content":'.$content.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
	
}

/**/



?>

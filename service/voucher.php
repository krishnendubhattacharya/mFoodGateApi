<?php
function getAllActiveVoucher() {     
        $is_active = 1;   
        $sql = "SELECT * FROM vouchers WHERE is_active=:is_active";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("is_active", $is_active);
		$stmt->execute();			
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$db = null;
		echo  json_encode($vouchers); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}
function getExpireSoonVoucher() {
        $is_active = 1;
        $current_date = date('Y-m-d');
        $newDate = date('Y-m-d',strtotime('+7 days'));
        $sql = "SELECT * FROM vouchers WHERE is_active=:is_active and to_date BETWEEN :start and :end";
        try {
		$db = getConnection();
		$stmt = $db->prepare($sql); 
		$stmt->bindParam("is_active", $is_active); 
		$stmt->bindParam("start", $current_date);
		$stmt->bindParam("end", $newDate);
		$stmt->execute();			
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$db = null;
		echo  json_encode($vouchers); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
        //echo $current_date;
        //echo $newDate;
        //$result = findById($id,'vouchers');
        //echo $result;	
}




?>

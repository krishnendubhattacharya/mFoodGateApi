<?php
function getAllActiveVoucher($user_id) {     
        $is_active = 1;  
	$newdate = date('Y-m-d');
        $sql = "SELECT * FROM vouchers INNER JOIN offers ON vouchers.offer_id=offers.id where vouchers.to_date >=:date and vouchers.is_active=:is_active and vouchers.user_id=:user_id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("is_active", $is_active);
		$stmt->bindParam("user_id", $user_id);
		$stmt->bindParam("date", $newdate);
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    $todate = date('d M, Y', strtotime($vouchers[$i]->to_date));
		    $vouchers[$i]->to_date = $todate;
		    $sql1 = "SELECT * FROM voucher_resales WHERE voucher_id=:voucher_id";
		    $stmt1 = $db->prepare($sql1);  
		    $stmt1->bindParam("voucher_id", $vouchers[$i]->id);
		    $stmt1->execute();
		    //$resales = $stmt->fetchObject(); 
		    $resale_count = $stmt1->rowCount();
		    $vouchers[$i]->resale = $resale_count;
		}
		$db = null;
		echo  json_encode($vouchers); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}
function getExpireSoonVoucher($user_id) {
        $is_active = 1;
        $current_date = date('Y-m-d');
        $newDate = date('Y-m-d',strtotime('+7 days'));
        $sql = "SELECT * FROM vouchers INNER JOIN offers ON vouchers.offer_id=offers.id WHERE vouchers.is_active=:is_active and vouchers.user_id=:user_id and vouchers.to_date BETWEEN :start and :end";
        try {
		$db = getConnection();
		$stmt = $db->prepare($sql); 
		$stmt->bindParam("is_active", $is_active); 
		$stmt->bindParam("start", $current_date);
		$stmt->bindParam("end", $newDate);
		$stmt->bindParam("user_id", $user_id);
		$stmt->execute();			
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ); 
		$count = $stmt->rowCount();
		for($i=0;$i<$count;$i++){
		    $todate = date('d M, Y', strtotime($vouchers[$i]->to_date));
		    $vouchers[$i]->to_date = $todate;
		    $sql1 = "SELECT * FROM voucher_resales WHERE voucher_id=:voucher_id";
		    $stmt1 = $db->prepare($sql1);  
		    $stmt1->bindParam("voucher_id", $vouchers[$i]->id);
		    $stmt1->execute();
		    //$resales = $stmt->fetchObject(); 
		    $resale_count = $stmt1->rowCount();
		    $vouchers[$i]->resale = $resale_count;
		}
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

function getVoucherUserMerchentDetail($vid){
  //  $sql = "SELECT * FROM vouchers, offers, users where vouchers.offer_id = offers.id and vouchers.user_id = users.id and vouchers.id=:id";
    $sql = "SELECT vouchers.offer_id, vouchers.view_id, vouchers.price, vouchers.offer_price, vouchers.offer_percent, vouchers.from_date, vouchers.to_date, vouchers.is_used, vouchers.is_active, offers.title, offers.description, offers.merchant_id FROM vouchers , offers WHERE vouchers.offer_id=offers.id and vouchers.id=:id";
    $offerId ='';
    $offer_image = array();
    $restaurant_details = array();
    $site_path = SITEURL;
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $vid);
		$stmt->execute();
		$vouchers = $stmt->fetchObject(); 
		$to_date = date('d M,Y', strtotime($vouchers->to_date));
		//$offer_to_date = date('d M,Y', strtotime($vouchers->offer_to_date));
		$vouchers->to_date = $to_date;
		//$vouchers->offer_to_date = $offer_to_date;
		if(!empty($vouchers)){
			$marchantId = $vouchers->merchant_id;
			$offerId = $vouchers->offer_id;
			$sql1 = "SELECT * FROM offer_images WHERE offer_images.offer_id=:offerid";
			$stmt1 = $db->prepare($sql1);  
			$stmt1->bindParam("offerid", $offerId);
			$stmt1->execute();
			$countvoucherimage = $stmt1->rowCount();
			$offer_image = $stmt1->fetchAll(PDO::FETCH_OBJ);   
			if(empty($offer_image)){
			    $img = $site_path.'voucher_images/default.jpg';
			    $offer_image[0]['image'] = $img;
			}
			else{
			    for($i=0;$i<$countvoucherimage;$i++){
				$img = $site_path."voucher_images/".$offer_image[$i]->image;
				$offer_image[$i]['image'] = $img;
			    }
			}
			//print_r($offer_image);exit;
			$sql2 = "SELECT restaurant_details.title, restaurant_details.logo, restaurant_details.address, restaurant_details.user_id FROM restaurant_details, offers  WHERE offers.merchant_id = restaurant_details.user_id and offers.restaurant_id = restaurant_details.id and offers.id=:off_id and restaurant_details.user_id=:merchantid";
			$stmt2 = $db->prepare($sql2);  
			$stmt2->bindParam("off_id", $offerId);
			$stmt2->bindParam("merchantid", $marchantId);
			$stmt2->execute();
			$countrestaurant = $stmt2->rowCount();
			$restaurant_details = $stmt2->fetchObject(); 
			//$offer_to_date = date('d M,Y', strtotime($restaurant_details->offer_to_date));
			//$restaurant_details->offer_to_date = $offer_to_date;
			if(empty($restaurant_details)){
			    $image = $site_path.'restaurant_images/default.jpg';
			    $restaurant_details->logo = $image;
			}
			else{
			   // for($i=0;$i<$countrestaurant;$i++){
				$img = $site_path."restaurant_images/".$restaurant_details->logo;
				$restaurant_details->logo = $img;
			    //}
			}
			$unique_field = array();
			$unique_field['offer_id'] = $offerId;
			$unique_field['is_active'] = 1;
			$soldCount = soldCount(json_encode($unique_field),'vouchers');
			//echo $soldCount;exit;
			$voucher_details = json_encode($vouchers); 
			$restaurant_details = json_encode($restaurant_details);
			$offer_image = json_encode($offer_image);
			$db = null;
			$result = '{"type":"success","voucher_details":'.$voucher_details.',"restaurant_details":'.$restaurant_details.',"voucher_image":'.$offer_image.',"total_sold":'.$soldCount.'}';
		}
		else if(empty($vouchers)){
			$result = '{"type":"error","message":"Not found Offer Id"}';
		}

	} catch(PDOException $e) {
		$result =  '{"error":{"text":'. $e->getMessage() .'}}'; 
	}


	echo  $result;
}

/*function getResale(){
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $vid = $_POST['voucher_id'];
    $sql = "SELECT * FROM voucher_resales WHERE voucher_id=:voucher_id";
    try {
	    $db = getConnection();
	    $stmt = $db->prepare($sql);  
	    $stmt->bindParam("voucher_id", $vid);
	    $stmt->execute();
	    //$resales = $stmt->fetchObject(); 
	    $resale_count = $stmt->rowCount();
	    $stmt=null;
	    $db=null;
	    if($resale_count==0){
		$body->created_on = date('Y-m-d h:m:s');
		$allinfo['save_data'] = $body;
		$resale_details = add(json_encode($allinfo),'voucher_resales');
		if(!empty($resale_details)){
		    $resale_details = json_decode($resale_details);
		    $result = '{"success":{"resale_count":'. $resale_count .',"resale_details":'.$resale_details.'} }'; 
		}
	    }
	    
	    
       } catch(PDOException $e) {
	    $result =  '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
	echo  $result;
}*/

function soldCount($uniqueData,$tableName) {
        $uniqueData = json_decode($uniqueData);
        $queryField='';
        $aliseField='';
        $db = getConnection();
	$sql = "SELECT * FROM ".$tableName." WHERE ";
	$i=0;
        foreach($uniqueData as $key=>$value){
                if($i ==0){
                        $sql .= $key."=:".$key;
                }else{
                        $sql .= ' and '.$key."=:".$key;
                }
                $i++;                
        }
	$stmt = $db->prepare($sql);
	foreach($uniqueData as $key=>$value){
	        $stmt->bindParam($key, $value);
	}		
	$stmt->execute();	
	$userCount = $stmt->rowCount();
	$stmt=null;
	$db=null;
	
	return $userCount;
        
}


?>
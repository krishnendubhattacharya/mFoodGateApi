<?php
function getAllActiveVoucher($user_id) {     
        $is_active = 1;  
        $site_path = SITEURL;
	$newdate = date('Y-m-d');
        $sql = "SELECT * FROM vouchers, voucher_owner, offers where vouchers.offer_id=offers.id and voucher_owner.voucher_id=vouchers.id and DATE(vouchers.to_date) >=:date and voucher_owner.is_active=:is_active and voucher_owner.to_user_id=:user_id and offers.offer_type_id!=3";
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
		    $vouchers[$i]->expire_date = $todate;
		    if(empty($vouchers[$i]->image)){
			    $image = $site_path.'voucher_images/default.jpg';
			    $vouchers[$i]->image = $image;
			}
			else{
			   // for($i=0;$i<$countrestaurant;$i++){
				$image = $site_path."voucher_images/".$vouchers[$i]->image;
				$vouchers[$i]->image = $image;
				//$restaurant_details->image = $img;
			    //}
			}
		    $sql1 = "SELECT * FROM voucher_resales WHERE voucher_id=:voucher_id and user_id =:user_id and is_sold=0 ";
		    $stmt1 = $db->prepare($sql1);  
		    $stmt1->bindParam("voucher_id", $vouchers[$i]->voucher_id);
			$stmt1->bindParam("user_id", $user_id);
		    $stmt1->execute();
		    //$resales = $stmt->fetchObject(); 
		    $resale_count = $stmt1->rowCount();
		    $vouchers[$i]->resale = $resale_count;
			$vouchers[$i]->user_id = $vouchers[$i]->to_user_id;
		}
		$db = null;
		echo  json_encode($vouchers); 
	} catch(PDOException $e) {
		echo '{"error":{"message":'. $e->getMessage() .'}}'; 
	}
}
function getExpireSoonVoucher($user_id) {
        $is_active = 1;
        $current_date = date('Y-m-d');
        $newDate = date('Y-m-d',strtotime('+7 days'));
        $sql = "SELECT * FROM vouchers, voucher_owner, offers where vouchers.offer_id=offers.id and voucher_owner.voucher_id=vouchers.id and DATE(vouchers.to_date) BETWEEN :start and :end and voucher_owner.is_active=:is_active and voucher_owner.to_user_id=:user_id and offers.offer_type_id!=3";
		//$sql = "SELECT * FROM vouchers INNER JOIN offers ON vouchers.offer_id=offers.id WHERE vouchers.is_active=:is_active and vouchers.user_id=:user_id and vouchers.to_date BETWEEN :start and :end";
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
		    $vouchers[$i]->expire_date = $todate;
		    $sql1 = "SELECT * FROM voucher_resales WHERE voucher_id=:voucher_id and user_id =:user_id and is_sold=0 ";
		    $stmt1 = $db->prepare($sql1);  
		    $stmt1->bindParam("voucher_id", $vouchers[$i]->voucher_id);
			$stmt1->bindParam("user_id", $user_id);
		    $stmt1->execute();
                    if($vouchers[$i]->image)
                    {
                        $vouchers[$i]->image = SITEURL.'voucher_images/'.$vouchers[$i]->image;
                    }
		    //$resales = $stmt->fetchObject(); 
		    $resale_count = $stmt1->rowCount();
		    $vouchers[$i]->resale = $resale_count;
		}
		$db = null;
		echo  json_encode($vouchers); 
	} catch(PDOException $e) {
		echo '{"error":{"message":'. $e->getMessage() .'}}'; 
	}
        //echo $current_date;
        //echo $newDate;
        //$result = findById($id,'vouchers');
        //echo $result;	
}

function getVoucherUserMerchentDetail($vid){
  //  $sql = "SELECT * FROM vouchers, offers, users where vouchers.offer_id = offers.id and vouchers.user_id = users.id and vouchers.id=:id";
    $sql = "SELECT vouchers.offer_id, vouchers.view_id, vouchers.price, vouchers.offer_price, vouchers.offer_percent, vouchers.from_date, vouchers.to_date, vouchers.is_used, vouchers.is_active, offers.title, offers.description, offers.image, offers.benefits, offers.merchant_id FROM vouchers , offers WHERE vouchers.offer_id=offers.id and vouchers.id=:id";
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
		
		//$vouchers->offer_to_date = $offer_to_date;
		if(!empty($vouchers)){
			$to_date = date('d M,Y', strtotime($vouchers->to_date));
			//$offer_to_date = date('d M,Y', strtotime($vouchers->offer_to_date));
			$vouchers->to_date = $to_date;
			if(empty($vouchers->image)){
			    $image = $site_path.'voucher_images/default.jpg';
			    $vouchers->image = $image;
			}
			else{
			   // for($i=0;$i<$countrestaurant;$i++){
				$image = $site_path."voucher_images/".$vouchers->image;
				$vouchers->image = $image;
				//$restaurant_details->image = $img;
			    //}
			}
			
			$sql3 = "SELECT voucher_owner.to_user_id FROM voucher_owner WHERE voucher_owner.voucher_id=:vid and is_active=1";
			$stmt3 = $db->prepare($sql3);  
			$stmt3->bindParam("vid", $vid);
			$stmt3->execute();
			$voucher_owner = $stmt3->fetchObject();
			
			$sql4 = "SELECT * FROM voucher_resales WHERE voucher_id=:vid and user_id =:to_user_id and is_sold=0 ";
		    $stmt4 = $db->prepare($sql4);  
		    $stmt4->bindParam("vid", $vid);
			$stmt4->bindParam("to_user_id", $voucher_owner->to_user_id);
		    $stmt4->execute();
		    //$resales = $stmt->fetchObject(); 
		    $resale_count = $stmt4->rowCount();
		    $vouchers->resale = $resale_count;
			
			$marchantId = $vouchers->merchant_id;
			$offerId = $vouchers->offer_id;
			/*$sql1 = "SELECT * FROM offer_images WHERE offer_images.offer_id=:offerid";
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
			    //for($i=0;$i<$countvoucherimage;$i++){
				$img = $site_path."voucher_images/".$offer_image[$i]->image;
				$offer_image[$i]['image'] = $img;
			    //}
			}  */
			//print_r($offer_image);exit;
			$sql2 = "SELECT restaurants.title, restaurants.logo, restaurants.address, restaurants.user_id FROM restaurants, offers  WHERE offers.merchant_id = restaurants.user_id and offers.restaurant_id = restaurants.id and offers.id=:off_id and restaurants.user_id=:merchantid";
			$stmt2 = $db->prepare($sql2);  
			$stmt2->bindParam("off_id", $offerId);
			$stmt2->bindParam("merchantid", $marchantId);
			$stmt2->execute();
			$countrestaurant = $stmt2->rowCount();
			$restaurant_details = $stmt2->fetchObject(); 
			//$offer_to_date = date('d M,Y', strtotime($restaurant_details->offer_to_date));
			//$restaurant_details->offer_to_date = $offer_to_date;
			if(empty($restaurant_details->logo)){
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
			$voucher_owner = json_encode($voucher_owner);
			$db = null;
			$result = '{"type":"success","voucher_details":'.$voucher_details.',"restaurant_details":'.$restaurant_details.',"voucher_image":'.$offer_image.',"voucher_owner":'.$voucher_owner.',"total_sold":'.$soldCount.'}';
		}
		else if(empty($vouchers)){
			$result = '{"type":"error","message":"Not found Offer Id"}';
		}

	} catch(PDOException $e) {
		$result =  '{"error":{"message":'. $e->getMessage() .'}}'; 
	}


	echo  $result;
}

function resellVoucherDetail($vid,$resellid){
  //  $sql = "SELECT * FROM vouchers, offers, users where vouchers.offer_id = offers.id and vouchers.user_id = users.id and vouchers.id=:id";
    $sql = "SELECT vouchers.offer_id, vouchers.view_id, vouchers.price, vouchers.offer_price, vouchers.offer_percent, vouchers.from_date, vouchers.to_date, vouchers.is_used, vouchers.is_active, offers.title, offers.description, offers.image, offers.merchant_id FROM vouchers , offers WHERE vouchers.offer_id=offers.id and vouchers.id=:id";
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
		
		//$vouchers->offer_to_date = $offer_to_date;
		if(!empty($vouchers)){
			$to_date = date('d M,Y', strtotime($vouchers->to_date));
			//$offer_to_date = date('d M,Y', strtotime($vouchers->offer_to_date));
			$vouchers->to_date = $to_date;
			if(empty($vouchers->image)){
			    $image = $site_path.'voucher_images/default.jpg';
			    $vouchers->image = $image;
			}
			else{
			   // for($i=0;$i<$countrestaurant;$i++){
				$image = $site_path."voucher_images/".$vouchers->image;
				$vouchers->image = $image;
				//$restaurant_details->image = $img;
			    //}
			}
			$sql3 = "SELECT voucher_owner.to_user_id FROM voucher_owner WHERE voucher_owner.voucher_id=:vid and is_active=1";
			$stmt3 = $db->prepare($sql3);  
			$stmt3->bindParam("vid", $vid);
			$stmt3->execute();
			$voucher_owner = $stmt3->fetchObject();
			
			$sql4 = "SELECT * FROM voucher_resales WHERE voucher_resales.id=:resellid";
			$stmt4 = $db->prepare($sql4);  
			$stmt4->bindParam("resellid", $resellid);
			$stmt4->execute();
			$resellvoucherdetails = $stmt4->fetchObject();
			//print_r($resellvoucherdetails);exit;
			$marchantId = $vouchers->merchant_id;
			$offerId = $vouchers->offer_id;
			/*$sql1 = "SELECT * FROM offer_images WHERE offer_images.offer_id=:offerid";
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
			}*/
			//print_r($offer_image);exit;
			$sql2 = "SELECT restaurants.title, restaurants.logo, restaurants.address, restaurants.user_id FROM restaurants, offers  WHERE offers.merchant_id = restaurants.user_id and offers.restaurant_id = restaurants.id and offers.id=:off_id and restaurants.user_id=:merchantid";
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
			$voucher_owner = json_encode($voucher_owner);
			$resellvoucherdetails = json_encode($resellvoucherdetails);
			$db = null;
			$result = '{"type":"success","voucher_details":'.$voucher_details.',"restaurant_details":'.$restaurant_details.',"voucher_image":'.$offer_image.',"voucher_owner":'.$voucher_owner.',"resellvoucherdetails":'.$resellvoucherdetails.',"total_sold":'.$soldCount.'}';
		}
		else if(empty($vouchers)){
			$result = '{"type":"error","message":"Not found Offer Id"}';
		}

	} catch(PDOException $e) {
		$result =  '{"error":{"message":'. $e->getMessage() .'}}'; 
	}


	echo  $result;
}
function resaleCancel(){
	$request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $vid = $body->id;
    $resale_details = array();
    //echo $vid;exit;
    $sql = "SELECT * FROM voucher_resales WHERE id=:id and is_sold=1";
    try {
	    $db = getConnection();
	    $stmt = $db->prepare($sql);  
	    $stmt->bindParam("id", $vid);
	    $stmt->execute();
	    //$resales = $stmt->fetchObject(); 
	    $resale_count = $stmt->rowCount();
	    $stmt=null;
	    $db=null;
	    if($resale_count==0){
			$body->is_active = 0;
			$allinfo['save_data'] = $body;
			$resale_details = edit(json_encode($allinfo),'voucher_resales',$vid);
			if(!empty($resale_details)){
				$resale_details = json_decode($resale_details);
				$result = '{"type":"success","message":"Your have successfully cancel reselling Voucher." }'; 
			}
			else{
				$result = '{"type":"error","message":"Sorry! Please Try Again..."}'; 
			}
	    }
	    else if($resale_count > 0){
		$result = '{"type":"error","message":"You have already Re-selled the voucher so cannot cancel it anymore."}';
	    }
       } catch(PDOException $e) {
	    $result = '{"type":"error","message":'. $e->getMessage() .'}';
	}
	echo  $result;
}

function getDetailsVoucherBid($vid){
    $sql = "SELECT * FROM voucher_bids WHERE voucher_bids.id=:id";
    $bid_details = array();
    $site_path = SITEURL;
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $vid);
		$stmt->execute();
		$vouchers = $stmt->fetchObject(); 
		if(!empty($vouchers)){
			$bid_details = json_encode($vouchers); 
			//$restaurant_details = json_encode($restaurant_details);
			$result = '{"type":"success","bid_details":'.$bid_details.'}';
		}
		else if(empty($vouchers)){
			$result = '{"type":"error","message":"Not found Bid Id"}';
		}
	} catch(PDOException $e) {
		$result =  '{"error":{"message":'. $e->getMessage() .'}}'; 
	}
	echo  $result;
}

function updateVoucherBid(){
	$request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $vid = $body->id;
	$userid = $body->userid;
    $resale_details = array();
    //echo $vid;exit;
    $sql = "SELECT * FROM voucher_bids WHERE id=:id and user_id=:userid";
    try {
	    $db = getConnection();
	    $stmt = $db->prepare($sql);  
	    $stmt->bindParam("id", $vid);
		$stmt->bindParam("userid", $userid);
	    $stmt->execute();
	    $resales = $stmt->fetchObject(); 
	    $resale_count = $stmt->rowCount();
	    $stmt=null;
	    $db=null;
	    if($resale_count>0){
			if($resales->user_id==$userid)
			{
				$allinfo['save_data']['bid_price'] = $body->bid_price;
				$allinfo['save_data']['m_points'] = $body->m_points;
				$resale_details = edit(json_encode($allinfo),'voucher_bids',$vid);
				if(!empty($resale_details)){
					$resale_details = json_decode($resale_details);
					$result = '{"type":"success","message":"Your have successfully updated the Bid." }'; 
				}
				else{
					$result = '{"type":"error","message":"Sorry! Please Try Again..."}'; 
				}
			}else{
				$result = '{"type":"error","message":"Sorry you do not have permission to edit the bid."}';
			}
	    }
	    else if($resale_count == 0){
			$result = '{"type":"error","message":"Sorry no bid found."}';
	    }
       } catch(PDOException $e) {
	    $result = '{"type":"error","message":'. $e->getMessage() .'}';
	}
	echo  $result;
}

function addResale(){
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    //print_r($body);exit;
   //$vid = $_POST['voucher_id'];
    $vid = $body->voucher_id;
    $resale_details = array();
    //echo $vid;exit;
    $sql = "SELECT * FROM voucher_resales WHERE voucher_id=:voucher_id and is_sold=0";
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
               
			$body->created_on = date('Y-m-d h:i:s');
			$body->is_sold = 0;
			$body->is_active = 1;
                         
                        $allinfo = array();
			$allinfo['save_data'] = (array)$body;
			$allinfo['save_data']['status'] = 'resale';
			$resale_details = add(json_encode($allinfo),'voucher_resales');
			if(!empty($resale_details)){
				$resale_details = json_decode($resale_details);
				$dd = date('d M, Y',strtotime($resale_details->created_on));
				$resale_details->created_on = $dd;
				$resale_details = json_encode($resale_details);
				$result = '{"type":"success","message":"Your Voucher is posted for resale Successfully","resale_details":'.$resale_details.' }'; 
			}
			else{
				$result = '{"type":"error","message":"Try Again!","resale_details":'.$resale_details.'}'; 
			}
	    }
	    else if($resale_count > 0){
		$result = '{"type":"error","message":"You have already posted this voucher for resell"}';
	    }
       } catch(PDOException $e) {
	    $result =  '{"error":{"message":'. $e->getMessage() .'}}'; 
	}
	echo  $result;
}

function getMyMembership($user_id){
	$is_active = 1;
	$lastdate = date('Y-m-d');
	$sql = "SELECT * FROM vouchers, voucher_owner, offers where vouchers.offer_id=offers.id and voucher_owner.voucher_id=vouchers.id and vouchers.to_date >=:lastdate and voucher_owner.is_active=:is_active and voucher_owner.to_user_id=:user_id and offers.offer_type_id=3";
	
	try {
	$db = getConnection();
	$stmt = $db->prepare($sql); 
	$stmt->bindParam("is_active", $is_active); 
	$stmt->bindParam("lastdate", $lastdate);
	$stmt->bindParam("user_id", $user_id);
	$stmt->execute();			
	$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ); 
	$count = $stmt->rowCount();
	for($i=0;$i<$count;$i++){
		$todate = date('d M, Y', strtotime($vouchers[$i]->to_date));
		$vouchers[$i]->expire_date = $todate;
                $vouchers[$i]->purchased_date = date('d M, Y', strtotime($vouchers[$i]->purchased_date));
		$sql1 = "SELECT * FROM voucher_resales WHERE voucher_id=:voucher_id and user_id =:user_id and is_sold=0 ";
		$stmt1 = $db->prepare($sql1);  
		$stmt1->bindParam("voucher_id", $vouchers[$i]->voucher_id);
		$stmt1->bindParam("user_id", $user_id);
		$stmt1->execute();
		//$resales = $stmt->fetchObject(); 
		$resale_count = $stmt1->rowCount();
		$vouchers[$i]->resale = $resale_count;
                $vouchers[$i]->restaurant = findByIdArray($vouchers[$i]->restaurant_id,'restaurants');
	}
	$db = null;
	$vouchers = json_encode($vouchers);
		$result = '{"type":"success","membership":'.$vouchers.'}';
	} catch(PDOException $e) {
		$result =  '{"error":{"message":'. $e->getMessage() .'}}'; 
	}
	echo  $result;
	//echo $current_date;
	//echo $newDate;
	//$result = findById($id,'vouchers');
	//echo $result;
}

function getMyExpiredMembership($user_id){
	$is_active = 1;
	$lastdate = date('Y-m-d');
	$sql = "SELECT * FROM vouchers, voucher_owner, offers where vouchers.offer_id=offers.id and voucher_owner.voucher_id=vouchers.id and vouchers.to_date <:lastdate and voucher_owner.is_active=:is_active and voucher_owner.to_user_id=:user_id and offers.offer_type_id=3";
	
	try {
	$db = getConnection();
	$stmt = $db->prepare($sql); 
	$stmt->bindParam("is_active", $is_active); 
	$stmt->bindParam("lastdate", $lastdate);
	$stmt->bindParam("user_id", $user_id);
	$stmt->execute();			
	$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ); 
	$count = $stmt->rowCount();
	for($i=0;$i<$count;$i++){
		$todate = date('d M, Y', strtotime($vouchers[$i]->to_date));
		$vouchers[$i]->expire_date = $todate;
                $vouchers[$i]->purchased_date = date('d M, Y', strtotime($vouchers[$i]->purchased_date));
		$sql1 = "SELECT * FROM voucher_resales WHERE voucher_id=:voucher_id and user_id =:user_id and is_sold=0 ";
		$stmt1 = $db->prepare($sql1);  
		$stmt1->bindParam("voucher_id", $vouchers[$i]->voucher_id);
		$stmt1->bindParam("user_id", $user_id);
		$stmt1->execute();
		//$resales = $stmt->fetchObject(); 
		$resale_count = $stmt1->rowCount();
		$vouchers[$i]->resale = $resale_count;
                $vouchers[$i]->restaurant = findByIdArray($vouchers[$i]->restaurant_id,'restaurants');
	}
	$db = null;
	$vouchers = json_encode($vouchers);
		$result = '{"type":"success","membership":'.$vouchers.'}';
	} catch(PDOException $e) {
		$result =  '{"error":{"message":'. $e->getMessage() .'}}'; 
	}
	echo  $result;
	//echo $current_date;
	//echo $newDate;
	//$result = findById($id,'vouchers');
	//echo $result;
}

function getMyMembershipExpireSoon($user_id){
	$is_active = 1;
	$start = date('Y-m-d');
	$end = date('Y-m-d',strtotime('+7 days'));
	$sql = "SELECT * FROM vouchers, voucher_owner, offers where vouchers.offer_id=offers.id and voucher_owner.voucher_id=vouchers.id and vouchers.to_date BETWEEN :start and :end and voucher_owner.is_active=:is_active and voucher_owner.to_user_id=:user_id and offers.offer_type_id=3";
	
	try {
	$db = getConnection();
	$stmt = $db->prepare($sql); 
	$stmt->bindParam("is_active", $is_active); 
	$stmt->bindParam("start", $start);
	$stmt->bindParam("end", $end);
	$stmt->bindParam("user_id", $user_id);
	$stmt->execute();			
	$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ); 
	$count = $stmt->rowCount();
	for($i=0;$i<$count;$i++){
		$todate = date('d M, Y', strtotime($vouchers[$i]->to_date));
		$vouchers[$i]->expire_date = $todate;
		$sql1 = "SELECT * FROM voucher_resales WHERE voucher_id=:voucher_id and user_id =:user_id and is_sold=0 ";
		$stmt1 = $db->prepare($sql1);  
		$stmt1->bindParam("voucher_id", $vouchers[$i]->voucher_id);
		$stmt1->bindParam("user_id", $user_id);
		$stmt1->execute();
		//$resales = $stmt->fetchObject(); 
		$resale_count = $stmt1->rowCount();
		$vouchers[$i]->resale = $resale_count;
	}
	$db = null;
	$vouchers = json_encode($vouchers);
		$result = '{"type":"success","membership":'.$vouchers.'}';
	} catch(PDOException $e) {
		$result =  '{"error":{"message":'. $e->getMessage() .'}}'; 
	}
	echo  $result;
}

function getResellListPostOwn($userid){
            $current_date = date('Y-m-d');
            $resales = array();
            $sql = "SELECT offers.title, voucher_resales.id as voucher_resale_id, voucher_resales.voucher_id, voucher_resales.price, voucher_resales.points, voucher_resales.status, voucher_resales.user_id, voucher_resales.is_sold, voucher_resales.is_active, vouchers.to_date as expire_date, vouchers.price as voucher_price,vouchers.offer_price as purchase_price FROM offers, voucher_resales, vouchers WHERE offers.id = vouchers.offer_id and voucher_resales.voucher_id = vouchers.id and DATE(vouchers.to_date) >=:current_date and voucher_resales.is_active ='1' and voucher_resales.is_sold='0' and voucher_resales.user_id=:user_id";
	   	
	   
        try {
	    $db = getConnection();
	    $stmt = $db->prepare($sql);  
	    $stmt->bindParam("user_id", $userid);
	    $stmt->bindParam("current_date", $current_date);
	    $stmt->execute();
	    $resales = $stmt->fetchAll(PDO::FETCH_OBJ);  
	    //print_r($resales);exit;
	  	$list_count = $stmt->rowCount();
	    $stmt=null;
	    $db=null;
	    if(!empty($resales)){
			for($i=0;$i<$list_count;$i++){
			    $todate = date('d M, Y', strtotime($resales[$i]->expire_date));
			    $resales[$i]->expire_date = $todate;
				if($resales[$i]->is_sold=='1')
				{
					$resales[$i]->status = 'Closed';
				}else if($resales[$i]->status==''){
					$resales[$i]->status = 'Resell';
				}else{
					$resales[$i]->status = 'Resell';
				}
				$rid = $resales[$i]->voucher_resale_id;
				$sql = "SELECT max(voucher_bids.bid_price) as high_bid,min(voucher_bids.bid_price) as low_bid FROM voucher_bids where voucher_bids.voucher_resale_id =:rid";
				$db = getConnection();
				$stmt = $db->prepare($sql);  
				$stmt->bindParam("rid", $rid);
				$stmt->execute();
				$resalesBid = $stmt->fetchObject();
				//print_r($resalesBid);
				if($resalesBid->high_bid!='' && $resalesBid->low_bid!='')
				{
					$resales[$i]->high_bid = $resalesBid->high_bid;
					$resales[$i]->low_bid = $resalesBid->low_bid;
				}else{
					$resales[$i]->high_bid = 0;
					$resales[$i]->low_bid = 0;
				}
				//print_r($resales[$i]);exit;
			    
                        
			}
	    		$resales = json_encode($resales);
	    	     $result = '{"type":"success","resale_details":'.$resales.' }';
	    }
	    else if(empty($resales)){
		    $result = '{"type":"error","message":"No Records Found" }'; 
		}
        } 
        catch(PDOException $e) {
	    $result =  '{"error":{"message":'. $e->getMessage() .'}}'; 
	}
	echo  $result;
}

function getResellListPostOthers($userid){
	$current_date = date('Y-m-d');
	 $resales = array();
	   	 $sql = "SELECT offers.title, voucher_resales.id as voucher_resale_id, voucher_resales.voucher_id, voucher_resales.price, voucher_resales.points, voucher_resales.user_id, voucher_resales.is_sold, voucher_resales.is_active, vouchers.to_date as expire_date, vouchers.price as voucher_price,vouchers.offer_price as purchase_price  FROM offers, voucher_resales, vouchers WHERE offers.id = vouchers.offer_id and voucher_resales.voucher_id = vouchers.id and DATE(vouchers.to_date) >=:current_date and voucher_resales.is_active ='1' and voucher_resales.is_sold='0' and voucher_resales.user_id!=:user_id";   	
	   	 //$sql = "SELECT offers.title, voucher_resales.id as voucher_resale_id, voucher_resales.voucher_id, voucher_resales.price, voucher_resales.points, voucher_resales.user_id, voucher_resales.is_sold, voucher_resales.is_active, vouchers.to_date as expire_date, vouchers.price as voucher_price, max(voucher_bids.bid_price) as max_bid_price FROM offers, voucher_resales,voucher_bids, vouchers WHERE offers.id = vouchers.offer_id and voucher_resales.voucher_id = vouchers.id and voucher_bids.voucher_resale_id = voucher_resales.id and vouchers.to_date >=:current_date and voucher_resales.is_active ='1' and voucher_resales.is_sold='0' and voucher_resales.user_id!=:user_id";
	   
    try {
	    $db = getConnection();
	    $stmt = $db->prepare($sql);  
	    $stmt->bindParam("user_id", $userid);
	    $stmt->bindParam("current_date", $current_date);
	    $stmt->execute();
	    $resales = $stmt->fetchAll(PDO::FETCH_OBJ);  
	    //print_r($resales);exit;
	    $list_count = $stmt->rowCount();
	    $stmt=null;
	    $db=null;
	    if(!empty($resales)){
	    		for($i=0;$i<$list_count;$i++){
			    $todate = date('d M, Y', strtotime($resales[$i]->expire_date));
			    $resales[$i]->expire_date = $todate;
			}
	    		$resales = json_encode($resales);
	    	     $result = '{"type":"success","resale_details":'.$resales.'}';
	    }
	    else if(empty($resales)){
		    $result = '{"type":"error","message":"No Records Found"}'; 
		}
       } 
       catch(PDOException $e) {
	    $result =  '{"error":{"message":'. $e->getMessage() .'}}'; 
	}
	echo  $result;
}

function getResellListBidOwn($userid){
	$current_date = date('Y-m-d');
	 $resales = array();
	   	 //$sql = "SELECT voucher_resales.voucher_id, voucher_resales.price, voucher_resales.points, voucher_resales.user_id, voucher_resales.is_sold, voucher_resales.is_active, vouchers.to_date, vouchers.price as voucher_price FROM voucher_resales, vouchers WHERE voucher_resales.voucher_id = vouchers.id and vouchers.to_date >=:current_date and voucher_resales.is_active ='1' and voucher_resales.is_sold='0' and vouchers.user_id=:user_id";
	   	 
	   	 $sql = "SELECT voucher_bids.id as bid_id,voucher_bids.voucher_id,voucher_bids.user_id,voucher_bids.voucher_resale_id,voucher_bids.bid_price,voucher_bids.m_points as bid_points, voucher_bids.is_accepted, voucher_resales.price, voucher_resales.points, voucher_resales.is_sold, voucher_resales.is_active, voucher_resales.status, vouchers.to_date, vouchers.price as voucher_price,vouchers.offer_price as purchase_price,vouchers.to_date as expire_date, offers.title FROM voucher_bids, voucher_resales, vouchers, offers WHERE voucher_resales.id = voucher_bids.voucher_resale_id and vouchers.id = voucher_bids.voucher_id and offers.id = vouchers.offer_id and vouchers.to_date >=:current_date and voucher_resales.is_active ='1' and voucher_bids.user_id=:user_id";
	   	
	   
	   
    try {
	    $db = getConnection();
	    $stmt = $db->prepare($sql);  
	    $stmt->bindParam("user_id", $userid);
	    $stmt->bindParam("current_date", $current_date);
	    $stmt->execute();
	    $resales = $stmt->fetchAll(PDO::FETCH_OBJ);  
	    //print_r($resales);exit;
	    $resale_count = $stmt->rowCount();
	    $stmt=null;
	    $db=null;
	    //print_r($resales);
	    if(!empty($resales)){
	                for($i=0;$i<$resale_count;$i++){
			    $todate = date('d M, Y', strtotime($resales[$i]->to_date));
			    $resales[$i]->to_date = $todate;
				$expire_date = date('d M, Y', strtotime($resales[$i]->expire_date));
			    $resales[$i]->expire_date = $expire_date;
			    if(($resales[$i]->is_accepted == 1) && ($resales[$i]->is_sold == 1)){
			        $resales[$i]->Status = 'Accepted';
			    }else if(($resales[$i]->is_accepted == 0) && ($resales[$i]->is_sold == 1)){
			        $resales[$i]->Status = 'Closed';
			    }else{
			        $resales[$i]->Status = 'Resell';
			    }
			}
	    		$resales = json_encode($resales);
	    		
	    	     $result = '{"type":"success","bid_details":'.$resales.'}';
	    }
	    else if(empty($resales)){
		    $result = '{"type":"error","message":"No Records Found"}'; 
		}
       } 
       catch(PDOException $e) {
	    $result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo  $result;
}

function getBidderList($id,$userid){
	$current_date = date('Y-m-d');
	$resales = array();
	/*if(!isset($id)){
		echo '{"type":"error","message":"Missing First parameter"}';
	}
	else if(!isset($userid)){
		echo '{"type":"error","Missing Second parameter"}';
	}
	else if($id!='' && $userid!=''){*/
		$sql1 = "SELECT * FROM voucher_resales WHERE id=:id";
		try {
			$db = getConnection();
			$stmt = $db->prepare($sql1);  
			$stmt->bindParam("id", $id);
			$stmt->execute();
			//$resales = $stmt->fetchObject(); 
			$resale_count = $stmt->rowCount();
			if($resale_count==0){
				$result = '{"type":"error","message":"No Records Found, Invalid Resale Id"}'; 
			}
			else if($resale_count > 0){
				$sql2 = "SELECT * FROM users WHERE id=:userid";
				$stmt = $db->prepare($sql2);  
				$stmt->bindParam("userid", $userid);
				$stmt->execute();
				//$resales = $stmt->fetchObject(); 
				$user_count = $stmt->rowCount();
				if($user_count==0){
					$result = '{"type":"error","message":"No Records Found, Invalid User "}'; 
				}
				else if($user_count > 0){	
					   	 $sql = "SELECT users.first_name, users.last_name, users.email, users.image, users.phone, users.last_login, voucher_resales.voucher_id, voucher_resales.price, voucher_resales.points, voucher_resales.user_id as voucher_resale_user, voucher_resales.is_sold, voucher_resales.is_active, vouchers.to_date as expire_date, vouchers.price as voucher_price, voucher_bids.user_id as bidder_id, voucher_bids.bid_price, voucher_bids.id as bid_id FROM voucher_resales, vouchers, users, voucher_bids  WHERE voucher_bids.user_id=users.id and vouchers.to_date >=:current_date and voucher_bids.is_active ='1' and voucher_resales.is_sold='0' and voucher_bids.voucher_resale_id = voucher_resales.id and voucher_bids.voucher_id = vouchers.id and voucher_bids.user_id!=:userid and voucher_bids.voucher_resale_id=:voucher_resale_id";   	
					   
				    /*try {
					    $db = getConnection();*/
					    $stmt = $db->prepare($sql); 
					    $stmt->bindParam("userid", $userid); 
					    $stmt->bindParam("voucher_resale_id", $id);
					    $stmt->bindParam("current_date", $current_date);
					    $stmt->execute();
					    $resales = $stmt->fetchAll(PDO::FETCH_OBJ);  
					    //print_r($resales);exit;
					    $bid_count = $stmt->rowCount();
					    $stmt=null;
					    $db=null;
						    if(!empty($resales)){
						    		for($i=0;$i<$bid_count;$i++){
								    $todate = date('d M, Y', strtotime($resales[$i]->expire_date));
								    $resales[$i]->expire_date = $todate;
								}
						    		$resales = json_encode($resales);
						    	     $result = '{"type":"success","bidder_details":'.$resales.' }';
						    }
						    else if(empty($resales)){
							    $result = '{"type":"error","message":"No Records Found"}'; 
							}
						}
				}
			} 
			 catch(PDOException $e) {
				 $result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
			}		  
		//}
	echo $result;
}

function addBid(){
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $voucher_resale_id = $body->voucher_resale_id;
    $uid = $body->user_id;
    $resale_details = array();
    //echo $vid;exit;
    $sql1 = "SELECT * FROM voucher_resales WHERE id=:voucher_resale_id and is_sold='0' and is_active='1'";
    try {
	    $db = getConnection();
	    $stmt = $db->prepare($sql1);  
	    $stmt->bindParam("voucher_resale_id", $voucher_resale_id);
	    $stmt->execute();
	    //$resales = $stmt->fetchObject(); 
	    $resale_count = $stmt->rowCount();
	    if($resale_count==0){
	    		 $result = '{"type":"error","message":"Already Sold"}'; 
	    }
	    else{
		    $sql = "SELECT * FROM voucher_bids WHERE voucher_resale_id=:voucher_resale_id and user_id=:userid";
		    $stmt = $db->prepare($sql);  
		    $stmt->bindParam("voucher_resale_id", $voucher_resale_id);
		    $stmt->bindParam("userid", $uid);
		    $stmt->execute();
		    //$resales = $stmt->fetchObject(); 
		    $bid_count = $stmt->rowCount();
		    $stmt=null;
		    $db=null;
		    if($bid_count==0){
			//$body->m_points = null;
			$body->is_accepted = 0;
			$body->is_active = 1;
			$allinfo['save_data'] = $body;
			$bid_details = add(json_encode($allinfo),'voucher_bids');
				if(!empty($bid_details)){
					    //$bid_details  = json_encode($bid_details);
					    $result = '{"type":"success","message":"You bid this voucher Successfully","bid_details":'.$bid_details.' }'; 
				}
				else{
					    $result = '{"type":"error","message":"Try Again!","bid_details":'.$bid_details.'}'; 
				}
		    }
		    else if($bid_count > 0){
				$result = '{"type":"error","message":"You have already bid this voucher"}';
		    }
		} 
	}catch(PDOException $e) {
	    $result =  '{"error":{"message":'. $e->getMessage() .'}}'; 
	}
	echo  $result;
}




function saveVoucherResale(){
	$request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	//print_r($body);exit;
	$sql = "SELECT voucher_owner.id, voucher_owner.is_active, vouchers.id as voucher_id, vouchers.offer_id, vouchers.view_id, vouchers.offer_price, vouchers.offer_percent, vouchers.price, users.first_name, users.last_name, users.email  FROM vouchers, voucher_owner, users WHERE voucher_owner.voucher_id = vouchers.id and voucher_owner.voucher_id=:voucherid and voucher_owner.is_active = '1' and voucher_owner.to_user_id=:userid";
	   	  
    try {
	    $db = getConnection();
	    $stmt = $db->prepare($sql);  
	    $stmt->bindParam("voucherid", $body->voucher_id);
	    $stmt->bindParam("userid", $body->from_user_id);
	    $stmt->execute();
	    $saveresales = $stmt->fetchObject();  
	    //print_r($saveresales);exit;
	    //$resale_count = $stmt->rowCount();
	   
	    //print_r($resales);
	   if(!empty($saveresales)){
	   	$ownerid = $saveresales->id;
	   $offerid = $saveresales->offer_id;
	   $viewid = $saveresales->view_id;
	   $offer_price = $saveresales->offer_price;
	   $offer_percent = $saveresales->offer_percent;
	   $price = $saveresales->price;
	   $resaleid = $body->resell_id;
	   $bidid = $body->bid_id;
	   $buy_price = $body->price;
	    $vid = $saveresales->voucher_id;
		$sql2 = "SELECT * FROM users WHERE users.id=:uid";
	    	$stmt2 = $db->prepare($sql2);  
	    	$stmt2->bindParam("uid", $body->from_user_id);
	    	$stmt2->execute();
	    	$fromUser = $stmt2->fetchObject();
			
			$sql2 = "SELECT * FROM users WHERE users.id=:uid";
	    	$stmt2 = $db->prepare($sql2);  
	    	$stmt2->bindParam("uid", $body->to_user_id);
	    	$stmt2->execute();
	    	$toUser = $stmt2->fetchObject();
	    	
	     $sql1 = "SELECT * FROM voucher_resales WHERE voucher_resales.is_sold ='1' AND voucher_resales.id=:resaleId";
	    $stmt1 = $db->prepare($sql1);  
	    //$stmt1->bindParam("voucherid", $vid);
	    $stmt1->bindParam("resaleId", $resaleid);
	    //$stmt1->bindParam("bidId", $bidid);
	    $stmt1->execute();
	    //$checkSales = $stmt1->fetchObject();
	    $checkSales = $stmt1->rowCount();
	    //print_r($checkSales);exit;
	    $stmt=null;
	    $db=null;
	    if($checkSales==0){
	    		$arr = array();
	    		$arr['is_active'] = 0;
				//$arr['buy_price'] = $buy_price;
	    		$arr['sold_date'] = date('Y-m-d h:i:s');
	    		$allinfo['save_data'] = $arr;
			$old_owner_details = edit(json_encode($allinfo),'voucher_owner',$ownerid);
			//print_r($old_owner_details);exit;
			if($old_owner_details){
				$arr1 = array();
				$arr1['is_sold'] = '1';
				$arr1['voucher_bid_id'] = $bidid;
				$arr1['sold_on'] =  date('Y-m-d h:i:s');
				$resaleinfo['save_data'] = $arr1;
				$voucher_resales = edit(json_encode($resaleinfo),'voucher_resales',$resaleid);
				if($voucher_resales){
					$arr2 = array();
					$arr2['is_accepted'] = '1';
					$bidinfo['save_data'] = $arr2;
					$voucher_bids = edit(json_encode($bidinfo),'voucher_bids',$bidid);
					if($voucher_bids){
						$data = array();
						$data['voucher_id'] = $body->voucher_id;
						$data['offer_id'] = $offerid;
						$data['voucher_view_id'] = $viewid;
						$data['from_user_id'] = $body->from_user_id;
						$data['to_user_id'] = $body->to_user_id;
						$data['price'] = $price;
						$data['offer_price'] = $offer_price;
						$data['offer_percent'] = $offer_percent;
						$data['is_active'] = '1';
						$data['buy_price'] = $buy_price;
						$data['purchased_date'] = date('Y-m-d h:i:s');
						$newinfo['save_data'] = $data;
						$new_owner_details  = add(json_encode($newinfo),'voucher_owner');
						if(!empty($new_owner_details)){
						$new = json_decode($new_owner_details);
						    $from = ADMINEMAIL;
						    //$to = $saveresales->email;
						    $to = $toUser->email;  //'nits.ananya15@gmail.com';
						    $subject ='Resale Voucher Sell';
						    $body ='<html><body><p>Dear '.$toUser->first_name.' '.$toUser->last_name.',</p>

							    <p>'.$fromUser->first_name.' '.$fromUser->last_name. ' have Sold  a resale voucher<br />
							    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Voucher Id :</span>'.$new->voucher_view_id.'<br />
							    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Voucher price :</span>'.$new->price.'<br />
							    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Purchased Price :</span>'.$new->buy_price.'<br />
							    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">If we can help you with anything in the meantime just let us know by e-mailing&nbsp;</span>'.$from.'<br />
							    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif"></span><span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">!&nbsp;</span></p>

							    <p>Thanks,<br />
							    mFood&nbsp;Team</p>

							    <p>&nbsp;</p></body></html>';
							    
						    sendMail($to,$subject,$body);
						  	$result = '{"type":"success","message":"You have sold the voucher successfully" }';
						  }
					}
				}
			}
		  }
		  else if($checkSales > 0){
		  		$result = '{"type":"error","message":"Already accepted"}'; 
		  }	
		}
		else if(empty($saveresales)){
			$result = '{"type":"error","message":"No Records Found"}'; 
		}
       } 
       catch(PDOException $e) {
	    $result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	  }
	echo  $result;

}

function giftVoucher(){
	$request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	//print_r($body);exit;
	$sql = "SELECT * from users where users.email=:email and users.id!=:userid";
	   	  
    try {
	    $db = getConnection();
	    $stmt = $db->prepare($sql);  
	    $stmt->bindParam("email", $body->email);
	    $stmt->bindParam("userid", $body->userid);
	    $stmt->execute();
	    $to_user = $stmt->fetchObject();  
	    //print_r($saveresales);exit;
	    //$resale_count = $stmt->rowCount();
		$sql = "SELECT * from users where users.id=:userid";
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("userid", $body->userid);
		$stmt->execute();
		$from_user = $stmt->fetchObject();
	    if(!empty($to_user))
		{
			
			if(!empty($from_user))
			{
				$sql = "SELECT voucher_owner.id, voucher_owner.is_active, vouchers.id as voucher_id, vouchers.offer_id, vouchers.view_id, vouchers.offer_price, vouchers.offer_percent, vouchers.price, users.first_name, users.last_name, users.email  FROM vouchers, voucher_owner, users WHERE voucher_owner.voucher_id = vouchers.id and voucher_owner.voucher_id=:vid and voucher_owner.is_active = '1' and voucher_owner.to_user_id=:userid";
				$stmt = $db->prepare($sql);  
				$stmt->bindParam("vid", $body->vid);
				$stmt->bindParam("userid", $body->userid);
				$stmt->execute();
				$voucherowner = $stmt->fetchObject();
				if(!empty($voucherowner))
				{
					$ownerid = $voucherowner->id;
					$offerid = $voucherowner->offer_id;
					$viewid = $voucherowner->view_id;
					$offer_price = $voucherowner->offer_price;
					$offer_percent = $voucherowner->offer_percent;
					$price = $voucherowner->price;
					$vid = $voucherowner->voucher_id;
					
					$arr = array();
					$arr['is_active'] = 0;
					//$arr['buy_price'] = $buy_price;
					$arr['sold_date'] = date('Y-m-d h:i:s');
					$allinfo['save_data'] = $arr;
					$old_owner_details = edit(json_encode($allinfo),'voucher_owner',$ownerid);
					if($old_owner_details){
						$data = array();
						$data['voucher_id'] = $body->vid;
						$data['offer_id'] = $offerid;
						$data['voucher_view_id'] = $viewid;
						$data['from_user_id'] = $body->userid;
						$data['to_user_id'] = $to_user->id;
						$data['price'] = $price;
						$data['offer_price'] = $offer_price;
						$data['offer_percent'] = $offer_percent;
						$data['is_active'] = '1';
						$data['buy_price'] = '0.00';
						$data['purchased_date'] = date('Y-m-d h:i:s');
						$data['transfer_type'] = 'Gift';
						$newinfo['save_data'] = $data;
						$new_owner_details  = add(json_encode($newinfo),'voucher_owner');
						if(!empty($new_owner_details)){
							$new = json_decode($new_owner_details);
						    $giveData['voucher_id'] = $body->vid;
							$giveData['offer_id'] = $offerid;
							$giveData['from_user_id'] = $body->userid;
							$giveData['to_user_id'] = $to_user->id;
							$giveData['created_on'] = date('Y-m-d h:i:s');
							$giveData['is_active'] = 1;
							$newgiveData['save_data'] = $giveData;
							$new_owner_details  = add(json_encode($newgiveData),'give_voucher');
							
							$from = ADMINEMAIL;
						    //$to = $saveresales->email;
						    $to = $to_user->email;  //'nits.ananya15@gmail.com';
						    $subject ='Voucher Gifted';
						    $body ='<html><body><p>Dear '.$to_user->first_name.' '.$to_user->last_name.',</p>

							    <p>'.$from_user->first_name.' '.$from_user->last_name. ' have gifted you a voucher<br />
							    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Voucher Id :</span>'.$new->voucher_view_id.'<br />
							    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Voucher price :</span>'.$new->price.'<br />
							    
							    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">If we can help you with anything in the meantime just let us know by e-mailing&nbsp;</span>'.$from.'<br />
							    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif"></span><span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">!&nbsp;</span></p>

							    <p>Thanks,<br />
							    mFood&nbsp;Team</p>

							    <p>&nbsp;</p></body></html>';
							    
						    sendMail($to,$subject,$body);
						  	$result = '{"type":"success","message":"You have gifted the voucher successfully" }';
						}
					}
				}else{
					$result = '{"type":"error","message":"Sorry. Voucher not found."}'; 
				}
			}else{
				$result = '{"type":"error","message":"Sorry. Please login first."}'; 
			}
		}else{
			//$result = '{"type":"error","message":"Sorry this email is not registered with us."}'; 
			$tempData = array();
			$tempData['voucher_id'] = $body->vid;
			$tempData['user_id'] = $body->userid;			
			$tempData['email'] = $body->email;
			$newtempinfo['save_data'] = $tempData;			
			$new_owner_details  = add(json_encode($newtempinfo),'gift_voucher_non_user');

			$from = ADMINEMAIL;
						    //$to = $saveresales->email;
			    $to = $body->email;  //'nits.ananya15@gmail.com';
			    $subject ='Voucher Gifted';
			    $body ='<html><body><p>Dear User,</p>


				    <p>'.$from_user->first_name.' '.$from_user->last_name. ' have gifted you a voucher<br />
				    
				    

					<span style="color:rgb(77, 76, 76); font-family:helvetica,arial">To get this voucher, you need to register, please <a href="'.WEBSITEURL.'register/'.$body->email.'">Click Here</a>  </span><span style="color:rgb(77, 76, 76); font-family:helvetica,arial">&nbsp;to verify your account.</span><br />
				    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">If we can help you with anything in the meantime just let us know by e-mailing&nbsp;</span>'.$from.'<br />

				    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif"></span><span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">!&nbsp;</span></p>


				    <p>Thanks,<br />
				    mFood&nbsp;Team</p>



				    <p>&nbsp;</p></body></html>';
				    
			    sendMail($to,$subject,$body);
			$result = '{"type":"success","message":"Mail successfully sent."}';

		}
	    //print_r($resales);
	   
       } 
       catch(PDOException $e) {
	    $result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	  }
	echo  $result;
}

function getGiftedByMe($userid){
	$is_active = 1;  
	$site_path = SITEURL;
	//$newdate = "2016-03-08";
    $sql = "SELECT * from give_voucher where give_voucher.from_user_id =:userid ";
	
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("userid", $userid);
		
		$stmt->execute();
		$gives = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    $offerId = $gives[$i]->offer_id;
			$sql = "SELECT offers.id, offers.title, offers.price, offers.offer_percent, offers.offer_from_date, offers.offer_to_date, offers.image,offers.restaurant_id FROM offers where offers.id =:offerId ";
			$stmt = $db->prepare($sql);		
			$stmt->bindParam("offerId", $offerId);
			$stmt->execute();
			$offer = $stmt->fetchObject();
			
			$returantId = $offer->restaurant_id;
			$sql = "SELECT restaurants.title, restaurants.address FROM restaurants where restaurants.id =:returantId ";
			$stmt = $db->prepare($sql);		
			$stmt->bindParam("returantId", $returantId);
			$stmt->execute();
			$returant = $stmt->fetchObject();
			
			$toUid = $gives[$i]->to_user_id;
			$sql = "SELECT users.first_name, users.last_name, users.email FROM users where users.id =:toUid ";
			$stmt = $db->prepare($sql);		
			$stmt->bindParam("toUid", $toUid);
			$stmt->execute();
			$toUser = $stmt->fetchObject();
			
			$todate = date('M d,Y', strtotime($offer->offer_to_date));
		    //$offer[$i]->offer_to_date = $todate;
			
			$gives[$i]->voucher_name = $offer->title;
			$gives[$i]->resturant_name = $returant->title;
			$gives[$i]->offer_to_date = $todate;
			$gives[$i]->price = number_format($offer->price,2,'.',',');
			$gives[$i]->friend = $toUser->first_name.' '.$toUser->last_name;
			
		}
		$db = null;
		//echo  json_encode($vouchers);
		$gifts = json_encode($gives);
	    $result = '{"type":"success","gifts":'.$gifts.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getGiftedToMe($userid){
	$is_active = 1;  
	$site_path = SITEURL;
	//$newdate = "2016-03-08";
    $sql = "SELECT * from give_voucher where give_voucher.to_user_id =:userid ";
	
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("userid", $userid);
		
		$stmt->execute();
		$gives = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    $offerId = $gives[$i]->offer_id;
			$sql = "SELECT offers.id, offers.title, offers.price, offers.offer_percent, offers.offer_from_date, offers.offer_to_date, offers.image,offers.restaurant_id FROM offers where offers.id =:offerId ";
			$stmt = $db->prepare($sql);		
			$stmt->bindParam("offerId", $offerId);
			$stmt->execute();
			$offer = $stmt->fetchObject();
			
			$returantId = $offer->restaurant_id;
			$sql = "SELECT restaurants.title, restaurants.address FROM restaurants where restaurants.id =:returantId ";
			$stmt = $db->prepare($sql);		
			$stmt->bindParam("returantId", $returantId);
			$stmt->execute();
			$returant = $stmt->fetchObject();
			
			$toUid = $gives[$i]->from_user_id;
			$sql = "SELECT users.first_name, users.last_name, users.email FROM users where users.id =:toUid ";
			$stmt = $db->prepare($sql);		
			$stmt->bindParam("toUid", $toUid);
			$stmt->execute();
			$toUser = $stmt->fetchObject();
			
			$todate = date('M d,Y', strtotime($offer->offer_to_date));
		    //$offer[$i]->offer_to_date = $todate;
			
			$gives[$i]->voucher_name = $offer->title;
			$gives[$i]->resturant_name = $returant->title;
			$gives[$i]->offer_to_date = $todate;
			$gives[$i]->price = number_format($offer->price,2,'.',',');
			$gives[$i]->friend = $toUser->first_name.' '.$toUser->last_name;
			
		}
		$db = null;
		//echo  json_encode($vouchers);
		$gifts = json_encode($gives);
	    $result = '{"type":"success","gifts":'.$gifts.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

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

function getAllFeaturedCats() {     

        $is_active = 1;  
	$newdate = date('Y-m-d');
	$site_path = SITEURL;
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.special_tag,offers.buy_count,offers.quantity FROM offers where offers.offer_to_date >=:newdate and offers.is_featured =1 and offers.is_active=1 and offers.offer_type_id!=3 ORDER BY offers.offer_from_date DESC";
        
        //echo $sql;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("newdate", $newdate);
		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
                        //$vouchers[$i]->price = number_format($vouchers[$i]->price,2,'.',',');
		    
		}
		$db = null;
		//echo  json_encode($vouchers);
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","featuredcat":'.$vouchers.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getAllFeaturedPromoAds() {     

        $is_active = 1;  
	$newdate = date('Y-m-d');
	$site_path = SITEURL;
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.special_tag,offers.buy_count,offers.quantity FROM offers where offers.offer_to_date >=:newdate and offers.is_featured =1 and offers.is_active=1 and offers.offer_type_id!=3 ORDER BY offers.created_on DESC";
        
        //echo $sql;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("newdate", $newdate);
		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
                        //$vouchers[$i]->price = number_format($vouchers[$i]->price,2,'.',',');
		    
		}
		$db = null;
		//echo  json_encode($vouchers);
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","featuredcat":'.$vouchers.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getLaunchTodayPromo() {     
        $site_settings = findByIdArray('1','site_settings');
        $is_active = 1;  
        if(!empty($site_settings['new_promo_days']))
        {
            $start_day = date('Y-m-d',  strtotime('-'.($site_settings['new_promo_days']).'days'));
        }
        else {
            $start_day = date('Y-m-d');
        }
	
	$todayDate = date('Y-m-d');
	$site_path = SITEURL;
        
        
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.buy_count,offers.quantity FROM offers where DATE(offers.offer_from_date) <=CURDATE() and DATE(offers.offer_from_date)> '$start_day' and DATE(offers.offer_to_date)>=CURDATE() and offers.is_active=1 ORDER BY offers.offer_from_date DESC";
        
        //echo $sql; and offers.offer_type_id!=3
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		#$stmt->bindParam("lastdate", $lastdate);
		#$stmt->bindParam("todayDate", $todayDate);
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                        $img = $site_path.'voucher_images/default.jpg';
                        $vouchers[$i]->image = $img;
                    }
                    else{                            
                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
                        $vouchers[$i]->image = $img;                            
                    }
					/*$now = time(); // or your date as well
					$last_date = strtotime($vouchers[$i]->offer_to_dat);
					$datediff = $last_date - $now;
					if($datediff<0)
					{
						$vouchers[$i]->end_time = 0
					}else{
						$vouchers[$i]->end_time = floor($datediff/(60*60*24));
					}*/
                    $datediff = strtotime($vouchers[$i]->offer_to_date) - time();                    ;
                    $vouchers[$i]->end_time = ceil($datediff/(60*60*24));
                    if(empty($vouchers[$i]->buy_count))
                        $vouchers[$i]->buy_count = "0";
		    
		}
		$db = null;
		//echo  json_encode($vouchers);
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","todayPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}


function getLastdayPromo() {     

        $site_settings = findByIdArray('1','site_settings');
        $is_active = 1;  
        if(!empty($site_settings['last_day_promo']))
        {
            $start_day = date('Y-m-d',  strtotime('+'.($site_settings['last_day_promo']).'days'));
        }
        else {
            $start_day = date('Y-m-d');
        }
        $is_active = 1;  
	$newdate = date('Y-m-d');
	$site_path = SITEURL;
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.buy_count,offers.quantity FROM offers where offers.is_active=1 and DATE(offers.offer_to_date) >=CURDATE() and DATE(offers.offer_to_date) < '$start_day' ORDER BY offers.offer_to_date ASC";
        
        // and offers.offer_type_id!=3
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		//$stmt->bindParam("newdate", $newdate);
		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","lastdayPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getHotSellingPromo() {     

        $is_active = 1;  
	$lastdate = date('Y-m-d');
	//$newdate = "2016-03-08";
        $site_settings = findByIdArray('1','site_settings');
        if(!empty($site_settings['hot_percentage']))
        {
            $perc = $site_settings['hot_percentage'];
        }
        else
        {
            $perc = 0;
        }
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.buy_count,offers.quantity FROM offers where offers.is_active=1 and offers.offer_to_date >=:lastdate and ((offers.buy_count/offers.quantity)*100)>=$perc order by offers.buy_count DESC";
        
        //echo $sql;desc limit 2 and offers.offer_type_id!=3
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("lastdate", $lastdate);
		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    $datediff = strtotime($vouchers[$i]->offer_to_date) - time();                    ;
                    $vouchers[$i]->end_time = ceil($datediff/(60*60*24));
                    if(empty($vouchers[$i]->buy_count))
                        $vouchers[$i]->buy_count = "0";
		}
		$db = null;
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","getHotSellingPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getSpecialPromo() {     

        $is_active = 1;  
	$lastdate = date('Y-m-d');
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.special_tag,offers.buy_count,offers.quantity FROM offers where offers.is_active=1 and offers.is_special=1 and offers.offer_to_date >=:lastdate order by offers.offer_from_date DESC";
        $site_settings = findByIdArray(1,'site_settings');
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("lastdate", $lastdate);
		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
                        $vouchers[$i]->end_time = strtotime($vouchers[$i]->offer_to_date);
		    
		}
		$db = null;
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","getSpecialPromo":'.$vouchers.',"count":'.$count.',"site_setting":'.  json_encode($site_settings).'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}


function getPromoList() {     
    $is_active = 1;  
	$lastdate = date('Y-m-d');
	$offer_type_id = 3;
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.special_tag FROM offers where offers.is_active=1 and DATE(offers.offer_to_date) >=:lastdate and offers.offer_type_id !=:offer_type_id order by offers.title";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("lastdate", $lastdate);
		$stmt->bindParam("offer_type_id", $offer_type_id);
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","getMenuPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getExpireSoonPromoList() {
        $is_active = 1;
        $current_date = date('Y-m-d');
		$offer_type_id = 3;
        $newDate = date('Y-m-d',strtotime('+7 days'));
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.special_tag FROM offers where offers.is_active=1 and offers.offer_to_date BETWEEN :start and :end and offers.offer_type_id !=:offer_type_id order by offers.title";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("start", $current_date);
		$stmt->bindParam("end", $newDate);
		$stmt->bindParam("offer_type_id", $offer_type_id);
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","getMenuPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getMenuPromo() {     
    $is_active = 1;  
	$lastdate = date('Y-m-d');
	$offer_type_id = 1;
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.special_tag FROM offers where offers.is_active=1 and offers.offer_to_date >=:lastdate and offers.offer_type_id =:offer_type_id order by rand()";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("lastdate", $lastdate);
		$stmt->bindParam("offer_type_id", $offer_type_id);
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","getMenuPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getPaymentPromo() {     
    $is_active = 1;  
	$lastdate = date('Y-m-d');
	$offer_type_id = 2;
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.special_tag FROM offers where offers.is_active=1 and offers.offer_to_date >=:lastdate and offers.offer_type_id =:offer_type_id order by rand()";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("lastdate", $lastdate);
		$stmt->bindParam("offer_type_id", $offer_type_id);
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","getPaymentPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getMerchantPromo() {     
    $is_active = 1;  
	$lastdate = date('Y-m-d');
	$offer_type_id = 3;
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.special_tag FROM offers where offers.is_active=1 and offers.offer_to_date >=:lastdate and offers.offer_type_id =:offer_type_id order by rand()";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("lastdate", $lastdate);
		$stmt->bindParam("offer_type_id", $offer_type_id);
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","getMerchantPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getResturantPromo($rid) {  
	//$outlets = findByConditionArray(array('restaurant_id' => $rid),'outlets');
	//$outlet_ids = array_column($outlets,'id');
	//$offers_query = "select * from offer_outlet_map where outlet_id IN(".implode(',',$outlet_ids).") group by offer_id";
	//$offers = findByQuery($offers_query);
	//$offer_ids = array_column($offers,'offer_id');
    $restaurant_details = findByIdArray($rid,'restaurants');
    if(!empty($restaurant_details['description']))
    {
        //$restaurant_details['description'] = strip_tags($restaurant_details['description']);
    }
    $is_active = 1;  
	$lastdate = date('Y-m-d');
	if(!empty($rid))
	{
		$sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.special_tag,offers.buy_count,offers.quantity FROM offers where offers.is_active=1 and offers.offer_to_date >=:lastdate and offers.restaurant_id=:rid  order by title DESC";
			
			//echo $sql;
			$site_path = SITEURL;
			
		try {
			$db = getConnection();
			$stmt = $db->prepare($sql);		
			$stmt->bindParam("lastdate", $lastdate);
		        $stmt->bindParam("rid", $rid);
			$stmt->execute();
			$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
			$count = $stmt->rowCount();
			
			for($i=0;$i<$count;$i++){
				//$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
				$todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
				$vouchers[$i]->offer_to_date = $todate;
				if(empty($vouchers[$i]->image)){
								$img = $site_path.'voucher_images/default.jpg';
								$vouchers[$i]->image = $img;
							}
							else{                            
								$img = $site_path."voucher_images/".$vouchers[$i]->image;
								$vouchers[$i]->image = $img;                            
							}
				
			}
			$db = null;
			$vouchers = json_encode($vouchers);
			$result = '{"type":"success","getMerchantPromo":'.$vouchers.',"count":'.$count.',"restaurant" :'.  json_encode($restaurant_details).'}';
			
		} catch(PDOException $e) {
			$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
		}
	}
	else
	{
		$result =  '{"type":"error","message":"No promo found."}'; 
	}
	echo $result;
}

function getResturantSpecialPromo($rid) {  
	/*$outlets = findByConditionArray(array('restaurant_id' => $rid),'outlets');
	$outlet_ids = array_column($outlets,'id');
	$offers_query = "select * from offer_outlet_map where outlet_id IN(".implode(',',$outlet_ids).") group by offer_id";
	$offers = findByQuery($offers_query);
	$offer_ids = array_column($offers,'offer_id');*/
	
	if(!empty($rid))
	{
		$restaurant_details = findByIdArray($rid,'restaurants');
		$is_active = 1;  
		$site_settings = findByIdArray('1','site_settings');
		$lastdate = date('Y-m-d');
		$sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.special_tag,offers.buy_count,offers.quantity FROM offers where offers.is_active=1 and offers.offer_to_date >=:lastdate and offers.restaurant_id=:rid and is_special=1 order by title DESC";
			
			//echo $sql;
			$site_path = SITEURL;
			
		try {
			$db = getConnection();
			$stmt = $db->prepare($sql);		
			$stmt->bindParam("lastdate", $lastdate);
			$stmt->bindParam("rid", $rid);
			$stmt->execute();
			$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
			$count = $stmt->rowCount();
			
			for($i=0;$i<$count;$i++){
				//$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
				$todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
				$vouchers[$i]->offer_to_date = $todate;
				if(empty($vouchers[$i]->image)){
								$img = $site_path.'voucher_images/default.jpg';
								$vouchers[$i]->image = $img;
							}
							else{                            
								$img = $site_path."voucher_images/".$vouchers[$i]->image;
								$vouchers[$i]->image = $img;                            
							}
				
			}
			$db = null;
			$vouchers = json_encode($vouchers);
			$result = '{"type":"success","getMerchantPromo":'.$vouchers.',"count":'.$count.',"restaurant" :'.  json_encode($restaurant_details).',"site_setting":'.  json_encode($site_settings).'}';
			
		} catch(PDOException $e) {
			$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
		}
	}
	else{
		$result =  '{"type":"error","message":"No promo foiund."}'; 
	}
	echo $result;
}

function getResturantLaunchTodayPromo($rid) {  
	/*$outlets = findByConditionArray(array('restaurant_id' => $rid),'outlets');
	$outlet_ids = array_column($outlets,'id');
	$offers_query = "select * from offer_outlet_map where outlet_id IN(".implode(',',$outlet_ids).") group by offer_id";
	$offers = findByQuery($offers_query);
	$offer_ids = array_column($offers,'offer_id');*/
	
	if(!empty($rid))
	{
		$restaurant_details = findByIdArray($rid,'restaurants');
		$is_active = 1;  
		$lastdate = date('Y-m-d');
			 $site_settings = findByIdArray('1','site_settings');
			if(!empty($site_settings['new_promo_days']))
			{
				$start_day = date('Y-m-d',  strtotime('-'.($site_settings['new_promo_days']).'days'));
			}
			else {
				$start_day = date('Y-m-d');
			}
		$sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.special_tag,offers.buy_count,offers.quantity FROM offers where offers.is_active=1 and DATE(offers.offer_from_date) <=CURDATE() and DATE(offers.offer_from_date)> '$start_day' and DATE(offers.offer_to_date)>=CURDATE() and offers.restaurant_id=:rid order by title DESC";
			
			//echo $sql;
			$site_path = SITEURL;
			
		try {
			$db = getConnection();
			$stmt = $db->prepare($sql);		
			//$stmt->bindParam("lastdate", $lastdate);
		        $stmt->bindParam("rid", $rid);
			$stmt->execute();
			$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
			$count = $stmt->rowCount();
			
			for($i=0;$i<$count;$i++){
				//$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
				$todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
				$vouchers[$i]->offer_to_date = $todate;
				if(empty($vouchers[$i]->image)){
								$img = $site_path.'voucher_images/default.jpg';
								$vouchers[$i]->image = $img;
							}
							else{                            
								$img = $site_path."voucher_images/".$vouchers[$i]->image;
								$vouchers[$i]->image = $img;                            
							}
							if(empty($vouchers[$i]->buy_count))
							$vouchers[$i]->buy_count = "0";
				
			}
			$db = null;
			$vouchers = json_encode($vouchers);
			$result = '{"type":"success","getMerchantPromo":'.$vouchers.',"count":'.$count.',"restaurant" :'.  json_encode($restaurant_details).'}';
			
		} catch(PDOException $e) {
			$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
		}
	}
	else
	{
		$result =  '{"type":"error","message":"No promo foiund."}'; 
	}
	echo $result;
}

function getResturantLastDayPromo($rid) {  
	/*$outlets = findByConditionArray(array('restaurant_id' => $rid),'outlets');
	$outlet_ids = array_column($outlets,'id');
	$offers_query = "select * from offer_outlet_map where outlet_id IN(".implode(',',$outlet_ids).") group by offer_id";
	$offers = findByQuery($offers_query);
	$offer_ids = array_column($offers,'offer_id');*/
	
	if(!empty($rid))
	{
		$restaurant_details = findByIdArray($rid,'restaurants');
		$is_active = 1;  
		$lastdate = date('Y-m-d');
			$site_settings = findByIdArray('1','site_settings');
			if(!empty($site_settings['last_day_promo']))
			{
				$start_day = date('Y-m-d',  strtotime('+'.($site_settings['last_day_promo']).'days'));
			}
			else {
				$start_day = date('Y-m-d');
			}
		$sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.special_tag,offers.buy_count,offers.quantity FROM offers where offers.is_active=1 and DATE(offers.offer_to_date) >=CURDATE() and DATE(offers.offer_to_date) < '$start_day' and offers.restaurant_id=:rid order by title DESC";
			
			//echo $sql;
			$site_path = SITEURL;
			
		try {
			$db = getConnection();
			$stmt = $db->prepare($sql);		
			//$stmt->bindParam("lastdate", $lastdate);
			$stmt->bindParam("rid", $rid);
			$stmt->execute();
			$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
			$count = $stmt->rowCount();
			
			for($i=0;$i<$count;$i++){
				//$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
				$todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
				$vouchers[$i]->offer_to_date = $todate;
				if(empty($vouchers[$i]->image)){
								$img = $site_path.'voucher_images/default.jpg';
								$vouchers[$i]->image = $img;
							}
							else{                            
								$img = $site_path."voucher_images/".$vouchers[$i]->image;
								$vouchers[$i]->image = $img;                            
							}
							if(empty($vouchers[$i]->buy_count))
							$vouchers[$i]->buy_count = "0";
				
			}
			$db = null;
			$vouchers = json_encode($vouchers);
			$result = '{"type":"success","getMerchantPromo":'.$vouchers.',"count":'.$count.',"restaurant" :'.  json_encode($restaurant_details).'}';
			
		} catch(PDOException $e) {
			$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
		}
	}
	else
	{
		$result =  '{"type":"error","message":"No promo found."}';
	}
	echo $result;
}

function getResturantHotSellingPromo($rid) {  
	/*$outlets = findByConditionArray(array('restaurant_id' => $rid),'outlets');
	$outlet_ids = array_column($outlets,'id');
	$offers_query = "select * from offer_outlet_map where outlet_id IN(".implode(',',$outlet_ids).") group by offer_id";
	$offers = findByQuery($offers_query);
	$offer_ids = array_column($offers,'offer_id');*/
	
	if(!empty($rid))
	{		
		$restaurant_details = findByIdArray($rid,'restaurants');
		$is_active = 1;  
		$lastdate = date('Y-m-d');
			$site_settings = findByIdArray('1','site_settings');
			if(!empty($site_settings['hot_percentage']))
			{
				$perc = $site_settings['hot_percentage'];
			}
			else
			{
				$perc = 0;
			}
		$sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.special_tag,offers.buy_count,offers.quantity FROM offers where offers.is_active=1 and offers.offer_to_date >=:lastdate and ((offers.buy_count/offers.quantity)*100)>=$perc and offers.restaurant_id=:rid order by title DESC";
			
			//echo $sql;
			$site_path = SITEURL;
			
		try {
			$db = getConnection();
			$stmt = $db->prepare($sql);		
			$stmt->bindParam("lastdate", $lastdate);
			$stmt->bindParam("rid", $rid);
			$stmt->execute();
			$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
			$count = $stmt->rowCount();
			
			for($i=0;$i<$count;$i++){
				//$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
				$todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
				$vouchers[$i]->offer_to_date = $todate;
				if(empty($vouchers[$i]->image)){
								$img = $site_path.'voucher_images/default.jpg';
								$vouchers[$i]->image = $img;
							}
							else{                            
								$img = $site_path."voucher_images/".$vouchers[$i]->image;
								$vouchers[$i]->image = $img;                            
							}
							if(empty($vouchers[$i]->buy_count))
							$vouchers[$i]->buy_count = "0";
				
			}
			$db = null;
			$vouchers = json_encode($vouchers);
			$result = '{"type":"success","getMerchantPromo":'.$vouchers.',"count":'.$count.',"restaurant" :'.  json_encode($restaurant_details).'}';
			
		} catch(PDOException $e) {
			$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
		}
	}
	else
	{
		$result =  '{"type":"error","message":"No promo foiund."}';
	}
	echo $result;
}

function getOwnOffer($user_id) {     

        $is_active = 1;  
	$newdate = date('Y-m-d');
	//$newdate = "2016-03-08";
        $sql = "SELECT * FROM offers where offers.merchant_id =:user_id and offers.is_active=1";
        
        
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("user_id", $user_id);
		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    $todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->expire_date = $todate;
		    
		}
		$db = null;
		echo  json_encode($vouchers); 
	} catch(PDOException $e) {
		echo '{"error":{"message":'. $e->getMessage() .'}}'; 
	}
}

function addOffer(){
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
   // print_r($body);exit;
   //$vid = $_POST['voucher_id'];
    //$vid = $body->voucher_id;
    $offer = array();
    //echo $vid;exit;
    //$sql = "SELECT * FROM voucher_resales WHERE voucher_id=:voucher_id";
    //try {
	    
	    //if($resale_count==0){
		$body->created_on = date('Y-m-d h:m:s');
		$body->offer_from_date = date('Y-m-d',strtotime($body->offer_from_date));
		$body->offer_to_date = date('Y-m-d',strtotime($body->offer_to_date));
		//$body->is_sold = 0;
		$body->is_active = 1;
		$allinfo['save_data'] = $body;
		$offer = add(json_encode($allinfo),'offers');
		if(!empty($offer)){
		    $offer = json_decode($offer);
		    $dd = date('d M, Y',strtotime($offer->created_on));
		    $offer->created_on = $dd;
		    $offers = json_encode($offer);
		    $result = '{"type":"success","message":"Added Successfully","offers":'.$offers.' }'; 
		}
		else{
		    $result = '{"type":"error","message":"Try Again!"}'; 
		}
	    //}
	    
       //} 
	echo  $result;
}


function getOfferDetail($vid){
  //  $sql = "SELECT * FROM vouchers, offers, users where vouchers.offer_id = offers.id and vouchers.user_id = users.id and vouchers.id=:id";
    $sql = "SELECT * FROM offers WHERE offers.id=:id";
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
		$to_date = date('d M,Y', strtotime($vouchers->offer_to_date));
		//$offer_to_date = date('d M,Y', strtotime($vouchers->offer_to_date));
		$vouchers->expire_date = $to_date;
		//$vouchers->offer_to_date = $offer_to_date;
		if(!empty($vouchers)){
			$marchantId = $vouchers->merchant_id;
			$offerId = $vouchers->id;
			$sql1 = "SELECT * FROM offer_images WHERE offer_images.offer_id=:offerid";
			$stmt1 = $db->prepare($sql1);  
			$stmt1->bindParam("offerid", $offerId);
			$stmt1->execute();
			$countvoucherimage = $stmt1->rowCount();
			$offerr_image = $stmt1->fetchAll(PDO::FETCH_OBJ);  
			//print_r($offer_image);
			//exit; 
			if(empty($offerr_image)){
			    $img = $site_path.'voucher_images/default.jpg';
			    $offer_image[0]['image'] = $img;

			}
			else{
			    for($i=0;$i<$countvoucherimage;$i++){
				$img = $site_path."voucher_images/".$offerr_image[$i]->image;
				//echo $img;
				
				$offer_image[$i]['image'] = $img;
				
			    }
			}
			//print_r($offer_image);exit;
			/*$sql2 = "SELECT restaurant_details.title, restaurant_details.logo, restaurant_details.address, restaurant_details.user_id FROM restaurant_details, offers  WHERE offers.merchant_id = restaurant_details.user_id and offers.restaurant_id = restaurant_details.id and offers.id=:off_id and restaurant_details.user_id=:merchantid";
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
			}*/
			$unique_field = array();
			$unique_field['offer_id'] = $offerId;
			$unique_field['is_active'] = 1;
			$soldCount = soldCount(json_encode($unique_field),'vouchers');
			//echo $soldCount;exit;
			$voucher_details = json_encode($vouchers); 
			//$restaurant_details = json_encode($restaurant_details);
			$offer_image = json_encode($offer_image);
			$db = null;
			$result = '{"type":"success","offer_details":'.$voucher_details.',"offer_image":'.$offer_image.',"total_sold":'.$soldCount.'}';
		}
		else if(empty($vouchers)){
			$result = '{"type":"error","message":"Not found Offer Id"}';
		}

	} catch(PDOException $e) {
		$result =  '{"error":{"message":'. $e->getMessage() .'}}'; 
	}


	echo  $result;
}

function offerImageUpload(){
        $request = Slim::getInstance()->request();
        $body = json_decode($request->getBody());
	$site_path = SITEURL;
        $target_dir = realpath('voucher_images')."/";
        $unique_code = time().rand(100000,1000000);
	$img = $unique_code.'_'.basename($_FILES["file"]["name"]);
        $target_file = $target_dir . $img;
        if(move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)){
	    //echo realpath('user_images');        
	 //   print_r($_FILES["file"]);
	 //   print_r($_POST);
	    $path = $site_path . "voucher_images/" . $img;
	    $uid = $_POST['offer_id'];
	    $offer = array();
	    $offer['image'] = $img;
	    $offer['offer_id'] = $uid;
	    $updateinfo['save_data'] = $offer;
		    // print_r($updateinfo);exit;
	    $update = add(json_encode($updateinfo),'offer_images');

	    if(!empty($update)){
		    $result = '{"type":"success","message":"Image Uploaded Successfully","offer_image_details":"'.$path.'"}';
	    }
	    else{
		    $result = '{"type":"error","message":"Try again! Not Uploaded"}';
	    }
	}
	else{
		$result = '{"type":"error","message":"Try again! Not Uploaded"}';
	}
        echo $result;
}

function getAllResellList(){
	$current_date = date('Y-m-d');
	$site_path = SITEURL;
	 $resales = array();
	   	 $sql = "SELECT offers.title, offers.image, offers.buy_count, offers.offer_percent, voucher_resales.id as voucher_resale_id, voucher_resales.voucher_id, voucher_resales.price, voucher_resales.points, voucher_resales.user_id, voucher_resales.is_sold, voucher_resales.is_active, vouchers.to_date as expire_date, vouchers.price as voucher_price  FROM offers, voucher_resales, vouchers WHERE offers.id = vouchers.offer_id and voucher_resales.voucher_id = vouchers.id and vouchers.to_date >=:current_date and voucher_resales.is_active ='1' and voucher_resales.is_sold='0' ORDER BY voucher_resales.created_on DESC";   	
	   	 //$sql = "SELECT offers.title, voucher_resales.id as voucher_resale_id, voucher_resales.voucher_id, voucher_resales.price, voucher_resales.points, voucher_resales.user_id, voucher_resales.is_sold, voucher_resales.is_active, vouchers.to_date as expire_date, vouchers.price as voucher_price, max(voucher_bids.bid_price) as max_bid_price FROM offers, voucher_resales,voucher_bids, vouchers WHERE offers.id = vouchers.offer_id and voucher_resales.voucher_id = vouchers.id and voucher_bids.voucher_resale_id = voucher_resales.id and vouchers.to_date >=:current_date and voucher_resales.is_active ='1' and voucher_resales.is_sold='0' and voucher_resales.user_id!=:user_id";
	   
    try {
	    $db = getConnection();
	    $stmt = $db->prepare($sql);  
	    //$stmt->bindParam("user_id", $userid);
	    $stmt->bindParam("current_date", $current_date);
	    $stmt->execute();
	    $resales = $stmt->fetchAll(PDO::FETCH_OBJ);  
	    //print_r($resales);//exit;
	    $list_count = $stmt->rowCount();
	    $stmt=null;
	    $db=null;
	    if(!empty($resales)){
	    	for($i=0;$i<$list_count;$i++){
				$rid = $resales[$i]->voucher_resale_id;
				$sql = "SELECT max(voucher_bids.bid_price) as high_bid,min(voucher_bids.bid_price) as low_bid FROM voucher_bids where voucher_bids.voucher_resale_id =:rid";
				$db = getConnection();
				$stmt = $db->prepare($sql);  
				$stmt->bindParam("rid", $rid);
				$stmt->execute();
				$resalesBid = $stmt->fetchObject();
				//print_r($resalesBid);
				if($resalesBid->high_bid!='' && $resalesBid->low_bid!='')
				{
					$resales[$i]->high_bid = $resalesBid->high_bid;
					$resales[$i]->low_bid = $resalesBid->low_bid;
				}else{
					$resales[$i]->high_bid = 0;
					$resales[$i]->low_bid = 0;
				}
				//print_r($resales[$i]);exit;
			    $todate = date('d M, Y H:i:s', strtotime($resales[$i]->expire_date));
			    $resales[$i]->to_date = date('d M, Y', strtotime($resales[$i]->expire_date));
			    $resales[$i]->expire_date = $todate;
                        if(empty($resales[$i]->image)){
                                $img = $site_path.'voucher_images/default.jpg';
                                $resales[$i]->image = $img;
                        }
                        else{                            
                                $img = $site_path."voucher_images/".$resales[$i]->image;
                                $resales[$i]->image = $img;                            
                        }
			}
	    		$resales = json_encode($resales);
	    	     $result = '{"type":"success","resale_details":'.$resales.'}';
	    }
	    else if(empty($resales)){
		    $result = '{"type":"error","message":"No Records Found"}'; 
		}
       } 
       catch(PDOException $e) {
	    $result =  '{"error":{"message":'. $e->getMessage() .'}}'; 
	}
	echo  $result;
}

function getPromoDetails($id) {     

        $sql = "SELECT * from offers where offers.id=:id";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);	
		$stmt->bindParam("id", $id);
		
		$stmt->execute();
		$offer = $stmt->fetchObject();  
		$count = $stmt->rowCount();
		if($count>0)
		{
                    $offer->created_on = date('m-d-Y',strtotime($offer->created_on));
                    $offer->offer_from_date = date('m-d-Y',strtotime($offer->offer_from_date));
                    $offer->offer_to_date = date('m-d-Y',strtotime($offer->offer_to_date));
                            if(empty($offer->image)){
                                    $img = $site_path.'voucher_images/default.jpg';
                                    $offer->image = $img;
                            }
                            else{                            
                                    $img = $site_path."voucher_images/".$offer->image;
                                    $offer->image = $img;                            
                            }
                    $rid = 	$offer->restaurant_id;
                    $sql = "SELECT * from restaurants where restaurants.id=:rid";	
                    $db = getConnection();
                    $stmt = $db->prepare($sql);	
                    $stmt->bindParam("rid", $rid);
                    $stmt->execute();
                    $restaurants = $stmt->fetchObject();
                            if(empty($restaurants->logo)){
                                    $img = $site_path.'restaurant_images/default.jpg';
                                    $restaurants->logo = $img;
                            }
                            else{                            
                                    $img = $site_path."restaurant_images/".$restaurants->logo;
                                    $restaurants->logo = $img;                            
                            }
                    $db = null;
                    //echo '<pre>';print_r($offer);print_r($restaurants);exit;
                    $offer = json_encode($offer);
                    $restaurants = json_encode($restaurants);
                    $categories = findByConditionArray(array('offer_id' => $id),'offer_category_map');
                    $outlets = findByConditionArray(array('offer_id' => $id),'offer_outlet_map');

                    $result = '{"type":"success","offer":'.$offer.',"restaurants":'.$restaurants.',"count":'.$count.',"categories" : '.json_encode($categories).',"outlets" : '.json_encode($outlets).'}';
		}else{
			$result =  '{"type":"error","message":"Sorry no promo found"}'; 
		}
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getRelatedPromo() {     

    $is_active = 1;  
	$lastdate = date('Y-m-d');
	$sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.special_tag FROM offers where offers.is_active=1 and offers.offer_to_date >=:lastdate order by rand() limit 3";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("lastdate", $lastdate);
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    $todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","getRelatedPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getAllActiveMembership($userid) {     
        //echo 'aqb';exit;
        $is_active = 1;  
        $site_path = SITEURL;
	$newdate = date('Y-m-d');
        $sql = "SELECT id, title FROM offers where offers.offer_to_date >=:date and offers.is_active=:is_active and offers.merchant_id=:user_id and offers.offer_type_id=3";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("is_active", $is_active);
		$stmt->bindParam("user_id", $userid);
		$stmt->bindParam("date", $newdate);
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		$db = null;
		echo  '{"type":"success","memberships":'.json_encode($vouchers).'}'; 
	} catch(PDOException $e) {
		echo '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
}

function merchantSaveMember() {     
        //echo 'aqb';exit;
        $is_active = 1;  
        $site_path = SITEURL;
	$newdate = date('Y-m-d');
	$request = Slim::getInstance()->request();
	$membership_info = json_decode($request->getBody());
	$user_exist = $membership_info->user_type;
	unset($membership_info->user_type);
	if($user_exist == "New User"){
	        $unique_field = array();
	        $user = array();
	        $unique_field['email']=$membership_info->email;
	        //$unique_field['username']=$user->username;
	        $rowCount = rowCount(json_encode($unique_field),'users');
	        if($rowCount == 0){
                    $unique_code = time().rand(100000,1000000);
	            $pass = '123456';
	            $user['txt_pwd'] = $pass;
	            $user['email'] = $membership_info->email;
	            $user['unique_code'] = $unique_code;
	            $user['password'] = md5($pass);
	            
	            $user['user_type_id'] = 2;
	            $user['is_active'] = 1;	            
	            $user['registration_date'] = date('Y-m-d h:m:s');
	            //$activation_link = $user->activation_url;
	            
	            //unset($user->activation_url);
	            $allinfo['save_data'] = $user;
	            //$allinfo['unique_data'] = $unique_field;
	            $user_details = add(json_encode($allinfo),'users');
	             if(!empty($user_details)){
	            $user_details = json_decode($user_details);
	            //echo  $user_details->email; exit;
	          //  print_r($user_details);exit;
	            $from = ADMINEMAIL;
	            $to = $user_details->email;
	            $subject ='User Registration';
	            $body ='<html><body><p>Dear User,</p>

		            <p>Thank You for signing up with mFoodGate.<br />
		            <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Your Account Details:</span><br />
		            <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif"><strong>Email: '.$to.'</strong></span><br />
		            <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif"><strong>Password: '.$pass.'</strong></span></p>

		            <p><span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">If we can help you with anything in the meantime just let us know by e-mailing&nbsp;</span>'.$from.'<br />
		            <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Thanks again for signing up with the site</span><span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">!&nbsp;</span></p>

		            <p>Thanks,<br />
		            mFood&nbsp;Team</p>

		            <p>&nbsp;</p></body></html>
		            ';
		            //$body = "Hello";
	            sendMail($to,$subject,$body);
	            $new_user_id = $user_details->id;
	            $offer_details = json_decode(findById($membership_info->offer_id,'offers'));
                    
                    $voucher_data = array();
                    $voucher_data['offer_id'] =$membership_info->offer_id;
                    $voucher_data['created_on'] = $offer_details->created_on;
                    $voucher_data['price'] = $offer_details->price;
                    $voucher_data['offer_price'] = $offer_details->offer_price;
                    $voucher_data['offer_percent'] = $offer_details->offer_percent;
                    $voucher_data['from_date'] = $offer_details->offer_from_date;
                    $voucher_data['to_date'] = $offer_details->offer_to_date;
                    $voucher_data['is_active'] = 1;
                    $s = add(json_encode(array('save_data' => $voucher_data)),'vouchers');
                    $s = json_decode($s);
                    $owner_data = array();
                    $owner_data['offer_id'] = $membership_info->offer_id;
                    $owner_data['voucher_id'] = $s->id;
                    $owner_data['from_user_id'] = 0;
                    $owner_data['to_user_id'] = $new_user_id;
                    $owner_data['purchased_date'] = date('Y-m-d h:i:s');
                    $owner_data['is_active'] = 1;
                    $owner_data['price'] = $offer_details->price;
                    $owner_data['offer_price'] = $offer_details->offer_price;
                    $owner_data['offer_percent'] = $offer_details->offer_percent;
                    $owner_data['buy_price'] = $offer_details->offer_price;
                    add(json_encode(array('save_data' => $owner_data)),'voucher_owner');
	            
	            $result = '{"type":"success","message":"Added Succesfully"}'; 
	            }
	            else{
		         $result = '{"type":"error","message":"Try Again"}'; 
	            }
	        }
	        else if($rowCount > 0){
	           $result = '{"type":"error","message":"Email already exist"}'; 
	        }
	}
	else if($user_exist == "Existing User"){
	        $unique_code = $membership_info->unique_code;
	        $sql = "SELECT * FROM users where users.unique_code=:unique_code";
	        $db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("unique_code", $unique_code);		
		$stmt->execute();
		$user_info = $stmt->fetchObject();  
		$count = $stmt->rowCount();
		if($count == 0){
		        $result = '{"type":"error","message":"User code does not exist"}';
		}else{
		        $new_user_id = $user_info->id;
                        $offer_details = json_decode(findById($membership_info->offer_id,'offers'));

                        $voucher_data = array();
                        $voucher_data['offer_id'] =$membership_info->offer_id;
                        $voucher_data['created_on'] = $offer_details->created_on;
                        $voucher_data['price'] = $offer_details->price;
                        $voucher_data['offer_price'] = $offer_details->offer_price;
                        $voucher_data['offer_percent'] = $offer_details->offer_percent;
                        $voucher_data['from_date'] = $offer_details->offer_from_date;
                        $voucher_data['to_date'] = $offer_details->offer_to_date;
                        $voucher_data['is_active'] = 1;
                        $s = add(json_encode(array('save_data' => $voucher_data)),'vouchers');
                        $s = json_decode($s);
                        $owner_data = array();
                        $owner_data['offer_id'] = $membership_info->offer_id;
                        $owner_data['voucher_id'] = $s->id;
                        $owner_data['from_user_id'] = 0;
                        $owner_data['to_user_id'] = $new_user_id;
                        $owner_data['purchased_date'] = date('Y-m-d h:i:s');
                        $owner_data['is_active'] = 1;
                        $owner_data['price'] = $offer_details->price;
                        $owner_data['offer_price'] = $offer_details->offer_price;
                        $owner_data['offer_percent'] = $offer_details->offer_percent;
                        $owner_data['buy_price'] = $offer_details->offer_price;
                        add(json_encode(array('save_data' => $owner_data)),'voucher_owner');
                        
                        $result = '{"type":"success","message":"Added successfully"}';
                }
                
	}
	echo $result;	
        
}


function getLaunchTodayMembership() {   
        
        $site_settings = findByIdArray('1','site_settings');
        $is_active = 1;  
        if(!empty($site_settings['new_promo_days']))
        {
            $start_day = date('Y-m-d',  strtotime('-'.($site_settings['new_promo_days']).'days'));
        }
        else {
            $start_day = date('Y-m-d');
        }
        $is_active = 1;  
	//$lastdate = date('Y-m-d');
	$todayDate = date('Y-m-d');
	$site_path = SITEURL;	
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.buy_count,offers.quantity FROM offers where DATE(offers.offer_from_date) <=CURDATE() and DATE(offers.offer_from_date)> '$start_day' and offers.is_active=1 and offers.offer_type_id=3 ORDER BY offers.offer_from_date DESC";
        
        //echo $sql;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("lastdate", $lastdate);
		$stmt->bindParam("todayDate", $todayDate);
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    $datediff = strtotime($vouchers[$i]->offer_to_date) - time();                    ;
                    $vouchers[$i]->end_time = ceil($datediff/(60*60*24));
                    if(empty($vouchers[$i]->buy_count))
                        $vouchers[$i]->buy_count = "0";
		}
		$db = null;
		//echo  json_encode($vouchers);
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","todayPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getActiveMembershipPromo() {   
        
        $site_settings = findByIdArray('1','site_settings');
        
        $is_active = 1;  
	//$lastdate = date('Y-m-d');
	$todayDate = date('Y-m-d');
	$site_path = SITEURL;	
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image FROM offers where DATE(offers.offer_to_date) >=CURDATE() and offers.is_active=1 and offers.offer_type_id=3";
        
        //echo $sql;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("lastdate", $lastdate);
		$stmt->bindParam("todayDate", $todayDate);
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		//echo  json_encode($vouchers);
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","Promo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}


function getLastdayMembership() {    
        $site_settings = findByIdArray('1','site_settings');
        $is_active = 1;  
        if(!empty($site_settings['last_day_promo']))
        {
            $start_day = date('Y-m-d',  strtotime('+'.($site_settings['last_day_promo']).'days'));
        }
        else {
            $start_day = date('Y-m-d');
        }
        
        $is_active = 1;  
	$newdate = date('Y-m-d');
	$site_path = SITEURL;
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.buy_count,offers.quantity FROM offers where offers.is_active=1 and DATE(offers.offer_to_date) >=CURDATE() and DATE(offers.offer_to_date) < '$start_day' and offers.offer_type_id=3 ORDER BY offers.offer_to_date ASC";
        
        //echo $sql;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("newdate", $newdate);
		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","lastdayPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getHotSellingMembership() {     

        $site_settings = findByIdArray('1','site_settings');
        if(!empty($site_settings['hot_percentage']))
        {
            $perc = $site_settings['hot_percentage'];
        }
        else
        {
            $perc = 0;
        }
        $is_active = 1;  
	$lastdate = date('Y-m-d');
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image FROM offers where offers.is_active=1 and offers.offer_to_date >=:lastdate and ((offers.buy_count/offers.quantity)*100)>=$perc and offers.offer_type_id=3 order by offers.buy_count DESC";
        
        //echo $sql; limit 2
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("lastdate", $lastdate);
		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","getHotSellingPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getSpecialMembership() {     

        $is_active = 1;  
	$lastdate = date('Y-m-d');
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.special_tag FROM offers where offers.is_active=1 and offers.is_special=1 and offers.offer_to_date >=:lastdate and offers.offer_type_id=3 ORDER BY offers.offer_from_date DESC";
        $site_settings = findByIdArray(1,'site_settings');
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("lastdate", $lastdate);
		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","getSpecialPromo":'.$vouchers.',"count":'.$count.',"site_setting":'.json_encode($site_settings).'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}



function getLaunchTodayMenuPromo() {     

        $site_settings = findByIdArray('1','site_settings');
        $is_active = 1;  
        if(!empty($site_settings['new_promo_days']))
        {
            $start_day = date('Y-m-d',  strtotime('-'.($site_settings['new_promo_days']).'days'));
        }
        else {
            $start_day = date('Y-m-d');
        }
	
	$todayDate = date('Y-m-d');
	$site_path = SITEURL;
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.buy_count,offers.quantity FROM offers where DATE(offers.offer_from_date) <=CURDATE() and DATE(offers.offer_from_date)> '$start_day' and offers.is_active=1 and offers.offer_type_id=1 ORDER BY offers.offer_from_date DESC";
        
        //echo $sql;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("lastdate", $lastdate);
		$stmt->bindParam("todayDate", $todayDate);
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
                        $datediff = strtotime($vouchers[$i]->offer_to_date) - time();                    ;
                    $vouchers[$i]->end_time = ceil($datediff/(60*60*24));
                    if(empty($vouchers[$i]->buy_count))
                        $vouchers[$i]->buy_count = "0";
		    
		}
		$db = null;
		//echo  json_encode($vouchers);
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","todayPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getActiveMenuPromo() {     

        $site_settings = findByIdArray('1','site_settings');
        $is_active = 1;  
        
	
	$todayDate = date('Y-m-d');
	$site_path = SITEURL;
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image FROM offers where DATE(offers.offer_to_date)>= '$todayDate' and offers.is_active=1 and offers.offer_type_id=1";
        
        //echo $sql;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		//$stmt->bindParam("lastdate", $lastdate);
		//$stmt->bindParam("todayDate", $todayDate);
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		//echo  json_encode($vouchers);
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","Promo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}


function getLastdayMenuPromo() {     

        $site_settings = findByIdArray('1','site_settings');
        $is_active = 1;  
        if(!empty($site_settings['last_day_promo']))
        {
            $start_day = date('Y-m-d',  strtotime('+'.($site_settings['last_day_promo']).'days'));
        }
        else {
            $start_day = date('Y-m-d');
        }
        
        $is_active = 1;  
	$newdate = date('Y-m-d');
	$site_path = SITEURL;
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.buy_count,offers.quantity FROM offers where offers.is_active=1 and DATE(offers.offer_to_date) >=CURDATE() and DATE(offers.offer_to_date) < '$start_day' and offers.offer_type_id=1 ORDER BY offers.offer_to_date ASC";
        
        //echo $sql;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("newdate", $newdate);
		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","lastdayPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getHotSellingMenuPromo() {     

        $is_active = 1;  
	$lastdate = date('Y-m-d');
	//$newdate = "2016-03-08";
        $site_settings = findByIdArray('1','site_settings');
        if(!empty($site_settings['hot_percentage']))
        {
            $perc = $site_settings['hot_percentage'];
        }
        else
        {
            $perc = 0;
        }
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image FROM offers where offers.is_active=1 and DATE(offers.offer_to_date) >=:lastdate and offers.offer_type_id=1 and ((offers.buy_count/offers.quantity)*100)>=$perc order by offers.buy_count DESC";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("lastdate", $lastdate);
		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){

		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","getHotSellingPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getSpecialMenuPromo() {     

        $is_active = 1;  
	$lastdate = date('Y-m-d');
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.special_tag FROM offers where offers.is_active=1 and offers.is_special=1 and DATE(offers.offer_to_date) >=:lastdate and offers.offer_type_id=1 ORDER BY offers.offer_from_date DESC";
        
        $site_settings = findByIdArray(1,'site_settings');
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
            $db = getConnection();
            $stmt = $db->prepare($sql);		
            $stmt->bindParam("lastdate", $lastdate);

            $stmt->execute();
            $vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
            $count = $stmt->rowCount();

            for($i=0;$i<$count;$i++){
                //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
                $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
                $vouchers[$i]->offer_to_date = $todate;
                if(empty($vouchers[$i]->image)){
                        $img = $site_path.'voucher_images/default.jpg';
                        $vouchers[$i]->image = $img;
                    }
                    else{                            
                            $img = $site_path."voucher_images/".$vouchers[$i]->image;
                            $vouchers[$i]->image = $img;                            
                    }

            }
            $db = null;
            $vouchers = json_encode($vouchers);
	    $result = '{"type":"success","getSpecialPromo":'.$vouchers.',"site_setting":'.json_encode($site_settings).',"count":'.$count.'}';
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}


function getLaunchTodayPaymentPromo() {     
        $site_settings = findByIdArray('1','site_settings');
        $is_active = 1;  
        if(!empty($site_settings['new_promo_days']))
        {
            $start_day = date('Y-m-d',  strtotime('-'.($site_settings['new_promo_days']).'days'));
        }
        else {
            $start_day = date('Y-m-d');
        }
	$todayDate = date('Y-m-d');
	$site_path = SITEURL;
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.buy_count,offers.quantity FROM offers where DATE(offers.offer_from_date) <=CURDATE() and DATE(offers.offer_from_date)> '$start_day' and offers.is_active=1 and offers.offer_type_id=2 ORDER BY offers.offer_from_date DESC";
        
        //echo $sql;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		//$stmt->bindParam("lastdate", $lastdate);
		//$stmt->bindParam("todayDate", $todayDate); 
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
                         $datediff = strtotime($vouchers[$i]->offer_to_date) - time();                    ;
                    $vouchers[$i]->end_time = ceil($datediff/(60*60*24));
                    if(empty($vouchers[$i]->buy_count))
                        $vouchers[$i]->buy_count = "0";
		    
		}
		$db = null;
		//echo  json_encode($vouchers);
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","todayPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getActivePaymentPromo() {     
        $site_settings = findByIdArray('1','site_settings');
        $is_active = 1;  
       
	$todayDate = date('Y-m-d');
	$site_path = SITEURL;
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image FROM offers where DATE(offers.offer_to_date) >= CURDATE() and offers.is_active=1 and offers.offer_type_id=2";
        
        //echo $sql;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		//$stmt->bindParam("lastdate", $lastdate);
		//$stmt->bindParam("todayDate", $todayDate); 
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		//echo  json_encode($vouchers);
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","Promo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}


function getLastdayPaymentPromo() {     

        $site_settings = findByIdArray('1','site_settings');
        $is_active = 1;  
        if(!empty($site_settings['last_day_promo']))
        {
            $start_day = date('Y-m-d',  strtotime('+'.($site_settings['last_day_promo']).'days'));
        }
        else {
            $start_day = date('Y-m-d');
        }
        $is_active = 1;  
	$newdate = date('Y-m-d');
	$site_path = SITEURL;
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.buy_count,offers.quantity FROM offers where offers.is_active=1 and DATE(offers.offer_to_date) >=CURDATE() and DATE(offers.offer_to_date) < '$start_day' and offers.offer_type_id=2 ORDER BY offers.offer_to_date ASC";
        
        //echo $sql;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		//$stmt->bindParam("newdate", $newdate);
		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","lastdayPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getHotSellingPaymentPromo() {     

        $is_active = 1;  
	$lastdate = date('Y-m-d');
	//$newdate = "2016-03-08";
        $site_settings = findByIdArray('1','site_settings');
        if(!empty($site_settings['hot_percentage']))
        {
            $perc = $site_settings['hot_percentage'];
        }
        else
        {
            $perc = 0;
        }
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.buy_count,offers.quantity FROM offers where offers.is_active=1 and DATE(offers.offer_to_date) >=:lastdate and offers.offer_type_id=2 and ((offers.buy_count/offers.quantity)*100)>=$perc order by offers.buy_count DESC";
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("lastdate", $lastdate);
		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
                        
                        $datediff = strtotime($vouchers[$i]->offer_to_date) - time();                    ;
                    $vouchers[$i]->end_time = ceil($datediff/(60*60*24));
                    if(empty($vouchers[$i]->buy_count))
                        $vouchers[$i]->buy_count = "0";
		    
		}
		$db = null;
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","getHotSellingPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}

function getSpecialPaymentPromo() {     

        $is_active = 1;  
	$lastdate = date('Y-m-d');
	//$newdate = "2016-03-08";
        $sql = "SELECT offers.id,offers.title,offers.price,offers.offer_percent,offers.offer_from_date,offers.offer_to_date,offers.image,offers.special_tag FROM offers where offers.is_active=1 and offers.is_special=1 and DATE(offers.offer_to_date) >=:lastdate and offers.offer_type_id=2 ORDER BY offers.offer_from_date DESC";
        $site_settings = findByIdArray(1,'site_settings');
        
        //echo $sql;
        $site_path = SITEURL;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("lastdate", $lastdate);
		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    //$todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('M d,Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    if(empty($vouchers[$i]->image)){
                            $img = $site_path.'voucher_images/default.jpg';
                            $vouchers[$i]->image = $img;
                        }
                        else{                            
	                        $img = $site_path."voucher_images/".$vouchers[$i]->image;
	                        $vouchers[$i]->image = $img;                            
                        }
		    
		}
		$db = null;
		$vouchers = json_encode($vouchers);
	    $result = '{"type":"success","site_setting":'.json_encode($site_settings).',"getSpecialPromo":'.$vouchers.',"count":'.$count.'}';
		
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}







?>

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

function addResale(){
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
   // print_r($body);exit;
   //$vid = $_POST['voucher_id'];
    $vid = $body->voucher_id;
    $resale_details = array();
    //echo $vid;exit;
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
		$body->is_sold = 0;
		$body->is_active = 1;
		$allinfo['save_data'] = $body;
		$resale_details = add(json_encode($allinfo),'voucher_resales');
		if(!empty($resale_details)){
		    $resale_details = json_decode($resale_details);
		    $dd = date('d M, Y',strtotime($resale_details->created_on));
		    $resale_details->created_on = $dd;
		    $resale_details = json_encode($resale_details);
		    $result = '{"type":"success","text":"Your Voucher is posted for resale Successfully","resale_details":'.$resale_details.' }'; 
		}
		else{
		    $result = '{"type":"error","text":"Try Again!","resale_details":'.$resale_details.'}'; 
		}
	    }
	    else if($resale_count > 0){
		$result = '{"error":{"resale_count":'. $resale_count .'} }';
	    }
       } catch(PDOException $e) {
	    $result =  '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
	echo  $result;
}

function getResellListPostOwn($userid){
	 $current_date = date('Y-m-d');
	 $resales = array();
	   	 $sql = "SELECT offers.title, voucher_resales.id as voucher_resale_id, voucher_resales.voucher_id, voucher_resales.price, voucher_resales.points, voucher_resales.user_id, voucher_resales.is_sold, voucher_resales.is_active, vouchers.to_date as expire_date, vouchers.price as voucher_price FROM offers, voucher_resales, vouchers WHERE offers.id = vouchers.offer_id and voucher_resales.voucher_id = vouchers.id and vouchers.to_date >=:current_date and voucher_resales.is_active ='1' and voucher_resales.is_sold='0' and voucher_resales.user_id=:user_id";
	   	
	   
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
	    	     $result = '{"type":"success","resale_details":'.$resales.' }';
	    }
	    else if(empty($resales)){
		    $result = '{"type":"error","text":"No Records Found" }'; 
		}
       } 
       catch(PDOException $e) {
	    $result =  '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
	echo  $result;
}

function getResellListPostOthers($userid){
	$current_date = date('Y-m-d');
	 $resales = array();
	   	 $sql = "SELECT offers.title, voucher_resales.id as voucher_resale_id, voucher_resales.voucher_id, voucher_resales.price, voucher_resales.points, voucher_resales.user_id, voucher_resales.is_sold, voucher_resales.is_active, vouchers.to_date as expire_date, vouchers.price as voucher_price FROM offers, voucher_resales, vouchers WHERE offers.id = vouchers.offer_id and voucher_resales.voucher_id = vouchers.id and vouchers.to_date >=:current_date and voucher_resales.is_active ='1' and voucher_resales.is_sold='0' and voucher_resales.user_id!=:user_id";   	
	   
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
		    $result = '{"type":"error","text":"No Records Found"}'; 
		}
       } 
       catch(PDOException $e) {
	    $result =  '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
	echo  $result;
}

function getBidderList($id,$userid){
	$current_date = date('Y-m-d');
	$resales = array();
	/*if(!isset($id)){
		echo '{"type":"error","text":"Missing First parameter"}';
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
				$result = '{"type":"error","text":"No Records Found, Invalid Resale Id"}'; 
			}
			else if($resale_count > 0){
				$sql2 = "SELECT * FROM users WHERE id=:userid";
				$stmt = $db->prepare($sql2);  
				$stmt->bindParam("userid", $userid);
				$stmt->execute();
				//$resales = $stmt->fetchObject(); 
				$user_count = $stmt->rowCount();
				if($user_count==0){
					$result = '{"type":"error","text":"No Records Found, Invalid User "}'; 
				}
				else if($user_count > 0){	
					   	 $sql = "SELECT users.first_name, users.last_name, users.email, users.image, users.phone, users.last_login, voucher_resales.voucher_id, voucher_resales.price, voucher_resales.points, voucher_resales.user_id as voucher_resale_user, voucher_resales.is_sold, voucher_resales.is_active, vouchers.to_date as expire_date, vouchers.price as voucher_price, voucher_bids.user_id as bidder_id FROM voucher_resales, vouchers, users, voucher_bids  WHERE voucher_bids.user_id=users.id and vouchers.to_date >=:current_date and voucher_bids.is_active ='1' and voucher_resales.is_sold='0' and voucher_bids.voucher_resale_id = voucher_resales.id and voucher_bids.voucher_id = vouchers.id and voucher_bids.user_id!=:userid and voucher_bids.voucher_resale_id=:voucher_resale_id";   	
					   
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
						    	     $result = '{"type":"success","resale_details":'.$resales.' }';
						    }
						    else if(empty($resales)){
							    $result = '{"type":"error","text":"No Records Found"}'; 
							}
						}
				}
			} 
			 catch(PDOException $e) {
				 $result =  '{"type":"error","text":'. $e->getMessage() .'}'; 
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
	    		 $result = '{"type":"error","text":"Already Sold"}'; 
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
					    $result = '{"type":"success","text":"You bid this voucher Successfully","bid_details":'.$bid_details.' }'; 
				}
				else{
					    $result = '{"type":"error","text":"Try Again!","bid_details":'.$bid_details.'}'; 
				}
		    }
		    else if($bid_count > 0){
				$result = '{"type":"error","text":"You have already bid this voucher"}';
		    }
		} 
	}catch(PDOException $e) {
	    $result =  '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
	echo  $result;
}


function getResellListBidOwn(){
	$current_date = date('Y-m-d');
	 $resales = array();
	   	 $sql = "SELECT voucher_resales.voucher_id, voucher_resales.price, voucher_resales.points, voucher_resales.user_id, voucher_resales.is_sold, voucher_resales.is_active, vouchers.to_date, vouchers.price as voucher_price FROM voucher_resales, vouchers WHERE voucher_resales.voucher_id = vouchers.id and vouchers.to_date >=:current_date and voucher_resales.is_active ='1' and voucher_resales.is_sold='0' and vouchers.user_id=:user_id";
	   	
	   
    try {
	    $db = getConnection();
	    $stmt = $db->prepare($sql);  
	    $stmt->bindParam("user_id", $userid);
	    $stmt->bindParam("current_date", $current_date);
	    $stmt->execute();
	    $resales = $stmt->fetchAll(PDO::FETCH_OBJ);  
	    //print_r($resales);exit;
	  //  $resale_count = $stmt->rowCount();
	    $stmt=null;
	    $db=null;
	    if(!empty($resales)){
	    		$resales = json_encode($resales);
	    	     $result = '{"success":{"resale_details":'.$resales.'} }';
	    }
	    else if(empty($resales)){
		    $result = '{"error":{"text":"No Records Found"} }'; 
		}
       } 
       catch(PDOException $e) {
	    $result =  '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
	echo  $result;
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


?>

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
		echo '{"error":{"message":'. $e->getMessage() .'}}'; 
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
		echo '{"error":{"message":'. $e->getMessage() .'}}'; 
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
		$result =  '{"error":{"message":'. $e->getMessage() .'}}'; 
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
	   	 $sql = "SELECT offers.title, voucher_resales.id as voucher_resale_id, voucher_resales.voucher_id, voucher_resales.price, voucher_resales.points, voucher_resales.user_id, voucher_resales.is_sold, voucher_resales.is_active, vouchers.to_date as expire_date, vouchers.price as voucher_price  FROM offers, voucher_resales, vouchers WHERE offers.id = vouchers.offer_id and voucher_resales.voucher_id = vouchers.id and vouchers.to_date >=:current_date and voucher_resales.is_active ='1' and voucher_resales.is_sold='0' and voucher_resales.user_id!=:user_id";   	
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


function getResellListBidOwn($userid){
	$current_date = date('Y-m-d');
	 $resales = array();
	   	 //$sql = "SELECT voucher_resales.voucher_id, voucher_resales.price, voucher_resales.points, voucher_resales.user_id, voucher_resales.is_sold, voucher_resales.is_active, vouchers.to_date, vouchers.price as voucher_price FROM voucher_resales, vouchers WHERE voucher_resales.voucher_id = vouchers.id and vouchers.to_date >=:current_date and voucher_resales.is_active ='1' and voucher_resales.is_sold='0' and vouchers.user_id=:user_id";
	   	 
	   	 $sql = "SELECT voucher_bids.voucher_id,voucher_bids.user_id,voucher_bids.voucher_resale_id,voucher_bids.bid_price,voucher_bids.m_points as bid_points, voucher_bids.is_accepted, voucher_resales.price, voucher_resales.points, voucher_resales.is_sold, voucher_resales.is_active, vouchers.to_date, vouchers.price as voucher_price,offers.title FROM voucher_bids, voucher_resales, vouchers, offers WHERE voucher_resales.id = voucher_bids.voucher_resale_id and vouchers.id = voucher_bids.voucher_id and offers.id = vouchers.offer_id and vouchers.to_date >=:current_date and voucher_resales.is_active ='1' and voucher_bids.user_id=:user_id";
	   	
	   
	   
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
			    if(($resales[$i]->is_accepted == 1) && ($resales[$i]->is_sold == 1)){
			        $resales[$i]->Status = 'Accteped';
			    }else if(($resales[$i]->is_accepted == 0) && ($resales[$i]->is_sold == 1)){
			        $resales[$i]->Status = 'Cancelled';
			    }else{
			        $resales[$i]->Status = 'Pending';
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

function saveVoucherResale(){
	$request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	//print_r($body);exit;
	$sql = "SELECT voucher_owner.id, voucher_owner.is_active, vouchers.id as voucher_id, vouchers.offer_id, vouchers.view_id, vouchers.user_id, users.first_name, users.last_name, users.email  FROM vouchers, voucher_owner, users WHERE voucher_owner.voucher_id = vouchers.id and vouchers.user_id = users.id and voucher_owner.is_active = '1' and voucher_owner.voucher_id=:voucherid and vouchers.user_id=:userid";
	   	  
    try {
	    $db = getConnection();
	    $stmt = $db->prepare($sql);  
	    $stmt->bindParam("voucherid", $body->voucher_id);
	    $stmt->bindParam("userid", $body->to_user_id);
	    $stmt->execute();
	    $saveresales = $stmt->fetchObject();  
	   // print_r($saveresales);exit;
	    //$resale_count = $stmt->rowCount();
	   $ownerid = $saveresales->id;
	   $offerid = $saveresales->offer_id;
	   $viewid = $saveresales->view_id;
	   $resaleid = $body->resell_id;
	   $bidid = $body->bid_id;
	    $vid = $saveresales->voucher_id;
	    //print_r($resales);
	   if(!empty($saveresales)){
	   	$sql2 = "SELECT * FROM users WHERE users.id=:uid";
	    	$stmt2 = $db->prepare($sql2);  
	    	$stmt2->bindParam("uid", $body->from_user_id);
	    	$stmt2->execute();
	    	$fromUser = $stmt2->fetchObject();
	    	
	     $sql1 = "SELECT * FROM voucher_resales, voucher_bids WHERE voucher_resales.voucher_id = voucher_bids.voucher_id AND voucher_resales.is_sold =  '1' AND voucher_resales.id=:resaleId AND voucher_bids.id=:bidId AND voucher_bids.is_accepted =  '1' AND voucher_bids.voucher_id=:voucherid";
	    $stmt1 = $db->prepare($sql1);  
	    $stmt1->bindParam("voucherid", $vid);
	    $stmt1->bindParam("resaleId", $resaleid);
	    $stmt1->bindParam("bidId", $bidid);
	    $stmt1->execute();
	    //$checkSales = $stmt1->fetchObject();
	    $checkSales = $stmt1->rowCount();
	    //print_r($checkSales);exit;
	    $stmt=null;
	    $db=null;
	    if($checkSales==0){
	    		$arr = array();
	    		$arr['is_active'] = 0;
	    		$arr['sold_date'] = date('Y-m-d h:i:s');
	    		$allinfo['save_data'] = $arr;
			$old_owner_details = edit(json_encode($allinfo),'voucher_owner',$ownerid);
			//print_r($old_owner_details);exit;
			if($old_owner_details){
				$arr1 = array();
				$arr1['is_sold'] = '1';
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
						$data['price'] = $body->price;
						$data['offer_price'] = '0.00';
						$data['offer_percent'] = '0.00';
						$data['is_active'] = '1';
						$data['purchased_date'] = date('Y-m-d h:i:s');
						$newinfo['save_data'] = $data;
						$new_owner_details  = add(json_encode($newinfo),'voucher_owner');
						if(!empty($new_owner_details)){
						$new = json_decode($new_owner_details);
						    $from = ADMINEMAIL;
						    //$to = $saveresales->email;
						    $to = 'nits.ananya15@gmail.com';
						    $subject ='Resale Voucher Sell';
						    $body ='<html><body><p>Dear '.$saveresales->first_name.' '.$saveresales->last_name.',</p>

							    <p>'.$fromUser->first_name.' '.$fromUser->last_name. ' have Sold  a resale voucher<br />
							    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Voucher View Id :</span>'.$new->voucher_view_id.'<br />
							    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Voucher price :</span>'.$new->price.'<br />
							    
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

function getAllFeaturedCats($cat_id) {     

        $is_active = 1;  
	$newdate = date('Y-m-d');
	//$newdate = "2016-03-08";
        $sql = "SELECT * FROM offers where offers.offer_to_date =:date and offers.is_active=1";
        if($cat_id != 0){
                $sql.=" and offers.category_id =:cat_id";
        }
        echo $newdate;
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);		
		$stmt->bindParam("date", $newdate);
		if($cat_id != 0){
		        $stmt->bindParam("cat_id", $cat_id);
		}
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    $todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = $todate;
		    
		}
		$db = null;
		echo  json_encode($vouchers); 
	} catch(PDOException $e) {
		echo '{"error":{"message":'. $e->getMessage() .'}}'; 
	}
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


?>

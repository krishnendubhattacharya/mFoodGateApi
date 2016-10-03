<?php
function addSwap(){
    
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $voucher_id = $body->voucher_id;
	$offer_id = $body->offer_id;
	$user_id = $body->user_id;
	$newdate = date('Y-m-d');
        $typedata='';
        $areadata='';
        
    //echo $voucher_id.'|'.$offer_id.'|'.$user_id;exit;
    $sql = "SELECT * FROM vouchers, voucher_owner, offers where vouchers.offer_id=offers.id and voucher_owner.voucher_id=vouchers.id and vouchers.to_date >=:date and voucher_owner.is_active='1' and voucher_owner.to_user_id=:user_id and vouchers.id=:voucher_id";
    try {
	    $db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("voucher_id", $voucher_id);
		$stmt->bindParam("user_id", $user_id);
		$stmt->bindParam("date", $newdate);
		$stmt->execute();
		$vouchers = $stmt->fetchObject();  
		$count = $stmt->rowCount();
		//print_r($vouchers);exit;
		if(!empty($vouchers))
		{  
			if($vouchers->offer_type_id!=3)
			{
				$sql = "SELECT * FROM swap where swap.offer_id=:offer_id and swap.voucher_id=:voucher_id and swap.user_id=:user_id";
				$stmt = $db->prepare($sql);  
				$stmt->bindParam("voucher_id", $voucher_id);
				$stmt->bindParam("user_id", $user_id);
				$stmt->bindParam("offer_id", $offer_id);
				$stmt->execute();
				$isswap = $stmt->fetchObject();  
				if(empty($isswap))
				{
                                    $arr = array();
                                                
                                    //$arr['buy_price'] = $buy_price;
                                    $arr['save_data']['voucher_status'] = 1;
                                    //$allinfo['save_data'] = $arr;
                                    $voucher_edit = edit(json_encode($arr),'vouchers',$voucher_id);
                                        if(isset($body->typedata))
                                        {
                                            $typedata = $body->typedata;
                                            unset($body->typedata);
                                        }
                                        if(isset($body->areadata))
                                        {
                                            $areadata = $body->areadata;
                                            unset($body->areadata);
                                        }
                                        
					$body->is_active = 1;
					$body->voucher_price = $vouchers->price;
					$body->voucher_expire_date = $vouchers->to_date;
                                        $body->offering_start_date = $body->offering_start_date.' 23:59:59';
                                        $body->offering_end_date = $body->offering_end_date.' 23:59:59';
					$body->posted_on = date('Y-m-d H:i:s');
					$allinfo['save_data'] = $body;
					
					//echo '<pre>';print_r($allinfo);
					$swap = add(json_encode($allinfo),'swap');
					if(!empty($swap)){
                                            $swap = json_decode($swap);
                                            if(!empty($typedata))
                                            {
                                                $temp=array();
                                                foreach($typedata as $typedat)
                                                {
                                                    //print_r($typedata);
                                                    $temp['save_data'] = array('swap_id' => $swap->id,'voucher_id' => $voucher_id,'category_id' => $typedat->id);
                                                    $s = add(json_encode($temp),'swap_category_map');
                                                    //var_dump
                                                }
                                            }
                                            if(!empty($areadata))
                                            {
                                                $temp=array();
                                                foreach($areadata as $typedat)
                                                {
                                                    $temp['save_data'] = array('swap_id' => $swap->id,'voucher_id' => $voucher_id,'location_id' => $typedat->id);
                                                    add(json_encode($temp),'swap_location_map');
                                                }
                                            }
						
						$result = '{"type":"success","message":"Voucher posted for swap successfully."}'; 
					}
					else{
						$result = '{"type":"error","message":"Sorry Voucher could not be posted for swap."}'; 
					}
				}else{
					$result = '{"type":"error","message":"Sorry you have already posted the voucher for swapping."}'; 
				}
			}else{
				$result = '{"type":"error","message":"Sorry Membership Voucher cannot be swapped."}'; 
			}
			
		}else{
			
			$result = '{"type":"error","message":"Sorry No Voucher found!"}'; 
		}
	     
	} catch(PDOException $e) {
		$result = '{"type":"error","message":'. $e->getMessage() .'}';
	}   
	echo  $result;
}

function swaplist() {     
        $is_active = 1;  
	$newdate = date('Y-m-d');
        $sql = "SELECT offers.title, offers.price, offers.offer_percent, offers.offer_to_date, offers.image, swap.id, swap.voucher_id, swap.user_id, swap.offer_id  FROM vouchers, swap, offers where swap.offer_id=offers.id and vouchers.id=swap.voucher_id and offers.offer_to_date >=:date and swap.is_active=1 ORDER BY swap.posted_on DESC";
	
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("date", $newdate);  
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    $todate = date('d M, Y H:i:s', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->offer_to_date = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->expire_date = $todate;
                    if(!empty($vouchers[$i]->image))
                    {
                        $vouchers[$i]->image_url = SITEURL.'voucher_images/'.$vouchers[$i]->image;
                    }
		    
		}
		$db = null;
		$vouchers =  json_encode($vouchers); 
		$result = '{"type":"success","data":'.$vouchers.',"count":'.$count.'}'; 
	} catch(PDOException $e) {
		$result = '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo  $result;
}

function mySwapList($uid) {     
        $is_active = 1;  
	$newdate = date('Y-m-d');
        $sql = "SELECT offers.title, offers.price, offers.offer_percent,vouchers.item_expire_date as offer_to_date, offers.image, swap.id, swap.voucher_id, swap.user_id, swap.offer_id, swap.posted_on, swap.offering_end_date  FROM vouchers, swap, offers where swap.offer_id=offers.id and vouchers.id=swap.voucher_id and swap.is_active=1 and swap.user_id=:uid and swap.is_completed=0";
	
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
        $stmt->bindParam("uid", $uid);		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    $today = date('Y-m-d');
		    $checkdate = date('Y-m-d', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->expire_date = $todate;
			
			if($checkdate<$today)
				{
					$vouchers[$i]->Status = 'Expired';
				}
				else{
			        $vouchers[$i]->Status = '';
			    }
			    
			$posted_on = date('d M, Y', strtotime($vouchers[$i]->posted_on));
		    $vouchers[$i]->posted_on = $posted_on;
                        if($vouchers[$i]->offering_end_date == "0000-00-00 00:00:00"){
                            $vouchers[$i]->offering_end_date = "";
                        }else{
                            $vouchers[$i]->offering_end_date = date('d M, Y', strtotime($vouchers[$i]->offering_end_date));
                        }
                    
			
			if(!empty($vouchers[$i]->image))
			{
				$vouchers[$i]->image_url = SITEURL.'voucher_images/'.$vouchers[$i]->image;
			}
			$rid = $vouchers[$i]->id;
			$sql = "SELECT max(interested_swap.voucher_price) as high_bid FROM interested_swap where interested_swap.swap_id =:rid";
			$db = getConnection();
			$stmt = $db->prepare($sql);  
			$stmt->bindParam("rid", $rid);
			$stmt->execute();
			$swapBid = $stmt->fetchObject();
			//print_r($resalesBid);
			if($swapBid->high_bid!='')
			{
				$vouchers[$i]->high_bid = $swapBid->high_bid;
			}else{
				$vouchers[$i]->high_bid = 0;
			}
                        $netprice = ($vouchers[$i]->price - ($vouchers[$i]->price * $vouchers[$i]->offer_percent)/100);                      
                        
                        
                        $vouchers[$i]->price = number_format($vouchers[$i]->price,1,'.',',');
                        $vouchers[$i]->netprice = number_format($netprice,1,'.',',');
		    
		}
		$db = null;
		$vouchers =  json_encode($vouchers); 
		$result = '{"type":"success","data":'.$vouchers.',"count":'.$count.'}'; 
	} catch(PDOException $e) {
		$result = '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo  $result;
}

function otherSwapList($uid) {  
    
        $is_active = 1;  
	$newdate = date('Y-m-d');
        $sql = "SELECT offers.title, offers.price, offers.offer_percent, vouchers.item_expire_date as offer_to_date, offers.image, swap.id, swap.voucher_id, swap.user_id, swap.offer_id,swap.posted_on,swap.offering_end_date   FROM vouchers, swap, offers where swap.offer_id=offers.id and vouchers.id=swap.voucher_id and swap.is_active=1 and swap.user_id !=:uid and swap.is_completed=0";
	
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
        $stmt->bindParam("uid", $uid);		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
                    $swap_id = $vouchers[$i]->id;
                    $swap_interested = findByConditionArray(array('user_id' => $uid,'swap_id' => $swap_id),'interested_swap');
                    if(empty($swap_interested)){
		    $today = date('Y-m-d');
		    $checkdate = date('Y-m-d', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->expire_date = $todate;
		    if($checkdate<$today)
				{
					$vouchers[$i]->Status = 'Expired';
				}
				else{
			        $vouchers[$i]->Status = '';
			    }
			    
                    if($vouchers[$i]->offering_end_date == "0000-00-00 00:00:00"){
                        $vouchers[$i]->offering_end_date = "";
                    }else{
                        $vouchers[$i]->offering_end_date = date('d M, Y', strtotime($vouchers[$i]->offering_end_date));
                    }
			$vouchers[$i]->posted_on = date('d M,Y',strtotime($vouchers[$i]->posted_on));
                    if(!empty($vouchers[$i]->image))
                    {
                        $vouchers[$i]->image_url = SITEURL.'voucher_images/'.$vouchers[$i]->image;
                    }
                    $vouchers[$i]->price = number_format($vouchers[$i]->price,1,'.',',');
                }else{
                    unset($vouchers[$i]);
                }
		    
		}
		$db = null;
		$vouchers =  json_encode($vouchers); 
		$result = '{"type":"success","data":'.$vouchers.',"count":'.$count.'}'; 
	} catch(PDOException $e) {
		$result = '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo  $result;
}

function myBidSwapList($uid) {     
        $is_active = 1;  
	$newdate = date('Y-m-d');
		
        $sql = "SELECT offers.title, offers.price, offers.offer_percent, vouchers.item_expire_date as offer_to_date, offers.image, swap.id as swap_id, interested_swap.id, interested_swap.voucher_url, interested_swap.voucher_id as my_voucher_id, swap.voucher_id as to_voucher_id, swap.user_id, swap.offer_id,swap.posted_on,swap.voucher_id,swap.offering_end_date  FROM vouchers, swap, offers, interested_swap where swap.offer_id=offers.id and vouchers.id=swap.voucher_id and swap.is_active=1 and swap.id=interested_swap.swap_id and interested_swap.user_id=:uid and swap.is_completed=0";
	
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
        $stmt->bindParam("uid", $uid);		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
                //print_r($vouchers);
                
		$count = $stmt->rowCount();
		//my_voucher.price, swap_voucher.price
		for($i=0;$i<$count;$i++){
		    $today = date('Y-m-d');
		    $checkdate = date('Y-m-d', strtotime($vouchers[$i]->offer_to_date));
		    $todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->expire_date = $todate;
               
               if($checkdate<$today)
				{
					$vouchers[$i]->Status = 'Expired';
				}
				else{
			        $vouchers[$i]->Status = '';
			    }
			         
                    if($vouchers[$i]->offering_end_date == "0000-00-00 00:00:00"){
                        $vouchers[$i]->offering_end_date = "";
                    }else{
                        $vouchers[$i]->offering_end_date = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
                    }
			$vouchers[$i]->posted_on = date('d M, Y',strtotime($vouchers[$i]->posted_on));
			$vouchers[$i]->swap_voucher = findByIdArray($vouchers[$i]->voucher_id,'vouchers');
			$vouchers[$i]->swap_offer = findByIdArray($vouchers[$i]->swap_voucher['offer_id'],'offers');
                        //print_r($vouchers[$i]->swap_voucher);
			$vouchers[$i]->my_voucher = findByIdArray($vouchers[$i]->my_voucher_id,'vouchers');
			$vouchers[$i]->my_offer = findByIdArray($vouchers[$i]->my_voucher['offer_id'],'offers');
                        $vouchers[$i]->swap_voucher['price'] = number_format($vouchers[$i]->swap_voucher['price'],1,'.',',');
                        $vouchers[$i]->my_voucher['price'] = number_format($vouchers[$i]->my_voucher['price'],1,'.',',');
			
                    if(!empty($vouchers[$i]->image))
                    {
                        $vouchers[$i]->image_url = SITEURL.'voucher_images/'.$vouchers[$i]->image;
                    }
		    
		}
                //exit;
		$db = null;
		$vouchers =  json_encode($vouchers); 
		$result = '{"type":"success","data":'.$vouchers.',"count":'.$count.'}'; 
	} catch(PDOException $e) {
		$result = '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo  $result;
}

function interestedSwapList($uid) {     
        $is_active = 1;  
	$newdate = date('Y-m-d');
		
        $sql = "SELECT offers.title,users.first_name, users.last_name, users.email, offers.price, offers.offer_percent, offers.offer_to_date, offers.image, swap.id as swap_id, swap.offering_end_date, interested_swap.id, interested_swap.voucher_url, interested_swap.voucher_id as my_voucher_id, swap.voucher_id as to_voucher_id, swap.user_id, swap.offer_id  FROM vouchers, swap, offers, interested_swap, users where users.id=interested_swap.user_id and interested_swap.voucher_id=vouchers.id and offers.id=vouchers.offer_id and interested_swap.is_active=1 and swap.id=interested_swap.swap_id and interested_swap.swap_id=:uid";
	
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
        $stmt->bindParam("uid", $uid);		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    $todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->expire_date = $todate;
                    if($vouchers[$i]->offering_end_date == "0000-00-00 00:00:00"){
                        $vouchers[$i]->offering_end_date = "";
                    }else{
                        $vouchers[$i]->offering_end_date = date('d M, Y', strtotime($vouchers[$i]->offering_end_date));
                    }
                    $vouchers[$i]->price = number_format($vouchers[$i]->price,1,'.',',');
                    if(!empty($vouchers[$i]->image))
                    {
                        $vouchers[$i]->image_url = SITEURL.'voucher_images/'.$vouchers[$i]->image;
                    }
		    
		}
		$db = null;
		$vouchers =  json_encode($vouchers); 
		$result = '{"type":"success","data":'.$vouchers.',"count":'.$count.'}'; 
	} catch(PDOException $e) {
		$result = '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo  $result;
}

function swapdetails($sid) {     
        $sql = "SELECT vouchers.offer_id, swap.id as sid, swap.voucher_id, swap.offer_id, swap.user_id, swap.offering_start_date, swap.offering_end_date, vouchers.view_id, vouchers.price, vouchers.offer_price, vouchers.offer_percent, vouchers.from_date, vouchers.to_date, vouchers.is_used, vouchers.is_active, offers.title, offers.description, offers.image, offers.benefits, offers.merchant_id FROM vouchers , offers, swap WHERE vouchers.offer_id=offers.id and vouchers.id=swap.voucher_id and swap.id=:sid";
    $offerId ='';
    $offer_image = array();
    $restaurant_details = array();
    $cur_date = date('Y-m-d H:i:s');
    $site_path = SITEURL;
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("sid", $sid);
		$stmt->execute();
		$vouchers = $stmt->fetchObject(); 
		
		//$vouchers->offer_to_date = $offer_to_date;
		if(!empty($vouchers)){
		    $to_date = date('d M,Y', strtotime($vouchers->to_date));
			//$offer_to_date = date('d M,Y', strtotime($vouchers->offer_to_date));
			$vouchers->to_date = $to_date;
                        if($vouchers->offering_start_date == "0000-00-00 00:00:00"){
                            $vouchers->swap_start_date = '';
                        }else{
                            $vouchers->swap_start_date = date('m-d-Y',strtotime($vouchers->offering_start_date));
                        }
                        if($vouchers->offering_end_date == "0000-00-00 00:00:00"){
                            $vouchers->swap_end_date = '';
                            $vouchers->swap_expire_date = '';
                        }else{
                            $vouchers->swap_end_date = date('m-d-Y',strtotime($vouchers->offering_end_date));
                            $vouchers->swap_expire_date = date('M d,Y H:i:s',strtotime($vouchers->offering_end_date));
                        }
                        if($vouchers->offering_end_date >= $cur_date){
                            $offer->swap_expire_date_status =1;
                        }else{
                            $offer->swap_expire_date_status =0;
                        }
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
			$result = '{"type":"error","message":"No Voucher for swapping found"}';
		}

	} catch(PDOException $e) {
		$result =  '{"error":{"message":'. $e->getMessage() .'}}'; 
	}


	echo  $result;
}

function swapCancel(){
	$request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $vid = $body->id;
    $voucher_id = $body->voucher_id;
    $resale_details = array();
    //echo $vid;exit;
    $sql = "SELECT * FROM swap WHERE id=:id";
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
                        $arr = array();
                                                
                        //$arr['buy_price'] = $buy_price;
                        $arr['save_data']['voucher_status'] = 0;
                        //$allinfo['save_data'] = $arr;
                        $voucher_edit = edit(json_encode($arr),'vouchers',$voucher_id);
			//$resale_details = edit(json_encode($allinfo),'swap',$vid);
                        $resale_details=delete('swap',$vid);
			if(!empty($resale_details)){
				$resale_details = json_decode($resale_details);
				$result = '{"type":"success","message":"Your have successfully cancel swap Voucher." }'; 
			}
			else{
				$result = '{"type":"error","message":"Sorry! Please Try Again..."}'; 
			}
	    }
	    else if($resale_count > 0){
		$result = '{"type":"error","message":"Sorry you cannot cancel the swap."}';
	    }
       } catch(PDOException $e) {
	    $result = '{"type":"error","message":'. $e->getMessage() .'}';
	}
	echo  $result;
}

function swapBidCancel(){
	$request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $vid = $body->id;
    //$voucher_id = $body->voucher_id;
    $result1 =  delete('interested_swap',$vid);
    $result = '{"type":"success","message":"Your have successfully cancelled your bid." }';
    
	echo  $result;
}

function updateSwapBid(){
	$request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $vid = $body->id;
	$userid = $body->userid;
    $resale_details = array();
    //echo $vid;exit;
    $sql = "SELECT * FROM interested_swap WHERE id=:id and user_id=:userid";
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
				$voucher_id = $body->voucher_id;
				$body->voucher_url = WEBSITEURL.'voucherdetail/'.$voucher_id;
				
				$db = getConnection();
				$sql = "SELECT * FROM vouchers WHERE vouchers.id=:vid";
				$stmt = $db->prepare($sql);  
				$stmt->bindParam("vid", $voucher_id);
				$stmt->execute();
				$voucher = $stmt->fetchObject();
				 
				$allinfo['save_data']['voucher_url'] = $body->voucher_url;
				$allinfo['save_data']['voucher_id'] = $voucher_id;
				$allinfo['save_data']['subject'] = $body->subject;
				$allinfo['save_data']['comment'] = $body->comment;
				$allinfo['save_data']['voucher_price'] = $voucher->price;
				$allinfo['save_data']['voucher_expire_date'] = $voucher->to_date;
				//$arr['posted_on'] = date('Y-m-d H:i:s');
				
				$resale_details = edit(json_encode($allinfo),'interested_swap',$vid);
				if(!empty($resale_details)){
					$resale_details = json_decode($resale_details);
					$result = '{"type":"success","message":"Your have successfully updated the Bid for swap." }'; 
				}
				else{
					$result = '{"type":"error","message":"Sorry! Please Try Again..."}'; 
				}
			}else{
				$result = '{"type":"error","message":"Sorry you do not have permission to edit the bid for swap."}';
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

function getDetailsSwapBid($vid){
    $sql = "SELECT * FROM interested_swap WHERE interested_swap.id=:id";
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

function swapinterest(){
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $webUrl = WEBSITEURL;
	$voucher_id = $body->voucher_id;
	$user_id = $body->user_id;
	$body->voucher_url = WEBSITEURL.'voucherdetail/'.$voucher_id;
	$sid = $body->swap_id;
	
	$sql = "SELECT * from users where users.id=:userid";
	$db = getConnection();
	$stmt = $db->prepare($sql);  
	$stmt->bindParam("userid", $user_id);
	$stmt->execute();
	$from_user = $stmt->fetchObject();
	if(!empty($from_user))
	{
		//echo '<pre>';print_r($from_user);exit;
		$sql = "SELECT * FROM voucher_owner WHERE voucher_owner.to_user_id=:userid and voucher_owner.voucher_id=:voucher_id and voucher_owner.is_active=1";
		
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("voucher_id", $voucher_id);
		$stmt->bindParam("userid", $user_id);
		$stmt->execute();
		$voucherowner = $stmt->fetchObject();
		if(!empty($voucherowner))
		{
			
			$sql = "SELECT * FROM voucher_resales WHERE voucher_resales.user_id=:userid and voucher_resales.voucher_id=:voucher_id and voucher_resales.is_sold=0";
		
			$stmt = $db->prepare($sql);  
			$stmt->bindParam("voucher_id", $voucher_id);
			$stmt->bindParam("userid", $user_id);
			$stmt->execute();
			$voucherresale = $stmt->fetchObject();
			if(empty($voucherresale))
			{
				
				$sql = "SELECT * FROM swap WHERE swap.id=:sid";
				$stmt = $db->prepare($sql);  
				$stmt->bindParam("sid", $sid);
				$stmt->execute();
				$swapdetail = $stmt->fetchObject();
				if(!empty($swapdetail))
				{
					$sql = "SELECT * FROM users WHERE users.id=:uid";
					$stmt = $db->prepare($sql);  
					$stmt->bindParam("uid", $swapdetail->user_id);
					$stmt->execute();
					$to_user = $stmt->fetchObject();
					if($to_user->id!=$user_id)
					{
						$sql = "SELECT * FROM vouchers WHERE vouchers.id=:vid";
						$stmt = $db->prepare($sql);  
						$stmt->bindParam("vid", $voucher_id);
						$stmt->execute();
						$voucher = $stmt->fetchObject();
						
						$arr = array();
						$arr['user_id'] = $user_id;
						$arr['swap_id'] = $body->swap_id;
						$arr['date'] = date('Y-m-d h:i:s');
						$arr['voucher_url'] = $body->voucher_url;
						$arr['voucher_id'] = $voucher_id;
						$arr['is_active'] = 1;
						$arr['subject'] = $body->subject;
						$arr['comment'] = $body->comment;
						$arr['voucher_price'] = $voucher->price;
						$arr['voucher_expire_date'] = $voucher->to_date;
						$arr['posted_on'] = date('Y-m-d H:i:s');
						
						$allinfo['save_data'] = $arr;
						$interestedSwapDetails = add(json_encode($allinfo),'interested_swap');
						if($interestedSwapDetails){
							$new = json_decode($interestedSwapDetails);
							//echo '<pre>';print_r($new);exit;
								$offer_detail = findByIdArray( $voucher->offer_id,'offers');
							     $vname = $offer_detail['title'];
								$viewid = 'MFG-000000000'.$voucher_id;
								$startdate = date('d M, Y',strtotime($voucher->item_start_date));
								$enddate = date('d M, Y',strtotime($voucher->item_expire_date));
								$price = $voucher->price;
								
								$svoucher = findByIdArray( $swapdetail->voucher_id,'vouchers');
							     $sviewid = 'MFG-000000000'.$svoucher['id'];
								$sstartdate = date('d M, Y',strtotime($svoucher['item_start_date']));
								$senddate = date('d M, Y',strtotime($svoucher['item_expire_date']));
								$sprice = $svoucher['price'];
								
								$swapoffer_detail = findByIdArray( $swapdetail->offer_id,'offers');
							     $svname = $swapoffer_detail['title'];
																
								$from = ADMINEMAIL;
								//$to = $saveresales->email;
								$to = $to_user->email;  //'nits.ananya15@gmail.com';
								$subject ='Interested to Swap Voucher';
								$body ='<html><body><p>Dear '.$to_user->first_name.' '.$to_user->last_name.',</p>

									<p>'.$from_user->first_name.' '.$from_user->last_name. ' wanted to swap voucher<br />
									<span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Voucher Id :</span>'.$viewid.'<br />
								    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Voucher Name :</span>'.$vname.'<br />
								    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Voucher Start Date :</span>'.$startdate.'<br />
								    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Voucher End Date :</span>'.$enddate.'<br />
								    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Voucher price :</span>'.$price.'<br /><br />
								    
								    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Swap With:<br />
									<span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Voucher Id :</span>'.$sviewid.'<br />
								    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Voucher Name :</span>'.$svname.'<br />
								    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Voucher Start Date :</span>'.$sstartdate.'<br />
								    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Voucher End Date :</span>'.$senddate.'<br />
								    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Voucher price :</span>'.$sprice.'<br /><br />
								    
									
									If you want to swap with the voucher given by '.$from_user->first_name.' '.$from_user->last_name. ' then please Login first to the site and then click the Accept button below
									<br />
									<a href="'.WEBSITEURL.'acceptswap/'.$new->id.'" target="_blank" >ACCEPT</a>
									<span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">If we can help you with anything in the meantime just let us know by e-mailing&nbsp;</span>'.$from.'<br />
									<span style="color:rgb(34, 34, 34); font-family:arial,sans-serif"></span><span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">!&nbsp;</span></p>

									<p>Thanks,<br />
									mFood&nbsp;Team</p>

									<p>&nbsp;</p></body></html>';
									//echo $to.'|'.$subject.'|'.$body;
								sendMail($to,$subject,$body);
								$result = '{"type":"success","message":"You have successfully requested to Swap the voucher." }';
							
						}
					}else{
						$result = '{"type":"error","message":"Sorry. You can show interest on the voucher that you have posted for swap."}';
					}
					
				}else{
					$result = '{"type":"error","message":"Sorry. No such Voucher found for swapping."}';
				}
				
				
			}
			else{
				$result = '{"type":"error","message":"Sorry. Voucher cannot be used to swap as you have already put it for resale."}';
			}
			
		}else{
			$result = '{"type":"error","message":"Sorry. Voucher not found."}'; 
		}
	}else{
		$result = '{"type":"error","message":"Sorry. Please login first."}'; 
	}   
	echo  $result;
}
function swapInterestAccept($siid){
        $sql = "SELECT * from interested_swap where id=:siid";
	$db = getConnection();
	$stmt = $db->prepare($sql);  
	$stmt->bindParam("siid", $siid);
	$stmt->execute();
	$siid_details = $stmt->fetchObject();
	if(!empty($siid_details)){
	        $first_voucher_id = $siid_details->voucher_id;
	        $second_voucher_id = '';
	        $first_user_id = '';
	        $second_user_id = $siid_details->user_id;
	        $swap_id = $siid_details->swap_id;
                
	        
	        $sql = "SELECT * from swap where id=:sid";
	        $db = getConnection();
	        $stmt = $db->prepare($sql);  
	        $stmt->bindParam("sid", $swap_id);
	        $stmt->execute();
	        $swap_details = $stmt->fetchObject();
	        $second_voucher_id = $swap_details->voucher_id;	        
	        $first_user_id = $swap_details->user_id;
                
                $firstUserInfo = findByConditionArray(array('id' => $first_user_id),'users');
                $secondUserInfo = findByConditionArray(array('id' => $second_user_id),'users');
                $firstUseremail = $firstUserInfo[0]['email'];
                $secondUseremail = $secondUserInfo[0]['email'];
                
                $firstUsername = $firstUserInfo[0]['first_name'].' '.$firstUserInfo[0]['last_name'];
                $secondUsername = $firstUserInfo[0]['first_name'].' '.$firstUserInfo[0]['last_name'];
	        
	        $sql = "SELECT * from voucher_owner where to_user_id=:user_id and voucher_id=:voucher_id and is_active=1";
	        $db = getConnection();
	        $stmt = $db->prepare($sql);  
	        $stmt->bindParam("user_id", $first_user_id);
	        $stmt->bindParam("voucher_id", $second_voucher_id);
	        $stmt->execute();
	        $owner_details = $stmt->fetchObject();
	        if(!empty($owner_details)){
	                $owner_id = $owner_details->id;
	                $data = array();
			$data['voucher_id'] = $owner_details->voucher_id;
			$data['offer_id'] = $owner_details->offer_id;
			//$data['voucher_view_id'] = $viewid;
			$data['from_user_id'] = $first_user_id;
			$data['to_user_id'] = $second_user_id;
			$data['price'] = $owner_details->price;
			$data['offer_price'] = $owner_details->offer_price;
			$data['offer_percent'] = $owner_details->offer_percent;
			$data['is_active'] = '1';
			$data['buy_price'] = $owner_details->buy_price;
			$data['purchased_date'] = date('Y-m-d h:i:s');
			$newinfo['save_data'] = $data;
			$new_owner_details  = add(json_encode($newinfo),'voucher_owner');
			
			$arr2 = array();
			$arr2['is_active'] = '0';
			$editinfo['save_data'] = $arr2;
			$voucher_owner_edit = edit(json_encode($editinfo),'voucher_owner',$owner_id);
	        }
	        
	        $sql = "SELECT * from voucher_owner where to_user_id=:user_id and voucher_id=:voucher_id and is_active=1";
	        $db = getConnection();
	        $stmt = $db->prepare($sql);  
	        $stmt->bindParam("user_id", $second_user_id);
	        $stmt->bindParam("voucher_id", $first_voucher_id);
	        $stmt->execute();
	        $second_owner_details = $stmt->fetchObject();
	        if(!empty($second_owner_details)){
	                $second_owner_id = $second_owner_details->id;
	                $data = array();
			$data['voucher_id'] = $second_owner_details->voucher_id;
			$data['offer_id'] = $second_owner_details->offer_id;
			//$data['voucher_view_id'] = $viewid;
			$data['from_user_id'] = $second_user_id;
			$data['to_user_id'] = $first_user_id;
			$data['price'] = $second_owner_details->price;
			$data['offer_price'] = $second_owner_details->offer_price;
			$data['offer_percent'] = $second_owner_details->offer_percent;
			$data['is_active'] = '1';
			$data['buy_price'] = $second_owner_details->buy_price;
			$data['purchased_date'] = date('Y-m-d h:i:s');
			$newinfo = array();
			$newinfo['save_data'] = $data;
			$new_owner_details  = add(json_encode($newinfo),'voucher_owner');
			
			$arr2 = array();
			$arr2['is_active'] = '0';
			$editinfo = array();
			$editinfo['save_data'] = $arr2;
			$voucher_owner_edit = edit(json_encode($editinfo),'voucher_owner',$second_owner_id);
                        $from = ADMINEMAIL;
								//$to = $saveresales->email;
                        $to = $firstUseremail;  //'nits.ananya15@gmail.com';
                        $subject ='Successfully Swap';
                        $body ='<html><body><p>Dear '.$firstUsername.',</p>

                                <p>You have successfully swaped you voucher with the voucher of '.$secondUsername.'<br />
                                
                                <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">If we can help you with anything in the meantime just let us know by e-mailing&nbsp;</span>'.$from.'<br />
                                <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif"></span><span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">!&nbsp;</span></p>

                                <p>Thanks,<br />
                                mFood&nbsp;Team</p>

                                <p>&nbsp;</p></body></html>';
                                //echo $to.'|'.$subject.'|'.$body;
                        sendMail($to,$subject,$body);
                        
                        $to1 = $secondUseremail;  //'nits.ananya15@gmail.com';
                        $subject1 ='Successfully Swap';
                        $body1 ='<html><body><p>Dear '.$secondUsername.',</p>

                                <p>You have successfully swaped you voucher with the voucher of '.$firstUsername.'<br />
                                
                                <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">If we can help you with anything in the meantime just let us know by e-mailing&nbsp;</span>'.$from.'<br />
                                <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif"></span><span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">!&nbsp;</span></p>

                                <p>Thanks,<br />
                                mFood&nbsp;Team</p>

                                <p>&nbsp;</p></body></html>';
                                //echo $to.'|'.$subject.'|'.$body;
                        sendMail($to1,$subject1,$body1);
	        }
	        if(!empty($owner_details) && !empty($second_owner_details)){
                        $arr3 = array();
			$arr3['is_completed'] = 1;
			$editswapinfo['save_data'] = $arr3;
			$voucher_swap_edit = edit(json_encode($editswapinfo),'swap',$swap_id);
	                $result = '{"type":"success","message":"Swap successfully done." }';
	        }
	        echo  $result;
	        
	}
	//echo $first_voucher_id.'|'.$second_voucher_id.'|'.$first_user_id.'|'.$second_user_id.'|';

}

function getSwapVoucherDetail($sid) {  
    
        $is_active = 1;  
	$newdate = date('Y-m-d');
        $sql = "SELECT offers.title, offers.price, offers.offer_percent, vouchers.item_expire_date as offer_to_date, offers.image, swap.id, swap.voucher_id, swap.user_id, swap.offer_id,swap.posted_on,swap.offering_end_date,swap.offering_start_date,swap.description  FROM vouchers, swap, offers where swap.offer_id=offers.id and vouchers.id=swap.voucher_id and swap.is_active=1 and swap.id =:sid";
	
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
                $stmt->bindParam("sid", $sid);		
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    $todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->expire_date = $todate;
                    if($vouchers[$i]->offering_end_date == "0000-00-00 00:00:00"){
                        $vouchers[$i]->offering_end_date = "";
                    }else{
                        $vouchers[$i]->offering_end_date = date('d M, Y', strtotime($vouchers[$i]->offering_end_date));
                    }
                    if($vouchers[$i]->offering_start_date == "0000-00-00 00:00:00"){
                        $vouchers[$i]->offering_start_date = "";
                    }else{
                        $vouchers[$i]->offering_start_date = date('d M, Y', strtotime($vouchers[$i]->offering_start_date));
                    }
			$vouchers[$i]->posted_on = date('d M,Y',strtotime($vouchers[$i]->posted_on));
                    if(!empty($vouchers[$i]->image))
                    {
                        $vouchers[$i]->image_url = SITEURL.'voucher_images/'.$vouchers[$i]->image;
                    }                    
                    $netprice = ($vouchers[$i]->price-($vouchers[$i]->price*$vouchers[$i]->offer_percent)/100);
                    $vouchers[$i]->price = number_format($vouchers[$i]->price,1,'.',',');
                    $vouchers[$i]->netprice = number_format($netprice,1,'.',',');
                    //$vouchers[$i]->categories = json_decode(findByCondition(array('swap_id'=>$vouchers[$i]->id),'swap_category_map'));
                    //$vouchers[$i]->locations = json_decode(findByCondition(array('swap_id'=>$vouchers[$i]->id),'swap_location_map'));
                    $vouchers[$i]->locations = array();
                    $locations = findByConditionArray(array('swap_id' => $sid),'swap_location_map');
                    foreach($locations as $cat)
                    {
                        $vouchers[$i]->locations[] = findByIdArray($cat['location_id'],'locations');
                    }

                    $vouchers[$i]->categories = array();
                    $category = findByConditionArray(array('swap_id' => $sid),'swap_category_map');
                    foreach($category as $cat)
                    {
                        $vouchers[$i]->categories[] = findByIdArray($cat['category_id'],'category');
                    }
                    
		    
		}
		$db = null;
		$vouchers =  json_encode($vouchers); 
		$result = '{"type":"success","data":'.$vouchers.',"count":'.$count.'}'; 
	} catch(PDOException $e) {
		$result = '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo  $result;
}
?>

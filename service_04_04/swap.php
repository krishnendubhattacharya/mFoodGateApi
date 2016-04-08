<?php
function addSwap(){
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $voucher_id = $body->voucher_id;
	$offer_id = $body->offer_id;
	$user_id = $body->user_id;
	$newdate = date('Y-m-d');
    
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
					$body->is_active = 1;
					$allinfo['save_data'] = $body;
					//echo '<pre>';print_r($allinfo);
					$swap = add(json_encode($allinfo),'swap');
					if(!empty($swap)){
						$swap = json_decode($swap);
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
        $sql = "SELECT offers.title, offers.price, offers.offer_percent, offers.offer_to_date, swap.id, swap.voucher_id, swap.user_id, swap.offer_id  FROM vouchers, swap, offers where swap.offer_id=offers.id and vouchers.id=swap.voucher_id and swap.is_active=1";
	
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->execute();
		$vouchers = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$count = $stmt->rowCount();
		
		for($i=0;$i<$count;$i++){
		    $todate = date('d M, Y', strtotime($vouchers[$i]->offer_to_date));
		    $vouchers[$i]->expire_date = $todate;
		    
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
        $sql = "SELECT vouchers.offer_id, swap.id as sid, swap.voucher_id, swap.offer_id, swap.user_id, vouchers.view_id, vouchers.price, vouchers.offer_price, vouchers.offer_percent, vouchers.from_date, vouchers.to_date, vouchers.is_used, vouchers.is_active, offers.title, offers.description, offers.image, offers.benefits, offers.merchant_id FROM vouchers , offers, swap WHERE vouchers.offer_id=offers.id and vouchers.id=swap.voucher_id and swap.id=:sid";
    $offerId ='';
    $offer_image = array();
    $restaurant_details = array();
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

function swapinterest(){
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    
	$voucher_url = $body->voucher_url;
	$user_id = $body->user_id;
	$url = explode('/',$voucher_url);
	$countUrl = count($url);
	$voucher_id = $url[$countUrl-1]; 
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
						$arr = array();
						$arr['user_id'] = $user_id;
						$arr['swap_id'] = $body->swap_id;
						$arr['date'] = date('Y-m-d h:i:s');
						$arr['voucher_url'] = $body->voucher_url;
						$arr['voucher_id'] = $voucher_id;
						$arr['is_active'] = 1;
						$arr['subject'] = $body->subject;
						$arr['comment'] = $body->comment;
						$allinfo['save_data'] = $arr;
						$interestedSwapDetails = add(json_encode($allinfo),'interested_swap');
						if($interestedSwapDetails){
							$new = json_decode($interestedSwapDetails);
							//echo '<pre>';print_r($new);exit;
								$from = ADMINEMAIL;
								//$to = $saveresales->email;
								$to = $to_user->email;  //'nits.ananya15@gmail.com';
								$subject ='Interested to Swap Voucher';
								$body ='<html><body><p>Dear '.$to_user->first_name.' '.$to_user->last_name.',</p>

									<p>'.$from_user->first_name.' '.$from_user->last_name. ' wanted to swap voucher<br />
									
									<span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Voucher Url want to swap with :</span><a href="'.$new->voucher_url.'" target="_blank">'.$new->voucher_url.'</a><br />
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
	        }
	        if(!empty($owner_details) && !empty($second_owner_details)){
	                $result = '{"type":"success","message":"Swap successfully done." }';
	        }
	        echo  $result;
	        
	}
	//echo $first_voucher_id.'|'.$second_voucher_id.'|'.$first_user_id.'|'.$second_user_id.'|';

}
?>
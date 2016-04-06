<?php
function getAllCoupons() {
        $result = getAll('coupons');
	echo $result;
}
function getCoupon($id) {
        $result = findById($id,'coupons');
        echo $result;	
}
function addCoupon() {	
	
	$request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	if(isset($body->id)){
	        unset($body->id);
	}

	   $allinfo['save_data'] = $body;
	    //$allinfo['unique_data'] = $unique_field;
	  $coupon_details  = add(json_encode($allinfo),'coupons');
	  if(!empty($coupon_details)){
	    /* $user_details = add(json_encode($allinfo),'coupons');
	    $user_details = json_decode($user_details);*/
	
	    $result = '{"type":"success","message":"Added Succesfully"}'; 
	  }
	  else{
	       $result = '{"type":"error","message":"Not Added"}'; 
	  }
	echo $result;
	
}

function updateCoupon($id) {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$coupon = json_decode($body);
	if(isset($coupon->id)){
	        unset($coupon->id);
	}	
	$allinfo['save_data'] = $coupon;
	$coupon_details = edit(json_encode($allinfo),'coupons',$id);
	if(!empty($coupon_details)){
	    $result = '{"type":"success","message":"Changed Succesfully"}'; 
	}
	else{
	    $result = '{"type":"error","message":"Not Changed"}'; 
	}
	echo $result;
	
}

function deleteCoupon($id) {
       $result =  delete('coupons',$id);
       echo $result;	
}
?>

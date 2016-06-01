<?php
function addMerchantMenuCategory(){
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $allinfo['save_data'] = $body;
    //print_r($allinfo);

    //$allinfo['unique_data'] = $unique_field;
    $cat_details  = add(json_encode($allinfo),'merchant_menu_categories');
    //echo $cat_details;
    //exit;
    if(!empty($cat_details)){	
        $result = '{"type":"success","message":"Added Succesfully","data":'.$cat_details.'}'; 
    }
    else{
        $result = '{"type":"error","message":"Not Added"}'; 
    }
    echo $result;
    exit;
}

function getMerchantMenuCategory($user_id)
{
    $rarray = array();
    $result = findByConditionArray(array('user_id' => $user_id),'merchant_menu_categories');
    if(!empty($result))
    {
        $rarray = array('type' => 'success','data' => $result);
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'No category found.','data' => array());
    }
    echo json_encode($rarray);
}

function updateMerchantMenuCategory() {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$coupon = json_decode($body);
        $id = $coupon->id;
	if(isset($coupon->id)){
	        unset($coupon->id);
	}	
       
	$allinfo['save_data'] = $coupon;
	$coupon_details = edit(json_encode($allinfo),'merchant_menu_categories',$id);
	if(!empty($coupon_details)){
	    $result = '{"type":"success","message":"Changed Succesfully"}'; 
	}
	else{
	    $result = '{"type":"error","message":"Not Changed"}'; 
	}
	echo $result;
	
}

/**/
function deleteMerchantMenuCategory($id) {
       $result =  delete('merchant_menu_categories',$id);
       echo $result;	
}
?>
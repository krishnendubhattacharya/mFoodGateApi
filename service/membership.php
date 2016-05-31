<?php
function getMembershipByMerchant($id)
{
    $condition = array('merchant_id' => $id);
    $details = findByConditionArray($condition,'membership_map');
    $details = array_map(function($t){
        $t['promo'] = findByIdArray($t['promo_id'],'offers');
        $t['start_date'] = date('d M, Y',strtotime($t['start_date']));
        $t['expire_date'] = date('d M, Y',strtotime($t['expire_date']));
        return $t;
    },$details);
    if(!empty($details))
    {
        $rarray = array('type' => 'success', 'data' => $details);
    }
    else
    {
        $rarray = array('type' => 'error', 'data' => $details);
    }
    echo json_encode($rarray);
}

/*
 * membership_name
 * membership_id
 * merchant_id
 * promo_id
 * start_date
 * expire_date
 * created_on(Default current date)
 * 
 */
function addNewMembership()
{
    $rarray = array();   
    if(!empty($_POST))
    {
        if(!empty($_POST['membership_name']) && !empty($_POST['membership_id']) && !empty($_POST['merchant_id']) && !empty($_POST['promo_id']) && !empty($_POST['start_date']) && !empty($_POST['expire_date']) && !empty($_POST['email']))
        {
            $membership_name = $_POST['membership_name'];
            $membership_id = $_POST['membership_id'];
            $merchant_id = $_POST['merchant_id'];
            $promo_id = $_POST['promo_id'];
            $start_date = $_POST['start_date'];
            $expire_date = $_POST['expire_date'];
            $email = $_POST['email'];
            $sql = "select * from members where email='$email'";
            $member_details = findByQuery($sql,'one');
            if(!empty($member_details))
            {
                $upinfo = array();
                $upinfo['save_data']['membership_name'] = $membership_name;
                $upinfo['save_data']['membership_id'] = $membership_id;
                $upinfo['save_data']['merchant_id'] = $merchant_id;
                $upinfo['save_data']['member_id'] = $member_details['id'];
                $upinfo['save_data']['promo_id'] = $promo_id;
                $upinfo['save_data']['start_date'] = date('Y-m-d H:i:s',strtotime($start_date));
                $upinfo['save_data']['expire_date'] = date('Y-m-d H:i:s',strtotime($expire_date));
                $upinfo['save_data']['created_on'] = date('Y-m-d H:i:s');
                $s = add(json_encode($upinfo),'membership_map');
                $rarray = array('status' => 'success', 'message' => 'Membership updated successfully','data' => json_decode($s));
            }
            else
            {
                $member_data = array();
                $member_data['save_data']['email'] = $email;
                $add_det = add(json_encode($member_data),'members');
                $add_det = json_decode($add_det);
                if(!empty($add_det->id))
                {
                   $allinfo = array();
                   $allinfo['save_data']['membership_name'] = $membership_name;
                   $allinfo['save_data']['membership_id'] = $membership_id;
                   $allinfo['save_data']['merchant_id'] = $merchant_id;
                   $allinfo['save_data']['promo_id'] = $promo_id;
                   $allinfo['save_data']['start_date'] = date('Y-m-d H:i:s',strtotime($start_date));
                   $allinfo['save_data']['expire_date'] = date('Y-m-d H:i:s',strtotime($expire_date));
                   $allinfo['save_data']['created_on'] = date('Y-m-d H:i:s');
                   $allinfo['save_data']['member_id'] = $add_det->id;
                   $s = add(json_encode($allinfo),'membership_map');
                   $rarray = array('status' => 'success', 'message' => 'Membership saved successfully','data'=>json_decode($s));
                }
                else
                {
                    $rarray = array('status' => 'error', 'message' => 'Internal error. Please try again later.');
                }
            }
            /*$conditions = array('membership_id' => $membership_id, 'merchant_id' => $merchant_id);  
            $sql = "SELECT * FROM membership_map where membership_id='$membership_id' and merchant_id='$merchant_id' and promo_id='$promo_id'";
            $exist = findByQuery($sql,'one');
            
            
            if(!empty($exist))
            {
                $upinfo = array();
                $upinfo['save_data']['membership_name'] = $membership_name;
                $upinfo['save_data']['membership_id'] = $membership_id;
                $upinfo['save_data']['merchant_id'] = $merchant_id;
                $upinfo['save_data']['promo_id'] = $promo_id;
                $upinfo['save_data']['start_date'] = date('Y-m-d H:i:s',strtotime($start_date));
                $upinfo['save_data']['expire_date'] = date('Y-m-d H:i:s',strtotime($expire_date));
                $upinfo['save_data']['created_on'] = date('Y-m-d H:i:s');
                $s = edit(json_encode($upinfo),'membership_map',$exist['id']);
                $rarray = array('status' => 'success', 'message' => 'Membership updated successfully','data' => json_decode($s));
            }
            else
            {*/
               
            //}
        }
        else
        {
            $rarray = array('status' => 'error', 'message' => 'Requered fields not found');
        }        
    }
    else
    {
        $rarray = array('status' => 'error', 'message' => 'No data posted.');
    }
    echo json_encode($rarray);
}

function deleteMembership($id)
{
    $result =  delete('membership_map',$id);
    echo $result;	
}

function addMerchantMembership()
{
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $body->purchased_date = date('Y-m-d H:i:s');
    
    $allinfo['save_data'] = $body;
    //print_r($allinfo);

    //$allinfo['unique_data'] = $unique_field;
    $cat_details  = add(json_encode($allinfo),'memberships');
    //echo $cat_details;
    //exit;
    if(!empty($cat_details)){	
        $result = '{"type":"success","message":"Added Succesfully","data":'.  $cat_details.'}'; 
      }
      else{
           $result = '{"type":"error","message":"Not Added"}'; 
      }
      echo $result;
      exit;
}

function getMerchantMembership($id)
{
    $rarray = array();
    $details = findByConditionArray(array('user_id' => $id),'memberships');
    if(!empty($details))
    {
        $details = array_map(function($t){
            $t['promo'] = findByIdArray($t['promo_id'],'offers');
            return $t;
        },$details);
        $rarray = array('type' => 'success', 'data' => $details);
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'No membership found.', 'data' => array());
    }
    echo json_encode($rarray);
    exit;
}

function updateMerchantMembership() {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$coupon = json_decode($body);
        $id = $coupon->id;
	if(isset($coupon->id)){
	        unset($coupon->id);
	}
	$allinfo['save_data'] = $coupon;
	$coupon_details = edit(json_encode($allinfo),'memberships',$id);
	if(!empty($coupon_details)){
	    $result = '{"type":"success","message":"Changed Succesfully"}'; 
	}
	else{
	    $result = '{"type":"error","message":"Not Changed"}'; 
	}
	echo $result;
	
}

function deleteMerchantMembership($id) { 
       $result =  delete('memberships',$id);
       echo $result;	
}
?>
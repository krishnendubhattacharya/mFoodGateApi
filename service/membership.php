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

function getMerResDetail($mid,$rid,$uid,$vid) { 
       $restaurant_details = findByIdArray($rid,'restaurants');
       $merchant_details = findByIdArray($mid,'users');
       $restaurant_id = $restaurant_details['restaurant_id'];
       $merchant_id = $merchant_details['merchant_id'];
       $member_query = "SELECT * FROM `member_user_map` WHERE merchant_id='".$merchant_id."' and user_id=$uid and  voucher_id=$vid";
        $memberIdDetails = findByQuery($member_query);
        //print_r($memberIdDetails);
        if(!empty($memberIdDetails)){
                if(!empty($memberIdDetails[0]['member_id'])){
                        $member_membership_id = $memberIdDetails[0]['member_id'];
                }else{
                        $member_membership_id = '';
                }
        }else{
                $member_membership_id = '';
        }
       $result = array('type' => 'success', 'restaurant_id' => $restaurant_id, 'merchant_id' => $merchant_id, 'member_id' => $member_membership_id);
       echo json_encode($result);	
}

function addMemberUser()
{
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $body->created_dete = date('Y-m-d H:i:s'); 
       
    $unique_field = array();
        $unique_field['email']=$body->email;
        //$unique_field['username']=$user->username;
        $rowCount = rowCount(json_encode($unique_field),'users');
        if($rowCount == 0){
                
                $from = ADMINEMAIL;
						    //$to = $saveresales->email;
	    $to = $body->email;  //'nits.ananya15@gmail.com';
	    $subject ='Register in mFoodGate';
	    $bodyy ='<html><body><p>Dear '.$body->name.',</p><br />		    

			<span style="color:rgb(77, 76, 76); font-family:helvetica,arial">You need to register, please <a href="'.WEBSITEURL.'register/'.$body->email.'">Click Here</a>  </span><span style="color:rgb(77, 76, 76); font-family:helvetica,arial">&nbsp;to verify your account.</span><br />
		    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">If we can help you with anything in the meantime just let us know by e-mailing&nbsp;</span>'.$from.'<br />

		    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif"></span><span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">!&nbsp;</span></p>


		    <p>Thanks,<br />
		    mFood&nbsp;Team</p>



		    <p>&nbsp;</p></body></html>';
		    //print_r($body);
                //exit;
                $offercondition = array('visible_id' => $body->offer_visible_id);
            $offer_details = findByConditionArray($offercondition,'offers');
            if(!empty($offer_details)) {
	    sendMail($to,$subject,$bodyy);
	    //$body->offer_id = $body->offer_visible_id;
	    $body->membership_start_date = date('Y-m-d h:i:s', strtotime($body->member_start_date));
	    $body->membership_end_date = date('Y-m-d h:i:s', strtotime($body->member_end_date));
	    //unset($body->offer_visible_id);
	    unset($body->member_start_date);
	    unset($body->member_end_date);
	    
            
            $voucher_data = array();
            $voucher_data['offer_id'] =$offer_details[0]['id'];
            $voucher_data['created_on'] = $body->membership_start_date;
            $voucher_data['price'] = $offer_details[0]['price'];
            $voucher_data['offer_price'] = $offer_details[0]['offer_price'];
            $voucher_data['offer_percent'] = $offer_details[0]['offer_percent'];
            $voucher_data['from_date'] = $body->membership_start_date;
            $voucher_data['to_date'] = $body->membership_end_date;
            $voucher_data['is_active'] = 1;
            $s = add(json_encode(array('save_data' => $voucher_data)),'vouchers');
                $s = json_decode($s);
   //print_r($s);  exit;       
            $owner_data = array();
            $owner_data['offer_id'] = $offer_details[0]['id'];
            $owner_data['voucher_id'] = $s->id;
            $owner_data['from_user_id'] = 0;
            //$owner_data['to_user_id'] = $order_detail->user_id;
            $owner_data['purchased_date'] = $body->membership_start_date;
            $owner_data['is_active'] = 1;
            $owner_data['price'] = $offer_details[0]['price'];
            $owner_data['offer_price'] = $offer_details[0]['offer_price'];
            $owner_data['offer_percent'] = $offer_details[0]['offer_percent'];
            $owner_data['buy_price'] = $offer_details[0]['offer_price'];
            add(json_encode(array('save_data' => $owner_data)),'voucher_owner');
            
            $body->offer_id = $offer_details[0]['id'];
            $body->voucher_id = $s->id;
            unset($body->offer_visible_id);
            
	    $allinfo['save_data'] = $body;
	    
                $save_details  = add(json_encode($allinfo),'member_user_map');
                $result = '{"type":"success","message":"Added succesfully and mail sent to the user"}'; 
                }else{
                        $result = '{"type":"failure","message":"offer_visible_id is not exist."}'; 
                }
        }else{
        
    //exit;
                $condition = array('email' => $body->email);
                $user_details = findByConditionArray($condition,'users');
                //print_r($user_details);
                
                $user_id = $user_details[0]['id'];
                $body->user_id = $user_id;
                //print_r($body);
                //exit;
                $body->membership_start_date = date('Y-m-d h:i:s', strtotime($body->member_start_date));
	    $body->membership_end_date = date('Y-m-d h:i:s', strtotime($body->member_end_date));
	    //unset($body->offer_visible_id);
	    unset($body->member_start_date);
	    unset($body->member_end_date);
	    $offercondition = array('visible_id' => $body->offer_visible_id);
            $offer_details = findByConditionArray($offercondition,'offers');
            //print_r($body);
            //print_r($offer_details);
            //exit;
            if(!empty($offer_details)) {
            $voucher_data = array();
            $voucher_data['offer_id'] =$offer_details[0]['id'];
            $voucher_data['created_on'] = $body->membership_start_date;
            $voucher_data['price'] = $offer_details[0]['price'];
            $voucher_data['offer_price'] = $offer_details[0]['offer_price'];
            $voucher_data['offer_percent'] = $offer_details[0]['offer_percent'];
            $voucher_data['from_date'] = $body->membership_start_date;
            $voucher_data['to_date'] = $body->membership_end_date;
            $voucher_data['is_active'] = 1;
            $s = add(json_encode(array('save_data' => $voucher_data)),'vouchers');
                $s = json_decode($s);
   //print_r($s);  exit;       
            $owner_data = array();
            $owner_data['offer_id'] = $offer_details[0]['id'];
            $owner_data['voucher_id'] = $s->id;
            $owner_data['from_user_id'] = 0;
            $owner_data['to_user_id'] = $user_id;
            $owner_data['purchased_date'] = $body->membership_start_date;
            $owner_data['is_active'] = 1;
            $owner_data['price'] = $offer_details[0]['price'];
            $owner_data['offer_price'] = $offer_details[0]['offer_price'];
            $owner_data['offer_percent'] = $offer_details[0]['offer_percent'];
            $owner_data['buy_price'] = $offer_details[0]['offer_price'];
            add(json_encode(array('save_data' => $owner_data)),'voucher_owner');
            
            $body->offer_id = $offer_details[0]['id'];
            $body->voucher_id = $s->id;
            unset($body->offer_visible_id);
            //print_r($body);
            //exit;
                $allinfo['save_data'] = $body;
                $save_details  = add(json_encode($allinfo),'member_user_map');
                $result = '{"type":"success","message":"Added succesfully"}'; 
                }
                else{
                        $result = '{"type":"failure","message":"offer_visible_id is not exist."}'; 
                }
        }
      echo $result;
      exit;
}

function saveMembershipMemberMap()
{
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $body->created_dete = date('Y-m-d H:i:s');
                
        $allinfo['save_data'] = $body;

        $save_details  = add(json_encode($allinfo),'member_user_map');
        $result = '{"type":"success","message":"Added Succesfully"}'; 
        
      echo $result;
      exit;
}

?>

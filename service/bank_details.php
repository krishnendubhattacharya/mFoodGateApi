<?php 
    function addBankDetails()
    {
        $request = Slim::getInstance()->request();
        $body = json_decode($request->getBody());

        $allinfo['save_data'] = $body;

        //$allinfo['unique_data'] = $unique_field;
        $cat_details  = add(json_encode($allinfo),'bank_details');
        //echo $cat_details;
        //exit;
        if(!empty($cat_details)){	
            $result = '{"type":"success","message":"Added Succesfully"}'; 
        }
        else{
           $result = '{"type":"error","message":"Not Added"}'; 
        }
        echo $result;
        exit;
    }
    
    function getBankDetailsByUser($user_id)
    {
        $rarray = array();
        $query = "select * from bank_details where user_id=$user_id";
        $details = findByQuery($query,'one');
        if(!empty($details))
        {
            $rarray = array("type" => "success","details" => $details);
        }
        else {
            $rarray = array("type" => "error", "message" => "Bank details not found.");
        }
        echo json_encode($rarray);
        exit;
    }
    
    function updateBankDetails() {
        $request = Slim::getInstance()->request();
        $body = $request->getBody();
        $coupon = json_decode($body);
        $id = $coupon->id;
        if(isset($coupon->id)){
                unset($coupon->id);
        }	

        $allinfo['save_data'] = $coupon;
        $coupon_details = edit(json_encode($allinfo),'bank_details',$id);
        if(!empty($coupon_details)){
            $result = '{"type":"success","message":"Changed Succesfully"}'; 
        }
        else{
            $result = '{"type":"error","message":"Not Changed"}'; 
        }
        echo $result;	
    }
?>
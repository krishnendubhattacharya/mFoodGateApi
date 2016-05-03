<?php
function addSms()
{
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());

    if($body->is_active)
    {
        $body->is_active = 1;
    }
    else
    {
        $body->is_active = 0;
    }

    $allinfo['save_data'] = $body;
    //print_r($allinfo);

    //$allinfo['unique_data'] = $unique_field;
    $cat_details  = add(json_encode($allinfo),'sms');
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

function getAllSms()
{
    $result = getAll('sms');
    echo $result;
}

function updateSms() {
    $request = Slim::getInstance()->request();
    $body = $request->getBody();
    $coupon = json_decode($body);
    $id = $coupon->id;
    if(isset($coupon->id)){
            unset($coupon->id);
    }	

    $allinfo['save_data'] = $coupon;
    $coupon_details = edit(json_encode($allinfo),'sms',$id);
    if(!empty($coupon_details)){
        $result = '{"type":"success","message":"Changed Succesfully"}'; 
    }
    else{
        $result = '{"type":"error","message":"Not Changed"}'; 
    }
    echo $result;	
}

function deleteSms($id) {
       $result =  delete('sms',$id);
       echo $result;	
}
?>
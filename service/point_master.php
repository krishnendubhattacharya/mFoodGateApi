<?php
function getAllPointMaster()
{
    $rarray = array();
    $conditions = array();
    
    $restaurants = findByConditionArray(array(),'point_master');
    if(!empty($restaurants))
    {
        $restaurants = array_map(function($t){
            $s = array();
            $user = findByIdArray( $t['user_id'],'users');
            $s['id'] = $t['id'];
            $s['user_id'] = $t['user_id'];
            $s['merchant_name'] = (!empty($user['merchant_name'])?$user['merchant_name']:''); 
            $s['name'] = $t['name'];
            $s['type'] = $t['type'];
            $s['value'] = $t['value'];
            $s['money_point'] = $t['money_point'];
            $s['valid_days'] = $t['valid_days'];
            $s['start_date'] = $t['start_date'];
            $s['expire_date'] =  date('d M, Y',  strtotime($t['expire_date']));
            //$t['restaurant'] = findByIdArray( $t['restaurant_id'],'restaurants'); 
            return $s;
        }, $restaurants);
        $rarray = array('type' => 'success', 'data' => $restaurants);
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'No outlets found');
    }
    echo json_encode($rarray);
}

function getAllMFoodPointMaster()
{
    $rarray = array();
    $conditions = array();
    
    $restaurants = findByConditionArray(array('type' => 0),'point_master');
    if(!empty($restaurants))
    {
        $restaurants = array_map(function($t){
            $s = array();
            $user = findByIdArray( $t['user_id'],'users');
            $s['id'] = $t['id'];
            $s['user_id'] = $t['user_id'];
            $s['merchant_name'] = (!empty($user['merchant_name'])?$user['merchant_name']:''); 
            $s['name'] = $t['name'];
            $s['type'] = $t['type'];
            $s['value'] = $t['value'];
            $s['money_point'] = $t['money_point'];
            $s['valid_days'] = $t['valid_days'];
            $s['start_date'] = $t['start_date'];
            $s['expire_date'] =  date('d M, Y',  strtotime($t['expire_date']));
            //$t['restaurant'] = findByIdArray( $t['restaurant_id'],'restaurants'); 
            return $s;
        }, $restaurants);
        $rarray = array('type' => 'success', 'data' => $restaurants);
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'No outlets found');
    }
    echo json_encode($rarray);
}

function getActivePointMasterByMerchant($user_id)
{
    $rarray = array();
    $conditions = array();
    
    //$restaurants_query = "SELECT * FROM point_master WHERE "
    $restaurants = findByConditionArray(array('user_id' => $user_id),'point_master');
    if(!empty($restaurants))
    {
        $restaurants = array_map(function($t){
            $s = array();
            $user = findByIdArray( $t['user_id'],'users');
            $s['id'] = $t['id'];
            $s['user_id'] = $t['user_id'];
            $s['merchant_name'] = (!empty($user['merchant_name'])?$user['merchant_name']:''); 
            $s['name'] = $t['name'];
            $s['type'] = $t['type'];
            $s['value'] = $t['value'];
            $s['money_point'] = $t['money_point'];
            $s['valid_days'] = $t['valid_days'];
            $s['start_date'] = $t['start_date'];
            $s['expire_date'] =  date('d M, Y',  strtotime($t['expire_date']));
            //$t['restaurant'] = findByIdArray( $t['restaurant_id'],'restaurants'); 
            return $s;
        }, $restaurants);
        $rarray = array('type' => 'success', 'data' => $restaurants);
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'No outlets found');
    }
    echo json_encode($rarray);
}

function addNewPointMaster() {	
	
    $rarray = array();
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    
    $allinfo['save_data'] = $body; 
	//$allinfo['save_data']['offer_to_date'] = $body->offer_to_date.' 23:59:59';
    $save = add(json_encode($allinfo),'point_master');
    if(!empty($save))
    {        
        $rarray = array('type' => 'success', 'offers' => $save);
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'Internal error please try again later.');
    }
    echo json_encode($rarray);
    //echo  $result;
	
}

function updatePointMaster() {
    $rarray = array();
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $id = $body->id;
    unset($body->id);
    
    if(isset($body->merchant_name))
        unset($body->merchant_name);
    
    $allinfo['save_data'] = $body; 
	//$allinfo['save_data']['offer_to_date'] = $body->offer_to_date.' 23:59:59';
    $save = edit(json_encode($allinfo),'point_master',$id);
    if(!empty($save))
    {        
        $rarray = array('type' => 'success', 'offers' => $save);
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'Internal error please try again later.');
    }
    echo json_encode($rarray);
	
}

function deletePointMaster($id) {
       $result =  delete('point_master',$id);
       echo $result;	
}
?>
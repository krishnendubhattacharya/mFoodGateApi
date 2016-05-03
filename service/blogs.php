<?php
function getAllBlogs()
{
    $rarray = array();
    $conditions = array();
    
    $restaurants = findByConditionArray(array(),'blogs');
    if(!empty($restaurants))
    {
        $restaurants = array_map(function($t){
            $s = array();
            $user = findByIdArray( $t['user_id'],'users');
            $s['id'] = $t['id'];
            $s['user_id'] = $t['user_id'];
            //$s['merchant_name'] = (!empty($user['merchant_name'])?$user['merchant_name']:''); 
            $s['title'] = $t['title'];
            $s['description'] = $t['description'];
            $s['image'] = $t['image'];
            if(!empty($s['image']))
                $s['image_url'] = SITEURL.'blog_images/'.$s['image'];
            $s['money_point'] = $t['money_point'];
            $s['posted_on'] =  date('d M, Y h:i A',  strtotime($t['posted_on']));
            //$t['restaurant'] = findByIdArray( $t['restaurant_id'],'restaurants'); 
            return $s;
        }, $restaurants);
        $rarray = array('type' => 'success', 'data' => $restaurants);
    }
    else 
    {
        $rarray = array('type' => 'error', 'message' => 'No blogs found');
    }
    echo json_encode($rarray);
}

function addNewBlog() {	
	
    $rarray = array();
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    
    $allinfo['save_data'] = $body; 
	//$allinfo['save_data']['offer_to_date'] = $body->offer_to_date.' 23:59:59';
    $save = add(json_encode($allinfo),'blogs');
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

?>
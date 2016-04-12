<?php
function getOutletsByRestaurant($id)
{
    $rarray = array();
    $conditions = array();
    $conditions['restaurant_id'] = $id;
    $restaurants = findByConditionArray($conditions,'outlets');
    if(!empty($restaurants))
    {
        $restaurants = array_map(function($t){
            $location = findByIdArray( $t['location_id'],'locations');
            $t['city'] =  $location['city'];
            return $t;
        }, $restaurants);
        $rarray = array('type' => 'success', 'data' => $restaurants);
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'No outlets found');
    }
    echo json_encode($rarray);
}

function getOutletDetails($id)
{
    $rarray = array();
    $temp = array();
    $conditions = array();
    $restaurants = findByIdArray($id,'outlets');
    if(!empty($restaurants))
    {
        $temp['outlet'] =  $restaurants;
        $temp['location'] = findByIdArray( $restaurants['location_id'],'locations');
        $temp['categories'] = findByConditionArray(array('outlet_id' => $id),'outlet_category_map');
        $rarray = array('type' => 'success', 'data' => $temp);
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'No outlets found');
    }
    echo json_encode($rarray);
}

function addOutlet()
{
    $rarray = array();
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $categories = $body->category_id;
    if(isset($body->category_id))
        unset($body->category_id);
    $body->restaurant_id = $body->resturant_id;
    unset($body->resturant_id);
    $allinfo['save_data'] = $body;
    $restaurant = findByIdArray( $body->restaurant_id,'restaurants');
    //print_r($restaurant);
    //exit;
    if(!empty($restaurant))
    {
        $body->user_id = $restaurant['user_id'];
        $cat_details  = add(json_encode($allinfo),'outlets');
        //var_dump($cat_details);
        //exit;
        if(!empty($cat_details)){
            $outlet_details = json_decode($cat_details);
            foreach($categories as $category)
            {
                $temp = array();
                $temp['outlet_id'] = $outlet_details->id;
                $temp['category_id']   = $category->id;
                $data = add(json_encode(array('save_data' => $temp)),'outlet_category_map');
            }
            $rarray = array('type' => 'success', 'message' => 'Added Succesfully');
            #$result = '{"type":"success","message":"Added Succesfully"}'; 
        }
        else{
           $rarray = array('type' => 'error', 'message' => 'Internal server error.');
        }
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'No restaurant found');
    }
   
    echo json_encode($rarray);
    exit;
}

function updateOutlet()
{
    $rarray = array();
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $categories = $body->category_id;
    
    $outlet_id = $body->id;
    unset($body->id);
    
    if(isset($body->category_id))
        unset($body->category_id);
    
    //$body->restaurant_id = $body->restaurant_id;
    unset($body->resturant_id);
    $allinfo['save_data'] = $body;
    $restaurant = findByIdArray( $body->restaurant_id,'restaurants');
    //print_r($restaurant);
    //exit;
    if(!empty($restaurant))
    {
        $body->user_id = $restaurant['user_id'];
        $cat_details  = edit(json_encode($allinfo),'outlets',$outlet_id);
        //var_dump($cat_details);
        //exit;
        if(!empty($cat_details)){
            $outlet_details = json_decode($cat_details);
            deleteAll('outlet_category_map',array('outlet_id' => $outlet_id));
            foreach($categories as $category)
            {
                $temp = array();
                $temp['outlet_id'] = $outlet_details->id;
                $temp['category_id']   = $category->id;
                $data = add(json_encode(array('save_data' => $temp)),'outlet_category_map');
            }
            $rarray = array('type' => 'success', 'message' => 'Added Succesfully');
            #$result = '{"type":"success","message":"Added Succesfully"}'; 
        }
        else{
           $rarray = array('type' => 'error', 'message' => 'Internal server error.');
        }
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'No restaurant found');
    }
   
    echo json_encode($rarray);
    exit;
}

/**/
function deleteOutlet($id) {
       $result =  delete('outlets',$id);
       deleteAll('outlet_category_map',array('outlet_id' => $id));
       echo $result;	
}
    
?>
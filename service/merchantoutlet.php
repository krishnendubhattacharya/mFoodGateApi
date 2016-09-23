<?php 

function getMerchantOutletsByRestaurant($id)
{    
    $rarray = array();
    $conditions = array();
    $conditions['restaurant_id'] = $id;
    $restaurants = findByConditionArray($conditions,'merchantoutlets');
    if(!empty($restaurants))
    {
        $restaurants = array_map(function($t){
            $location = findByIdArray( $t['location_id'],'locations');
            $t['city'] =  $location['city'];
            $t['imageurl'] = SITEURL.'merchantoutlet_images/'.$t['image'];
            return $t;
        }, $restaurants);
        for($i=0;$i<count($restaurants);$i++){
            $restaurants[$i]['locations'] = json_decode(findByCondition(array('outlet_id'=>$restaurants[$i]['id']),'merchantoutlet_location_map'));
        }
        $rarray = array('type' => 'success', 'data' => $restaurants);
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'No outlets found');
    }
    echo json_encode($rarray);
}

function addMerchantOutlet()
{
    $rarray = array();
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $locations='';
    
    //$body->restaurant_id = $body->resturant_id;
    //unset($body->resturant_id);
    
    if(isset($body->location_id)){
        $locations = $body->location_id;
        unset($body->location_id);
    }
        
    $allinfo['save_data'] = $body;
    $restaurant = findByIdArray( $body->restaurant_id,'merchantrestaurants');
    //print_r($restaurant);
    //exit;
    if(!empty($restaurant))
    {
        $body->user_id = $restaurant['user_id'];
        $cat_details  = add(json_encode($allinfo),'merchantoutlets');
        //var_dump($cat_details);
        //exit;
        if(!empty($cat_details)){
            $cat_details = json_decode($cat_details);
            if(!empty($locations)){
                foreach ($locations as $location)
                {
                    $temp = array();
                    $temp['outlet_id'] = $cat_details->id;
                    $temp['location_id']   = $location->id;
                    $data = add(json_encode(array('save_data' => $temp)),'merchantoutlet_location_map');
                }
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

function getMerchantsOutlet($id)
{
    $rarray = array();
    $conditions = array();
    $conditions['user_id'] = $id;
    $restaurants = findByConditionArray($conditions,'merchantoutlets');
    if(!empty($restaurants))
    {
        $restaurants = array_map(function($t){
            $location = findByIdArray( $t['location_id'],'locations');
            $t['city'] =  $location['city'];
            $t['imageurl'] = SITEURL.'merchantoutlet_images/'.$t['image'];
            return $t;
        }, $restaurants);
        for($i=0;$i<count($restaurants);$i++){
            $restaurants[$i]['locations'] = json_decode(findByCondition(array('outlet_id'=>$restaurants[$i]['id']),'merchantoutlet_location_map'));
        }
        $rarray = array('type' => 'success', 'data' => $restaurants);
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'No outlets found');
    }
    echo json_encode($rarray);
}
function MerchantOutletFileUpload()
{
    //print_r($_FILES);
    $filename = $_FILES['files']['name']['0'];
    move_uploaded_file($_FILES['files']['tmp_name']['0'], 'merchantoutlet_images/'.$filename);
    echo json_encode($filename);
    exit;
}

function updateMerchantOutlet()
{
    $rarray = array();
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $locations='';
    //$categories = $body->category_id;
    
    $outlet_id = $body->id;
    unset($body->id);
    
    if(isset($body->imageurl))
        unset($body->imageurl);
    
    if(isset($body->category_id))
        unset($body->category_id);
    
    if(isset($body->location_id)){
        $locations = $body->location_id;
        unset($body->location_id);
    }
        
    
    //$body->restaurant_id = $body->restaurant_id;
    
    $allinfo['save_data'] = $body;
    $restaurant = findByIdArray( $body->restaurant_id,'merchantrestaurants');
    //print_r($restaurant);
    //exit;
    if(!empty($restaurant))
    {
        $body->user_id = $restaurant['user_id'];
        $cat_details  = edit(json_encode($allinfo),'merchantoutlets',$outlet_id);
        //var_dump($cat_details);
        //exit;
        if(!empty($cat_details)){
            $cat_details = json_decode($cat_details);
            if(!empty($locations)){
                deleteAll('outlet_location_map',array('outlet_id' => $outlet_id));
                foreach($locations as $location)
                {
                    $temp = array();
                    $temp['outlet_id'] = $outlet_id;
                    $temp['location_id']   = $location->id;
                    $data = add(json_encode(array('save_data' => $temp)),'merchantoutlet_location_map');
                }
            }
            
           
            $rarray = array('type' => 'success', 'message' => 'Updated Succesfully');
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
function deleteMerchantOutlet($id) {
       $result =  delete('merchantoutlets',$id);
       echo $result;	
}
?>

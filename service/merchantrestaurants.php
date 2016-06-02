<?php
function getMerchantsRestaurants($merchant_id)
{    
    $rarray = array();
    $restaturants = findByConditionArray(array('user_id' => $merchant_id),'merchantrestaurants');
    if(!empty($restaturants))
    {
        $restaturants = array_map(function($s){
            $s['imageurl'] = SITEURL.'merchantrestaurant_images/'.$s['logo'];
            return $s;
        },$restaturants);
        $rarray = array('type' => 'success','restaurants' => $restaturants);
    }
    else
    {
        $rarray = array('type' => 'error','message' => 'No restaurants found.');
    }
    echo json_encode($rarray);
}

function getActiveMerchantRestaurant($merchant_id)
{    
    $rarray = array();
    $query = "SELECT * FROM merchantrestaurants WHERE user_id=$merchant_id and is_active=1";
    $restaturants = findByQuery($query);
    if(!empty($restaturants))
    {
        $restaturants = array_map(function($s){
            $s['imageurl'] = SITEURL.'merchantrestaurant_images/'.$s['logo'];
            return $s;
        },$restaturants);
        $rarray = array('type' => 'success','restaurants' => $restaturants);
    }
    else
    {
        $rarray = array('type' => 'error','message' => 'No restaurants found.','restaurants'=>array());
    }
    echo json_encode($rarray);
}

function getMerchantRestaurantDetails($res_id)
{
    $rarray = array();
    $details = findByIdArray($res_id,'merchantrestaurants');
    if(!empty($details))
    {
        $details['logo_url'] =  SITEURL.'merchantrestaurant_images/'.$details['logo'];
        $outlet_query = "SELECT * FROM merchantoutlets WHERE restaurant_id=$res_id and is_active=1";
        $outlets = findByQuery($outlet_query);
        $outlets = array_map(function($t){
            $t['image_url'] = SITEURL.'merchantoutlet_images/'.$t['image'];
            return $t;
        },$outlets);
        $details['outlets'] = $outlets;
        $rarray = array('type' => 'success', 'data' => $details);
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'Restaurant not found.');
    }
    echo json_encode($rarray);
}

function addMerchantsResturant() { 	
	
	$request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	//print_r($body);exit;
	if(isset($body->id)){
	        unset($body->id);
	}
        $categories = array();
//        if(isset($body->category_id))
//        {
//            $categories = $body->category_id;
//        }
//        unset($body->category_id);
        //unset($body->is_active); 
       
           $allinfo['save_data'] = $body;
           
            //$allinfo['unique_data'] = $unique_field;
          $location_details  = add(json_encode($allinfo),'merchantrestaurants');
          //echo $location_details;
          if(!empty($location_details)){
            /* $user_details = add(json_encode($allinfo),'coupons');
            $user_details = json_decode($user_details);*/
              $restaurant_details = json_decode($location_details);
              
              foreach($categories as $category)
              {
                    $temp = array();
                    $temp['restaurant_id'] = $restaurant_details->id;
                    $temp['category_id']   = $category->id;
                    $data = add(json_encode(array('save_data' => $temp)),'resturant_category_map');
                    //echo $data;
              }

            $result = '{"type":"success","message":"Added Succesfully"}'; 
          }
          else{
               $result = '{"type":"error","message":"Not Added"}'; 
          }
	
	echo $result;
	
}

function updateMerchantsResturant() { 	
	
	$request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	//print_r($body);exit;
        $id = $body->id;
	if(isset($body->id)){
	        unset($body->id);
	}
        
        if(isset($body->imageurl)){
	        unset($body->imageurl);
	}
        
        $categories = array();
//        if(isset($body->category_id))
//        {
//            $categories = $body->category_id;
//        }
//        unset($body->category_id);
        //unset($body->is_active); 
       
           $allinfo['save_data'] = $body;
           
            //$allinfo['unique_data'] = $unique_field;
          $location_details  = edit(json_encode($allinfo),'merchantrestaurants',$id);
          //echo $location_details;
          if(!empty($location_details)){
            /* $user_details = add(json_encode($allinfo),'coupons');
            $user_details = json_decode($user_details);*/
              $restaurant_details = json_decode($location_details);
              
              foreach($categories as $category)
              {
                    $temp = array();
                    $temp['restaurant_id'] = $restaurant_details->id;
                    $temp['category_id']   = $category->id;
                    $data = add(json_encode(array('save_data' => $temp)),'resturant_category_map');
                    //echo $data;
              }

            $result = '{"type":"success","message":"Updated Succesfully"}'; 
          }
          else{
               $result = '{"type":"error","message":"Could Not Updated"}'; 
          }
	
	echo $result;
	
}

function MerchantRestaurantLogoUpload()
{
    move_uploaded_file($_FILES['files']['tmp_name']['0'], 'merchantrestaurant_images/'.$_FILES['files']['name']['0']);
    echo json_encode($_FILES['files']['name']['0']);
    exit; 
}

function deleteMerchantRestaurant($id) {
       $result =  delete('merchantrestaurants',$id);
       echo $result;	
}
?>
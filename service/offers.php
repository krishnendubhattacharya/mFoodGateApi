<?php
function getOffersByRestaurant($restaurant_id)
{
    $rarray = array();
    $conditions = array();
    $conditions['restaurant_id'] = $restaurant_id;
    $restaurants = findByConditionArray($conditions,'offers');
    if(!empty($restaurants))
    {
        $restaurants = array_map(function($t){
            $location = findByIdArray( $t['offer_type_id'],'offer_types');
            $t['offer_type'] =  $location['name'];
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


	

function updateOffer() {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
       
	$body = json_decode($body);
         
        unset($body->date);
        if(!empty($body->image))
        {
            file_put_contents('./voucher_images/'.$body->image->filename,  base64_decode($body->image->base64));
            $body->image = $body->image->filename;
        }
        else
        {
            unset($body->image);
        }
        $id = $body->id;
        if(isset($body->image_url))
            unset($body->image_url);
	if(isset($body->id)){
	    unset($body->id);
	}	
        
        if(isset($body->category_id))
        {
            $categories = $body->category_id;
            unset($body->category_id);
        }

        if(isset($body->outlet_id))
        {
            $outlets = $body->outlet_id;
            unset($body->outlet_id);
        }
        
	$body->offer_to_date = $body->offer_to_date.' 23:59:59'; 
	$allinfo['save_data'] = $body;
	//$allinfo['save_data']['offer_to_date'] = $body->offer_to_date.' 23:59:59'; 
	//$allinfo['save_data']['offer_to_date'] = date('Y-m-d H:i:s', strtotime($allinfo['save_data']['offer_to_date'].' 23:59:59')); 
	$coupon_details = edit(json_encode($allinfo),'offers',$id);
        
	if(!empty($coupon_details)){
             $offers = json_decode($coupon_details);
             deleteAll('offer_category_map',array('offer_id' => $offers->id));
            if(!empty($categories))
            {
                foreach($categories as $category)
                {
                    $temp = array();
                    $temp['offer_id'] = $offers->id;
                    $temp['category_id'] = $category->id;
                    add(json_encode(array('save_data' => $temp)),'offer_category_map');
                }
            }

            deleteAll('offer_outlet_map',array('offer_id' => $offers->id));
            if(!empty($outlets))
            {
                foreach($outlets as $outlet)
                {
                    $temp = array();
                    $temp['offer_id'] = $offers->id;
                    $temp['outlet_id'] = $outlet->id;
                    add(json_encode(array('save_data' => $temp)),'offer_outlet_map');
                }
            }
	    $result = '{"type":"success","message":"Changed Succesfully"}'; 
	}
	else{
	    $result = '{"type":"error","message":"Not Changed"}'; 
	}
	echo $result;
	
}

/**/
function deleteOffer($id) { 
       $result =  delete('offers',$id);
       echo $result;	
}


function addNewOffer() {	
	
    $rarray = array();
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
   
    $news = array();
    
    $body->created_on = date('Y-m-d H:i:s');
    file_put_contents('./voucher_images/'.$body->image->filename,  base64_decode($body->image->base64));
    $body->image = $body->image->filename;
    unset($body->image_url);
    
    $restaurant_details = findByIdArray($body->restaurant_id,'restaurants');
    
    if(isset($body->category_id))
    {
        $categories = $body->category_id;
        unset($body->category_id);
    }
    
    if(isset($body->outlet_id))
    {
        $outlets = $body->outlet_id;
        unset($body->outlet_id);
    }
    $body->offer_to_date = $body->offer_to_date.' 23:59:59'; 
    $allinfo['save_data'] = $body; 
	//$allinfo['save_data']['offer_to_date'] = $body->offer_to_date.' 23:59:59';
    if(!empty($restaurant_details))
    {
        $body->merchant_id = $restaurant_details['user_id'];

        $news = add(json_encode($allinfo),'offers');
        if(!empty($news)){
            $offers = json_decode($news);
            if(!empty($categories))
            {
                foreach($categories as $category)
                {
                    $temp = array();
                    $temp['offer_id'] = $offers->id;
                    $temp['category_id'] = $category->id;
					
                    add(json_encode(array('save_data' => $temp)),'offer_category_map');
                }
            }

            if(!empty($outlets))
            {
                foreach($outlets as $outlet)
                {
                    $temp = array();
                    $temp['offer_id'] = $offers->id;
                    $temp['outlet_id'] = $outlet->id;
                    add(json_encode(array('save_data' => $temp)),'offer_outlet_map');
                }
            }
            $rarray = array('type' => 'success', 'offers' => $offers);
            //$result = '{"type":"success","message":"Added Successfully","offers":'.$offers.' }'; 
        }
        else{
            $rarray = array('type' => 'error', 'message' => 'Internal error');
            //$result = '{"type":"error","message":"Try Again!"}'; 
        }
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'Invalid restaurant');
    }
    echo json_encode($rarray);
    //echo  $result;
	
}
?>
<?php
function getAllOffers()
{
    $rarray = array();
    $conditions = array();
    
    $restaurants = findByConditionArray(array(),'offers');
    if(!empty($restaurants))
    {
        $restaurants = array_map(function($t){
            $location = findByIdArray( $t['offer_type_id'],'offer_types');
            $t['offer_type'] =  $location['name'];
            $t['restaurant'] = findByIdArray( $t['restaurant_id'],'restaurants');
            return $t;
        }, $restaurants);
        //echo "<pre>";
        
        
        foreach($restaurants as $index=>$restaurants_val){
             $restaurants_list = findByConditionArray(array('offer_id' => $restaurants_val['id']),'offer_restaurent_map');
             //print_r($restaurants_list);
             $restaurant_name = "";
             if(!empty($restaurants_list)){
                foreach($restaurants_list as $res_key=>$res_val){
                        $restaurant_detail = findByIdArray( $res_val['restaurent_id'],'restaurants');
                        if(!empty($restaurant_name)){
                              $restaurant_name = $restaurant_name.', '.$restaurant_detail['title'];  
                        }else{
                           $restaurant_name = $restaurant_detail['title'];     
                        }
                }
             }
             $restaurants[$index]['restaurant_name'] = $restaurant_name;
        }
        
        $rarray = array('type' => 'success', 'data' => $restaurants);
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'No outlets found');
    }
    echo json_encode($rarray);
}

function checkExpiredOffers()
{
    $rarray = array();
    $request = Slim::getInstance()->request();
    $body = $request->getBody();

    $body = json_decode($body);
    $offer_ids = $body->offer_ids;
    $user_id = $body->user_id;
    $check_offer=array();
    $rejectted_offer=array();
    if(!empty($user_id)){
        //print_r($offer_ids);
        
        foreach($offer_ids as $offer_index=>$offer_id){            
            $sql = "select * from offers where id='".$offer_id."'";
            $offer_result = findByQuery($sql);
                if(!empty($offer_result)){
                    $max_purchased = 0;
                    $max_purchased = $offer_result[0]['max_purchased'];
                    //print_r($offer_result);
                    $sql = "select * from voucher_owner where offer_id='".$offer_id."' and to_user_id='".$user_id."' and is_active=1";
                    $voucher_result = findByQuery($sql);                    
                    $voucher_count = count($voucher_result);
                    //echo $voucher_count;
                    if($voucher_count >= $max_purchased){
                        //unset($offer_ids[$offer_index]);
                        $check_offer[]=$offer_ids[$offer_index];
                        
                    }
                    
                    
            }
        }
        //print_r($check_offer);
    }
    if(!empty($offer_ids))
    {
        $sql = "select * from offers where DATE(offer_to_date)<CURDATE() and id in(".implode(",",$offer_ids).")";
        $results = findByQuery($sql);
        if(!empty($results))
        {
            $rejectted_offer = array_column($results,'id');
            if(!empty($check_offer)){
                foreach($check_offer as $check_offer_val){
                    if(in_array($check_offer_val, $rejectted_offer)){                        
                    }else{
                        $rejectted_offer[count($rejectted_offer)]=$check_offer_val;
                    }
                }
            }
            //print_r($rejectted_offer);
            $rarray = array('type' => 'success','ids' => $rejectted_offer);
        }
        else
        {
            if(!empty($check_offer)){
                foreach($check_offer as $check_offer_val){
                    if(in_array($check_offer_val, $rejectted_offer)){                        
                    }else{
                        $rejectted_offer[count($rejectted_offer)]=$check_offer_val;
                    }
                }
                $rarray = array('type' => 'success','ids' => $rejectted_offer);
            }else{
                $rarray = array('type' => 'error', 'message' => 'No offers found');
            }
        }
    }
    else
    {
        if(!empty($check_offer)){
                foreach($check_offer as $check_offer_val){
                    if(in_array($check_offer_val, $rejectted_offer)){                        
                    }else{
                        $rejectted_offer[count($rejectted_offer)]=$check_offer_val;
                    }
                }
                $rarray = array('type' => 'success','ids' => $rejectted_offer);
            }else{
                $rarray = array('type' => 'error', 'message' => 'No offers found');
            }
    }
    echo json_encode($rarray);
}

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
        
        if(isset($body->weekdays))
        {
            $weekdays = $body->weekdays;
            unset($body->weekdays);
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
        if(isset($body->restaurant_id))
        {
            $restaurants = $body->restaurant_id;
            unset($body->restaurant_id);
        }
        
        if(!empty($body->item_start_hour))
        {
            $body->item_start_hour = date('H:i:s',strtotime($body->item_start_hour));
        }

        if(!empty($body->item_end_hour))
        {
            $body->item_end_hour = date('H:i:s',strtotime($body->item_end_hour));
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

            
            if(!empty($outlets))
            {
                deleteAll('offer_outlet_map',array('offer_id' => $offers->id));
                foreach($outlets as $outlet)
                {
                    $temp = array();
                    $temp['offer_id'] = $offers->id;
                    $temp['outlet_id'] = $outlet->id;
                    add(json_encode(array('save_data' => $temp)),'offer_outlet_map');
                }
            }
            if(!empty($restaurants))
            {
                deleteAll('offer_restaurent_map',array('offer_id' => $offers->id));
                foreach($restaurants as $restaurant)
                {
                    $temp = array();
                    $temp['offer_id'] = $offers->id;
                    $temp['restaurent_id'] = $restaurant->id;
                    add(json_encode(array('save_data' => $temp)),'offer_restaurent_map');
                }
            }
            if(!empty($weekdays))
            {
                deleteAll('offer_days_map',array('offer_id' => $offers->id));
                foreach($weekdays as $weekday)
                {
                    $temp = array();
                    $temp['offer_id'] = $offers->id;
                    $temp['day'] = $weekday->id;
                    add(json_encode(array('save_data' => $temp)),'offer_days_map');
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

function deleteOfferImage($id) { 
       $result =  delete('offer_images',$id);
       echo $result;	
}


function addNewOffer() {	
	
    $rarray = array();
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
   
    $news = array();
    
    $body->created_on = date('Y-m-d H:i:s');
    $body->image->filename = time().$body->image->filename;
    file_put_contents('./voucher_images/'.$body->image->filename,  base64_decode($body->image->base64));
    $body->image = $body->image->filename;
    unset($body->image_url);
    
    //$restaurant_details = findByIdArray($body->restaurant_id,'restaurants');
    
    if(isset($body->weekdays))
    {
        $weekdays = $body->weekdays;
        unset($body->weekdays);
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
    if(isset($body->restaurant_id))
    {
        $restaurents = $body->restaurant_id;
        unset($body->restaurant_id);
    }
    if(!empty($body->item_start_hour))
    {
        $body->item_start_hour = date('H:i:s',strtotime($body->item_start_hour));
    }
    
    if(!empty($body->item_end_hour))
    {
        $body->item_end_hour = date('H:i:s',strtotime($body->item_end_hour));
    }
    
    $body->offer_to_date = $body->offer_to_date.' 23:59:59'; 
    $allinfo['save_data'] = $body; 
	//$allinfo['save_data']['offer_to_date'] = $body->offer_to_date.' 23:59:59';
    //if(!empty($restaurant_details))
    //{
        //$body->merchant_id = $restaurant_details['user_id'];

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
            if(!empty($restaurents))
            {
                foreach($restaurents as $restaurent)
                {
                    $temp = array();
                    $temp['offer_id'] = $offers->id;
                    $temp['restaurent_id'] = $restaurent->id;
                    add(json_encode(array('save_data' => $temp)),'offer_restaurent_map');
                }
            }
            
            if(!empty($weekdays))
            {
                foreach($weekdays as $weekday)
                {
                    $temp = array();
                    $temp['offer_id'] = $offers->id;
                    $temp['day'] = $weekday->id;
                    add(json_encode(array('save_data' => $temp)),'offer_days_map');
                }
            }
            $rarray = array('type' => 'success', 'offers' => $offers);
            //$result = '{"type":"success","message":"Added Successfully","offers":'.$offers.' }'; 
        }
        else{
            $rarray = array('type' => 'error', 'message' => 'Internal error');
            //$result = '{"type":"error","message":"Try Again!"}'; 
        }
    //}
    
    echo json_encode($rarray);
    //echo  $result;
	
}

function getOfferImages($pid)
{
    $rarray = array();
    $conditions = array();
    $site_path = SITEURL;
    $offer_images = findByConditionArray(array('offer_id'=>$pid),'offer_images');
    //print_r($offer_images);
    if(!empty($offer_images))
    {     
        $count = count($offer_images);
        for($i=0;$i<$count;$i++){
                $image = $site_path."voucher_images/".$offer_images[$i]['image'];
		$offer_images[$i]['image'] = $image;
        }  
        $rarray = array('type' => 'success', 'data' => $offer_images);
    }
    else
    {
        $rarray = array('type' => 'error', 'message' => 'No Images found');
    }
    echo json_encode($rarray);
}
function addNewOfferImage() {	
	
    $rarray = array();
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
   
    $news = array();
    
    
    $body->image->filename = time().$body->image->filename;
    $s = file_put_contents('./voucher_images/'.$body->image->filename,  base64_decode($body->image->base64));
    
    $body->image = $body->image->filename;
    unset($body->image_url);
    
    
    
    
    //$body->offer_to_date = $body->offer_to_date.' 23:59:59'; 
    $allinfo['save_data'] = $body; 
	

        $news = add(json_encode($allinfo),'offer_images');
        if(!empty($news)){
            $offers = json_decode($news);
            

            
            
            $rarray = array('type' => 'success', 'offers' => $offers);
            //$result = '{"type":"success","message":"Added Successfully","offers":'.$offers.' }'; 
        }
        else{
            $rarray = array('type' => 'error', 'message' => 'Internal error');
            //$result = '{"type":"error","message":"Try Again!"}'; 
        }
    //}
    
    echo json_encode($rarray);
    //echo  $result;
	
}

?>

<?php
    function getCartByUser($user_id)
    {
        $cart =  array();
        $all_cart = findByConditionArray(array('user_id' => $user_id),'cart');
        if(!empty($all_cart))
        {
            foreach($all_cart as $prod)
            {
                $temp_cart = array();
                $offer = findByIdArray($prod['offer_id'],'offers');
                if(!empty($offer) && ($offer['buy_count']<$offer['quantity']))
                {
                    $temp_cart['offer_id'] = $offer['id'];
                    $temp_cart['offer_percent'] = $offer['offer_percent'];
                    $temp_cart['offer_price'] = $offer['offer_price'];
                    $temp_cart['offer_title'] = $offer['title'];
                    $temp_cart['price'] = $offer['price'];
                    if($prod['quantity']<$offer['quantity']-$offer['buy_count'])
                    {
                        $temp_cart['quantity'] = $prod['quantity'];
                    }
                    else
                    {
                        $temp_cart['quantity'] = $offer['quantity']-$offer['buy_count'];
                    }
                    $temp_cart['mpoints'] = $offer['mpoints'];
                    $temp_cart['image'] = SITEURL.'voucher_images/'.$offer['image'];
                    $temp_cart['restaurant_id'] = $offer['restaurant_id'];
                    $restaurant = findByIdArray($offer['restaurant_id'],'restaurants');
                    $temp_cart['restaurant_title'] = $restaurant['title'];
                    $cart[] = $temp_cart;
                }
            }
        }
        echo json_encode($cart);
    }
    
    function addToCart()
    {
        $cart =  array();
        $request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
        $user_id = $body->user_id;
        if(isset($body->condition)){
            unset($body->condition);
        }
        if(!empty($body->cart))
        {  
            foreach($body->cart as $offer)
            {
                $query = "SELECT * from cart where user_id=$user_id and offer_id=".$offer->offer_id;
                $ifexist = findByQuery($query);
                if(empty($ifexist))
                {
                    $temp = array();
                    $temp['user_id'] = $user_id;
                    $temp['offer_id'] = $offer->offer_id;
                    $temp['quantity'] = $offer->quantity;
                    add(json_encode(array('save_data' => $temp)),'cart');
                }
            }
        }
        $all_cart = findByConditionArray(array('user_id' => $user_id),'cart');
        if(!empty($all_cart))
        {
            //print_r($all_cart);
            foreach($all_cart as $prod)
            {
                $point_name='';
                $temp_cart = array();
                $offer = findByIdArray($prod['offer_id'],'offers');
                if(!empty($offer['point_master_id'])){
                    $point_details = findByIdArray($offer['point_master_id'],'point_master');
                    if(!empty($point_details))
                        $point_name = $point_details['name'];
                }
                if(!empty($offer) && ($offer['buy_count']<$offer['quantity']))
                {
                    $temp_cart['offer_id'] = $offer['id'];
                    $temp_cart['offer_percent'] = $offer['offer_percent'];
                    $temp_cart['offer_price'] = $offer['offer_price'];
                    $temp_cart['offer_title'] = $offer['title'];
                    $temp_cart['price'] = $offer['price'];
                    if($prod['quantity']<$offer['quantity']-$offer['buy_count'])
                    {
                        $temp_cart['quantity'] = $prod['quantity'];
                    }
                    else
                    {
                        $temp_cart['quantity'] = $offer['quantity']-$offer['buy_count'];
                    }
                    $temp_cart['mpoints'] = $offer['mpoints'];
                    $temp_cart['image'] = SITEURL.'voucher_images/'.$offer['image'];
                    $temp_cart['restaurant_id'] = $offer['restaurant_id'];
                    $restaurant = findByIdArray($offer['restaurant_id'],'restaurants');
                    $temp_cart['restaurant_title'] = $restaurant['title'];
                    $temp_cart['point_id'] = $offer['point_master_id'];
                    $temp_cart['point_name'] = $point_name;                    
                    $temp_cart['condtn'] = $offer['conditions'];  
                    if($offer['conditions'] == 1){
                        $temp_cart['payments'] = true;
                        $temp_cart['paymentscash'] = true;
                        
                    }else{
                        $temp_cart['payments'] = false;
                        $temp_cart['paymentscash'] = true;
                    }
                    $cart[] = $temp_cart;
                }
            }
        }
        echo json_encode($cart);
        
    }
    
    function deleteFromCart()
    {
        $request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
        $qry = "SELECT * from cart where user_id=".$body->user_id." and offer_id=".$body->offer_id;
        $details = findByQuery($qry,'one');
        if(!empty($details))
        {
            delete('cart',$details['id']);
        }
    }
    
    function deleteCartByUser($user_id)
    {
        deleteAll('cart',array('user_id' => $user_id));
    }
    
    function updateCartQuantity()
    {
        $request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
        $rarray =array();
        if(!empty($body->item))
        {
            
            $item = $body->item;
            $offer_id = $item->offer_id;
            $promo_details = findByConditionArray(array('id' => $offer_id),'offers');
            //print_r($promo_details);
            $remaining_promo = $promo_details[0]['quantity'] - $promo_details[0]['buy_count'];
            $max_purchased = $promo_details[0]['max_purchased'];
            //$sql = "select * from voucher_owner where offer_id='".$offer_id."' and to_user_id='".$body->user_id."' and is_active=1";
            //$voucher_result = findByQuery($sql);                    
            //$voucher_count = count($voucher_result);
            /*if($max_purchased > $voucher_count){
                $qty = $max_purchased - $voucher_count;
            }else{
                $qty = 0;
            }*/
            //echo $max_purchased.'a';
            //echo $qty.'b';
            //echo $voucher_count.'c';
            if($item->quantity <= $remaining_promo){
                if($item->quantity <= $max_purchased){
                    $query = "UPDATE cart set quantity=".$item->quantity." where user_id=".$body->user_id." and offer_id=".$item->offer_id;
                    updateByQuery($query);
                    $rarray = array('type' => 'success', 'data' => 'ok');
                }else{
                    $rarray = array('type' => 'failure', 'data' => 'Quantity must be less than of maximum purchased of this promo');
                }
            }else{
                $rarray = array('type' => 'failure', 'data' => 'Quantity must be less than of remaining quantity');
            }           
            
        }
        echo json_encode($rarray);
        
    }
?>
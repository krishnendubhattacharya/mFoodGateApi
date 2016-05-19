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
                if(!empty($offer))
                {
                    $temp_cart['offer_id'] = $offer['id'];
                    $temp_cart['offer_percent'] = $offer['offer_percent'];
                    $temp_cart['offer_price'] = $offer['offer_price'];
                    $temp_cart['offer_title'] = $offer['title'];
                    $temp_cart['price'] = $offer['price'];
                    $temp_cart['quantity'] = $prod['quantity'];
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
            foreach($all_cart as $prod)
            {
                $temp_cart = array();
                $offer = findByIdArray($prod['offer_id'],'offers');
                if(!empty($offer))
                {
                    $temp_cart['offer_id'] = $offer['id'];
                    $temp_cart['offer_percent'] = $offer['offer_percent'];
                    $temp_cart['offer_price'] = $offer['offer_price'];
                    $temp_cart['offer_title'] = $offer['title'];
                    $temp_cart['price'] = $offer['price'];
                    $temp_cart['quantity'] = $prod['quantity'];
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
        if(!empty($body->item))
        {
            $item = $body->item;
            $query = "UPDATE cart set quantity=".$item->quantity." where user_id=".$body->user_id." and offer_id=".$item->offer_id;
            updateByQuery($query);
        }
    }
?>
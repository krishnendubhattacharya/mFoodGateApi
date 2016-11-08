<?php
    function getCartByUser($user_id)
    {
        $cart =  array();
        $point_name='';
        $all_cart = findByConditionArray(array('user_id' => $user_id),'cart');
        if(!empty($all_cart))
        {
            foreach($all_cart as $prod)
            {
                if($prod['event'] == 1){
                    $event = findByIdArray($prod['event_id'],'events');
                    $temp_cart = array();
                    $temp_cart['offer_id'] = 0;
                    $temp_cart['offer_percent'] = 0;
                    $temp_cart['price'] = $prod['event_price'];
                    $temp_cart['mpoints'] = 0;
                    $temp_cart['offer_price'] = $prod['event_price'];
                    $temp_cart['resell'] = 0;
                    $temp_cart['resell_id'] = 0;
                    $temp_cart['quantity'] = 1;
                    $temp_cart['image'] = SITEURL.'event_images/'.$event['image'];
                    $temp_cart['restaurant_id'] = 0;
                    $temp_cart['restaurant_title'] = 'Event';
                    $temp_cart['point_id'] = 0;
                    $temp_cart['point_name'] = '';                    
                    $temp_cart['condtn'] = 0; 
                    $temp_cart['payments'] = false;
                    $temp_cart['paymentscash'] = true;
                    $temp_cart['event'] = 1;
                    $temp_cart['event_id'] = $prod['event_id'];
                    $temp_cart['event_price'] = $prod['event_price'];
                    $temp_cart['event_bid_id'] = $prod['event_bid_id'];
                    $temp_cart['offer_title'] = $event['title'];
                    $temp_cart['typemem'] = $prod['typemem'];
                    $cart[] = $temp_cart;
                }else{
                $temp_cart = array();
                $offer = findByIdArray($prod['offer_id'],'offers');
                if(!empty($offer) && ($offer['buy_count']<$offer['quantity'] || $prod['resell'] == 1))
                {
                    $temp_cart['offer_id'] = $offer['id'];
                    $temp_cart['offer_percent'] = $offer['offer_percent'];
                    //$temp_cart['offer_price'] = $offer['offer_price'];
                    $temp_cart['offer_title'] = $offer['title'];
                    //$temp_cart['price'] = $offer['price'];
                    if(!empty($offer['point_master_id'])){
                        $point_details = findByIdArray($offer['point_master_id'],'point_master');
                        if(!empty($point_details))
                            $point_name = $point_details['name'];
                    }
                    if($prod['quantity']<$offer['quantity']-$offer['buy_count'] || $prod['resell'] == 1)
                    {
                        $temp_cart['quantity'] = $prod['quantity'];
                    }
                    else
                    {
                        $temp_cart['quantity'] = $offer['quantity']-$offer['buy_count'];
                    }
                    if($prod['resell'] == 1){
                        $temp_cart['price'] = $prod['resell_price'];
                        $temp_cart['mpoints'] = $prod['resell_mpoint'];
                        $temp_cart['offer_price'] = $prod['resell_price'];
                    }else{
                        $temp_cart['price'] = $offer['price'];
                        $temp_cart['mpoints'] = $offer['mpoints'];
                        $temp_cart['offer_price'] = $offer['offer_price'];
                    }
                    $temp_cart['resell'] = $prod['resell'];
                    $temp_cart['resell_id'] = $prod['resell_id'];
                    
                    $temp_cart['point_id'] = $offer['point_master_id'];
                    $temp_cart['point_name'] = $point_name; 
                    $temp_cart['event'] = 0;
                    $temp_cart['event_id'] = 0;
                    $temp_cart['event_price'] = 0;
                    $temp_cart['event_bid_id'] = 0;
                    $temp_cart['payments'] = $prod['point']==1?true:false;
                    $temp_cart['paymentscash'] = $prod['price']==1?true:false;
                    $temp_cart['typemem'] = $prod['typemem'];
                    //$temp_cart['mpoints'] = $offer['mpoints'];
                    if($offer['mpoints'] == 0){
                        $temp_cart['paymentscash'] = true;
                        $temp_cart['payments'] = false;
                    }
                    $temp_cart['image'] = SITEURL.'voucher_images/'.$offer['image'];
                    $temp_cart['restaurant_id'] = $offer['restaurant_id'];
                    $restaurant = findByIdArray($offer['restaurant_id'],'restaurants');
                    $temp_cart['restaurant_title'] = $restaurant['title'];
                    $temp_quantity = $temp_cart['quantity'];
                    $offer_id = $temp_cart['offer_id'];
                    $up_query = "UPDATE offers SET buy_count=buy_count+".$temp_quantity." where id=".$offer_id;
                    updateByQuery($up_query);
                    if($temp_cart['quantity'] >= 1)
                        $cart[] = $temp_cart;
                    //$cart[] = $temp_cart;
                }
            }
            }
        }
        echo json_encode($cart);
    }
    
    function addToCart()
    {
        //echo strtotime('+'.VALIDITYFORCART);
        //exit;
        $cart =  array();
        $request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
        $user_id = $body->user_id;
        $point_id_array=array();
        if(isset($body->condition)){
            unset($body->condition);
        }
        //print_r($body->cart);
        //exit;
        if(!empty($body->cart))
        {  
            foreach($body->cart as $offer)
            {
                if($offer->event == 1){
                    $temp = array();
                    $temp['user_id'] = $user_id;
                    $temp['offer_id'] = $offer->offer_id;
                    $temp['quantity'] = 1;
                    $temp['price'] = 1;
                    $temp['point'] = 0;
                    $temp['resell'] = 0;
                    $temp['resell_id'] = 0;
                    $temp['resell_price'] = 0;
                    $temp['resell_mpoint'] = 0;
                    $temp['event'] = $offer->event;
                    $temp['event_id'] = $offer->event_id;
                    $temp['event_price'] = $offer->event_price;
                    $temp['event_bid_id'] = $offer->event_bid_id;
                    $temp['validity'] = strtotime('+'.VALIDITYFORCART);
                    $temp['typemem'] = '';
                    add(json_encode(array('save_data' => $temp)),'cart');
                }else{
                    $query = "SELECT * from cart where user_id=$user_id and offer_id=".$offer->offer_id;
                    $ifexist = findByQuery($query);
                    
                    
                    if(empty($ifexist))
                    {
                        $offerrr = findByIdArray($offer->offer_id,'offers');
                        if(($offerrr['quantity']-$offerrr['buy_count']) > 0){
                            $temp = array();
                            if($offer->paymentscash == 1){
                                $offer->paymentscash = 1;
                            }else{
                                $offer->paymentscash =0;
                            }
                            if($offer->payments == 1){
                                $offer->payments = 1;
                            }else{
                                $offer->payments =0;
                            }
                            $temp['user_id'] = $user_id;
                            $temp['offer_id'] = $offer->offer_id;
                            $temp['quantity'] = $offer->quantity;
                            $temp['price'] = $offer->paymentscash;
                            $temp['point'] = $offer->payments;
                            $temp['resell'] = $offer->resell;
                            $temp['resell_id'] = $offer->resell_id;
                            $temp['resell_price'] = $offer->price;
                            $temp['resell_mpoint'] = $offer->mpoints;
                            $temp['validity'] = strtotime('+'.VALIDITYFORCART);
                            $temp['typemem'] = $offer->typemem;
                            add(json_encode(array('save_data' => $temp)),'cart');
                        }
                        
                    }
                }
            }
        }
        $all_cart = findByConditionArray(array('user_id' => $user_id),'cart');
        if(!empty($all_cart))
        {
            //print_r($all_cart);
            foreach($all_cart as $prod)
            {
                if($prod['event'] == 1){
                    $event = findByIdArray($prod['event_id'],'events');
                    $temp_cart = array();
                    $temp_cart['offer_id'] = 0;
                    $temp_cart['offer_percent'] = 0;
                    $temp_cart['price'] = $prod['event_price'];
                    $temp_cart['mpoints'] = 0;
                    $temp_cart['offer_price'] = $prod['event_price'];
                    $temp_cart['resell'] = 0;
                    $temp_cart['resell_id'] = 0;
                    $temp_cart['quantity'] = 1;
                    $temp_cart['image'] = SITEURL.'event_images/'.$event['image'];;
                    $temp_cart['restaurant_id'] = 0;
                    $temp_cart['restaurant_title'] = 'Event';
                    $temp_cart['point_id'] = 0;
                    $temp_cart['point_name'] = '';                    
                    $temp_cart['condtn'] = 0; 
                    $temp_cart['payments'] = false;
                    $temp_cart['paymentscash'] = true;
                    $temp_cart['event'] = 1;
                    $temp_cart['event_id'] = $prod['event_id'];
                    $temp_cart['event_price'] = $prod['event_price'];
                    $temp_cart['event_bid_id'] = $prod['event_bid_id'];
                    $temp_cart['offer_title'] = $event['title'];
                    $temp_cart['typemem'] = $prod['typemem'];
                    $cart[] = $temp_cart;
                }else{
                $point_name='';
                $point_id='';
                $point_needed = 0;
                $temp_cart = array();
                $offer = findByIdArray($prod['offer_id'],'offers');
                $point_id = $offer['point_master_id'];
                $point_needed = $offer['mpoints'];
                if(!empty($offer['point_master_id'])){
                    $point_details = findByIdArray($offer['point_master_id'],'point_master');
                    if(!empty($point_details))
                        $point_name = $point_details['name'];
                }
                if(!empty($offer) && ($offer['buy_count']<$offer['quantity'] || $prod['resell'] == 1))
                {
                    $temp_cart['offer_id'] = $offer['id'];
                    $temp_cart['offer_percent'] = $offer['offer_percent'];
                    
                    $temp_cart['offer_title'] = $offer['title'];
                    if($prod['resell'] == 1){
                        $temp_cart['price'] = $prod['resell_price'];
                        $temp_cart['mpoints'] = $prod['resell_mpoint'];
                        $temp_cart['offer_price'] = $prod['resell_price'];
                    }else{
                        $temp_cart['price'] = $offer['price'];
                        $temp_cart['mpoints'] = $offer['mpoints'];
                        $temp_cart['offer_price'] = $offer['offer_price'];
                    }
                    $temp_cart['resell'] = $prod['resell'];
                    $temp_cart['resell_id'] = $prod['resell_id'];
                    
                    if(($prod['quantity']<$offer['quantity']-$offer['buy_count']) || ($prod['resell']==1))
                    {
                        $temp_cart['quantity'] = $prod['quantity'];
                    }
                    else
                    {
                        $temp_cart['quantity'] = $offer['quantity']-$offer['buy_count'];
                    }
                    
                    $temp_cart['image'] = SITEURL.'voucher_images/'.$offer['image'];
                    $temp_cart['restaurant_id'] = $offer['restaurant_id'];
                    $restaurant = findByIdArray($offer['restaurant_id'],'restaurants');
                    $temp_cart['restaurant_title'] = $restaurant['title'];
                    $temp_cart['point_id'] = $offer['point_master_id'];
                    $temp_cart['point_name'] = $point_name;                    
                    $temp_cart['condtn'] = $offer['conditions'];  
                    $temp_cart['event'] = 0;
                    $temp_cart['event_id'] = 0;
                    $temp_cart['event_price'] = 0;
                    $temp_cart['event_bid_id'] = 0;
                    $temp_cart['typemem'] = $prod['typemem'];
                    if($offer['conditions'] == 1){
                        $temp_cart['payments'] = true;
                        $temp_cart['paymentscash'] = true;
                        
                    }else{
                        if(!empty($point_id)){
                            $point_sql = "select * from point_details where user_id='".$user_id."' and point_id='".$point_id."'";
                            $point_res = findByQuery($point_sql);
                            $available_point = 0;
                            $total_point=0;
                            $redeem_point=0;
                            foreach($point_res as $point_detail_val){
                                $total_point = $total_point + $point_detail_val['points'];
                                $redeem_point = $redeem_point + $point_detail_val['redeemed_points'];
                            }
                            $available_point = $total_point-$redeem_point;
                            //echo $available_point;
                            
                            if($available_point >= $point_needed){
                                if(!empty($point_id_array)){
                                    $temp_cart['payments'] = true;
                                    $temp_cart['paymentscash'] = false;
                                    $point_id_array[$point_id] = $available_point-$point_needed;
                                }else{
                                    if (array_key_exists($point_id,$point_id_array)){
                                        if($point_id_array[$point_id] >= $point_needed){
                                            $temp_cart['payments'] = true;
                                            $temp_cart['paymentscash'] = false;
                                            $point_id_array[$point_id] = $point_id_array[$point_id]-$point_needed;
                                        }else{
                                            $temp_cart['payments'] = false;
                                            $temp_cart['paymentscash'] = true;
                                        }
                                    }else{
                                        $temp_cart['payments'] = true;
                                        $temp_cart['paymentscash'] = false;
                                        $point_id_array[$point_id] = $available_point-$point_needed;
                                    }
                                }
                                
                            }else{
                                $temp_cart['payments'] = false;
                                $temp_cart['paymentscash'] = true;
                            }
                            
                        }else{
                            $temp_cart['payments'] = false;
                            $temp_cart['paymentscash'] = true;
                        }
                        
                    }
                    if($offer['mpoints'] == 0){
                        $temp_cart['paymentscash'] = true;
                        $temp_cart['payments'] = false;
                    }
                    $pc = $temp_cart['paymentscash']?1:0 ;                    
                    $p = $temp_cart['payments']?1:0 ;
                    $query = "UPDATE cart set price=".$pc.", point=".$p." where user_id=".$user_id." and offer_id=".$offer['id'];
                    updateByQuery($query);
                    $temp_quantity = $temp_cart['quantity'];
                    $offer_id = $temp_cart['offer_id'];
                    $up_query = "UPDATE offers SET buy_count=buy_count+".$temp_quantity." where id=".$offer_id;
                    updateByQuery($up_query);
                    if($temp_cart['quantity'] >= 1)
                        $cart[] = $temp_cart;
                }else{
                    //delete('cart',$prod['id']);
                    
                }
            }
            }
        }
        echo json_encode($cart);
        
    }
    
    function deleteFromCart()
    {
        $request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
        if($body->event_promo == 'p'){
            $qry = "SELECT * from cart where user_id=".$body->user_id." and offer_id=".$body->offer_id;
            if($body->promo_qty == 'yes'){
                $up_query = "UPDATE offers SET buy_count=buy_count -".$body->quantity." where id=".$body->offer_id;
                updateByQuery($up_query);
            }
        }else{
            $qry = "SELECT * from cart where user_id=".$body->user_id." and event_id=".$body->offer_id;
        }
        
        $details = findByQuery($qry,'one');
        if(!empty($details))
        {
            $cart_id = $details['id'];
            delete('cart',$cart_id);
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
                    if($item->payments == true){
                        $point_id = $item->point_id;
                        $needed_point = $item->mpoints;
                        $needed_point = $needed_point * $item->quantity;
                        $point_name = $item->point_name;
                        $point_sql = "select * from point_details where user_id='".$body->user_id."' and point_id='".$point_id."'";
                        $point_res = findByQuery($point_sql);
                        $available_point = 0;
                        $total_point=0;
                        $redeem_point=0;
                        foreach($point_res as $point_detail_val){
                            $total_point = $total_point + $point_detail_val['points'];
                            $redeem_point = $redeem_point + $point_detail_val['redeemed_points'];
                        }
                        $available_point = $total_point-$redeem_point;
                        if($available_point >= $needed_point){
                            $query = "UPDATE cart set quantity=".$item->quantity." where user_id=".$body->user_id." and offer_id=".$item->offer_id;
                            updateByQuery($query);
                            $rarray = array('type' => 'success', 'data' => 'ok');
                        }else{
                            $rarray = array('type' => 'failure', 'data' => 'You dont have '.$needed_point.' '.$point_name.' point');
                        }
                        
                    }else{
                        $query = "UPDATE cart set quantity=".$item->quantity." where user_id=".$body->user_id." and offer_id=".$item->offer_id;
                        updateByQuery($query);
                        $rarray = array('type' => 'success', 'data' => 'ok');
                    }
                    
                    
                }else{
                    $rarray = array('type' => 'failure', 'data' => 'Quantity must be less than of maximum purchased of this promo');
                }
            }else{
                $rarray = array('type' => 'failure', 'data' => 'Quantity must be less than of remaining quantity');
            }           
            
        }
        echo json_encode($rarray);
        
    }
    
    function updateCheckType()
    {
        $request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
        //print_r($body);
        //exit;
        $rarray =array();
        if(!empty($body->item))
        {
            foreach($body->item as $bodykey=>$bodyval){
                if($bodyval->payments == 1){
                    $payment = 1;
                }else{
                    $payment = 0;
                }
                
                if($bodyval->paymentscash == 1){
                    $paymentcash = 1;
                }else{
                    $paymentcash = 0;
                }
                //$item = $body->item;
                $offer_id = $bodyval->offer_id;       
                $query = "UPDATE cart set price=".$paymentcash.", point=".$payment." where user_id=".$body->user_id." and offer_id=".$bodyval->offer_id;
                updateByQuery($query);
            }
            
            //foreach()
            
                      
            
        }
        $rarray = array('type' => 'success', 'data' => 'ok');
        echo json_encode($rarray);
        
    }
?>

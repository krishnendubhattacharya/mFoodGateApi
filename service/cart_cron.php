<?php

function cornForCartValidity(){
    $cur_time = time();
    //echo $cur_time;
    
    $cartsql = "select * from cart where  validity <= '".$cur_time."'";
    $cartres = findByQuery($cartsql);    
    if(!empty($cartres)){
        
        
        foreach ($cartres as $cartres_key=>$cartres_val){
            $offer_id = $cartres_val['offer_id'];
            $offer_qty = $cartres_val['quantity'];
            $cart_id = $cartres_val['id'];
            //$offer_details = findByConditionArray(array('id' => $offer_id),'offers');
            //print_r
            $up_query = "UPDATE offers SET buy_count=buy_count -".$offer_qty." where id=".$offer_id;
            updateByQuery($up_query);
            delete('cart',$cart_id);
        }
    }
    
}
?>
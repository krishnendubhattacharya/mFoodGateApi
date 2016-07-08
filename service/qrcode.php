<?php 
/*
 * GET Parameters
 * merchant_id
 * restaurant_ids
 * voucher_id
 */
function genVoucherQrCode(){
    $code_string = '';
    
    if(!empty($_GET['merchant_id']))
    {
        $code_string .= $_GET['merchant_id'].',';
    }
    
    if(!empty($_GET['restaurant_ids']))
    {
        $code_string = $code_string.'['. $_GET['restaurant_ids'].'],';
    }
    
    if(!empty($_GET['voucher_id']))
    {
        $code_string .= $_GET['voucher_id'];
    }
    if(!empty($code_string))
    {
        echo QRcode::png($code_string,false,'L',9,0);
    }
    else
    {
        echo QRcode::png('mFoodGate',false,'L',9,0);
    }
}

function genMembershipQrCode(){
    $code_string = '';
    
    if(!empty($_GET['merchant_id']))
    {
        $code_string .= $_GET['merchant_id'].',';
    }    
    if(!empty($_GET['member_id']))
    {
        $code_string .= $_GET['member_id'];
    }
    if(!empty($code_string))
    {
        echo QRcode::png($code_string,false,'L',9,0);
    }
    else
    {
        echo QRcode::png('mFoodGate',false,'L',9,0);
    }
}
?>
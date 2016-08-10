<?php
/*function getMyPoints($user_id){
    $rarray = array();
    try
    {
        $sql = "SELECT * FROM points WHERE user_id=:user_id order by id DESC";
        $db = getConnection();
        $stmt = $db->prepare($sql); 
        $stmt->bindParam("user_id", $user_id);
        $stmt->execute();
        $points = $stmt->fetchAll(PDO::FETCH_OBJ);
        if(!empty($points))
        {
            $points = array_map(function($t,$k){
                $t->sl = $k+1;
                //$t->type = ($t->type=='C'?'Credit':'Debit');
				$t->date = date('m/d/Y',  strtotime($t->date));
                $t->expire_date = date('m/d/Y',  strtotime($t->expire_date));
                if($t->type=='A')
                {
                    $advertisement = findByIdArray($t->parent_id,'advertisements');
                    if($advertisement['mpoint_name']==1)
                    {
                        $t->mpoint_name = 'mPoints';
                    }
                    else if($advertisement['mpoint_name']==3)
                    {
                        $t->mpoint_name = 'DK Point';
                    }
                    else if($advertisement['mpoint_name']==4)
                    {
                        $t->mpoint_name = 'mCash';
                    }
                    else if($advertisement['mpoint_name']==5)
                    {
                        $t->mpoint_name = 'mrPoint';
                    }
                }
                else if($t->type=='A')
                {
                    $advertisement = findByIdArray($t->parent_id,'banners');
                    if($advertisement['mpoint_name']==1)
                    {
                        $t->mpoint_name = 'mPoints';
                    }
                    else if($advertisement['mpoint_name']==3)
                    {
                        $t->mpoint_name = 'DK Point';
                    }
                    else if($advertisement['mpoint_name']==4)
                    {
                        $t->mpoint_name = 'mCash';
                    }
                    else if($advertisement['mpoint_name']==5)
                    {
                        $t->mpoint_name = 'mrPoint';
                    }
                }
                
                return $t;
            }, $points,  array_keys($points));
        }
        $rarray = array('status' => 'success','data' => $points);
    }
    catch(PDOException $e) {
        $rarray = array('status' => 'error','error' => array('text' => $e->getMessage()));
    } 
    echo json_encode($rarray);
    exit;
}*/

function getMyPoints($user_id){
    $rarray = array();    
    $sql = "select * from point_master where DATE(expire_date) != '0000-00-00 00:00:00'";
    $point_master_res = findByQuery($sql);
    $cnt = 0;
    if(!empty($point_master_res)){    
        $pointsid = array_column($point_master_res, 'id');
        $point_data = array();
        //echo '<pre>';

        
        //$pointsid = array_column($point_res, 'id');
        
            foreach($point_master_res as $point_key=>$point_val){
                $point_data = array();
                $point_id = $point_val['id'];
                $merchant_name = '';
                $point_name = '';                
                $point_name = $point_val['name'];
                if($point_val['type'] != 0){
                    $mer_id = $point_val['user_id'];
                    $user_sql = "select merchant_name from users where id='".$mer_id."'";
                    $user_res = findByQuery($user_sql);
                    //print_r($user_res);
                    if(!empty($user_res)){
                        $merchant_name = $user_res[0]['merchant_name'];
                    }                    
                }else{
                    $merchant_name = 'mFoodGate';
                }
                $point_sql = "select * from points where user_id='".$user_id."' and point_id='".$point_id."'";
                $point_res = findByQuery($point_sql);
                //print_r($point_res);
                if(!empty($point_res)){
                    //print_r($point_res);
                    $point_data['id']=$point_id;
                    $point_data['merchant_name']=$merchant_name;
                    $point_data['point_name']=$point_name;
                    $point_data['merchant_name']=$merchant_name;
                    $total_point = 0;
                    $redeem_point = 0;
                    $expired_point = 0;
                    $available_point = 0;
                    $cur_date = date('Y-m-d');
                    if($point_val['expire_date'] >= $cur_date){
                        foreach($point_res as $point_detail_val){
                            $total_point = $total_point + $point_detail_val['points'];
                            $redeem_point = $redeem_point + $point_detail_val['redeemed_points'];
                        }
                        $available_point = $total_point-$redeem_point;
                        $point_data['total_point']=$total_point;
                        $point_data['redeem_point']=$redeem_point;
                        $point_data['available_point']=$available_point;
                        $point_data['expired_point']=$expired_point;
                    }else{
                        foreach($point_res as $point_detail_val){ 
                            $total_point = $total_point + $point_detail_val['points'];
                            $redeem_point = $redeem_point + $point_detail_val['redeemed_points'];
                            $expired_point = $expired_point + $point_detail_val['remaining_points'];
                        }
                        $available_point = $total_point-$redeem_point;
                        $point_data['total_point']=$total_point;
                        $point_data['redeem_point']=$redeem_point;
                        $point_data['available_point']=$available_point;
                        $point_data['expired_point']=$expired_point;
                    }
                    $rarray[]=$point_data;
                    $cnt=1;
                }else{                    
                    unset($point_master_res[$point_key]);
                }
                //print_r($point_detail_res);
            }
        
        //print_r($point_data);
        //exit;
            if($cnt == 1){
                $rarray = array('status' => 'success','data' => $rarray); 
            }else{
                $rarray = array('status' => 'error','data' => 'No Data found'); 
            }
        
    }else{
        $rarray = array('status' => 'error','data' => 'No Data found'); 
    }
      
     
    echo json_encode($rarray);
    exit;
}

function redeemUserPoints()
{
     $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $user_id = $body->user_id;
    $points = $body->points;
    $carts = $body->cart;
    $offerr_id = $carts[0]->offer_id;
    $offer_detaill = json_decode(findById($offerr_id,'offers'));
    $mer_id=$offer_detaill->merchant_id;
    
    //print_r($points);
    //print_r($carts);
    //exit;
    $rarray = array();
    try{
        $points_left = $points;
        $points_redeemed = 0;
        $date = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM points WHERE user_id=:user_id and remaining_points>0 and merchant_id=:mer_id";
        $db = getConnection();
        $stmt = $db->prepare($sql); 
        $stmt->bindParam("user_id", $user_id);
        $stmt->bindParam("mer_id", $mer_id);
        //$stmt->bindParam("expire_date", $date);
        $stmt->execute();
        $allpoints = $stmt->fetchAll(PDO::FETCH_OBJ);
        //print_r($allpoints);
        //exit;
        if(!empty($allpoints))
        {
            foreach($allpoints as $point)
            {
                if($points_left>0)
                {
                    if($points_left>=$point->remaining_points)
                    {
                        //if($point->remaining_points>)
                        //$temp = $points_redeemed
                        $points_left = $points_left - $point->remaining_points;
                        $points_redeemed = $points_redeemed + $point->remaining_points;
                        $sqlInsert = 'UPDATE points set remaining_points=0, redeemed_points=redeemed_points + :redeemed_points where id=:id';
                        $preparedStatement = $db->prepare($sqlInsert);
                        $preparedStatement->bindParam("id", $point->id);
                        $preparedStatement->bindParam("redeemed_points", $point->remaining_points);
                        $preparedStatement->execute();
                    }
                    else
                    {
                        
                        $rem_pt = $point->remaining_points - $points_left;
                        $sqlInsert = 'UPDATE points set remaining_points=:remaining_points, redeemed_points= redeemed_points + :redeemed_points where id=:id';
                        $preparedStatement = $db->prepare($sqlInsert);
                        $preparedStatement->bindParam("id", $point->id);
                        $preparedStatement->bindParam("remaining_points", $rem_pt);
                        $preparedStatement->bindParam("redeemed_points", $points_left);
                        $preparedStatement->execute();
                        $points_left = 0;
                    }
                }
                
            }
        }
        if($points_left==0)
        {
            foreach($carts as $cart)
            {
                    $offer_details = json_decode(findById($cart->offer_id,'offers'));
                    $merchant_id='';
                    $offer_id ='';
                    $point_id = '';
                    $point = '';
                    $merchant_id=$offer_details->merchant_id;
                    $offer_id =$offer_details->id;
                    $point_id = $offer_details->given_point_master_id;
                    $point = $offer_details->mpoints_given;
                    
                    $voucher_data = array();
                    $voucher_data['offer_id'] =$cart->offer_id;
                    $voucher_data['created_on'] = $offer_details->created_on;
                    $voucher_data['price'] = $offer_details->price;
                    $voucher_data['offer_price'] = $offer_details->offer_price;
                    $voucher_data['offer_percent'] = $offer_details->offer_percent;
                    $voucher_data['from_date'] = $offer_details->offer_from_date;
                    $voucher_data['to_date'] = $offer_details->offer_to_date;
                    $voucher_data['is_active'] = 1;
                    $s = add(json_encode(array('save_data' => $voucher_data)),'vouchers');
                    $s = json_decode($s);
                    $owner_data = array();
                    $owner_data['offer_id'] = $cart->offer_id;
                    $owner_data['voucher_id'] = $s->id;
                    $owner_data['from_user_id'] = 0;
                    $owner_data['to_user_id'] = $user_id;
                    $owner_data['purchased_date'] = date('Y-m-d h:i:s');
                    $owner_data['is_active'] = 1;
                    $owner_data['price'] = $offer_details->price;
                    $owner_data['offer_price'] = $offer_details->offer_price;
                    $owner_data['offer_percent'] = $offer_details->offer_percent;
                    $owner_data['buy_price'] = $offer_details->offer_price;
                    add(json_encode(array('save_data' => $owner_data)),'voucher_owner');
                    $point_data = array();
                    $point_data['offer_id'] = $offer_id;
                    $point_data['points'] = $point;
                    $point_data['source'] = 'earn from promo click';
                    $point_data['user_id'] = $user_id;
                    $point_data['date'] = date('Y-m-d h:i:s');
                    $point_data['type'] = 'P';
                    $point_data['remaining_points'] = $point;
                    $point_data['merchant_id'] = $merchant_id;
                    $point_data['point_id'] = $point_id;                    
                    add(json_encode(array('save_data' => $point_data)),'points');
                    
                    
                    
            }
        }
        $rarray = array('status' => 'success','data' => $points);
    } catch (PDOException $ex) {
        $rarray = array('status' => 'error','error' => array('text' => $ex->getMessage()));
    }
    echo json_encode($rarray);
    exit;
}

function getUsersPoints($user_id,$promo_id)
{
    $rarray = array();
    $offer_details = json_decode(findById($promo_id,'offers'));
    $merchant_id =$offer_details->merchant_id;
    //echo $merchant_id;
    try
    {
        $date = date('Y-m-d H:i:s');
        $sql = "SELECT sum(remaining_points) as sum FROM points WHERE user_id=:user_id and merchant_id=:merchant_id";
        $db = getConnection();
        $stmt = $db->prepare($sql); 
        $stmt->bindParam("user_id", $user_id);
        $stmt->bindParam("merchant_id", $merchant_id);
        //$stmt->bindParam("expire_date", $date);
        $stmt->execute();
        $points = $stmt->fetch();        
        $rarray = array('status' => 'success','data' => $points);
    }
    catch(PDOException $e) {
        $rarray = array('status' => 'error','error' => array('text' => $e->getMessage()));
    } 
    echo json_encode($rarray);
    exit;
}

/*function getExpireSoonPoints($user_id)
{
    $rarray = array();
    try
    {
        $current_date = date('Y-m-d');
        $newDate = date('Y-m-d',strtotime('+7 days'));
        $sql = "SELECT * FROM points WHERE user_id=:user_id and expire_date BETWEEN :start and :end";
        $db = getConnection();
        $stmt = $db->prepare($sql); 
        $stmt->bindParam("user_id", $user_id);
        $stmt->bindParam("start", $current_date);
	$stmt->bindParam("end", $newDate);
        $stmt->execute();
        $points = $stmt->fetchAll(PDO::FETCH_OBJ); 
        if(!empty($points))
        {
            $points = array_map(function($t,$k){
                $t->sl = $k+1;
                //$t->type = ($t->type=='C'?'Credit':'Debit');
				$t->date = date('m/d/Y',  strtotime($t->date));
                $t->expire_date = date('m/d/Y',  strtotime($t->expire_date));
                return $t;
            }, $points,  array_keys($points));
        }
        $rarray = array('status' => 'success','data' => $points);
    }
    catch(PDOException $e) {
        $rarray = array('status' => 'error','error' => array('text' => $e->getMessage()));
    } 
    echo json_encode($rarray);
    exit;
}*/

function getExpireSoonPoints($user_id){
    $rarray = array();    
    $cur_year = date('Y');
    $cur_month = date('m');
    
    $sql = "select * from point_master where MONTH(expire_date)='".$cur_month."'";
    $point_master_res = findByQuery($sql);
    $cnt = 0;
    if(!empty($point_master_res)){    
        $pointsid = array_column($point_master_res, 'id');
        $point_data = array();
        //echo '<pre>';

        
        //$pointsid = array_column($point_res, 'id');
        
            foreach($point_master_res as $point_key=>$point_val){
                $point_data = array();
                $point_id = $point_val['id'];
                $merchant_name = '';
                $point_name = ''; 
                $month_count = 0;
                $year_count = 0;
                $expired_point = 0;
                $available_point = 0;
                /*$monthsql = "select * from point_master where MONTH(expire_date)='".$cur_month."' and id='".$point_id."'";
                $point_master_month_res = findByQuery($monthsql);
                if(!empty($point_master_month_res)){
                    $point_month_sql = "select * from points where user_id='".$user_id."' and point_id='".$point_id."'";
                    $point_month_res = findByQuery($point_month_sql);
                    //print_r($point_res);
                    if(!empty($point_month_res)){
                        foreach($point_month_res as $point_month_detail_val){                            
                            $month_count = $month_count + $point_month_detail_val['remaining_points'];
                        }
                    }
                }*/
                $point_name = $point_val['name'];
                if($point_val['type'] != 0){
                    $mer_id = $point_val['user_id'];
                    $user_sql = "select merchant_name from users where id='".$mer_id."'";
                    $user_res = findByQuery($user_sql);
                    //print_r($user_res);
                    if(!empty($user_res)){
                        $merchant_name = $user_res[0]['merchant_name'];
                    }                    
                }else{
                    $merchant_name = 'mFoodGate';
                }
                $point_sql = "select * from points where user_id='".$user_id."' and point_id='".$point_id."'";
                $point_res = findByQuery($point_sql);
                //print_r($point_res);
                if(!empty($point_res)){
                    //print_r($point_res);
                    $point_data['id']=$point_id;
                    $point_data['merchant_name']=$merchant_name;
                    $point_data['point_name']=$point_name;
                    $point_data['merchant_name']=$merchant_name;                    
                    
                    foreach($point_res as $point_detail_val){
                        //$total_point = $total_point + $point_detail_val['points'];
                        $year_count = $year_count + $point_detail_val['remaining_points'];
                    }                    
                    //$point_data['year_count']=$year_count;
                    $point_data['month_count']=$year_count;
                    
                    $rarray[]=$point_data;
                    $cnt=1;
                }else{                    
                    unset($point_master_res[$point_key]);
                }
                
                
                //print_r($point_detail_res);
            }
        
        //print_r($point_data);
        //exit;
            if($cnt == 1){
                $rarray = array('status' => 'success','data' => $rarray); 
            }else{
                $rarray = array('status' => 'error','data' => 'No Data found'); 
            }
        
    }else{
        $rarray = array('status' => 'error','data' => 'No Data found'); 
    }
      
     
    echo json_encode($rarray);
    exit;
}

?>

<?php
function getMyPoints($user_id){
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
}

function redeemUserPoints()
{
     $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $user_id = $body->user_id;
    $points = $body->points;
    $carts = $body->cart;
    $rarray = array();
    try{
        $points_left = $points;
        $points_redeemed = 0;
        $date = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM points WHERE user_id=:user_id and expire_date>=:expire_date and remaining_points>0 ORDER BY expire_date ASC";
        $db = getConnection();
        $stmt = $db->prepare($sql); 
        $stmt->bindParam("user_id", $user_id);
        $stmt->bindParam("expire_date", $date);
        $stmt->execute();
        $allpoints = $stmt->fetchAll(PDO::FETCH_OBJ);
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
            }
        }
        $rarray = array('status' => 'success','data' => $points);
    } catch (PDOException $ex) {
        $rarray = array('status' => 'error','error' => array('text' => $ex->getMessage()));
    }
    echo json_encode($rarray);
    exit;
}

function getUsersPoints($user_id)
{
    $rarray = array();
    try
    {
        $date = date('Y-m-d H:i:s');
        $sql = "SELECT sum(remaining_points) as sum FROM points WHERE user_id=:user_id and expire_date>=:expire_date";
        $db = getConnection();
        $stmt = $db->prepare($sql); 
        $stmt->bindParam("user_id", $user_id);
        $stmt->bindParam("expire_date", $date);
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

function getExpireSoonPoints($user_id)
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
}

?>

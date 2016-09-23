<?php
function addEventBid() 
{
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    //echo '<pre>';print_r($body);exit;
    $imageBid = $body->imagebid;
    unset($body->imagebid);
    $event_id = $body->event_id;
    $user_id = $body->user_id;
    
    $sql = "SELECT * FROM event_bids WHERE event_id=:event_id and user_id=:userid";
    $db = getConnection();
    $stmt = $db->prepare($sql);  
    $stmt->bindParam("event_id", $event_id);
    $stmt->bindParam("userid", $user_id);
    $stmt->execute();
    $bid_count = $stmt->rowCount();
    $stmt=null;
    $db=null;
    
    if($bid_count != 0){
        $result = '{"type":"error","message":"You have already bidded this event"}';
    }
    else {
            $allinfo['save_data'] = $body;
            //print_r($allinfo);

            //$allinfo['unique_data'] = $unique_field;
            $cat_details = add(json_encode($allinfo),'event_bids');
            $cat_details = json_decode($cat_details);
            //exit;
            if(!empty($cat_details)){	
                foreach($imageBid as $k=>$v)
                {
                	$temp['save_data'] = array('event_bid_id' => $cat_details->id,'image' => $v);
                    $s = add(json_encode($temp),'event_bid_images');
                }
                $result = '{"type":"success","message":"You have bidded Succesfully"}'; 
              }
              else{
                   $result = '{"type":"error","message":"Not Added"}'; 
              }
      }
      echo $result;
      exit;
}
 
function getEventBidsWithUser($event_id)
{
    $sql = "SELECT *,CONCAT(U.first_name,' ',U.last_name) as name,E.id as id from event_bids as E,users as U where E.user_id = U.id and E.event_id=:event_id";
        
    //echo $sql;
    $site_path = SITEURL;

    try {
            $db = getConnection();
            $stmt = $db->prepare($sql);	
            $stmt->bindParam("event_id", $event_id);

            $stmt->execute();
            $restaurants = $stmt->fetchAll(PDO::FETCH_OBJ);  
            $count = $stmt->rowCount();

            
            $db = null;
            $restaurants = json_encode($restaurants);
        $result = '{"type":"success","event_bids":'.$restaurants.',"count":'.$count.'}';

    } catch(PDOException $e) {
            $result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
    }
    echo $result;
}

function acceptEventBid($event_id,$bid_id)
{
    //echo $bid_id;
    $temp = array();
    $temp['event_id'] = $event_id;
    editByField(json_encode(array('save_data' => array('is_accepted' => 2))),'event_bids',$temp);
    $t = edit(json_encode(array('save_data' => array('is_accepted' => 1))),'event_bids',$bid_id);
    edit(json_encode(array('save_data' => array('status' => 'C'))),'events',$event_id);
    //var_dump($t);
    $result = '{"type":"success","message":"Bid accepted successfully"}';
    echo $result;
}
    
?>

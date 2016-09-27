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
                
                $eve = findById($event_id,'events');
    			 $eve = json_decode($eve);
    			 
    			 $to_user_id = $eve->user_id;
			 $from_user_id = $user_id;
                    
                    $toUserInfo = findByConditionArray(array('id' => $to_user_id),'users');
                    $fromUserInfo = findByConditionArray(array('id' => $from_user_id),'users');
                    
                    $to_user_email = $toUserInfo[0]['email'];
                    $from_user_email = $fromUserInfo[0]['email'];
                    $from_user_name = $fromUserInfo[0]['first_name'].' '.$fromUserInfo[0]['last_name'];
                    $to_user_name = $toUserInfo[0]['first_name'].' '.$toUserInfo[0]['last_name'];
                    $from = ADMINEMAIL;
                    //$to = $saveresales->email;
                    $to1 = $to_user_email;  
                    //$to1 = 'nits.anup@gmail.com';
                    $subject1 ='New bid placed on your event';
                    $body1 ='<html><body><p>Dear '.$to_user_name.',</p>

                            <p> '.$from_user_name. ' have bidded on the event <b>'.$eve->title.'</b> that you have posted.<br />
                            
                            <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">If we can help you with anything in the meantime just let us know by e-mailing&nbsp;</span>'.$from.'<br />
                            <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif"></span><span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">!&nbsp;</span></p>

                            <p>Thanks,<br />
                            mFood&nbsp;Team</p>

                            <p>&nbsp;</p></body></html>';

                    sendMail($to1,$subject1,$body1);
    			 
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
    $sql = "SELECT *,CONCAT(U.first_name,' ',U.last_name) as name,E.id as id, E.contact_person as contact_name, E.contact_phone as contact_phone from event_bids as E,users as U where E.user_id = U.id and E.event_id=:event_id";
        
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
		if(!empty($restaurants))
		{
            foreach($restaurants as $k=>$v)
            {
            	$allRes = findById($v->resturant_id,'merchantrestaurants');
    			$allRes = json_decode($allRes);
            	$allOutlet = findById($v->outlet_id,'merchantoutlets');
    			$allOutlet = json_decode($allOutlet);
    			$restaurants[$k]->resturant_name = $allRes->title;
    			$restaurants[$k]->outlet_name = $allOutlet->title;
    			$restaurants[$k]->price = number_format($restaurants[$k]->price,2,'.',',');
            }	
          }
            
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

function getEventBidDetail($id) {     

        $sql = "SELECT * from event_bids where event_bids.id=:id";
        
        //echo $sql;
        $site_path = SITEURL;
        $offer_images = array();
        $point_master_details=array();
        
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);	
		$stmt->bindParam("id", $id);
		
		$stmt->execute();
		$offer = $stmt->fetchObject();  
		$count = $stmt->rowCount();
		if($count>0)
		{
                    
                    $rid = 	$offer->resturant_id;
                    $outlet_id = $offer->outlet_id;
                    $event_id = $offer->event_id;
                    
                    $events = findById($event_id,'events');
    				$events = json_decode($events);
    				
    				$allRes = findById($rid,'merchantrestaurants');
	    			$allRes = json_decode($allRes);
	    			
		       	$allOutlet = findById($outlet_id,'merchantoutlets');
	    			$allOutlet = json_decode($allOutlet);
    			
                    $promo_images = findByConditionArray(array('event_bid_id'=>$id),'event_bid_images');
                    
                        if(!empty($promo_images))
                        {     
                                $prmcount = count($promo_images);
                                for($i=0;$i<$prmcount;$i++){
                                        $image = $site_path."template_images/".$promo_images[$i]['image'];
                                        $offer_images[$i]['image'] = $image;
                                }  
                                
                        }else{
                        		$image = $site_path."template_images/default.png";
                              $offer_images[0]['image'] = $image;
                        }
                     $offer = json_encode($offer);   
                    $result = '{"type":"success","event_bid":'.$offer.',"restaurant":'.json_encode($allRes).',"outlet":'.json_encode($allOutlet).',"events" : '.json_encode($events).',"promo_images" : '.json_encode($offer_images).'}';
		}else{
			$result =  '{"type":"error","message":"Sorry no promo found"}'; 
		}
	} catch(PDOException $e) {
		$result =  '{"type":"error","message":'. $e->getMessage() .'}'; 
	}
	echo $result;
}
    
?>

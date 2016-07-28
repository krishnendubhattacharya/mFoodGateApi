<?php
    function getAllInfoUptoDate($merchant_id){
        $rarray = array();
        $restaurant_count = 0;
        $outlet_count = 0;
        $active_outlet_count = 0;
        $restaturants = findByConditionArray(array('user_id' => $merchant_id),'merchantrestaurants');
        $outlets = findByConditionArray(array('user_id' => $merchant_id),'merchantoutlets');
        $activeoutlets = findByConditionArray(array('user_id' => $merchant_id,'is_active'=>1),'merchantoutlets');
        $allPromo = findByConditionArray(array('merchant_id' => $merchant_id),'offers');
        $all_promo_ids = array_column($allPromo, 'id');
        //print_r($all_promo_ids);
        $allFeaturedPromo = findByConditionArray(array('merchant_id' => $merchant_id,'is_featured' => 1),'offers');
        $allBanner = findByConditionArray(array('merchant_id' => $merchant_id),'banners');
        $allAdd = findByConditionArray(array('merchant_id' => $merchant_id),'advertisements');
        $sqlActivePromo = "select * from offers where DATE(offer_from_date) <= CURDATE() and DATE(offer_to_date) >= CURDATE() and merchant_id='".$merchant_id."'";
        $allActivePromo = findByQuery($sqlActivePromo);
        $sqlActiveFeaturedPromo = "select * from offers where DATE(offer_from_date) <= CURDATE() and DATE(offer_to_date) >= CURDATE() and merchant_id='".$merchant_id."' and is_featured=1";
        $allActiveFeaturedPromo = findByQuery($sqlActiveFeaturedPromo);
        
        $sqlActiveFeaturedPromo = "select * from offers where DATE(offer_from_date) <= CURDATE() and DATE(offer_to_date) >= CURDATE() and merchant_id='".$merchant_id."' and is_featured=1";
        $allActiveFeaturedPromo = findByQuery($sqlActiveFeaturedPromo);
        
        $sqlActiveBanner = "select * from banners where DATE(start_date) <= CURDATE() and DATE(end_date) >= CURDATE() and merchant_id='".$merchant_id."'";
        $allActiveBanner = findByQuery($sqlActiveBanner);
        
        $sqlActiveAddvertise = "select * from advertisements where DATE(start_date) <= CURDATE() and DATE(end_date) >= CURDATE() and merchant_id='".$merchant_id."'";
        $allActiveAddvertisement = findByQuery($sqlActiveAddvertise);
        
        $site_settings = findByIdArray('1','site_settings');
        if(!empty($site_settings['hot_percentage']))
        {
            $perc = $site_settings['hot_percentage'];
        }
        else
        {
            $perc = 0;
        }
        $sqlHotPromo = "SELECT * FROM offers where merchant_id='".$merchant_id."' and DATE(offers.offer_from_date) <=CURDATE() and offers.is_active=1 and DATE(offers.offer_to_date) >=CURDATE() and offers.buy_count < offers.quantity and ((offers.buy_count/offers.quantity)*100)>=$perc";
        $hotPromo = findByQuery($sqlHotPromo);
        
        
        $restaurant_count = count($restaturants);
        $outlet_count = count($outlets);
        $active_outlet_count = count($activeoutlets);
        $all_promo = count($allPromo);
        $all_featured_promo = count($allFeaturedPromo);
        $all_banner = count($allBanner);
        $all_add = count($allAdd);
        $all_active_promo = count($allActivePromo);
        $all_active_featured_promo = count($allActiveFeaturedPromo);
        $all_active_banner = count($allActiveBanner);
        $all_active_add = count($allActiveAddvertisement);
        $all_hot_promo = count($hotPromo);
        
        
        $merchant_detail = findByConditionArray(array('id' => $merchant_id),'users');        
        
        $merchant_unique_id = $merchant_detail[0]['merchant_id'];        
        $all_member_sql = "select * from member_user_map where merchant_id = '".$merchant_unique_id."' and user_id != '' and member_id != ''";
        $all_member_res = findByQuery($all_member_sql);
        
        $all_member_ids = array_column($all_member_res, 'user_id');
        $all_member_ids = array_values(array_unique($all_member_ids));
        $all_member_count = count($all_member_ids);
        
        $all_user_count = 0;
        $male_user_count = 0;
        $female_user_count = 0;
        $active_member_user_count = 0;
        $inactive_member_user_count = 0;
        $all_active_member_ids = '';
        $active_male_user_count=0;
        $active_female_user_count=0;
        $first_count = 0;
        $second_count = 0;
        $third_count = 0;
        $fourth_count = 0;
        $fifth_count = 0;
        $sixth_count = 0;
        
        $first_promo_count = 0;
        $second_promo_count = 0;
        $third_promo_count = 0;
        $fourth_promo_count = 0;
        $fifth_promo_count = 0;
        $sixth_promo_count = 0;
        
        $first_order_count = 0;
        $second_order_count = 0;
        $third_order_count = 0;
        $fourth_order_count = 0;
        $fifth_order_count = 0;
        $sixth_order_count = 0;
        
        $first_month_year = date('Y m',strtotime("-5 months"));
        $second_month_year = date('Y m',strtotime("-4 months"));
        $third_month_year = date('Y m',strtotime("-3 months"));
        $fourth_month_year = date('Y m',strtotime("-2 months"));
        $fifth_month_year = date('Y m',strtotime("-1 months"));
        $sixth_month_year = date('Y m');
        
        $first_month_text = date('M',strtotime("-5 months"));
        $second_month_text = date('M',strtotime("-4 months"));
        $third_month_text = date('M',strtotime("-3 months"));
        $fourth_month_text = date('M',strtotime("-2 months"));
        $fifth_month_text = date('M',strtotime("-1 months"));
        $sixth_month_text = date('M');
        
        $first_month_year = explode(' ',$first_month_year);        
        $second_month_year = explode(' ',$second_month_year);
        $third_month_year = explode(' ',$third_month_year);
        $fourth_month_year = explode(' ',$fourth_month_year);
        $fifth_month_year = explode(' ',$fifth_month_year);
        $sixth_month_year = explode(' ',$sixth_month_year);
        
        $first_month = $first_month_year[1];
        $first_year = $first_month_year[0];
        $second_month = $second_month_year[1];
        $second_year = $second_month_year[0];
        $third_month = $third_month_year[1];
        $third_year = $third_month_year[0];
        $fourth_month = $fourth_month_year[1];
        $fourth_year = $fourth_month_year[0];
        $fifth_month = $fifth_month_year[1];
        $fifth_year = $fifth_month_year[0];
        $sixth_month = $sixth_month_year[1];
        $sixth_year = $sixth_month_year[0];
        
        
        
        if(!empty($all_member_ids)){
            if(!empty($all_promo_ids)){
                $order_sql = "select * from order_details where offer_id in(".implode(',',$all_promo_ids).")  and user_id in(".implode(',',$all_member_ids).") and offer_price >= 1000";
                $order_res = findByQuery($order_sql); 
                $all_order_ids = array_column($order_res, 'id');                
                if(!empty($all_order_ids)){
                    $first_order_sql = "select * from orders where MONTH(date) = '".$first_month."' and YEAR(date) = '".$first_year."' and id in(".implode(',',$all_order_ids).")"; 
                    $first_order_res = findByQuery($first_order_sql);
                    $first_order_count = count($first_order_res);
                    
                    $second_order_sql = "select * from orders where MONTH(date) = '".$second_month."' and YEAR(date) = '".$second_year."' and id in(".implode(',',$all_order_ids).")"; 
                    $second_order_res = findByQuery($second_order_sql);
                    $second_order_count = count($second_order_res);
                    
                    $third_order_sql = "select * from orders where MONTH(date) = '".$third_month."' and YEAR(date) = '".$third_year."' and id in(".implode(',',$all_order_ids).")"; 
                    $third_order_res = findByQuery($third_order_sql);
                    $third_order_count = count($third_order_res);
                    
                    $fourth_order_sql = "select * from orders where MONTH(date) = '".$fourth_month."' and YEAR(date) = '".$fourth_year."' and id in(".implode(',',$all_order_ids).")"; 
                    $fourth_order_res = findByQuery($fourth_order_sql);
                    $fourth_order_count = count($fourth_order_res);
                    
                    $fifth_order_sql = "select * from orders where MONTH(date) = '".$fifth_month."' and YEAR(date) = '".$fifth_year."' and id in(".implode(',',$all_order_ids).")"; 
                    $fifth_order_res = findByQuery($fifth_order_sql);
                    $fifth_order_count = count($fifth_order_res);
                    
                    $sixth_order_sql = "select * from orders where MONTH(date) = '".$sixth_month."' and YEAR(date) = '".$sixth_year."' and id in(".implode(',',$all_order_ids).")"; 
                    $sixth_order_res = findByQuery($sixth_order_sql);
                    $sixth_order_count = count($sixth_order_res);
                    
                }
                
            }
        $first_sql = "select * from users where MONTH(registration_date) = '".$first_month."' and YEAR(registration_date) = '".$first_year."' and id in(".implode(',',$all_member_ids).")";        
        $first_res = findByQuery($first_sql);
        //print_r($first_res);
        $first_count = count($first_res);
        
        $second_sql = "select * from users where MONTH(registration_date) = '".$second_month."' and YEAR(registration_date) = '".$second_year."' and id in(".implode(',',$all_member_ids).")";        
        $second_res = findByQuery($second_sql);
        //print_r($first_res);
        $second_count = count($second_res);
        
        $third_sql = "select * from users where MONTH(registration_date) = '".$third_month."' and YEAR(registration_date) = '".$third_year."' and id in(".implode(',',$all_member_ids).")";        
        $third_res = findByQuery($third_sql);
        //print_r($third_res);
        $third_count = count($third_res);
        
        $fourth_sql = "select * from users where MONTH(registration_date) = '".$fourth_month."' and YEAR(registration_date) = '".$fourth_year."' and id in(".implode(',',$all_member_ids).")";        
        $fourth_res = findByQuery($fourth_sql);
        //print_r($fourth_res);
        $fourth_count = count($fourth_res);
        
        $fifth_sql = "select * from users where MONTH(registration_date) = '".$fifth_month."' and YEAR(registration_date) = '".$fifth_year."' and id in(".implode(',',$all_member_ids).")";        
        $fifth_res = findByQuery($fifth_sql);
        //print_r($fifth_res);
        $fifth_count = count($fifth_res);
        
        $sixth_sql = "select * from users where MONTH(registration_date) = '".$sixth_month."' and YEAR(registration_date) = '".$sixth_year."' and id in(".implode(',',$all_member_ids).")";        
        $sixth_res = findByQuery($sixth_sql);
        //print_r($sixth_res);
        $sixth_count = count($sixth_res);
        
        $first_promo_view_sql = "select * from promo_visit where Month(created_date) ='".$first_month."' and YEAR(created_date) = '".$first_year."' and merchant_id='".$merchant_id."'";        
        $first_promo_visit = findByQuery($first_promo_view_sql);
        $first_promo_count = count($first_promo_visit);
        
        $second_promo_view_sql = "select * from promo_visit where Month(created_date) ='".$second_month."' and YEAR(created_date) = '".$second_year."' and merchant_id='".$merchant_id."'";        
        $second_promo_visit = findByQuery($second_promo_view_sql);
        $second_promo_count = count($second_promo_visit);
        
        $third_promo_view_sql = "select * from promo_visit where Month(created_date) ='".$third_month."' and YEAR(created_date) = '".$third_year."' and merchant_id='".$merchant_id."'";        
        $third_promo_visit = findByQuery($third_promo_view_sql);
        $third_promo_count = count($third_promo_visit);
        
        $fourth_promo_view_sql = "select * from promo_visit where Month(created_date) ='".$fourth_month."' and YEAR(created_date) = '".$fourth_year."' and merchant_id='".$merchant_id."'";        
        $fourth_promo_visit = findByQuery($fourth_promo_view_sql);
        $fourth_promo_count = count($fourth_promo_visit);
        
        $fifth_promo_view_sql = "select * from promo_visit where Month(created_date) ='".$fifth_month."' and YEAR(created_date) = '".$fifth_year."' and merchant_id='".$merchant_id."'";        
        $fifth_promo_visit = findByQuery($fifth_promo_view_sql);
        $fifth_promo_count = count($fifth_promo_visit);
        
        $sixth_promo_view_sql = "select * from promo_visit where Month(created_date) ='".$sixth_month."' and YEAR(created_date) = '".$sixth_year."' and merchant_id='".$merchant_id."'";        
        $sixth_promo_visit = findByQuery($sixth_promo_view_sql);
        $sixth_promo_count = count($sixth_promo_visit);
            
        $all_user_sql = "select id from users where id in(".implode(',',$all_member_ids).")";
        $all_user_res = findByQuery($all_user_sql);
        //print_r($all_user_res);
        $all_user_count = count($all_user_res);
    
        
        $male_user_sql = "select id from users where gender='M' and id in(".implode(',',$all_member_ids).")";
        $male_user_res = findByQuery($male_user_sql);
        $male_user_count = count($male_user_res);
        //echo $male_user_count;
        $female_user_sql = "select id from users where gender='F' and id in(".implode(',',$all_member_ids).")";
        $female_user_res = findByQuery($female_user_sql);
        $female_user_count = count($female_user_res);
        //echo $female_user_count;
        
        $member_user_sql = "select * from member_visit where merchant_id='".$merchant_id."' and user_id in(".implode(',',$all_member_ids).")";
        $member_user_res = findByQuery($member_user_sql);
        //print_r($member_user_res);
        $all_active_member_ids = array_column($member_user_res, 'user_id');
        $all_active_member_ids = array_values(array_unique($all_active_member_ids));
        //print_r($all_active_member_ids);
        $active_member_user_count = count($member_user_res);        
        $inactive_member_user_count = $all_member_count - $active_member_user_count;
        }
                
        
        $data=array('restaurant_count'=>$restaurant_count,'outlet_count'=>$outlet_count,'active_outlet_count'=>$active_outlet_count,'all_promo'=>$all_promo,'all_featured_promo'=>$all_featured_promo,'all_banner'=>$all_banner,'all_add'=>$all_add,'all_active_promo'=>$all_active_promo,'all_active_featured_promo'=>$all_active_featured_promo,'all_active_banner'=>$all_active_banner,'all_active_add'=>$all_active_add,'all_hot_promo'=>$all_hot_promo);
        $member_graph = array('all'=>$all_user_count,'active_user'=>$active_member_user_count,'inactive_user'=>$inactive_member_user_count,'male_user'=>$male_user_count,'female_user'=>$female_user_count,'purchase_member'=>$all_user_count);
        $new_member_graph = array($first_month_text=>$first_count,$second_month_text=>$second_count,$third_month_text=>$third_count,$fourth_month_text=>$fourth_count,$fifth_month_text=>$fifth_count,$sixth_month_text=>$sixth_count);
        $promo_graph = array($first_month_text=>$first_promo_count,$second_month_text=>$second_promo_count,$third_month_text=>$third_promo_count,$fourth_month_text=>$fourth_promo_count,$fifth_month_text=>$fifth_promo_count,$sixth_month_text=>$sixth_promo_count);

        $order_graph = array($first_month_text=>$first_order_count,$second_month_text=>$second_order_count,$third_month_text=>$third_order_count,$fourth_month_text=>$fourth_order_count,$fifth_month_text=>$fifth_order_count,$sixth_month_text=>$sixth_order_count);
        
        $month_name = array('first'=>$first_month_text,'second'=>$second_month_text,'third'=>$third_month_text,'fourth'=>$fourth_month_text,'fifth'=>$fifth_month_text,'sixth'=>$sixth_month_text);
        $rarray = array('type' => 'success', 'data' => $data,'member_graph'=>$member_graph,'new_member_graph'=>$new_member_graph,'promo_graph'=>$promo_graph,'order_graph'=>$order_graph,'month_name'=>$month_name);
        
        echo json_encode($rarray);      
        
    }
    
    function getAllInfoDateRange(){
        $request = Slim::getInstance()->request();
        $body = json_decode($request->getBody());        
        $from_date = $body->from_date;
        $to_date = $body->to_date;
        $merchant_id = $body->merchant_id; 
        $website_visit_count = 0;
        $show_start_date = date('d-M-Y',  strtotime($from_date));
        $show_end_date = date('d-M-Y',  strtotime($to_date));
        
        $allPromo = findByConditionArray(array('merchant_id' => $merchant_id),'offers');
        $all_promo_ids = array_column($allPromo, 'id');
        //print_r($all_promo_ids);
        $first_redeem_restaurant = '';
        $second_redeem_restaurant = '';
        $third_redeem_restaurant = '';
        $fourth_redeem_restaurant = '';
        $fifth_redeem_restaurant = '';
        
        $first_redeem_value = '';
        $second_redeem_value = '';
        $third_redeem_value = '';
        $fourth_redeem_value = '';
        $fifth_redeem_value = '';
         
        $reddeem_voucher_sql = "select restaurants.title,Sum(vouchers.offer_price) as amount from vouchers,restaurants where vouchers.RedeemRestaurant = restaurants.restaurant_id and DATE(vouchers.RedeemDate) between '".$from_date."' AND '".$to_date."' and vouchers.Status='redeem' and vouchers.offer_id in(".implode(',',$all_promo_ids).") group by vouchers.RedeemRestaurant";
        $reddeem_voucher_res = findByQuery($reddeem_voucher_sql);
        //print_r($reddeem_voucher_res);
        if(!empty($reddeem_voucher_res)){
            $sorted_val = array();
            foreach($reddeem_voucher_res as $reddeem_voucher_key => $reddeem_voucher_val){
                $sorted_val[$reddeem_voucher_key]=$reddeem_voucher_val['amount'];
            }
            //print_r($sorted_val);
            array_multisort($sorted_val, SORT_DESC, $reddeem_voucher_res);
            //print_r($reddeem_voucher_res);
            if(isset($reddeem_voucher_res[0])){
                $first_redeem_restaurant = $reddeem_voucher_res[0]['title'];
                $first_redeem_value = $reddeem_voucher_res[0]['amount'];               
                
            }
            if(isset($reddeem_voucher_res[1])){
                $second_redeem_restaurant = $reddeem_voucher_res[1]['title'];
                $second_redeem_value = $reddeem_voucher_res[1]['amount'];              
                
            }
            if(isset($reddeem_voucher_res[2])){
                $third_redeem_restaurant = $reddeem_voucher_res[2]['title'];
                $third_redeem_value = $reddeem_voucher_res[2]['amount'];               
                
            }
            if(isset($reddeem_voucher_res[3])){
                $fourth_redeem_restaurant = $reddeem_voucher_res[3]['title'];
                $fourth_redeem_value = $reddeem_voucher_res[3]['amount'];               
                
            }
            if(isset($reddeem_voucher_res[4])){
                $fifth_redeem_restaurant = $reddeem_voucher_res[4]['title'];
                $fifth_redeem_value = $reddeem_voucher_res[4]['amount'];               
                
            }
            
        }
        
        $target_banner_sql = "select * from banners where DATE(start_date) between '".$from_date."' AND '".$to_date."' and merchant_id='".$merchant_id."'";        
        $target_banner = findByQuery($target_banner_sql);        
        $total_target_banner = 0;
        $total_actual_banner = 0;
        if(!empty($target_banner)){
            foreach($target_banner as $target_banner_val){
                $total_target_banner = $total_target_banner + $target_banner_val['target_click'];
                $total_actual_banner = $total_actual_banner + $target_banner_val['number_of_click'];
            }
        } 
        
        $target_add_sql = "select * from advertisements where DATE(start_date) between '".$from_date."' AND '".$to_date."' and merchant_id='".$merchant_id."'";        
        $target_add = findByQuery($target_add_sql);        
        //print_r($target_add);
        $total_target_add = 0;
        $total_actual_add = 0;
        if(!empty($target_add)){
            foreach($target_add as $target_add_val){
                $total_target_add = $total_target_add + $target_add_val['target_click'];
                $total_actual_add = $total_actual_add + $target_add_val['number_of_click'];
            }
        }
        
        $promo_view_sql = "select * from promo_visit where DATE(created_date) between '".$from_date."' AND '".$to_date."' and merchant_id='".$merchant_id."'";        
        $promo_visit = findByQuery($promo_view_sql);
        $promo_visit_count = count($promo_visit);
        
        $news_view_sql = "select * from news_visit where DATE(created_date) between '".$from_date."' AND '".$to_date."' and merchant_id='".$merchant_id."'";        
        $news_visit = findByQuery($news_view_sql);
        $news_visit_count = count($news_visit);
        
        $web_view_sql = "select * from website_visit where DATE(created_date) between '".$from_date."' AND '".$to_date."'";        
        $website_visit = findByQuery($web_view_sql);
	//print_r($website_visit);
        $website_visit_count = count($website_visit);
        
        
        $redeem_view_sql = "select * from vouchers where DATE(created_on) between '".$from_date."' AND '".$to_date."' and Status='redeem'";        
        $redeem_visit = findByQuery($redeem_view_sql);        
        $offer_ids = array_column($redeem_visit, 'offer_id');
        $redeem_promo_count = 0;
        if(!empty($offer_ids)){
            $redeem_promo_sql = "select id from offers where merchant_id='".$merchant_id."' and id in(".implode(',',$offer_ids).")";        
            $redeem_promo_visit = findByQuery($redeem_promo_sql);            
            $redeem_promo_count = count($redeem_promo_visit);
            
        }  
        
        $unredeem_view_sql = "select * from vouchers where DATE(created_on) between '".$from_date."' AND '".$to_date."' and Status != 'redeem'";        
        $unredeem_visit = findByQuery($unredeem_view_sql);        
        $offer_ids_unredeem = array_column($unredeem_visit, 'offer_id');
        $unredeem_promo_count = 0;
        
        if(!empty($offer_ids_unredeem)){
            $unredeem_promo_sql = "select id from offers where merchant_id='".$merchant_id."' and id in(".implode(',',$offer_ids_unredeem).")";        
            $unredeem_promo_visit = findByQuery($unredeem_promo_sql);            
            $unredeem_promo_count = count($unredeem_promo_visit);
            
        }
        $merchant_detail = findByConditionArray(array('id' => $merchant_id),'users');        
        
        $merchant_unique_id = $merchant_detail[0]['merchant_id'];        
        $all_member_sql = "select * from member_user_map where merchant_id = '".$merchant_unique_id."' and user_id != '' and member_id != ''";
        $all_member_res = findByQuery($all_member_sql);
        
        $all_member_ids = array_column($all_member_res, 'user_id');
        $all_member_ids = array_values(array_unique($all_member_ids));
        $all_member_count = count($all_member_ids);
        
        $all_user_count = 0;
        $male_user_count = 0;
        $female_user_count = 0;
        $active_member_user_count = 0;
        $inactive_member_user_count = 0;
        $all_active_member_ids = '';
        $active_male_user_count=0;
        $active_female_user_count=0;
        //$website_visit_count = 0;
        if(!empty($all_member_ids)){
        $all_user_sql = "select id from users where DATE(registration_date) between '".$from_date."' AND '".$to_date."' and id in(".implode(',',$all_member_ids).")";
        $all_user_res = findByQuery($all_user_sql);
        //print_r($all_user_res);
        $all_user_count = count($all_user_res);
    
        
        $male_user_sql = "select id from users where DATE(registration_date) between '".$from_date."' AND '".$to_date."' and gender='M' and id in(".implode(',',$all_member_ids).")";
        $male_user_res = findByQuery($male_user_sql);
        $male_user_count = count($male_user_res);
        //echo $male_user_count;
        $female_user_sql = "select id from users where DATE(registration_date) between '".$from_date."' AND '".$to_date."' and gender='F' and id in(".implode(',',$all_member_ids).")";
        $female_user_res = findByQuery($female_user_sql);
        $female_user_count = count($female_user_res);
        //echo $female_user_count;
        
        $member_user_sql = "select * from member_visit where DATE(created_date) between '".$from_date."' AND '".$to_date."' and merchant_id='".$merchant_id."' and user_id in(".implode(',',$all_member_ids).")";
        $member_user_res = findByQuery($member_user_sql);
        //print_r($member_user_res);
        $all_active_member_ids = array_column($member_user_res, 'user_id');
        $all_active_member_ids = array_values(array_unique($all_active_member_ids));
        //print_r($all_active_member_ids);
        $active_member_user_count = count($member_user_res);        
        $inactive_member_user_count = $all_member_count - $active_member_user_count;
        }
        if(!empty($all_active_member_ids)){
        $active_male_user_sql = "select id from users where DATE(registration_date) between '".$from_date."' AND '".$to_date."' and gender='M' and id in(".implode(',',$all_active_member_ids).")";
        $active_male_user_res = findByQuery($active_male_user_sql);
        $active_male_user_count = count($active_male_user_res);
        //echo $active_male_user_count;
        $active_female_user_sql = "select id from users where DATE(registration_date) between '".$from_date."' AND '".$to_date."' and gender='F' and id in(".implode(',',$all_active_member_ids).")";
        $active_female_user_res = findByQuery($active_female_user_sql);
        $active_female_user_count = count($active_female_user_res);
        //echo $active_female_user_count;
        }
        $unactivated_member_sql = "select * from member_user_map where merchant_id = '".$merchant_unique_id."' and user_id != '' and member_id = ''";
        $unactivated_member_res = findByQuery($unactivated_member_sql);
        
        $unactivated_member_ids = array_column($unactivated_member_res, 'user_id');
        $unactivated_member_ids = array_values(array_unique($unactivated_member_ids));
        $unactivated_member_count=0;
        if(!empty($unactivated_member_ids)){
            $unactivated_user_sql = "select id from users where DATE(registration_date) between '".$from_date."' AND '".$to_date."' and id in(".implode(',',$unactivated_member_ids).")";
            $unactivated_user_res = findByQuery($unactivated_user_sql);            
            $unactivated_member_count = count($unactivated_user_res);
        }
        //echo $unactivated_member_count;
        //print_r($unactivated_member_ids);
        
        $data=array('total_target_banner'=>$total_target_banner,'total_actual_banner'=>$total_actual_banner,'total_target_add'=>$total_target_add,'total_actual_add'=>$total_actual_add,'promo_visit_count'=>$promo_visit_count,'news_visit_count'=>$news_visit_count,'website_visit_count'=>$website_visit_count,'redeem_promo_count'=>$redeem_promo_count,'unredeem_promo_count'=>$unredeem_promo_count,'all_user_count'=>$all_user_count,'male_user_count'=>$male_user_count,'female_user_count'=>$female_user_count,'active_member_user_count'=>$active_member_user_count,'inactive_member_user_count'=>$inactive_member_user_count,'active_male_user_count'=>$active_male_user_count,'active_female_user_count'=>$active_female_user_count,'unactivated_member_count'=>$unactivated_member_count,'first_redeem_restaurant'=>$first_redeem_restaurant,'second_redeem_restaurant'=>$second_redeem_restaurant,'third_redeem_restaurant'=>$third_redeem_restaurant,'fourth_redeem_restaurant'=>$fourth_redeem_restaurant,'fifth_redeem_restaurant'=>$fifth_redeem_restaurant,'first_redeem_value'=>$first_redeem_value,'second_redeem_value'=>$second_redeem_value,'third_redeem_value'=>$third_redeem_value,'fourth_redeem_value'=>$fourth_redeem_value,'fifth_redeem_value'=>$fifth_redeem_value,'show_start_date'=>$show_start_date,'show_end_date'=>$show_end_date);
        $rarray = array('type' => 'success', 'data' => $data);
        echo json_encode($rarray);
        //exit;       
        
    }
    
    function promoVisit($promo_id){
        $offer_detail = findByConditionArray(array('id' => $promo_id),'offers');
        //print_r($offer_detail);
        $merchant_id = $offer_detail[0]['merchant_id'];
        //echo $merchant_id;
        $created_on = date('Y-m-d H:i:s');
        $promo_visit = array();
        $promo_visit['merchant_id'] = $merchant_id;
        $promo_visit['offer_id'] = $promo_id;
        $promo_visit['created_date'] = $created_on;
          
        $promo = add(json_encode(array('save_data' => $promo_visit)),'promo_visit');
        $rarray = array('type' => 'success', 'data' =>'Successfully saved' );
        echo json_encode($rarray);     
        
        
    }
    function bannerVisit($banner_id){
        $banner_detail = findByConditionArray(array('id' => $banner_id),'banners');
        //print_r($offer_detail);
        $merchant_id = $banner_detail[0]['merchant_id'];
        //echo $merchant_id;
        $created_on = date('Y-m-d H:i:s');
        $banner_visit = array();
        $banner_visit['merchant_id'] = $merchant_id;
        $banner_visit['banner_id'] = $banner_id;
        $banner_visit['created_date'] = $created_on;
          
        $banner = add(json_encode(array('save_data' => $banner_visit)),'banner_visit');
        $rarray = array('type' => 'success', 'data' =>'Successfully saved' );
        echo json_encode($rarray);     
        
        
    }
    function addVisit($add_id){
        $add_detail = findByConditionArray(array('id' => $add_id),'advertisements');
        //print_r($offer_detail);
        $merchant_id = $add_detail[0]['merchant_id'];
        //echo $merchant_id;
        $created_on = date('Y-m-d H:i:s');
        $add_visit = array();
        $add_visit['merchant_id'] = $merchant_id;
        $add_visit['advertise_id'] = $add_id;
        $add_visit['created_date'] = $created_on;
          
        $advertise = add(json_encode(array('save_data' => $add_visit)),'advertise_visit');
        $rarray = array('type' => 'success', 'data' =>'Successfully saved' );
        echo json_encode($rarray);     
        
        
    }
    function newsVisit($news_id){
        $news_detail = findByConditionArray(array('id' => $news_id),'news');
        //print_r($offer_detail);
        $merchant_id = $news_detail[0]['user_id'];
        //echo $merchant_id;
        $created_on = date('Y-m-d H:i:s');
        $news_visit = array();
        $news_visit['merchant_id'] = $merchant_id;
        $news_visit['news_id'] = $news_id;
        $news_visit['created_date'] = $created_on;
          
        $news = add(json_encode(array('save_data' => $news_visit)),'news_visit');
        $rarray = array('type' => 'success', 'data' =>'Successfully saved' );
        echo json_encode($rarray);     
        
        
    }
    
    function websiteVisit(){
        
            $created_on = date('Y-m-d H:i:s');
            $member_visit = array();            
            $member_visit['created_date'] = $created_on;
            $member = add(json_encode(array('save_data' => $member_visit)),'website_visit');
            $rarray = array('type' => 'success', 'data' =>'Successfully saved' );        
            echo json_encode($rarray);      
        
    }
    
    function memberVisit($user_id,$merchant_id){
        $member_count = findByConditionArray(array('user_id' => $user_id,'merchant_id'=>$merchant_id),'member_visit');
        //print_r($offer_detail);
        //$merchant_id = $news_detail[0]['user_id'];
        //echo $merchant_id;
        if(empty($member_count)){
            $created_on = date('Y-m-d H:i:s');
            $member_visit = array();
            $member_visit['merchant_id'] = $merchant_id;
            $member_visit['user_id'] = $user_id;
            $member_visit['created_date'] = $created_on;

            $member = add(json_encode(array('save_data' => $member_visit)),'member_visit');
            $rarray = array('type' => 'success', 'data' =>'Successfully saved' );
        }else{
            $rarray = array('type' => 'success', 'data' =>'already exist' );
        }
        echo json_encode($rarray);      
        
    }
    
    function getMerchantOutletsBySelectedRestaurant()
    {
        $rarray = array();
        $request = Slim::getInstance()->request();
        $body = json_decode($request->getBody());
        //print_r($body);
        //exit;
        $allResArray = array();
        if(isset($body->restaurant_id))
        {
            $allResArray = $body->restaurant_id;
            unset($body->restaurant_id);
        }
            
            //print_r($allResArray);
            //exit;
        $conditions = array();
        //$conditions['restaurant_id'] = $id;

        $sql = "SELECT * FROM merchantoutlets where merchantoutlets.restaurant_id in(".implode(",",$allResArray).")";
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();	
        $restaurants=array();		
        $restaurants = $stmt->fetchAll();
        $db = null;

        //$restaurants = findByConditionArray($conditions,'outlets');
        if(!empty($restaurants))
        {
            $restaurants = array_map(function($t){
                $location = findByIdArray( $t['location_id'],'locations');
                $t['city'] =  $location['city'];
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
    
    function transactionTabDetails()
    {
        $rarray = array();
        $request = Slim::getInstance()->request();
        $body = json_decode($request->getBody());
        //print_r($body);
        //exit;
        $restaurant_id=array();
        $outlet_id =array();
        $offer_id = array();
        $total_res_ids = array();
        $total_outlet_ids = array();
	$offers_ids = array();
        $merchant_id = $body->merchant_id;
        $start_date = $body->start_date;
        $end_date = $body->end_date;
        if(isset($body->selRes)){
            $restaurant_id = $body->selRes;
            //print_r($restaurant_id);
            foreach($restaurant_id as $res_key=>$res_value){
                $res_ids=array();
                $res_sql = "select * from restaurants where restaurant_id='".$res_value."'";
                $res_sql_res = findByQuery($res_sql);
                $res_ids = array_column($res_sql_res, 'id');
                if(!empty($res_ids))
                    $total_res_ids[]=$res_ids[0];
                
                
            }
        }
        if(isset($body->selout)){
            $outlet_id = $body->selout;
            foreach($outlet_id as $outlet_key=>$outlet_value){
                $out_ids=array();
                $out_sql = "select * from outlets where outlet_id='".$outlet_value."'";
                $out_sql_res = findByQuery($out_sql);
                $out_ids = array_column($out_sql_res, 'id');
                if(!empty($out_ids))
                    $total_outlet_ids[]=$out_ids[0];        
                
                
            }
        }        
        $type = $body->type;
        if(!empty($total_res_ids)){
            	$sql = "SELECT * FROM offer_restaurent_map where offer_restaurent_map.restaurent_id in(".implode(",",$total_res_ids).")";
		$res_offer_res = findByQuery($sql);
		$offers_ids = array_column($res_offer_res, 'offer_id');
		//print_r($offers_ids);
        }
        if(!empty($total_outlet_ids)){
		if(!empty($offers_ids)){
			$sql = "SELECT * FROM offer_outlet_map where offer_outlet_map.offer_id in(".implode(",",$offers_ids).") and offer_outlet_map.outlet_id in(".implode(",",$total_outlet_ids).")";
			$out_offer_res = findByQuery($sql);
			$offers_ids = array_column($out_offer_res, 'offer_id');
			//print_r($offers_ids);
		}
            
        }
	$allPaymentOffer = 0;
	$allMenuOffer = 0;
	$allMembershipOffer = 0;

	$allRedeemPaymentOffer = 0;
	$allRedeemMenuOffer = 0;
	$allRedeemMembershipOffer = 0;

	$allAvailablePaymentOffer = 0;
	$allAvailableMenuOffer = 0;
	$allAvailableMembershipOffer = 0;

	$offerPaymentOffer = 0;
	$offerMenuOffer = 0;
	$offerMembershipOffer = 0;

	$redeemPaymentOffer = 0;
	$redeemMenuOffer = 0;
	$redeemMembershipOffer = 0;

	$availablePaymentOffer = 0;
	$availableMenuOffer = 0;
	$availableMembershipOffer = 0;

	$willActivePaymentOffer = 0;
	$willActiveMenuOffer = 0;
	$willActiveMembershipOffer = 0;

	$newPaymentOffer = 0;
	$newMenuOffer = 0;
	$newMembershipOffer = 0;

	$hotPaymentOffer = 0;
	$hotMenuOffer = 0;
	$hotMembershipOffer = 0;

	$lastdayPaymentOffer = 0;
	$lastdayMenuOffer = 0;
	$lastdayMembershipOffer = 0;

	$specialPaymentOffer = 0;
	$specialMenuOffer = 0;
	$specialMembershipOffer = 0;

	$viewPaymentOffer = 0;
	$viewMenuOffer = 0;
	$viewMembershipOffer = 0;

	$willActiveNews = 0;
	$willActiveAddv = 0;
	$willActiveBanner = 0;

	$activeNews = 0;
	$activeAddv = 0;
	$activeBanner = 0;

	$targetNews = 0;
	$targetAddv = 0;
	$targetBanner = 0;

	$actualNews = 0;
	$actualAddv = 0;
	$actualBanner = 0;

	$remainingNews = 0;
	$remainingAddv = 0;
	$remainingBanner = 0;

	$exceedNews = 0;
	$exceedAddv = 0;
	$exceedBanner = 0;

	$exceedNews = 0;
	$exceedAddv = 0;
	$exceedBanner = 0;

	$availableEvent = 0;
	$dealEvent = 0;
	$openEvent = 0;
	$feedbackEvent = 0;
	$myEvent = 0;
	$myOfferEvent = 0;

	$generatedPoint = 0;
	$redeemPoint = 0;
	$expiredPoint = 0;
	$balancedPoint = 0;
	$nextMonthExpPoint = 0;
	$thisYearExpPoint = 0;

	//print_r($offers_ids);
	if(!empty($offers_ids)){
		$payment_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and DATE(offer_from_date) between '".$start_date."' AND '".$end_date."' and id in(".implode(',',$offers_ids).") and offer_type_id=2"; 
		$menu_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and DATE(offer_from_date) between '".$start_date."' AND '".$end_date."' and id in(".implode(',',$offers_ids).") and offer_type_id=1";
		$membership_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and DATE(offer_from_date) between '".$start_date."' AND '".$end_date."' and id in(".implode(',',$offers_ids).") and offer_type_id=3";

		$all_payment_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and offer_type_id=2"; 
		$all_menu_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and offer_type_id=1";
		$all_membership_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and offer_type_id=3";

		$payment_promo_res = findByQuery($payment_promo_sql);
		$menu_promo_res = findByQuery($menu_promo_sql);
		$membership_promo_res = findByQuery($membership_promo_sql);

		$all_payment_promo_res = findByQuery($all_payment_promo_sql);
		$all_menu_promo_res = findByQuery($all_menu_promo_sql);
		$all_membership_promo_res = findByQuery($all_membership_promo_sql);
		//print_r($all_payment_promo_res);
		//print_r($all_menu_promo_res);
		//print_r($all_membership_promo_res);
                if(!empty($all_payment_promo_res)){
                    if($type == 'unit'){
                        $allPaymentOffer = count($all_payment_promo_res);
                        $allRedeemPaymentOffer = 0;
                        $allAvailablePaymentOffer = count($all_payment_promo_res);
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($all_payment_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $allPaymentOffer = $cntt;
                        $allRedeemPaymentOffer = $cntr;
                        $allAvailablePaymentOffer = $cnta;                        
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($all_payment_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $allPaymentOffer = $cntt;
                        $allRedeemPaymentOffer = $cntr;
                        $allAvailablePaymentOffer = $cnta;  
                        
                    }
                }
                
                if(!empty($all_menu_promo_res)){
                    if($type == 'unit'){
                        $allMenuOffer = count($all_menu_promo_res);
                        $allRedeemMenuOffer = 0;
                        $allAvailableMenuOffer = count($all_menu_promo_res);
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($all_menu_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $allMenuOffer = $cntt;
                        $allRedeemMenuOffer = $cntr;
                        $allAvailableMenuOffer = $cnta;                        
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($all_menu_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $allMenuOffer = $cntt;
                        $allRedeemMenuOffer = $cntr;
                        $allAvailableMenuOffer = $cnta;  
                        
                    }
                }
                
                if(!empty($all_membership_promo_res)){
                    if($type == 'unit'){
                        $allMembershipOffer = count($all_membership_promo_res);
                        $allRedeemMembershipOffer = 0;
                        $allAvailableMembershipOffer = count($all_membership_promo_res);
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($all_membership_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $allMembershipOffer = $cntt;
                        $allRedeemMembershipOffer = $cntr;
                        $allAvailableMembershipOffer = $cnta;                        
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($all_membership_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $allMembershipOffer = $cntt;
                        $allRedeemMembershipOffer = $cntr;
                        $allAvailableMembershipOffer = $cnta;  
                        
                    }
                }
                
                if(!empty($payment_promo_res)){
                    if($type == 'unit'){
                        $offerPaymentOffer = count($payment_promo_res);
                        $redeemPaymentOffer = 0;
                        $availablePaymentOffer = count($payment_promo_res);
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($payment_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $offerPaymentOffer = $cntt;
                        $redeemPaymentOffer = $cntr;
                        $availablePaymentOffer = $cnta;                        
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($payment_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $offerPaymentOffer = $cntt;
                        $redeemPaymentOffer = $cntr;
                        $availablePaymentOffer = $cnta;  
                        
                    }
                }
                
                if(!empty($menu_promo_res)){
                    if($type == 'unit'){
                        $offerMenuOffer = count($menu_promo_res);
                        $redeemMenuOffer = 0;
                        $availableMenuOffer = count($menu_promo_res);
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($menu_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $offerMenuOffer = $cntt;
                        $redeemMenuOffer = $cntr;
                        $availableMenuOffer = $cnta;                        
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($menu_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $offerMenuOffer = $cntt;
                        $redeemMenuOffer = $cntr;
                        $availableMenuOffer = $cnta;  
                        
                    }
                }
                
                if(!empty($membership_promo_res)){
                    if($type == 'unit'){
                        $offerMembershipOffer = count($membership_promo_res);
                        $redeemMembershipOffer = 0;
                        $availableMembershipOffer = count($membership_promo_res);
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($membership_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $offerMembershipOffer = $cntt;
                        $redeemMembershipOffer = $cntr;
                        $availableMembershipOffer = $cnta;                        
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($membership_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $offerMembershipOffer = $cntt;
                        $redeemMembershipOffer = $cntr;
                        $availableMembershipOffer = $cnta;  
                        
                    }
                }
                
                $payment_will_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and DATE(offer_from_date)>=CURDATE() and id in(".implode(',',$offers_ids).") and offer_type_id=2";
                $menu_will_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and DATE(offer_from_date)>=CURDATE() and id in(".implode(',',$offers_ids).") and offer_type_id=1";
                $membership_will_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and DATE(offer_from_date)>=CURDATE() and id in(".implode(',',$offers_ids).") and offer_type_id=3";
                
                $payment_new_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and DATE(offer_from_date)=CURDATE() and id in(".implode(',',$offers_ids).") and offer_type_id=2";
                $menu_new_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and DATE(offer_from_date)=CURDATE() and id in(".implode(',',$offers_ids).") and offer_type_id=1";
                $membership_new_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and DATE(offer_from_date)=CURDATE() and id in(".implode(',',$offers_ids).") and offer_type_id=3";
                
                $site_settings = findByIdArray('1','site_settings');
                if(!empty($site_settings['hot_percentage']))
                {
                    $perc = $site_settings['hot_percentage'];
                }
                else
                {
                    $perc = 0;
                }
                
                $payment_hot_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and id in(".implode(',',$offers_ids).") and offer_type_id=2 and DATE(offer_from_date) <=CURDATE() and buy_count < quantity and ((buy_count/quantity)*100)>=$perc";
                $menu_hot_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and id in(".implode(',',$offers_ids).") and offer_type_id=1 and DATE(offer_from_date) <=CURDATE() and buy_count < quantity and ((buy_count/quantity)*100)>=$perc";
                $membership_hot_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and id in(".implode(',',$offers_ids).") and offer_type_id=3 and DATE(offer_from_date) <=CURDATE() and buy_count < quantity and ((buy_count/quantity)*100)>=$perc";
                
                $payment_last_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and DATE(offer_to_date)=CURDATE() and id in(".implode(',',$offers_ids).") and offer_type_id=2";
                $menu_last_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and DATE(offer_to_date)=CURDATE() and id in(".implode(',',$offers_ids).") and offer_type_id=1";
                $membership_last_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and DATE(offer_to_date)=CURDATE() and id in(".implode(',',$offers_ids).") and offer_type_id=3";
                
                $payment_special_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and DATE(offer_from_date) between '".$start_date."' AND '".$end_date."' and id in(".implode(',',$offers_ids).") and offer_type_id=2 and is_special=1";
                $menu_special_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and DATE(offer_from_date) between '".$start_date."' AND '".$end_date."' and id in(".implode(',',$offers_ids).") and offer_type_id=1 and is_special=1";
                $membership_special_promo_sql = "select * from offers where merchant_id='".$merchant_id."' and DATE(offer_from_date) between '".$start_date."' AND '".$end_date."' and id in(".implode(',',$offers_ids).") and offer_type_id=3 and is_special=1";
                
                $payment_promo_view_sql = "select * from promo_visit where DATE(created_date) between '".$start_date."' AND '".$end_date."' and merchant_id='".$merchant_id."'";                
                $menu_promo_view_sql = "select * from promo_visit where DATE(created_date) between '".$start_date."' AND '".$end_date."' and merchant_id='".$merchant_id."'";
                $membership_promo_view_sql = "select * from promo_visit where DATE(created_date) between '".$start_date."' AND '".$end_date."' and merchant_id='".$merchant_id."'";

		$payment_will_promo_res = findByQuery($payment_will_promo_sql);
		$menu_will_promo_res = findByQuery($menu_will_promo_sql);
		$membership_will_promo_res = findByQuery($membership_will_promo_sql);

		$payment_new_promo_res = findByQuery($payment_new_promo_sql);
		$menu_new_promo_res = findByQuery($menu_new_promo_sql);
		$membership_new_promo_res = findByQuery($membership_new_promo_sql);
                
                $payment_hot_promo_res = findByQuery($payment_hot_promo_sql);
		$menu_hot_promo_res = findByQuery($menu_hot_promo_sql);
		$membership_hot_promo_res = findByQuery($membership_hot_promo_sql);
                
                $payment_last_promo_res = findByQuery($payment_last_promo_sql);
		$menu_last_promo_res = findByQuery($menu_last_promo_sql);
		$membership_last_promo_res = findByQuery($membership_last_promo_sql);
                
                $payment_special_promo_res = findByQuery($payment_special_promo_sql);
		$menu_special_promo_res = findByQuery($menu_special_promo_sql);
		$membership_special_promo_res = findByQuery($membership_special_promo_sql);
                
                $payment_promo_view_res = findByQuery($payment_promo_view_sql);
		$menu_promo_view_res = findByQuery($menu_promo_view_sql);
		$membership_promo_view_res = findByQuery($membership_promo_view_sql);
                
                if(!empty($payment_will_promo_res)){
                    if($type == 'unit'){
                        $willActivePaymentOffer = count($payment_will_promo_res);                        
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($payment_will_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $willActivePaymentOffer = $cntt;                       
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($payment_will_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $willActivePaymentOffer = $cntt;                       
                    }
                }
                
                if(!empty($menu_will_promo_res)){
                    if($type == 'unit'){
                        $willActiveMenuOffer = count($menu_will_promo_res);                        
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($menu_will_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $willActiveMenuOffer = $cntt;                       
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($menu_will_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $willActiveMenuOffer = $cntt;                       
                    }
                }
                
                if(!empty($membership_will_promo_res)){
                    if($type == 'unit'){
                        $willActiveMembershipOffer = count($membership_will_promo_res);                        
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($membership_will_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $willActiveMembershipOffer = $cntt;                       
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($membership_will_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $willActiveMembershipOffer = $cntt;                       
                    }
                }
                
                if(!empty($payment_new_promo_res)){
                    if($type == 'unit'){
                        $newPaymentOffer = count($payment_new_promo_res);                        
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($payment_new_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $newPaymentOffer = $cntt;                       
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($payment_new_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $newPaymentOffer = $cntt;                       
                    }
                }
                
                if(!empty($menu_new_promo_res)){
                    if($type == 'unit'){
                        $newMenuOffer = count($menu_new_promo_res);                        
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($menu_new_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $newMenuOffer = $cntt;                       
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($menu_new_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $newMenuOffer = $cntt;                       
                    }
                }
                
                if(!empty($membership_new_promo_res)){
                    if($type == 'unit'){
                        $newMembershipOffer = count($membership_new_promo_res);                        
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($membership_new_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $newMembershipOffer = $cntt;                       
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($membership_new_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $newMembershipOffer = $cntt;                       
                    }
                }
                
                if(!empty($payment_hot_promo_res)){
                    if($type == 'unit'){
                        $hotPaymentOffer = count($payment_hot_promo_res);                        
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($payment_hot_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $hotPaymentOffer = $cntt;                       
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($payment_hot_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $hotPaymentOffer = $cntt;                       
                    }
                }
                
                if(!empty($menu_hot_promo_res)){
                    if($type == 'unit'){
                        $hotMenuOffer = count($menu_hot_promo_res);                        
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($menu_hot_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $hotMenuOffer = $cntt;                       
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($menu_hot_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $hotMenuOffer = $cntt;                       
                    }
                }
                if(!empty($membership_hot_promo_res)){
                    if($type == 'unit'){
                        $hotMembershipOffer = count($membership_hot_promo_res);                        
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($menu_hot_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $hotMembershipOffer = $cntt;                       
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($membership_hot_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $hotMembershipOffer = $cntt;                       
                    }
                }
                if(!empty($payment_last_promo_res)){
                    if($type == 'unit'){
                        $lastdayPaymentOffer = count($payment_last_promo_res);                        
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($payment_last_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $lastdayPaymentOffer = $cntt;                       
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($payment_last_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $lastdayPaymentOffer = $cntt;                       
                    }
                }
                if(!empty($menu_last_promo_res)){
                    if($type == 'unit'){
                        $lastdayMenuOffer = count($menu_last_promo_res);                        
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($menu_last_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $lastdayMenuOffer = $cntt;                       
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($menu_last_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $lastdayMenuOffer = $cntt;                       
                    }
                }
                
                if(!empty($membership_last_promo_res)){
                    if($type == 'unit'){
                        $lastdayMembershipOffer = count($membership_last_promo_res);                        
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($membership_last_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $lastdayMembershipOffer = $cntt;                       
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($membership_last_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $lastdayMembershipOffer = $cntt;                       
                    }
                }
                
                if(!empty($payment_special_promo_res)){
                    if($type == 'unit'){
                        $specialPaymentOffer = count($payment_special_promo_res);                        
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($payment_special_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $specialPaymentOffer = $cntt;                       
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($payment_special_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $specialPaymentOffer = $cntt;                       
                    }
                }
                if(!empty($menu_special_promo_res)){
                    if($type == 'unit'){
                        $specialMenuOffer = count($menu_special_promo_res);                        
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($menu_special_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $specialMenuOffer = $cntt;                       
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($menu_special_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $specialMenuOffer = $cntt;                       
                    }
                }
                
                if(!empty($membership_special_promo_res)){
                    if($type == 'unit'){
                        $specialMembershipOffer = count($membership_special_promo_res);                        
                    }elseif($type == 'quantity'){
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($membership_special_promo_res as $key=>$value){
                            $cntr = $cntr+$value['buy_count'];
                            $cntt = $cntt+$value['quantity'];
                            $available = 0;
                            $available = $value['quantity'] - $value['buy_count'];
                            $cnta = $cnta+$available;
                            
                        }
                        $specialMembershipOffer = $cntt;                       
                    }else{
                        $cntr = 0;
                        $cnta = 0;
                        $cntt = 0;
                        foreach($membership_special_promo_res as $key=>$value){
                            $cntr = $cntr+($value['offer_price']*$value['buy_count']);
                            $cntt = $cntt+($value['offer_price']*$value['quantity']);
                            $available = 0;
                            $available = ($value['quantity'] - $value['buy_count'])*$value['offer_price'];
                            $cnta = $cnta+$available;
                            
                        }
                        $specialMembershipOffer = $cntt;                       
                    }
                }
                
                if(!empty($payment_promo_view_res)){
                    if($type == 'unit'){
                        $viewPaymentOffer = count($payment_promo_view_res);                        
                    }elseif($type == 'quantity'){
                        
                        $viewPaymentOffer = count($payment_promo_view_res);;                       
                    }else{
                        
                        $viewPaymentOffer = count($payment_promo_view_res);;                       
                    }
                }
                
                if(!empty($menu_promo_view_res)){
                    if($type == 'unit'){
                        $viewMenuOffer = count($menu_promo_view_res);                        
                    }elseif($type == 'quantity'){
                        
                        $viewMenuOffer = count($menu_promo_view_res);;                       
                    }else{                        
                        $viewMenuOffer = count($menu_promo_view_res);;                       
                    }
                }
                if(!empty($membership_promo_view_res)){
                    if($type == 'unit'){
                        $viewMembershipOffer = count($membership_promo_view_res);                        
                    }elseif($type == 'quantity'){
                        
                        $viewMembershipOffer = count($membership_promo_view_res);;                       
                    }else{                        
                        $viewMembershipOffer = count($membership_promo_view_res);;                       
                    }
                }
                if(!empty($total_res_ids)){
                    $will_active_add_sql = "select * from advertisements where merchant_id='".$merchant_id."' and restaurant_id in(".implode(',',$total_res_ids).") and DATE(start_date)>CURDATE() and is_active=1";
                    
                    $active_add_sql = "select * from advertisements where merchant_id='".$merchant_id."' and restaurant_id in(".implode(',',$total_res_ids).") and DATE(start_date) between '".$start_date."' AND '".$end_date."' and is_active=1";
                    
                    $will_active_banner_sql = "select * from banners where merchant_id='".$merchant_id."' and restaurant_id in(".implode(',',$total_res_ids).") and DATE(start_date)>CURDATE() and is_active=1";
                    
                    $active_banner_sql = "select * from banners where merchant_id='".$merchant_id."' and restaurant_id in(".implode(',',$total_res_ids).") and DATE(start_date) between '".$start_date."' AND '".$end_date."' and is_active=1";
                    
                    $will_active_news_sql = "select * from news where user_id='".$merchant_id."' and DATE(published_date)>CURDATE()";
                    
                    $active_news_sql = "select * from news where user_id='".$merchant_id."' and DATE(published_date) between '".$start_date."' AND '".$end_date."'";
                    
                    $will_active_add_res = findByQuery($will_active_add_sql);
                    $active_add_res = findByQuery($active_add_sql);
                    $will_active_banner_res = findByQuery($will_active_banner_sql);
                    $active_banner_res = findByQuery($active_banner_sql);
                    $will_active_news_res = findByQuery($will_active_news_sql);
                    $active_news_res = findByQuery($active_news_sql);
                    
                    if(!empty($will_active_add_res)){                        
                        $willActiveAddv = count($will_active_add_res);                       
                        
                    }
                    if(!empty($will_active_banner_res)){                        
                        $willActiveBanner = count($will_active_banner_res);                      
                        
                    }
                    if(!empty($will_active_news_res)){                        
                        $willActiveNews = count($will_active_news_res);                      
                        
                    }
                    if(!empty($active_news_res)){                        
                        $activeNews = count($active_news_res); 
                        $targetNews = count($active_news_res);
                        $actualNews = count($active_news_res);
                        $remainingNews = count($active_news_res);
                        $exceedNews = count($active_news_res);
                        
                    }
                    if(!empty($active_add_res)){                      
                        $activeAddv = count($active_add_res);                   

                        $cntt = 0;
                        $cnta = 0;
                        $cntr = 0;
                        $cnte = 0;
                        foreach($active_add_res as $key=>$value){
                            $cntt = $cntt+$value['target_click'];
                            $cnta = $cnta+$value['number_of_click'];
                            if($value['target_click']>$value['number_of_click']){
                                $cntr = $cntr+($value['target_click'] - $value['number_of_click']);
                            }else{
                                $cnte = $cnte+($value['number_of_click'] - $value['target_click']);
                            }
                        }
                        $targetAddv = $cntt;
                        $actualAddv = $cnta;
                        $remainingAddv = $cntr;
                        $exceedAddv = $cnte;
                        
                    }
                    if(!empty($active_banner_res)){                      
                        $activeBanner = count($active_banner_res);                   

                        $cntt = 0;
                        $cnta = 0;
                        $cntr = 0;
                        $cnte = 0;
                        foreach($active_banner_res as $key=>$value){
                            $cntt = $cntt+$value['target_click'];
                            $cnta = $cnta+$value['number_of_click'];
                            if($value['target_click']>$value['number_of_click']){
                                $cntr = $cntr+($value['target_click'] - $value['number_of_click']);
                            }else{
                                $cnte = $cnte+($value['number_of_click'] - $value['target_click']);
                            }
                        }
                        $targetBanner = $cntt;
                        $actualBanner = $cnta;
                        $remainingBanner = $cntr;
                        $exceedBanner = $cnte;
                        
                    }
                    
                }
                
                
                
                

		
	}

	$available_event_sql = "select * from events where status='O' and DATE(offer_from_date) between '".$start_date."' AND '".$end_date."' and is_active=1";
	$available_event_res = findByQuery($available_event_sql);
	$events_ids = array_column($available_event_res, 'id');
	$availableEvent = count($available_event_res);
	//print_r($events_ids);
	//print_r($available_event_res);

	$deal_event_sql = "select * from events where status='C' and DATE(offer_from_date) between '".$start_date."' AND '".$end_date."' and is_active=1";
	$deal_event_res = findByQuery($deal_event_sql);
	$dealEvent = count($deal_event_res);
	//print_r($deal_event_res);
	if(!empty($events_ids)){
		$open_event_sql = "select * from event_bids where event_id NOT in(".implode(',',$events_ids).") and is_accepted = 0";
		$open_event_res = findByQuery($open_event_sql);
		$openEvent = count($open_event_res);
		//print_r($open_event_res);
	
		$feedback_event_sql = "select * from event_bids where event_id in(".implode(',',$events_ids).") and is_accepted = 0 and user_id != '".$merchant_id."'";
		$feedback_event_res = findByQuery($feedback_event_sql);
		$feedbackEvent = count($feedback_event_res);
	}
	$events=array();
	$merchant_category = findByConditionArray(array('user_id' => $merchant_id),'merchant_category_map');
	$category_ids = array_column($merchant_category, 'category_id');

	$merchant_location = findByConditionArray(array('user_id' => $merchant_id),'merchant_location_map');
	$location_ids = array_column($merchant_location, 'location_id');
	

	$sql = "SELECT * FROM event_location_map WHERE location_id in(".implode(',',$location_ids).")";
        
        $data = findByQuery($sql); 
        if(!empty($data))
        {
            $events = array_column($data, 'event_id');
        }
        
        
        $sql = "SELECT * FROM event_category_map WHERE category_id in(".implode(',',$category_ids).")";
        
        $cat_data = findByQuery($sql);        
        if(!empty($cat_data))
        {
            $cat_events = array_column($cat_data, 'event_id');
            if(!empty($cat_events))
            {
                foreach($cat_events as $ta)
                {
                    $events[] = $ta;
                }
            }
        }
        
	$events = array_values(array_unique($events));
	if(!empty($events)){
		$my_event_sql = "select * from events where DATE(offer_from_date) between '".$start_date."' AND '".$end_date."' and is_active=1 and id in(".implode(',',$events).")";
		$my_event_res = findByQuery($my_event_sql);
		$myeventsid = array_column($my_event_res, 'id');
		$myEvent = count($my_event_res);
		if(!empty($myeventsid)){
			$offer_event_sql = "select * from event_bids where event_id in(".implode(',',$myeventsid).") and user_id = '".$merchant_id."'";
			$offer_event_res = findByQuery($offer_event_sql);
			$myOfferEvent = count($offer_event_res);
		}
	}
        $cur_month = date('m');
        $next_month = $cur_month+1;
        $cur_year = date('Y');
	$point_master_sql = "select * from point_master where user_id='".$merchant_id."' and DATE(start_date) between '".$start_date."' AND '".$end_date."'";
        
        $exp_master_sql = "select * from point_master where user_id='".$merchant_id."' and DATE(expire_date) < CURDATE()";
        $next_month_exp_master_sql = "select * from point_master where user_id='".$merchant_id."' and MONTH(expire_date)= '".$next_month."'";
        
        $cur_year_exp_master_sql = "select * from point_master where user_id='".$merchant_id."' and YEAR(expire_date)= '".$cur_year."'";
	$point_master_res = findByQuery($point_master_sql);
        $exp_master_res = findByQuery($exp_master_sql);
        $next_month_exp_master_res = findByQuery($next_month_exp_master_sql);
        $cur_year_exp_master_res = findByQuery($cur_year_exp_master_sql);
        
	$pointsid = array_column($point_master_res, 'id');
        $exppointsid = array_column($exp_master_res, 'id');
        $nextmonthpointsid = array_column($next_month_exp_master_res, 'id');
        $curyearpointsid = array_column($cur_year_exp_master_res, 'id');
        
	//print_r($pointsid);
        //print_r($exppointsid);
        //print_r($nextmonthpointsid);
        //print_r($curyearpointsid);
        if(!empty($pointsid)){
            $generated_mpoint_sql = "select sum(points) as generated,sum(redeemed_points) as redeem,sum(remaining_points) as balance from points where merchant_id='".$merchant_id."' and point_id in(".implode(',',$pointsid).")";
            $generated_mpoint_res = findByQuery($generated_mpoint_sql);
            //print_r($generated_mpoint_res);
            if(!empty($generated_mpoint_res)){
                $generatedPoint = $generated_mpoint_res[0]['generated'];
                $redeemPoint = $generated_mpoint_res[0]['redeem'];
                $balancedPoint = $generated_mpoint_res[0]['balance'];
                //echo $generatedPoint;
            }
            
        }
        if(!empty($exppointsid)){
            $expired_mpoint_sql = "select sum(remaining_points) as balance from points where merchant_id='".$merchant_id."' and point_id in(".implode(',',$exppointsid).")";
            $expired_mpoint_res = findByQuery($expired_mpoint_sql);
            //print_r($expired_mpoint_res);
            if(!empty($expired_mpoint_res)){                
                $expiredPoint = $expired_mpoint_res[0]['balance'];
                //echo $expiredPoint;
            }
            
        }
        if(!empty($nextmonthpointsid)){
            $nextmonth_mpoint_sql = "select sum(remaining_points) as balance from points where merchant_id='".$merchant_id."' and point_id in(".implode(',',$nextmonthpointsid).")";
            $nextmonth_mpoint_res = findByQuery($nextmonth_mpoint_sql);
            //print_r($nextmonth_mpoint_res);
            if(!empty($nextmonth_mpoint_res)){                
                $nextMonthExpPoint = $nextmonth_mpoint_res[0]['balance'];
                //echo $expiredPoint;
            }
            
        }
        if(!empty($curyearpointsid)){
            $curyear_mpoint_sql = "select sum(remaining_points) as balance from points where merchant_id='".$merchant_id."' and point_id in(".implode(',',$curyearpointsid).")";
            $curyear_mpoint_res = findByQuery($curyear_mpoint_sql);
            //print_r($curyear_mpoint_res);
            if(!empty($curyear_mpoint_res)){                
                $thisYearExpPoint = $curyear_mpoint_res[0]['balance'];
                //echo $expiredPoint;
            }
            
        }
        //exit;

	$data=array('allMenuOffer'=>$allMenuOffer,'allMembershipOffer'=>$allMembershipOffer,'allPaymentOffer'=>$allPaymentOffer,'allRedeemPaymentOffer'=>$allRedeemPaymentOffer,'allRedeemMenuOffer'=>$allRedeemMenuOffer,'allRedeemMembershipOffer'=>$allRedeemMembershipOffer,'allAvailablePaymentOffer'=>$allAvailablePaymentOffer,'allAvailableMenuOffer'=>$allAvailableMenuOffer,'allAvailableMembershipOffer'=>$allAvailableMembershipOffer,'offerPaymentOffer'=>$offerPaymentOffer,'offerMenuOffer'=>$offerMenuOffer,'offerMembershipOffer'=>$offerMembershipOffer,'redeemPaymentOffer'=>$redeemPaymentOffer,'redeemMenuOffer'=>$redeemMenuOffer,'redeemMembershipOffer'=>$redeemMembershipOffer,'availablePaymentOffer'=>$availablePaymentOffer,'availableMenuOffer'=>$availableMenuOffer,'availableMembershipOffer'=>$availableMembershipOffer,'willActivePaymentOffer'=>$willActivePaymentOffer,'willActiveMenuOffer'=>$willActiveMenuOffer,'willActiveMembershipOffer'=>$willActiveMembershipOffer,'newPaymentOffer'=>$newPaymentOffer,'newMenuOffer'=>$newMenuOffer,'newMembershipOffer'=>$newMembershipOffer,'newMembershipOffer'=>$newMembershipOffer,'hotPaymentOffer'=>$hotPaymentOffer,'hotMenuOffer'=>$hotMenuOffer,'hotMembershipOffer'=>$hotMembershipOffer,'lastdayPaymentOffer'=>$lastdayPaymentOffer,'lastdayMenuOffer'=>$lastdayMenuOffer,'lastdayMembershipOffer'=>$lastdayMembershipOffer,'specialPaymentOffer'=>$specialPaymentOffer,'specialMenuOffer'=>$specialMenuOffer,'specialMembershipOffer'=>$specialMembershipOffer,'viewPaymentOffer'=>$viewPaymentOffer,'viewMenuOffer'=>$viewMenuOffer,'viewMembershipOffer'=>$viewMembershipOffer,'willActiveNews'=>$willActiveNews,'willActiveAddv'=>$willActiveAddv,'willActiveBanner'=>$willActiveBanner,'activeNews'=>$activeNews,'activeAddv'=>$activeAddv,'activeBanner'=>$activeBanner,'targetNews'=>$targetNews,'targetAddv'=>$targetAddv,'targetBanner'=>$targetBanner,'actualNews'=>$allMenuOffer,'allMenuOffer'=>$actualNews,'actualAddv'=>$actualAddv,'actualBanner'=>$actualBanner,'remainingNews'=>$remainingNews,'remainingAddv'=>$remainingAddv,'remainingBanner'=>$remainingBanner,'exceedNews'=>$exceedNews,'exceedAddv'=>$exceedAddv,'exceedBanner'=>$exceedBanner,'availableEvent'=>$availableEvent,'dealEvent'=>$dealEvent,'openEvent'=>$openEvent,'feedbackEvent'=>$feedbackEvent,'myEvent'=>$myEvent,'myOfferEvent'=>$myOfferEvent,'generatedPoint'=>$generatedPoint,'redeemPoint'=>$redeemPoint,'expiredPoint'=>$expiredPoint,'balancedPoint'=>$balancedPoint,'nextMonthExpPoint'=>$nextMonthExpPoint,'thisYearExpPoint'=>$thisYearExpPoint);
        $rarray = array('type' => 'success', 'data' => $data);
        echo json_encode($rarray);

    }
   

?>

<?php
/*
 * $keyword : Search Keywords
 * $category :-(Default = promo)
 *      event - Search from events table
 *      promo - Search from Offers table
 *      news - Search from news table
 *      restaurant - Search from restaurants table
 * 
 * $page :- Page no (Default value = 1)
 * $lines :- No of lines per page (Default = 20)
 * $sort :- sort filed name (defaults = id)  
 * $sort_by :- Sort by ASC or DESC (default = DESC)
 */
function getSiteSearch()//$keyword,$category,$page=1
{
    $keyword = (!empty($_GET['keyword'])?$_GET['keyword']:'');
    $category = (!empty($_GET['category']) && $_GET['category']!='undefined'?$_GET['category']:'promo');
    $page = (!empty($_GET['page'])?$_GET['page']:1);
    $lines = (!empty($_GET['lines'])?$_GET['lines']:20);
    $sort = (!empty($_GET['sort'])?$_GET['sort']:'id');
    $sort_by = (!empty($_GET['sort_by'])?$_GET['sort_by']:'DESC');
    if(!empty($keyword) && !empty($category))
    {       
        $keyword = addslashes($keyword);
        $num_rec_per_page = $lines;
        $start_from = ($page-1) * $num_rec_per_page; 
        $limit = " LIMIT $start_from, $num_rec_per_page";
        $order = " ORDER BY ".$sort." ".$sort_by;
        $db = getConnection();
        if($category=='promo')
        {
            $sql = "SELECT * FROM offers WHERE is_active=1 and (title LIKE '%$keyword%' OR description LIKE '%$keyword%'OR benefits LIKE '%$keyword%')".$order.$limit;
            $stmt = $db->prepare($sql);  
            #$stmt->bindParam("like", "%$keyword%");
            $stmt->execute();			
            $rarray = $stmt->fetchAll();
            $db = null;
            $rarray = array_map(function($t){
                if(!empty($t['image']))
                    $t['image_url'] = SITEURL.'voucher_images/'.$t['image'];
                
                return $t;
            },$rarray);
        }
        else if($category=='location')
        {
            $query = "SELECT * FROM locations where city LIKE '%$keyword%' and is_active=1";
            $locations = findByQuery($query);
            $location_ids='';
            $outlet_ids = '';
            $offer_ids = '';
            if(!empty($locations))
            {
                $location_ids = implode(',',  array_column($locations, 'id'));
                $outlet_query = "SELECT * FROM outlet_location_map WHERE location_id in($location_ids)";
                $outlets = findByQuery($outlet_query);    
                if(!empty($outlets))
                {
                    $outlet_ids = implode(',',array_column($outlets,'outlet_id'));
                }
                if(!empty($outlet_ids))
                {
                    $offers_query = "SELECT * FROM offer_outlet_map WHERE outlet_id in($outlet_ids)";
                    $offers = findByQuery($offers_query);
                    if(!empty($offers))
                    {
                        $offer_ids = implode(',',  array_column($offers, 'offer_id'));
                    }
                }
            }
            
            if(!empty($offer_ids))
            {
                $sql = "SELECT * FROM offers WHERE is_active=1 and id in($offer_ids) ".$order.$limit;
                $stmt = $db->prepare($sql);  
                #$stmt->bindParam("like", "%$keyword%");
                $stmt->execute();			
                $rarray = $stmt->fetchAll();
                $db = null;
                $rarray = array_map(function($t){
                    if(!empty($t['image']))
                        $t['image_url'] = SITEURL.'voucher_images/'.$t['image'];

                    return $t;
                },$rarray);
            }
            else
            {
                $rarray = array();
            }
        }
        else if($category == 'event')
        {
            $sql = "SELECT * FROM events WHERE is_active=1 and status='O' and (title LIKE '%$keyword%' OR description LIKE '%$keyword%')".$order.$limit;
            
            $stmt = $db->prepare($sql);  
            #$stmt->bindParam("like", "%$keyword%");
            $stmt->execute();			
            $rarray = $stmt->fetchAll();
            $db = null;   
            $rarray = array_map(function($t){
                if(!empty($t['image']))
                    $t['image_url'] = SITEURL.'event_images/'.$t['image'];
                
                return $t;
            },$rarray);
        }
        else if($category == 'news')
        {
            $sql = "SELECT * FROM news WHERE is_active=1 and (title LIKE '%$keyword%' OR description LIKE '%$keyword%')".$order.$limit;
            
            $stmt = $db->prepare($sql);  
            #$stmt->bindParam("like", "%$keyword%");
            $stmt->execute();			
            $rarray = $stmt->fetchAll();
            $db = null;  
            $rarray = array_map(function($t){
                if(!empty($t['image']))
                    $t['image_url'] = SITEURL.'news_images/'.$t['image'];
                
                return $t;
            },$rarray);
        }
        else if($category == 'restaurant')
        {
            $sql = "SELECT * FROM restaurants WHERE is_active=1 and (title LIKE '%$keyword%' OR address LIKE '%$keyword%')".$order.$limit;
            
            $stmt = $db->prepare($sql);  
            #$stmt->bindParam("like", "%$keyword%");
            $stmt->execute();			
            $rarray = $stmt->fetchAll();
            $db = null;     
            $rarray = array_map(function($t){
                if(!empty($t['logo']))
                    $t['logo_url'] = SITEURL.'restaurant_images/'.$t['logo'];
                
                return $t;
            },$rarray);
        }
        $rarray = array('type' => 'success', 'data' => $rarray, 'category' => $category);
    }
    else 
    {
        $rarray = array('type' => 'error', 'message' => 'No match found');
    }
    echo json_encode($rarray);
    exit;
}
?>
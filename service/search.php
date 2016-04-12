<?php
/*
 * $keyword : Search Keywords
 * $category :-
 *      events - Search from events table
 *      promo - Search from Offers table
 *      
 */
function getSiteSearch($keyword,$category)
{
    
    $rarray = array();
    if(!empty($keyword) && !empty($category))
    {
        echo "hi";
    exit;
        if($category=='promo')
        {
            $sql = "SELECT * FROM offers WHERE is_active=1 and (title LIKE :like OR description LIKE :like)";

            $db = getConnection();
            $stmt = $db->prepare($sql);  
            $stmt->bindParam("like", "%$keyword%");
            $stmt->execute();			
            $rarray = $stmt->fetchAll();
            $db = null;
            $rarray = array('type' => 'success', 'data' => $rarray, 'category' => $category);
        }
    }
    else 
    {
        $rarray = array('type' => 'error', 'message' => 'Merchant not found.');
    }
    echo json_encode($rarray);
    exit;
}
?>
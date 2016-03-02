<?php
function getMyPoints($user_id){
    $rarray = array();
    try
    {
        $sql = "SELECT * FROM points WHERE user_id=:user_id";
        $db = getConnection();
        $stmt = $db->prepare($sql); 
        $stmt->bindParam("user_id", $user_id);
        $stmt->execute();
        $points = $stmt->fetchAll(PDO::FETCH_OBJ); 
        $i=1;
        if(!empty($points))
        {
            $points = array_map(function($t) use($i){
                $t->sl = $i++;
                $t->type = ($t->type=='C'?'Credit':'Debit');
                $t->date = date('m/d/Y',  strtotime($t->date));
                $t->expire_date = date('m/d/Y',  strtotime($t->expire_date));
                return $t;
            }, $points);
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

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
        if(!empty($points))
        {
            $points = array_map(function($t,$k){
                $t->sl = $k+1;
                $t->type = ($t->type=='C'?'Credit':'Debit');
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
        $i=1;
        if(!empty($points))
        {
            $points = array_map(function($t,$k) use($i){
                $t->sl = $k+1;
                $t->type = ($t->type=='C'?'Credit':'Debit');
                $t->date = date('m/d/Y',  strtotime($t->date));
                $t->expire_date = date('m/d/Y',  strtotime($t->expire_date));
                return $t;
            }, $points,array_keys($points));
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

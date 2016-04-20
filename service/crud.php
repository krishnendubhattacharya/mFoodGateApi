<?php
function getAll($table) {
	$sql = "select * FROM ".$table." ORDER BY id desc";
	
	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$users = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		return '{"'.$table.'": ' . json_encode($users) . '}';
	} catch(PDOException $e) {
		return '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}
function findById($id,$table) {
	$sql = "SELECT * FROM ".$table." WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->execute();			
		$user = $stmt->fetchObject();  
		$db = null;
		return json_encode($user); 
	} catch(PDOException $e) {
		return '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function findByIdArray($id,$table) {
    $rarray = array();
    $sql = "SELECT * FROM ".$table." WHERE id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);  
        $stmt->bindParam("id", $id);
        $stmt->execute();			
        $rarray = $stmt->fetch(PDO::FETCH_ASSOC);
        $db = null;
        return $rarray; 
    } catch(PDOException $e) {
        return $rarray; 
    }
}

function findByConditionArray($conditions,$table) {
        $rarray = array();
	$sql = "SELECT * FROM ".$table;
	try {
            if(!empty($conditions)) 
            {
                $sql .= " WHERE ";
                foreach ($conditions as $key=>$condition)
                {
                    $sql .= "$key=:$key ";
                    $$key = $condition;
                }
            }
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $s = json_decode(json_encode($conditions));
            if(!empty($s))
            {
                foreach ($s as $key => $t) 
                {
                    //echo $t.$key;
                    $stmt->bindValue($key, $s->$key);
                    //$sql .= "$key=:$key ";
                }
            }
            //exit;

            $stmt->execute();			
            $rarray = $stmt->fetchAll();
            $db = null;
            return $rarray; 
	} catch(PDOException $e) {
		return $rarray; 
	}
}

/*
 * $sql : Sql query with conditions
 * $type : all - find multiple row
 *         one - find one row
 */
function findByQuery($sql,$type='all')
{
    $rarray = array();
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();	
        if($type=='one')
            $rarray = $stmt->fetch(PDO::FETCH_ASSOC);
        else
            $rarray = $stmt->fetchAll();
        $db = null;
        return $rarray; 
    } catch(PDOException $e) {
	return $rarray; 
    }
}

function findByCondition($conditions,$table) {
	$sql = "SELECT * FROM ".$table;
	try {
            if(!empty($conditions)) 
            {
                $sql .= " WHERE ";
                foreach ($conditions as $key=>$condition)
                {
                    $sql .= "$key=:$key ";
                }
            }
            $db = getConnection();
            $stmt = $db->prepare($sql);  
            if(!empty($conditions))
            {
                foreach ($conditions as $key=>$condition)
                {
                    $stmt->bindParam("$key", $condition);
                    //$sql .= "$key=:$key ";
                }
            }

            $stmt->execute();			
            $user = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            return json_encode($user); 
	} catch(PDOException $e) {
		return '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function rowCount($uniqueData,$tableName) {
        $uniqueData = json_decode($uniqueData);
        $queryField='';
        $aliseField='';
        $db = getConnection();
	$sql = "SELECT * FROM ".$tableName." WHERE ";
	$i=0;
        foreach($uniqueData as $key=>$value){
                if($i ==0){
                        $sql .= $key."=:".$key;
                }else{
                        $sql .= ' or '.$key."=:".$key;
                }
                $i++;                
        }
	$stmt = $db->prepare($sql);
	foreach($uniqueData as $key=>$value){
	        $stmt->bindParam($key, $value);
	}		
	$stmt->execute();	
	$userCount = $stmt->rowCount();
	$stmt=null;
	$db=null;
	
	return $userCount;
}

function rowCountByCondition($conditions,$table) {
        $sql = "SELECT * FROM ".$table;
	try {
            if(!empty($conditions)) 
            {
                $sql .= " WHERE ";
                foreach ($conditions as $key=>$condition)
                {
                    $sql .= "LOWER($key)=:$key ";
                }
            }
            $db = getConnection();
            $stmt = $db->prepare($sql);  
            if(!empty($conditions))
            {
                foreach ($conditions as $key=>$condition)
                {
                    $stmt->bindParam("$key", strtolower($condition));
                    //$sql .= "$key=:$key ";
                }
            }

            $stmt->execute();			
            $userCount = $stmt->rowCount();
            $db = null;
            return $userCount; 
	} catch(PDOException $e) {
		return '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function add($data,$table) {
        $data = json_decode($data);       
        
        $queryField='';
        $aliseField='';
        $rowCount = 0;
        if(!empty($data->unique_data)){
               //$rowCount = rowCount(json_encode($data->unique_data),$table);               
        }
        if(!empty($data->save_data)){
	    
                foreach($data->save_data as $key=>$value){
                        if(empty($queryField)){
                                $queryField = $key;
                        }else{
                                $queryField = $queryField.', '.$key;
                        }
                        if(empty($aliseField)){
                                $aliseField = ':'.$key;
			}
			else{
                                $aliseField = $aliseField.', :'.$key;
			}
			
                }
                /*if($rowCount == 0){
                        $sql='';*/
                        $sql = "INSERT INTO ".$table."(".$queryField.") VALUES (".$aliseField.")";
                        try {		
		                $db = getConnection();
		                $stmt = $db->prepare($sql);
		                
		                foreach($data->save_data as $key=>$value){
					 $stmt->bindParam($key, $data->save_data->$key);	                        
		                        
		                }		               
		               	
		                //$stmt = $db->prepare("INSERT INTO users(first_name, last_name, merchant_name, email, username, password, registration_date, user_type_id) values('".$user->first_name."','".$user->last_name."','".$user->merchant_name."','".$user->email."','".$user->username."','".$user->password."','2016-02-1', 2)");           
                                          
		
		                $stmt->execute();
		                $data->save_data->id = $db->lastInsertId();
		                //sendMail('nits.sandeeptewary@gmail.com',$data->save_data->email,'Welcome to mFoodGate','Hii');
		                $db = null;
		                return json_encode($data->save_data); 
	                }
	                catch(PDOException $e) {
		                //error_log($e->getMessage(), 3, '/var/tmp/php.log');
		                return '{"error":{"text":'. $e->getMessage() .'}}'; 
	                }
                        
               /* }else{
                        return '{"error":{"text":"already exist"}}'; 
                }*/
        }
}
function edit($data,$table,$id) {
        $data = json_decode($data); 
        
        if(!empty($data->save_data)){
                $sql = "UPDATE ".$table." SET ";
                $updatequery = '';
                foreach($data->save_data as $key=>$value){
                        if(empty($updatequery)){
                                $updatequery = $key."=:".$key;
                        }else{
                                $updatequery .= ", ".$key."=:".$key;
                        }                        
                }
                $sql .= $updatequery." WHERE id=:id";
        
			   try {
		        $db = getConnection();
		        $stmt = $db->prepare($sql); 
		        foreach($data->save_data as $key=>$value){
		                $stmt->bindParam($key, $data->save_data->$key);
		        }			
		        $stmt->bindParam("id", $id);		
		        $stmt->execute();
		        $data->save_data->id=$id;
		        $db = null;
		        return json_encode($data->save_data); 
	        }
	        catch(PDOException $e) {
		        return '{"error":{"text":'. $e->getMessage() .'}}'; 
	        }
        }
}

function editByField($data,$table,$conditions) {
        $data = json_decode($data); 
       
        if(!empty($data->save_data)){
                $sql = "UPDATE ".$table." SET ";
                $updatequery = '';
                foreach($data->save_data as $key=>$value){
                        if(empty($updatequery)){
                                $updatequery = $key."=:".$key;
                        }else{
                                $updatequery .= ", ".$key."=:".$key;
                        }                        
                }
                $sql .= $updatequery." WHERE ";
                foreach($conditions as $key=>$condition)
                {
                    $sql .= "$key=:$key";
                }
                
                try {
		        $db = getConnection();
		        $stmt = $db->prepare($sql); 
		        foreach($data->save_data as $key=>$value){
		                $stmt->bindParam($key, $data->save_data->$key);
		        }
                        foreach($conditions as $key=>$condition)
                        {
                            $stmt->bindParam("$key", $condition);
                        }
		        		
		        $stmt->execute();
		        //$data->save_data->id=$id;
		        $db = null;
		        return json_encode($data->save_data); 
	        }
	        catch(PDOException $e) {
		        return '{"error":{"text":'. $e->getMessage() .'}}'; 
	        }
        }
}

function delete($table,$id) {
        $sql = "DELETE FROM ".$table." WHERE id=:id";
        try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
		return '{"success":{"text":"Deleted successfully"}}';
	} catch(PDOException $e) {
		return '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
        
}

/*
 * deleteAll - Delete multiple fields by multiple conditions
 * $table - Tablename
 * $conditions - conditions
 */
function deleteAll($table,$conditions){
    $sql = "DELETE FROM ".$table;
    try {
            if(!empty($conditions))
            {
                $sql .= " WHERE ";
                foreach($conditions as $key=>$condition)
                {
                    $sql .= "$key=:$key ";
                }
            }
            $db = getConnection();
            $stmt = $db->prepare($sql);  
            
            if(!empty($conditions))
            {
                foreach($conditions as $key=>$condition)
                {
                    $stmt->bindParam($key, $condition);
                }
            }
            $stmt->execute();
            $db = null;
            return '{"success":{"text":"Deleted successfully"}}';
    } catch(PDOException $e) {
            return '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}


/*function getlist($table,$conditions) {
        $sql = "select * FROM ".$table." where order_id=:order_id ORDER BY id desc";
	
	try {
            
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->execute();			
		$user = $stmt->fetchObject();  
		$db = null;
		return json_encode($user); 
	} catch(PDOException $e) {
		return '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}*/

function array_column($arr,$field)
{
    $temp = array();
    if(!empty($arr))
    {
       foreach($arr as $t)
       {
           if(!empty($t[$field]))
            $temp[] = $t[$field];
       }
    }
    
    return $temp;
}

?>
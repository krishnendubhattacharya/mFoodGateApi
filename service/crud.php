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

?>
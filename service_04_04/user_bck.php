<?php
function getAllUsers() {
	$sql = "select * FROM users ORDER BY id desc";
	//$request = Slim::getInstance()->request();
	//print_r($request);
	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$users = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"user": ' . json_encode($users) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}
function getUser($id) {
	$sql = "SELECT * FROM users WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->execute();			
		$user = $stmt->fetchObject();  
		$db = null;
		echo json_encode($user); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function addUser() {
	//error_log('adduser\n', 3, '/var/tmp/php.log');
	
	$request = Slim::getInstance()->request();
	
	$user = json_decode($request->getBody());
	$db = getConnection();
	$sql = "SELECT * FROM users WHERE email=:email or username=:username";
	$stmt = $db->prepare($sql);
	
	$stmt->bindParam('email', $user->email);
	$stmt->bindParam('username', $user->username);	
	$stmt->execute();	
	$userCount = $stmt->rowCount();	
	$stmt = null;	
	
	if($userCount == 0){
	
	        $sql = "INSERT INTO users(first_name, last_name, merchant_name, email, username, password, registration_date, user_type_id) VALUES (:first_name, :last_name, :merchant_name, :email, :username, :password, :registration_date, :user_type_id)";
	        try {
		
		
		        $curdate = date('Y-m-d H:i:s',time());		
		        $user->registration_date = $curdate;
		        $stmt = $db->prepare($sql);
		
		        $stmt->bindParam('first_name', $user->first_name);
		        $stmt->bindParam('last_name', $user->last_name);
		        $stmt->bindParam('merchant_name', $user->merchant_name);
		        $stmt->bindParam('email', $user->email);
		        $stmt->bindParam('username', $user->username);
		        $stmt->bindParam('password', $user->password);
		        $stmt->bindParam('registration_date', $curdate);
		        $stmt->bindParam('user_type_id', $user->user_type_id);		
		        //$stmt = $db->prepare("INSERT INTO users(first_name, last_name, merchant_name, email, username, password, registration_date, user_type_id) values('".$user->first_name."','".$user->last_name."','".$user->merchant_name."','".$user->email."','".$user->username."','".$user->password."','2016-02-1', 2)");           
                                  
		
		        $stmt->execute();
		        $user->id = $db->lastInsertId();
		        sendMail('nits.sandeeptewary@gmail.com',$user->email,'Welcome to mFoodGate','Hii');
		        $db = null;
		        echo json_encode($user); 
	        } catch(PDOException $e) {
		        error_log($e->getMessage(), 3, '/var/tmp/php.log');
		        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	        }
	}else{
	        echo '{"error":{"message":"User already exist."}}';
	}
}

function updateuser($id) {
	$request = Slim::getInstance()->request();
	
	$body = $request->getBody();
	$user = json_decode($body);
	
	$sql = "UPDATE users SET first_name=:first_name WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql); 
		
		$stmt->bindParam("first_name", $user->first_name);			
		$stmt->bindParam("id", $id);		
		$stmt->execute();
		
		$db = null;
		echo json_encode($user); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function deleteuser($id) {
	$sql = "DELETE FROM users WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}


?>

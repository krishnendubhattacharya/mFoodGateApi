<?php
function getAllUsers() {
        $result = getAll('users');
	echo $result;
}
function getUser($id) {
        $result = findById($id,'users');
        echo $result;	
}

function addUser() {	
	
	$request = Slim::getInstance()->request();
	$user = json_decode($request->getBody());
	if(isset($user->id)){
	        unset($user->id);
	}
	$unique_field = array();
	$unique_field['email']=$user->email;
	//$unique_field['username']=$user->username;
	$rowCount = rowCount(json_encode($unique_field),'users');
	if($rowCount == 0){
            $unique_code = time().rand(100000,1000000);
	    $pass = $user->password;
	    $user->txt_pwd = $pass;
	    $user->unique_code = $unique_code;
	    $user->password = md5($user->password);
	    if($user->user_type_id == 'Merchant'){
	        $user->user_type_id = 3;
	    }else{
	        $user->user_type_id = 2;
	    }
	    //$user->user_type_id = '2';
	    $user->registration_date = date('Y-m-d h:m:s');
	    $activation_link = $user->activation_url;
	    unset($user->activation_url);
	    $allinfo['save_data'] = $user;
	    //$allinfo['unique_data'] = $unique_field;
	    $user_details = add(json_encode($allinfo),'users');
	     if(!empty($user_details)){
	    $user_details = json_decode($user_details);
	    //echo  $user_details->email; exit;
	  //  print_r($user_details);exit;
	    $from = 'info@mfood.com';
	    $to = $user_details->email;
	    $subject ='User Registration';
	    $body ='<html><body><p>Dear User,</p>

		    <p>Thank You for signing up with mFoodGate.<br />
		    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Your Account Details:</span><br />
		    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif"><strong>Email: '.$user_details->email.'</strong></span><br />
		    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif"><strong>Password: '.$pass.'</strong></span></p>

		    <p><span style="color:rgb(77, 76, 76); font-family:helvetica,arial">Please <a href="'.$activation_link.'/'.$unique_code.'">Click Here</a> </span><span style="color:rgb(77, 76, 76); font-family:helvetica,arial">&nbsp;to verify your account.</span><br />
		    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">If we can help you with anything in the meantime just let us know by e-mailing&nbsp;</span>info@mfood.com<br />
		    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Thanks again for signing up with the site</span><span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">!&nbsp;</span></p>

		    <p>Thanks,<br />
		    mFood&nbsp;Team</p>

		    <p>&nbsp;</p></body></html>
		    ';
	    sendMail($from,$to,$subject,$body);
	    $result = '{"type":"success","message":"Added Succesfully"}'; 
	    }
	    else{
		 $result = '{"type":"error","message":"Try Again"}'; 
	    }
	}
	else if($rowCount > 0){
	   $result = '{"type":"error","message":"already exist"}'; 
	}
	echo $result;
	
}

function updateUser($id) {
	$request = Slim::getInstance()->request();
	
	$body = $request->getBody();
	$user = json_decode($body);
	if(isset($user->id)){
	        unset($user->id);
	}	
	$allinfo['save_data'] = $user;
	$result = edit(json_encode($allinfo),'users',$id);
	/*if(!empty($user_details)){
	        $result = '{"type":"success","message":"Updated Succesfully"}'; 
	}else{
	        $result = '{"type":"error","message":"Try Again"}'; 
	}*/
	$result = '{"type":"success","message":"Updated Succesfully"}';
	echo $result;
	
}

function deleteUser($id) {
       $result =  delete('users',$id);
       echo $result;	
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

function getlogin(){
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $is_active = 1;
    //print_r($body);exit;
	$email = $body->email;
	//$email = isset($user->email)?$user->email:$user->username;
	//echo $email;exit;
	$pass = md5($body->password);
	$result='';
	if(!empty($email) && !empty($pass)){
	    $db = getConnection();
	    $sql = "SELECT * FROM users WHERE email=:email and password=:password and is_active=:is_active";
	    $stmt = $db->prepare($sql);  
	    $stmt->bindParam("email", $email);
	    $stmt->bindParam("password", $pass);
	    $stmt->bindParam("is_active", $is_active);
	    $stmt->execute();	
	    $count = $stmt->rowCount();
	    //echo $count;exit;
	    $user = $stmt->fetchObject();
	    $db = null;
	    //print_r($user);exit;
	    if($count==0){
		$result = '{"type":"error","message":"You are Not Valid User"}'; 
	    }
	    elseif($count==1){
		//$result = json_encode($user); 
		$logged_in = $user->is_logged_in;
		if($logged_in==0){
		    $id = $user->id;
		    $arr['is_logged_in'] = 1;
		    $arr['last_login'] = date('Y-m-d h:m:s');
		    $updateinfo['save_data'] = $arr;
		    // print_r($updateinfo);exit;
		    $update = edit(json_encode($updateinfo),'users',$id);
		    if(!empty($update)){
		    //print_r($update);exit;
		    $user->is_active=1;
		    $user->is_logged_in=1;
		    $user->last_login=$arr['last_login'];
		    $user_details = json_encode($user);
		    $result = '{"type":"success","message":"Logged In Succesfully","user_details":'.$user_details.'}'; 
		    }
		}
		else{
		    $result = '{"type":"error","message":"You are already Logged In"}'; 
		}
	    }
	    //
	    echo $result;
	}
}

function activeProfile($unique_id){
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    //print_r($body);exit;
	//$email = $body->email;
	//$email = isset($user->email)?$user->email:$user->username;
	//echo $email;exit;
	//$pass = md5($body->password);
	$result='';
	if(!empty($unique_id)){
	    $db = getConnection();
	    $sql = "SELECT * FROM users WHERE unique_code=:unique_code";
	    $stmt = $db->prepare($sql);  
	    $stmt->bindParam("unique_code", $unique_id);	    
	    $stmt->execute();	
	    $count = $stmt->rowCount();
	    //echo $count;exit;
	    $user = $stmt->fetchObject();
	    $db = null;
	    //print_r($user);exit;
	    if($count==0){
		$result = '{"type":"error","message":"You are Not Valid User"}'; 
	    }
	    elseif($count==1){
		//$result = json_encode($user); 
		$logged_in = $user->is_logged_in;
		
		    $id = $user->id;
		    $arr['is_logged_in'] = 1;
		    $arr['is_active'] = 1;
		    $arr['last_login'] = date('Y-m-d h:m:s');
		    $updateinfo['save_data'] = $arr;
		    // print_r($updateinfo);exit;
		    $update = edit(json_encode($updateinfo),'users',$id);
		    if(!empty($update)){
		    //print_r($update);exit;
		    $user->is_active=1;
		    $user->is_logged_in=1;
		    $user->last_login=$arr['last_login'];
		    $user_details = json_encode($user);
		    
		    $result = '{"type":"success","message":"Logged In Succesfully","user_details":'.$user_details.'}'; 
		    }else{
		    $result = '{"type":"error","message":"Error occured"}';
		    }
		
	    }
	    //
	    echo $result;
	}
}

function getforgetPass(){
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
   // $rowCount = rowCount(json_encode($user),'users');
	$length = 6;
	$chars =  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.'0123456789';
	$pass = '';
	$max = strlen($chars) - 1;
	for ($i=0; $i < $length; $i++)
	$pass .= $chars[rand(0, $max)];
	
	//$email = isset($user->email)?$user->email:$user->username;
	$email = $body->email;
	$result='';
	if(!empty($email)){
	   // echo $pass;exit;
	    $db = getConnection();
	    $sql = "SELECT * FROM users WHERE email=:email or username=:email";
	    $stmt = $db->prepare($sql);  
	    $stmt->bindParam("email", $email);
	    $stmt->execute();	
	    $count = $stmt->rowCount();
	    //echo $count;exit;
	    $user = $stmt->fetchObject();
	    $db = null;
	  //  print_r($user);exit;
	    if($count==0){
		$result = '{"type":"error","message":"You are Not Valid User"}'; 
	    }
	    elseif($count==1){
		//$result = json_encode($user); 
		$id = $user->id;
		$user->txt_pwd = $pass;
		$user->password = md5($pass);
		$updateinfo['save_data'] = $user;
		// print_r($updateinfo);exit;
		$update = edit(json_encode($updateinfo),'users',$id);
		if(!empty($update)){
		    $from = 'info.mfood@gmail.com';
		    $to = $user->email;
		    $subject ='Forgot Password';
		    $body ='<html><body><p>Dear User,</p>

			    <pre>Your Password has been changed on mFoodGate&nbsp;.<br />
			    Your new login details:<br />
			   <strong> Email: "'.$user->email.'";<br />
			    Password: "'.$pass.'"</strong><br />
			    You can now login to your account and can change your password.</pre>
			    <p>Thanks,<br />
			    mFood&nbsp;Team</p>

			    <p>&nbsp;</p></body></html>
			    ';
		    sendMail($from,$to,$subject,$body);
		//print_r($update);exit;
		$result = '{"type":"success","message":"Changed Password Succesfully"}'; 
		}
	    }
	    //
	    echo $result;
	}
}

function getlogout(){
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
  // print_r($body);exit;
    $id = $body->userid;
    //echo $id;exit;
    $user = array();
    $user['is_logged_in'] = 0;
    $user['last_login'] = date('Y-m-d h:m:s');
    $updateinfo['save_data'] = $user;
    // print_r($updateinfo);exit;
    $update = edit(json_encode($updateinfo),'users',$id);
    if(!empty($update)){
	//print_r($update);exit;
    $result = '{"type":"success","message":"Logged Out Succesfully"}'; 
    }
    else{
	$result = '{"type":"error","message":"Still Logged In"}'; 
    }
    echo $result;
}


?>
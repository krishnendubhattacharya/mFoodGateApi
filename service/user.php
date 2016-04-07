<?php
function getAllUsers() {
        $result = getAll('users');
	echo $result;
}

function getAllMerchants() {
        $result = findByCondition(array('user_type_id' => 3),'users');
	echo $result;
}

function getUser($id) {
        $request = findById($id,'users');
	$body = json_decode($request);
	//print_r($body);exit;
	if(!empty($body)){
	    $site_path = SITEURL;
	    $aa = date("d M, Y", strtotime($body->registration_date));
	    $body->registration_date=$aa;
	    $ab = date("d M, Y", strtotime($body->last_login));
	    $body->last_login=$ab;
	    if(!empty($body->image)){
		$bb = $site_path . "user_images/" . $body->image;
	    }
	    else{
		$bb = $site_path . "user_images/default-profile.png";
	    }
	    $body->image =$bb;
            $body->locations = json_decode(findByCondition(array('user_id'=>$id),'merchant_location_map'));
           // print_r($body->locations);
           // exit;
            $body->categories = json_decode(findByCondition(array('user_id'=>$id),'merchant_category_map'));
	    $user_details = json_encode($body);
	    $result = '{"type":"success","user_details":'.$user_details.'}';
	}
	
	echo $result;
}

function addUser() {	
	
	$request = Slim::getInstance()->request();
	$user = json_decode($request->getBody());
	//print_r($user);
	//exit;
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
	    /*if($user->user_type_id == 'Merchant'){
	        $user->user_type_id = 3;
	    }else{
	        $user->user_type_id = 2;
	    }*/
	    $user->user_type_id = 2;
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
	    $from = ADMINEMAIL;
	    $to = $user_details->email;
	    $subject ='User Registration';
	    $body ='<html><body><p>Dear User,</p>

		    <p>Thank You for signing up with mFoodGate.<br />
		    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Your Account Details:</span><br />
		    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif"><strong>Email: '.$to.'</strong></span><br />
		    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif"><strong>Password: '.$pass.'</strong></span></p>

		    <p><span style="color:rgb(77, 76, 76); font-family:helvetica,arial">Please <a href="'.$activation_link.'/'.$unique_code.'">Click Here</a> </span><span style="color:rgb(77, 76, 76); font-family:helvetica,arial">&nbsp;to verify your account.</span><br />
		    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">If we can help you with anything in the meantime just let us know by e-mailing&nbsp;</span>'.$from.'<br />
		    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Thanks again for signing up with the site</span><span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">!&nbsp;</span></p>

		    <p>Thanks,<br />
		    mFood&nbsp;Team</p>

		    <p>&nbsp;</p></body></html>
		    ';
		    //$body = "Hello";
	    sendMail($to,$subject,$body);
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
       
        $user_id = $id;
	if(isset($user->id)){
	        unset($user->id);
	}	
        if(!empty($user->dob))
        {
            $user->dob = date("Y-m-d", strtotime($user->dob));
        }
        $arealist = $user->areaList;
        unset($user->areaList);
        
        $typelist = $user->typeList;
        unset($user->typeList);
        
	$allinfo['save_data'] = $user;
        //print_r($allinfo);
        //exit;
	$result = edit(json_encode($allinfo),'users',$id);
        if($result)
        {
            deleteAll('merchant_location_map',array('user_id' => $user_id));
            if(!empty($arealist))
            {
                foreach($arealist as $area)
                {
                    $temp = array();
                    $temp['user_id'] = $user_id;
                    $temp['location_id'] = $area->id;
                    add(json_encode(array('save_data'=>$temp)),'merchant_location_map');
                }
            }
            
            deleteAll('merchant_category_map',array('user_id' => $user_id));
            if(!empty($typelist))
            {
                foreach($typelist as $type)
                {
                    $temp = array();
                    $temp['user_id'] = $user_id;
                    $temp['category_id'] = $type->id;
                    add(json_encode(array('save_data'=>$temp)),'merchant_category_map');
                }
            }
        }
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
		    //$arr['is_logged_in'] = 1;
		    $arr['last_login'] = date('Y-m-d h:m:s');
		    $updateinfo['save_data'] = $arr;
		    // print_r($updateinfo);exit;
		    $update = edit(json_encode($updateinfo),'users',$id);
		    if(!empty($update)){
		    //print_r($update);exit;
		    $user->is_active=1;
		    //$user->is_logged_in=1;
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

function adminlogin(){
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
	    $sql = "SELECT * FROM users WHERE email=:email and password=:password and is_active=:is_active and user_type_id=1";
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
		    //$arr['is_logged_in'] = 1;
		    $arr['last_login'] = date('Y-m-d h:m:s');
		    $updateinfo['save_data'] = $arr;
		    // print_r($updateinfo);exit;
		    $update = edit(json_encode($updateinfo),'users',$id);
		    if(!empty($update)){
		    //print_r($update);exit;
		    $user->is_active=1;
		    //$user->is_logged_in=1;
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
		    //$arr['is_logged_in'] = 1;
		    $arr['is_active'] = 1;
		    $arr['last_login'] = date('Y-m-d h:m:s');
		    $updateinfo['save_data'] = $arr;
		    // print_r($updateinfo);exit;
		    $update = edit(json_encode($updateinfo),'users',$id);
		    if(!empty($update)){
		    //print_r($update);exit;
		    $user->is_active=1;
		    //$user->is_logged_in=1;
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
		    $from = ADMINEMAIL;
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
		    sendMail($to,$subject,$body);
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

function fbLoginUser(){
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
    $is_active = 1;
    
	$email = $body->email;
	$fb_id = $body->id;
	$body->fb_id  = $fb_id;
	$name = $body->name;
	
	$name = explode(' ',$name);
	
	$body->first_name = $name[0];
	$body->last_name = $name[1];
	
	unset($body->id);
	unset($body->name);
	
	//$email = isset($user->email)?$user->email:$user->username;
	//echo $email;exit;
	//$pass = md5($body->password);
	$result='';
	
	if(!empty($email)){
	    $db = getConnection();
	    $sql = "SELECT * FROM users WHERE email=:email";
	    $stmt = $db->prepare($sql);  
	    $stmt->bindParam("email", $email);
	    //$stmt->bindParam("password", $pass);
	    //$stmt->bindParam("is_active", $is_active);
	    $stmt->execute();	
	    $count = $stmt->rowCount();
	    //echo $count;exit;
	    $user = $stmt->fetchObject();
	    $stmt = null;
	    $db = null;
	    //print_r($user);exit;
	    if($count==0){
	        $unique_code = time().rand(100000,1000000);
                $body->unique_code = $unique_code;
                $body->user_type_id = 2;
                $body->is_active = 1;
                $body->registration_date = date('Y-m-d h:m:s');
                $body->last_login = date('Y-m-d h:m:s');
                
                $allinfo['save_data'] = $body;
                //$allinfo['unique_data'] = $unique_field;
                $user_details = add(json_encode($allinfo),'users');
                $result = '{"type":"success","message":"Logged In Succesfully","user_details":'.$user_details.'}';
		//$result = '{"type":"error","message":"You are Not Valid User"}'; 
	    }
	    elseif($count > 0){
		//$result = json_encode($user); 
		$logged_in = $user->is_logged_in;
		$db = getConnection();
                $sql = "SELECT * FROM users WHERE fb_id=:fb_id";
                $stmt = $db->prepare($sql);  
                $stmt->bindParam("fb_id", $fb_id);
                
                $stmt->execute();	
                $user_count = $stmt->rowCount();
                //echo $count;exit;
                $user = $stmt->fetchObject();
                $stmt = null;
                $db = null;
                if($user_count > 0){
                        $id = $user->id;
                        //$arr['is_logged_in'] = 1;
                        $arr['last_login'] = date('Y-m-d h:m:s');
                        $updateinfo['save_data'] = $arr;
                        // print_r($updateinfo);exit;
                        $update = edit(json_encode($updateinfo),'users',$id);
                        if(!empty($update)){
                        //print_r($update);exit;
                        $user->is_active=1;
			//$user->registration_date = date('d M, Y', strtotime($update->registration_date));
			//$user->image = $site_path . "user_images/" . $update->image;
                        //$user->is_logged_in=1;
                        $user->last_login=$arr['last_login'];
                        $user_details = json_encode($user);
                        $result = '{"type":"success","message":"Logged In Succesfully","user_details":'.$user_details.'}';
                        }
                }else{
                
                        $result = '{"type":"error","message":"Email already exist,please try with another fb account"}';
                
                }
		
		}
		
	   // }
	    //
	    echo $result;
	}
}

/*function profileImageUpload(){
        $request = Slim::getInstance()->request();
        $body = json_decode($request->getBody());
        $target_dir = realpath('user_images')."/";
        $unique_code = time().rand(100000,1000000);
        $target_file = $target_dir . $unique_code.'_'.basename($_FILES["file"]["name"]);
        move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
        //echo realpath('user_images');        
        print_r($_FILES["file"]);
        print_r($_POST);
        echo json_encode($body);
}*/

function profileImageUpload(){
        $request = Slim::getInstance()->request();
        $body = json_decode($request->getBody());
	$site_path = SITEURL;
        $target_dir = realpath('user_images')."/";
        $unique_code = time().rand(100000,1000000);
	$img = $unique_code.'_'.basename($_FILES["file"]["name"]);
        $target_file = $target_dir . $img;
        if(move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)){
	    //echo realpath('user_images');        
	 //   print_r($_FILES["file"]);
	 //   print_r($_POST);
	    $path = $site_path . "user_images/" . $img;
	    $uid = $_POST['user_id'];
	    $user = array();
	    $user['image'] = $img;
	    $updateinfo['save_data'] = $user;
		    // print_r($updateinfo);exit;
	    $update = edit(json_encode($updateinfo),'users',$uid);

	    if(!empty($update)){
		    $result = '{"type":"success","message":"Image Uploaded Successfully","user_image_details":"'.$path.'"}';
	    }
	    else{
		    $result = '{"type":"error","message":"Try again! Not Uploaded"}';
	    }
	}
	else{
		$result = '{"type":"error","message":"Try again! Not Uploaded"}';
	}
        echo $result;
}

function changePassword(){
    $request = Slim::getInstance()->request();
    $body = json_decode($request->getBody());
  //  print_r($body);exit;
    $id = $body->user_id;
    $oldpass = $body->oldpass;
    $newpass = $body->newpass;
    $confirmpass = $body->confirmpass;
	$db = getConnection();
	$sql = "SELECT * FROM users WHERE  users.id=:id";
	$stmt = $db->prepare($sql);  
	$stmt->bindParam("id", $id);
	$stmt->execute();	
	$count = $stmt->rowCount();
	$user = $stmt->fetchObject();
	//print_r($user);exit;
        $stmt = null;
        $db = null;
	if($count==0){
	    $result = '{"type":"error","message":"You are not a valid user"}';
	}
	else if($count>0){
	    if($user->password==md5($oldpass)){
		if($newpass==$confirmpass){
		    $userbody = array();
		    $userbody['txt_pwd'] = $newpass;
		    $userbody['password'] = md5($newpass);
		    $allinfo['save_data'] = $userbody;
		    $changePass = edit(json_encode($allinfo),'users',$id);
		    if($changePass){
			$result = '{"type":"success","message":"You have changed password Succesfully","userdetails":'.$changePass.'}';
		    }
		    else{
			$result = '{"type":"error","message":"Password could not save successfully. Try again"}';
		    }
		}
		else if($newpass!=$confirmpass){
		     $result = '{"type":"error","message":"New password is not matched to Confirm Password"}';
		}
	    }
	    else{
		$result = '{"type":"error","message":"You have entered wrong password"}';
	    }
	}
	echo $result;
}

function testMail(){
        $to = "nits.anup@gmail.com";
        $subject = "hello";
        //$body = "Hi Anup";
        $activation_link = "http://abcd.com";
        $pass = "123";
        $unique_code = "123";
        $from = "abc";
        $body ='<html><body><p>Dear User,</p>

		    <p>Thank You for signing up with mFoodGate.<br />

		    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Your Account Details:</span><br />
		    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif"><strong>Email: '.$to.'</strong></span><br />
		    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif"><strong>Password: '.$pass.'</strong></span></p>


		    <p><span style="color:rgb(77, 76, 76); font-family:helvetica,arial">Please <a href="'.$activation_link.'/'.$unique_code.'">Click Here</a> </span><span style="color:rgb(77, 76, 76); font-family:helvetica,arial">&nbsp;to verify your account.</span><br />

		    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">If we can help you with anything in the meantime just let us know by e-mailing&nbsp;</span>'.$from.'<br />
		    <span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">Thanks again for signing up with the site</span><span style="color:rgb(34, 34, 34); font-family:arial,sans-serif">!&nbsp;</span></p>

		    <p>Thanks,<br />

		    mFood&nbsp;Team</p>

		    <p>&nbsp;</p></body></html>
		    ';
        sendMail($to,$subject,$body);
        $result = '{"type":"success","message":"Send Succesfully"}';
        echo $result;

}

function getAllClientUser($id) {
	$sql = "select * FROM users where user_type_id='2' and is_active='1' and id !=:id ORDER BY id desc";
	
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id", $id);
		$stmt->execute();  
		$users = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		//return '{"'.$table.'": ' . json_encode($users) . '}';
		echo '{"type":"success","user_details":'.json_encode($users).'}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}


?>

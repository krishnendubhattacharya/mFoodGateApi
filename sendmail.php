<?php
/*function sendMail($to,$subject,$body) {
        $from = ADMINEMAIL;
	$headers = "From: " . strip_tags($from) . "\r\n";
	$headers .= "Reply-To: ". strip_tags($from) . "\r\n";
	$headers .= "CC: "."\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	mail($to,$subject,$body,$headers);
}*/

function sendMail($to,$subject,$body) {
        $mail = new PHPMailer();
        
        $MailTo= $to;
        $MailToName=$to;
        $MailFrom = ADMINEMAIL;
        $MailFromName= ADMINEMAIL;
        $YourEamilPassword= EMAILPASSWORD;
        $MailSubject=$subject;
        $MailHtmlMessage=$body;
        
        $IsMailType='SMTP';       
        $SmtpHost             = MAILSERVER; 
        $SmtpDebug            = 0;                     // enables SMTP debug information (for testing)
        $SmtpAuthentication   = true;                  // enable SMTP authentication
        $SmtpPort             = MAILPORT;                    
        $SmtpUsername       = $MailFrom; 
        $SmtpPassword       = $YourEamilPassword;
        
        if ( $IsMailType == "SMTP" ) {
                $mail->IsSMTP();  // telling the class to use SMTP
                $mail->SMTPDebug  = $SmtpDebug;
                $mail->SMTPAuth   =  $SmtpAuthentication;     // enable SMTP authentication
                $mail->Port       = $SmtpPort;             // set the SMTP port
                $mail->Host       = $SmtpHost;           // SMTP server
                $mail->SMTPSecure = "tls";
                $mail->Username   =  $SmtpUsername; // SMTP account username
                $mail->Password   = $SmtpPassword; // SMTP account password
        }
        
        if ( $MailFromName != '' ) {
                $mail->AddReplyTo($MailFrom,$MailFromName);
                $mail->From       = $MailFrom;
                $mail->FromName   = $MailFromName;
        } else {
                $mail->AddReplyTo($MailFrom);
                $mail->From       = $MailFrom;
                $mail->FromName   = $MailFrom;
        }

        if ( $MailToName != '' ) {
                $mail->AddAddress($MailTo,$MailToName);
        } else {
                $mail->AddAddress($MailTo);
        }
        
        $mail->Subject  = $MailSubject;  
        $mail->MsgHTML($MailHtmlMessage);
        
        try {
                if ( !$mail->Send() ) {
                        //$error = "Unable to send to: " . $to . "<br />";
                        //throw new phpmailerAppException($error);
                } else {
                        //echo 'Message has been sent <br /><br />';
                }
        }
        catch (phpmailerAppException $e) {
                //$errorMsg[] = $e->errorMessage();
        }
        
        /*if ( count($errorMsg) > 0 ) {
                foreach ($errorMsg as $key => $value) {
                $thisError = $key + 1;
                //echo $thisError . ': ' . $value;
                }
        }*/
        
        
	
}
?>

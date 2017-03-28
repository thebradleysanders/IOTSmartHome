<?php
//###################### SEND EMAIL ######################

function GF_sendEmail($email,$subject,$body,$image, $calledBy=""){
	global $GS_DBCONN;
	global $GS_emailServiceEnabled;
	global $GA_enabledService_email;
	global $GS_Config;
	
	if($GS_emailServiceEnabled == true){
		if(trim($body=="") || trim($email)==""){RETURN FALSE;}
		
		if($GA_enabledService_email['service_attr1']!="" && $GA_enabledService_email['service_attr2']!="" && $GA_enabledService_email['service_attr3']!="" && $GA_enabledService_email['service_attr4']!=""){
					
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->Mailer = 'smtp';
			$mail->SMTPAuth = true;
			$mail->Host = $GA_enabledService_email['service_attr1'];
			$mail->Port = (int)$GA_enabledService_email['service_attr2'];
			$mail->IsHTML(false); // if you are going to send HTML formatted emails
			$mail->SingleTo = true; // if you want to send a same email to multiple users. multiple emails will be sent one-by-one.
			$mail->SMTPSecure = 'ssl';
			$mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only			
			$mail->Username = $GA_enabledService_email['service_attr3'];
			$mail->Password = $GA_enabledService_email['service_attr4'];
			$mail->From = $GA_enabledService_email['service_attr3'];
			//$mail->FromName = "SmartHome";
			
			if(strpos($email,',') !== false){
				$EmailList = explode(",",$email);
				foreach ($EmailList as $emails){  $mail->addAddress($emails,"");  }
			}else{
				$mail->addAddress($email,"");	
			}
			
			$mail->Subject = $subject;
			$mail->Body = $body;
			
			if(!empty($image)){
				$jpeg_image = imagecreatefromjpeg($image);
				$mail->addStringAttachment($jpeg_image, 'filename');
			}
			
			if(!$mail->Send()){
				GF_logging( "Message was not sent PHPMailer Error: " . $mail->ErrorInfo,"Email|Services|Email Not Sent|Warnings|".$email."|".$subject."|");
			}else{
				GF_logging("Email Sent To: ".$email,"Email|Services|Email Sent|".$email."|".$subject."|");
			}
		}else{
			GF_logging("<b>ERROR:</b> SMTP Email setup is incomplete!","Email|Services|Email Not Sent|Warnings|".$email."|".$subject."|");
		}
	}	
}
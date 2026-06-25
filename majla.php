<?php


require 'mailer/PHPMailer-master/PHPMailer-master/PHPMailerAutoload.php';


$mail = new PHPMailer();

//$mail->IsSendmail();

$mail->isSMTP();   // Set mailer to use SMTP
//$mail->SMTPDebug  = 4; 
$mail->CharSet="utf-8";
                           
//sa kog mejl servera se salje

$mail->Host ="mail.kalendar.edu.rs";

$mail->SMTPAuth = true;       // Enable SMTP authentication 
                           
  // SMTP username
$mail->Username = "vojainfo@kalendar.edu.rs";
//$mail->Password = "bibernik1";                      // SMTP password
$mail->Password = "1mirodrojce2";
$mail->SMTPSecure = 'TLS';                            // Enable encryption, 'ssl' also accepted
$mail->Port = 587;                                  //Set the SMTP port number - 587 for authenticated TLS
$mail->setFrom('vojainfo@kalendar.edu.rs', "Goran Stevanovic");//Set who the message is to be sent from
//$mail->ConfirmReadingTo = 'goran.stevanovic@vihor-nis.com';
//$mail->addReplyTo('kome_oces@samo_ne_meni.com', 'isti takodje');  //Set an alternative reply-to address

$mail->addAddress('goranmstevanovic@gmail.com', ' aj');  // Add a recipient
//$mail->addAddress('stojanovic_dimitrije@yahoo.com', ' aj');
//$mail->addAddress('goranmstevanovic@gmail.com', ' aj');


//$mail->addAddress('goranmstevanovic@gmail.com', ' ');                     // Add a recipient
//$mail->addAddress('ellen@example.com');               // Name is optional
$mail->addCC('goran.stevanovic@bmbversicherungen.ch');
//$mail->addBCC('bcc@example.com');
//$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
//$mail->addAttachment('/usr/labnol/file.doc');         // Add attachments
//echo"atresa je:",$attach;
// $mail->addAttachment($attach); // Optional name
//$mail->isHTML(true);                                  // Set email format to HTML 
$mail->Subject = 'Dnevni izvestaj za dan '.date('d.m.Y');
//--------------------------------------------------------------------------------------------------------------------------------------------
$mail->Body    = ' Pozdrav Gorane, evo i izvestaja za danas ';
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
 
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
//$mail->msgHTML(file_get_contents('prikaci.html')); // , dirname(__FILE__)

if($mail->send()){

	
	echo '**   Mail je poslat na:   u ',date('h:i:s'),' sati','<br/>'; 
	$kraj=date('Y.m.d H:i:s');
	
	}
 else{
	echo 'poruka nije poslata.';	
    echo 'Mailer Error: ' . $mail->ErrorInfo;
	}


?>











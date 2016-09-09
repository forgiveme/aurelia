<?php
ini_set('display_errors', true);

include("../app/Mage.php");

$config = array('auth' => 'login',
                'username' => 'bladerunner01',
                'password' => 'andy33333055');
				
$transport = new Zend_Mail_Transport_Smtp('smtp.sendgrid.net', $config);

$fromEmail = "antonia@aureliaSkincare.com"; // sender email address
$fromName = "John Doe"; // sender name

$toEmail = "design@reactivegraphics.co.uk"; // recipient email address
$toName = "sanford"; // recipient name

$body = "This is Test Email!"; // body text
$subject = "Test Subject"; // subject text

$mail = new Zend_Mail();		

$mail->setBodyText($body);

$mail->setFrom($fromEmail, $fromName);

$mail->addTo($toEmail, $toName);

$mail->setSubject($subject);

try {
	$mail->send($transport);
}
catch(Exception $ex) {
	// I assume you have your custom module. 
	// If not, you may keep 'customer' instead of 'yourmodule'.
	echo $ex->getMessage();
	echo 'Unable to send email.';
}

?>
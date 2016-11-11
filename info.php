<?php
error_reporting(E_ALL);
include("app/Mage.php");
//Mage::app();
//$obj = Mage::getModel('Review_Reminder_Model_Observer');
//$obj->sendReviewReminder();
exit;
mail("sanford@123789.org", "test", "test");
error_reporting(E_ALL);
include("app/Mage.php");
//umask(0);
//Mage::app();
//$checkModel = Mage::getModel('Review_Reminder_Model_Observer')->sendReviewReminder();
//echo get_class($checkModel);
//exit;
echo "<pre>";
print_r(Mage::app()->getConfig()->getNode()->xpath('//global//rewrite'));
echo shell_exec("pwd");
exit;
phpinfo();
?>
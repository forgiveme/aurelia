<?php
ini_set('display_errors', true);
include("../app/Mage.php");
umask(0);
Mage::app();
$storeUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
Mage::getSingleton('core/session', array('name' => 'frontend'));
$session = Mage::getSingleton('customer/session');
if($session->isLoggedIn()) {	echo $storeUrl.'promotion/index/index';
    header('location:'.$storeUrl.'promotion/index/index');
} else {
	header("location:$storeUrl");
}
exit;
?>
<?php
class ChilliApple_Promotion_Model_Cron
{

	public function loyaltyPoints()
	{
	   $helper=Mage::helper('promotion');
	   
	   $helper->generateLoyaltyCoupons();
	   //mail('sedrick@123789.org','Aurelia loyaltyPoints','Done');
	   //echo "Mails Sent\n";
	}
	
	public function birthdayCoupons()
	{
	   $helper=Mage::helper('promotion');
	   
	   $helper->birthdayCoupons();
	}

}

<?php 
class ChilliApple_Promotion_Model_Customer extends Mage_Customer_Model_Customer
{

	public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0')
    {
       
        return $this;
    }
}
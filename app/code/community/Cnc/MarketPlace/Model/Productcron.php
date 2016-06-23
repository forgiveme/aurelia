<?php
class Cnc_MarketPlace_Model_Productcron
{
	public function execute()
	{
		$helper = Mage::helper( 'marketplace' );
        Mage::helper('marketplace/logger')->log( $log );
        $field_attributes = $helper->getProductsToUpload( true ); //true - for CRON
	}
}
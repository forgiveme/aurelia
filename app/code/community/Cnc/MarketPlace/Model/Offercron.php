<?php
class Cnc_MarketPlace_Model_Offercron
{
	public function execute()
	{
		$helper = Mage::helper( 'marketplace' );
        Mage::helper('marketplace/logger')->log( "Offer Uploaded - Cron" );
		$field_attributes = $helper->getOffersToUpload( true ); //true - for CRON
	}
}
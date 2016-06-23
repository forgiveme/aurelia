<?php
class Cnc_MarketPlace_Model_Offertable extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		parent::_construct();
		$this->_init( 'marketplace/offertable' );
	}
}

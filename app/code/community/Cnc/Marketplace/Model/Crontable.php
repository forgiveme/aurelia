<?php
class Cnc_Marketplace_Model_Crontable extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		parent::_construct();
		$this->_init( 'marketplace/crontable' );
	}
}

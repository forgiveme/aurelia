<?php
class Cnc_MarketPlace_Model_Resource_Crontable extends Mage_Core_Model_Resource_Db_Abstract
{
	protected function _construct()
	{
		$this->_init( 'marketplace/crontable', 'id' );
	}
}

<?php
class Cnc_Marketplace_Model_Resource_Importtable extends Mage_Core_Model_Resource_Db_Abstract
{
	protected function _construct()
	{
		$this->_init( 'marketplace/importtable', 'id' );
	}
}


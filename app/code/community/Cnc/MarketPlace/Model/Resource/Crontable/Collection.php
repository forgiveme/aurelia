<?php
class Cnc_MarketPlace_Model_Resource_Crontable_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	protected function _construct()
	{
		$this->_init( 'marketplace/crontable' );
		$this->_storeId = (int) Mage::app()->getStore()->getId();
	}
	public function addCronSelect( $cron_name )
	{
		if ( $cron_name ) {
			$where = 'name = "' . $cron_name . '"';
			$this->getSelect()->where( $where );
			return $this;
		}
	}
}

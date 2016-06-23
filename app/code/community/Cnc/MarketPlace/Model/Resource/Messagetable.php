<?php
class Cnc_MarketPlace_Model_Resource_Messagetable extends Mage_Core_Model_Resource_Db_Abstract
{
	protected function _construct()
	{
		$this->_init( 'marketplace/messagetable', 'id' );
	}
	public function deleteByCondition( $order_offer_id, $type )
	{
		$table    = $this->getMainTable();
		$where    = array();
		$where[ ] = $this->_getWriteAdapter()->quoteInto( 'order_offer_id = ?', $order_offer_id );
		$where[ ] = $this->_getWriteAdapter()->quoteInto( "type_msg = ? ", $type );
		$result   = $this->_getWriteAdapter()->delete( $table, $where );
		return $result;
	}
}

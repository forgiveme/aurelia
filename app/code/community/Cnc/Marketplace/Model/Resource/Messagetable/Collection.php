<?php
class Cnc_Marketplace_Model_Resource_Messagetable_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	protected function _construct()
	{
		$this->_init( 'marketplace/messagetable' );
		$this->_storeId = (int) Mage::app()->getStore()->getId();
	}
	public function getByMessageID( $msg_id )
	{
		if ( $msg_id ) {
			$where = 'message_id LIKE "' . $msg_id . '"';
			$this->getSelect()->where( $where );
			return $this;
		}
	}
	public function getByOrderOfferID( $id )
	{
		if ( $id ) {
			$where = 'order_offer_id LIKE "' . $id . '"';
			$this->getSelect()->where( $where );
			return $this;
		}
	}
	public function getCountMessages( $type )
	{
		if ( $type ) {
			$this->getSelect()->columns( 'COUNT(id) AS counts, GROUP_CONCAT(DISTINCT(order_offer_id)) as order_offer_ids' )->where( 'type_msg = "' . $type . '" AND read_msg = 0' );
			return $this;
		}
	}
}

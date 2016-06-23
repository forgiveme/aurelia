<?php
class Cnc_MarketPlace_Model_Resource_OrderTable_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	protected function _construct()
	{
		$this->_init( 'marketplace/ordertable' );
		$this->_storeId = (int) Mage::app()->getStore()->getId();
	}
	public function addOrderFilter( $order_state )
	{
		if ( $order_state ) {
			$sortByCustomer = 'order_state IN (' . $order_state . ')';
			$this->getSelect()->where( $sortByCustomer );
			return $this;
		}
	}
	public function searchOrder( $order_id, $which, $optional )
	{
		if ( $order_id ) {
			if ( $which ) {
				$search = 'orderid LIKE "' . $order_id . '"';
			} else {
				$search = 'm_order_id LIKE "' . $order_id . '"';
			}
		} else if ( $optional == 'error_read' ) {
			$search = 'message_read = "1"';
		} else if ( $optional == 'incident_read' ) {
			$search = 'has_incident = "1" AND incident_read = "1"';
		}
		$this->getSelect()->where( $search );
		return $this;
	}
	public function getCountOrderStatus( $value )
	{
		if ( $value ) {
			$search = 'order_read = "1" AND order_state = "' . $value . '"';
		}
		$this->getSelect()->columns( 'COUNT(order_state) AS counts' )->where( $search )->group( array(
			 'order_state' 
		) );
		return $this;
	}
	public function getUnreadIncidentsCount()
	{
		$search = 'has_incident = "1" AND incident_read = "1"';
		$this->getSelect()->columns( 'COUNT(id) AS counts' )->where( $search )->group( array(
			 'incident_read' 
		) );
		return $this;
	}
	public function getUnreadNewOrdersCount()
	{
		$search = 'order_read = "1"';
		$this->getSelect()->columns( 'COUNT(id) AS counts' )->where( $search )->group( array(
			 'order_read' 
		) );
		return $this;
	}
	public function getUnreadErrorsCount()
	{
		$search = 'message_read = "1"';
		$this->getSelect()->columns( 'COUNT(id) AS counts' )->where( $search )->group( array(
			 'message_read' 
		) );
		return $this;
	}
	public function getMessageUnread( $order_ids )
	{
		$implode_quote        = explode( ',', $order_ids );
		$implode_quote_orders = implode( "','", $implode_quote );
		if ( $order_ids ) {
			$where = "orderid IN ('" . $implode_quote_orders . "')";
			$this->getSelect()->where( $where );
			return $this;
		}
	}
}
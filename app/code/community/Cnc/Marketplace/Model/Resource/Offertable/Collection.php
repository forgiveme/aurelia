<?php
class Cnc_Marketplace_Model_Resource_Offertable_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	protected function _construct()
	{
		$this->_init( 'marketplace/offertable' );
		$this->_storeId = (int) Mage::app()->getStore()->getId();
	}
	public function getOfferById( $offerSku )
	{
		if ( $offerSku ) {
			$where = 'offer_sku = "' . $offerSku . '"';
			$this->getSelect()->where( $where );
			return $this;
		}
	}
	public function getOfferByOfferId( $offerId )
	{
		if ( $offerId ) {
			$where = 'offer_id LIKE "' . $offerId . '"';
			$this->getSelect()->where( $where );
			return $this;
		}
	}
	public function getOfferNotEqual( $offerSku )
	{
		if ( count( $offerSku ) > 0 ) {
			$offerSkus = implode( ',', $offerSku );
			$where     = 'offer_sku NOT IN (' . $offerSkus . ')';
			$this->getSelect()->where( $where );
			return $this;
		}
	}
	public function getMessageUnread( $offer_ids )
	{
		$implode_quote        = explode( ',', $offer_ids );
		$implode_quote_offers = implode( '","', $implode_quote );
		if ( $implode_quote_offers ) {
			$where = 'offer_id IN (' . $implode_quote_offers . ')';
			$this->getSelect()->where( $where );
			return $this;
		}
	}
}

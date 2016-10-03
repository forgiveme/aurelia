<?php
class Cnc_Marketplace_Model_Resource_Importtable_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	protected function _construct()
	{
		$this->_init( 'marketplace/importtable' );
	}
	public function getType( $type )
	{
		if ( $type ) {
			$where = 'type = "' . $type . '"';
			$this->getSelect()->where( $where );
			return $this;
		}
	}
	public function getImportIds( $importids )
	{
		if ( $importids ) {
			$importids = json_decode( $importids );
			foreach ( $importids as $importid ) {
				$arrimportids[ ] = $importid;
			}
			$importids_in = implode( ',', $arrimportids );
			$where        = 'import_id IN (' . $importids_in . ')';
			$this->getSelect()->where( $where );
			return $this;
		}
	}
	public function getByImportid( $importid )
	{
		if ( $importid ) {
			$where = 'import_id = ' . $importid . '';
			$this->getSelect()->where( $where );
			return $this;
		}
	}
}

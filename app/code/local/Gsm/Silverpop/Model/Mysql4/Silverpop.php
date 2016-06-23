<?php

class Gsm_Silverpop_Model_Mysql4_Silverpop extends Mage_Core_Model_Mysql4_Abstract {
	public function _construct() {
		// Note that the silverpop_id refers to the key field in your database table.
		$this->_init('silverpop/silverpop', 'silverpop_id');
	}
}

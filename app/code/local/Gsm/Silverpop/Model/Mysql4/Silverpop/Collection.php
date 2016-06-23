<?php

class Gsm_Silverpop_Model_Mysql4_Silverpop_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	public function _construct() {
		parent::_construct();
		$this->_init('silverpop/silverpop');
	}
}

<?php

class Gsm_Silverpop_Model_Customer_Customer extends Mage_Customer_Model_Customer {
	public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0') {
		if (Mage::getModel('silverpop/silverpop')->isSilverpopEnabled()) {
			$silverpop = Mage::getModel('silverpop/silverpop');
			$silverpop->createRegisteredAccount($this);
		}

		return parent::sendNewAccountEmail($type, $backUrl, $storeId);
	}
}
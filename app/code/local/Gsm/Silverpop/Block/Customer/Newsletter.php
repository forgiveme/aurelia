<?php
/**
 * Customer front  newsletter manage block
 *
 * @category   Silverpop
 * @package	   Gsm_Silverpop
 * @author	   Masood Ahmed
 */

class Gsm_Silverpop_Block_Customer_Newsletter extends Mage_Customer_Block_Newsletter {
	public function getIsSubscribed() {
		if (Mage::getModel('silverpop/silverpop')->isSilverpopEnabled()) {
			return Mage::getModel('silverpop/silverpop')->getIsSubscribed();
		}
		else {
			return parent::getIsSubscribed();
		}
	}
}

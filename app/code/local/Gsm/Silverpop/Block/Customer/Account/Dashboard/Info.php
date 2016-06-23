<?php
/**
 * Dashboard Customer Info
 *
 * @category   Silverpop
 * @package	   Gsm_Silverpop
 * @author	   Masood Ahmed
 */

class Gsm_Silverpop_Block_Customer_Account_Dashboard_Info extends Mage_Customer_Block_Account_Dashboard_Info
{
	/**
	 * Gets Customer subscription status
	 *
	 * @return bool
	 */
	public function getIsSubscribed() {
		if (Mage::getModel('silverpop/silverpop')->isSilverpopEnabled()) {
			return Mage::getModel('silverpop/silverpop')->getIsSubscribed();
		}
		else {
			return parent::getIsSubscribed();
		}
	}
}

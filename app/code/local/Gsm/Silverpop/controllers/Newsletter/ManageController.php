<?php

/**
 * Customers newsletter subscription controller
 *
 * @category   Silverpop
 * @package	   Gsm_Silverpop
 * @author	   Masood Ahmed
 */

require_once('Mage/Newsletter/controllers/ManageController.php');

class Gsm_Silverpop_Newsletter_ManageController extends Mage_Newsletter_ManageController
{
	public function saveAction() {
		$ip = $_SERVER['REMOTE_ADDR'];
		$allowed = array('202.122.21.136');

		if (Mage::getModel('silverpop/silverpop')->isSilverpopEnabled()) {
			if (!$this->_validateFormKey()) {
				return $this->_redirect('customer/account/');
			}
			try {
				Mage::getSingleton('customer/session')->getCustomer()
					->setStoreId(Mage::app()->getStore()->getId())
					->setIsSubscribed((boolean)$this->getRequest()->getParam('is_subscribed', false))
					->save();

				$silverpop = Mage::getModel('silverpop/silverpop');

				if ((boolean)$this->getRequest()->getParam('is_subscribed', false)) {
					$silverpop->setSubscribed(true);
				}
				else {
					$silverpop->setSubscribed(false);
				}

				if ((boolean)$this->getRequest()->getParam('is_subscribed', false)) {
					Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription has been saved.'));
				} else {
					Mage::getSingleton('customer/session')->addSuccess($this->__('The subscription has been removed.'));
				}
			}
			catch (Exception $e) {
				Mage::getSingleton('customer/session')->addError($this->__('An error occurred while saving your subscription.'));
			}
			$this->_redirect('customer/account/');
		}
		else {
			return parent::saveAction();
		}
	}
}

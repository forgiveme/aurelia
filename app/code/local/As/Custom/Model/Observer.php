<?php
/**
 * As Custom observer
 *
 */
class As_Custom_Model_Observer
{   
    /**
     * Event observer
     *
     * @var Varien_Event_Observer
     */
    private $_observer;
	
	public function redirecttoCheckout($observer) {
		
		if(Mage::helper('checkout/cart')->getItemsCount()) {
			
			try {
				$event = $observer->getEvent();
				$customer = $event->getCustomer();
				
				/* if customer has then login */
				if($customer->getId()>0) {
					$userSession = Mage::getSingleton('customer/session');
					$userSession->setCustomer($customer);
					Mage::dispatchEvent('customer_login', array('customer'=>$customer));
					
					Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('onestepcheckout', array('_secure' => true)))->sendResponse();
					exit;
					
				} else {
					Mage::log("Customer Registration Failed.", null, "exception.log");
				}
			} catch(Exception $e) {
				Mage::log($e->getMessage(), null, "exception.log");
			}
		}
		
	}
	
	public function redirecttoCheckoutlogin($observer) {
		
		if(Mage::helper('checkout/cart')->getItemsCount()) {
			
			try {
				$event = $observer->getEvent();
				$customer = $event->getCustomer();
				
				/* if customer has then login */
				if($customer->getId()>0) {
					
					Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('onestepcheckout', array('_secure' => true)))->sendResponse();
					exit;
					
				} else {
					Mage::log("Customer Login Failed.", null, "exception.log");
				}
			} catch(Exception $e) {
				Mage::log($e->getMessage(), null, "exception.log");
			}
		}
		
	}
}

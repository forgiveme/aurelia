<?php

class Star_Newslettersubscribe_IndexController extends Mage_Core_Controller_Front_Action
{
     public function saveAction()
    {
		$param = $this->getRequest()->getParam('newsletter');
		
	   if($param=='add'){
		  $params = 1;
		} else {
			$params = 0;
		}
		
		 try {
            Mage::getSingleton('customer/session')->getCustomer()
            ->setStoreId(Mage::app()->getStore()->getId())
			
            ->setIsSubscribed($params)
            ->save();
            if ($params) {
                Mage::getSingleton('customer/session')->addSuccess($this->__('Newsletter subscription has been saved.'));
            } else {
                Mage::getSingleton('customer/session')->addSuccess($this->__("Newsletter subscription has been removed."));
            }
			$return['success'] = true;
        }
        catch (Exception $e) {
            Mage::getSingleton('customer/session')->addError($this->__('An error occurred while saving your subscription.'));
        }
		echo json_encode($return);
            return;
      //  $this->_redirect('customer/account/');
    }

}

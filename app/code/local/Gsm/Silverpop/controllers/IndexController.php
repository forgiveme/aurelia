<?php

class Gsm_Silverpop_IndexController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {

	}
	
	 public function saveAction()
    {
		$param = $this->getRequest()->getParam('newsletter');
		
	   if($param=='add'){
		  $params = true;
		} else {
			$params = false;
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

	public function subscribeAction() {
		// Let's check whether the user exists or not.
		$email = $_GET['email'];

		$silverpop = Mage::getModel('silverpop/silverpop');

		if ($silverpop->checkUserExists($email)) {
			$silverpop->setSubscribed(true, $email);
		}
		else {
			$cust_data['Email'] = $email;
			$cust_data['First Name'] = '';
			$cust_data['Guest'] = 'Yes';
			$cust_data["Registered"] = 'No';
			$cust_data["Surname"] = '';
			$cust_data["Phone"] = '';
			$cust_data['Newsletter'] = 'Yes';

			$silverpop->createUser($cust_data);
		}

		echo "1";
	}
}

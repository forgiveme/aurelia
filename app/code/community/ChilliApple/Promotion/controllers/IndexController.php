<?php
class ChilliApple_Promotion_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	if(!Mage::getSingleton('customer/session')->isLoggedIn()) 
    	{
    	  $this->_redirect('customer/account/login');
    		  return;
		}

			
		$this->loadLayout();     
		$this->renderLayout();
    }
    
    public function referafriendAction()
    {   if(!Mage::getSingleton('customer/session')->isLoggedIn()) 
    	{
    	  $this->_redirect('customer/account/login');
    		  return;
    	}
    	$this->loadLayout();     
    	$request=$this->getRequest();
    	$this->_initLayoutMessages('customer/session');
    	if($request->isPost())
    	{
    		$email=$request->getParam('email');
    		
    		$isEmail=Zend_Validate::is($email,"EmailAddress");
    		
    		if(!$isEmail)
    		{
    		  Mage::getSingleton('customer/session')->addError(Mage::helper('promotion')->__('Please enter valid email address'));
    		  $this->_redirect('*/*/*');
    		  return;
    		}
    		
    		$user=Mage::getModel('customer/customer');
    		$user->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail($email);
    		
    		if($user->getId())
    		{
    		   Mage::getSingleton('customer/session')->addError(Mage::helper('promotion')->__('This email has already been contacted – please choose another friend.'));
    		  $this->_redirect('*/*/*');
    		  return;
    		}
    		
    		$referedUser=Mage::getModel('promotion/referafriend')
    		 			->getCollection()
            		 		->addFieldToFilter('friend_email',array('eq' => $email))
            		 		->getFirstItem();
    		if($referedUser->getId())
    		{
    		   Mage::getSingleton('customer/session')->addError(Mage::helper('promotion')->__('This email has already been contacted – please choose another friend.'));
    		  $this->_redirect('*/*/*');
    		  return;
    		}
    		$customer=Mage::getSingleton('customer/session')->getCustomer();
    		
    		$helper=Mage::helper('promotion');
    		$helper->referAFriend($email,$customer);
    		Mage::getSingleton('customer/session')->addSuccess(Mage::helper('promotion')->__('Success! Thank you for sharing the love.'));
    		  $this->_redirect('*/*/*');
    	}
    	
	$this->renderLayout();
    }
}

<?php

class ChilliApple_Promotion_Model_Observer
{
    public function updateLoyalty(Varien_Event_Observer $observer)
    {
    	$order = $observer->getOrder();
  
    	if($order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE)
    	{  
    	   $grandTotal=$order->getData('grand_total');
    	   $threshold=(float)Mage::getStoreConfig('promotion/loyalty/threshold');
    	   //
    	   $customerId=$order->getCustomerId();
    	   
    	   if($grandTotal<=0)
    	   return ;
    	   
    	   if($threshold<=0)
    	   return ;
    	   
    	   $customerId=$order->getCustomerId();
    	   
    	   if(empty($customerId))
    	     return;
    	     
    	   $customer=Mage::getModel('customer/customer')->load($customerId);
    	   
    	   
    	   if(!$customer->getId())
    	     return;
    	   
    	        
    	    	
    	    	// get old amount
    	        $accAmount=(float)$customer->getLoyalty();
    	    	// get order total
    	    	$amount=$order->getData('grand_total');
    	    	
    	    	$description=Mage::helper('promotion')->__('Loyalty points for order no. : %s',$order->getIncrementId());
    	    	//add new entry in account
    	    	$loyalty=Mage::getModel('promotion/loyalty');
    	    	
    	    	$loyalty->setDescription($description);
    	    	
    	    	$loyalty->setAmount($amount);
    	    	
    	    	$loyalty->setCustomerId($customerId);
    	    	
    	    	$loyalty->setOrderId($order->getId());
    	    	
    	    	
    	    	$loyalty->setCreatedTime(date('Y-m-d H:i:s'));
    	    	$loyalty->setUpdateTime(date('Y-m-d H:i:s'));
    	    	
    	    	$loyalty->save();
    	    	
    	    	//update total balance in user account
    	    	$totalAmount=$amount+$accAmount;
    	    	
    	    	$customer->setLoyalty($totalAmount);
    	    	
    	    	$customer->save();
    	    }
    	   
    	    
    }

    public function referAFriend(Varien_Event_Observer $observer)
    {
    	$order = $observer->getOrder();
  
    	if($order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE)
    	{
    		$email=$order->getCustomerEmail();
    		$couponCode=$order->getCouponCode();
    		if(empty($couponCode))
    		   return;
    		$reference=Mage::getModel('promotion/referafriend')
    		 			->getCollection()
            		 		->addFieldToFilter('friend_email',array('eq' => $email))
            		 		->addFieldToFilter('coupon_code',array('eq' => $couponCode))
            		 		->getFirstItem();
               $refreeId=$reference->getCustomerId();
               
               if(empty($refreeId))
                  return;
                  
               $refree=Mage::getModel('customer/customer');
    	       $refree->load($refreeId);
    	       
    	       if(!$refree->getId())
    	          return;
    	        
    	        Mage::helper('promotion')->sendRefereeCoupon($refree);
    	}
    }
    
    public function validateCoupon(Varien_Event_Observer $observer)
    {
    	//$rule = $observer->getEvent()->getRule();
        //$item = $observer->getEvent()->getItem();
        //$address = $observer->getEvent()->getAddress();
        //$quote = $observer->getEvent()->getQuote();
        //$qty = $observer->getEvent()->getQty();
        //$result = $observer->getEvent()->getResult();
        /*$quote->getShippingAddress()->setCollectShippingRates(true);
            $quote->setCouponCode('')
            ->collectTotals()
            ->save();*/
        //$email=$quote->getCustomerEmail();
        //$checkoutMethod=$quote->getcheckoutMethod();
         
        //Mage::log($checkoutMethod,null,'coupon.log');
        return true; 

    }
    
    public function appendLoyaltyColumn(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();
        if (!isset($block)) {
            return $this;
        }
 
        if ($block->getType() == 'adminhtml/customer_grid') {
            /* @var $block Mage_Adminhtml_Block_Customer_Grid */
            $block->addColumnAfter('loyalty', array(
                'header'    => 'Loyalty',
                'type'      => 'decimal',
                'index'     => 'loyalty',
            ), 'email');
        }
    }
    
    public function sendNewRegistrationCoupon(Varien_Event_Observer $observer)
    {
       //customer_register_success
    	$customer=$observer->getEvent()->getCustomer();
    	
    	if($customer->getId())
    	{
    		$helper=Mage::helper('promotion');
    		$helper->sendFreeShippingCoupons($customer);
    		return $this;
    	}
    }
    
}

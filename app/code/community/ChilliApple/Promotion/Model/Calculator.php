<?php

class ChilliApple_Promotion_Model_Calculator
{

	
	public function calculate($order)
	{
	   $threshold=(float)Mage::getStoreConfig('promotion/loyalty/threshold');
    	   
    	   if($threshold<=0)
    	   return ;
    	   
    	   $customerId=$order->getCustomerId();
    	   
    	   if(empty($customerId))
    	     return;
    	     $customer=Mage::getModel('customer/customer')->load($customerId);
    	   
    	   
    	   if(!$customer->getId())
    	     return;
	   
	   $loyalty=Mage::getModel('promotion/loyalty');
    	    
    	    $accAmount=(float)$customer->getLoyalty();
    	    
    	    if($accAmount>=$threshold)
    	    {
    	    	//send coupon code
    	    	
    	    	$accAmount=0;
    	    	
    	    }
    	    else
    	    {
    	    	$amount=($order->getData('grand_total')-$order->getData('discount_amount'));
    	    	$description=Mage::helper('promotion')->__('Order No. : %s',$order->getIncrementId());
    	    	$loyalty->setDescription($description);
    	    	$loyalty->setAmount($amount);
    	    	$loyalty->setCustomerId($customerId);
    	    	$loyalty->setOrderId($order->getId());
    	    	$loyalty->save();
    	    	$totalAmount=$amount+$accAmount;
    	    	$customer->setLoyalty($totalAmount);
    	    	$customer->save();
    	    }
	   
	
	}
	
	

}

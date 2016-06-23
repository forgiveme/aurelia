<?php

class ChilliApple_Promotion_Helper_Data extends ChilliApple_Promotion_Helper_Abstract
{
	
	
	public function generateLoyaltyCoupons()
	{	
		$threshold=(float)Mage::getStoreConfig('promotion/loyalty/threshold');
		
		$couponPrice=(float)Mage::getStoreConfig('promotion/loyalty/coupon_amount');
		
		
		if($threshold<=0 || $couponPrice<=0)
		 return;
		 
		$customers = Mage::getModel('customer/customer')->getCollection()
              			    ->addAttributeToSelect('*')
              			    ->addAttributeToFilter('loyalty',array('gteq' => $threshold))->load();
		
		if($customers->count()==0)
		return ;
		
		$couponCode=null;
		$customerGroups = Mage::getModel('customer/group')->getCollection();
		$customerGroupIds = array();

		foreach ($customerGroups AS $customerGroup) {
		    $customerGroupIds[] = $customerGroup->getId();
		}

		$ruleName = 'Loyalty-Coupons';

		$description = 'Loyalty Coupons issued on : '.date('Y-m-d');

		$ruleFromDate = date('Y-m-d');
		$ruleToDate = date('Y-m-d', strtotime("+30 days"));

		/*$rule = $this->createRule($ruleName,$description,$customerGroupIds,$couponCode,
						$couponPrice,$ruleFromDate,$ruleToDate);*/
		$rule = Mage::getModel('salesrule/rule')
            		 ->getCollection()
            		 ->addFieldToFilter('name',array('eq' => $ruleName))
            		 ->getFirstItem();
		if(!$rule->getId())
        {
		  $rule = $this->createRule($ruleName,$description,$customerGroupIds,$couponCode,
						$couponPrice,$ruleFromDate,null);
		}
		else
		{
		    $rule = Mage::getModel('salesrule/rule')->load($rule->getId());
		    $rule->setDiscountAmount($couponPrice);
		    $rule->save();
		}
		$this->_createLoyaltyCoupon($rule,$customers);
	
	}
	
	public function referAFriend($email,$customer)
	{	
		
		$couponPrice=(float)Mage::getStoreConfig('promotion/referafriend/friend_coupon_amount');
		
		
		if($couponPrice<=0)
		 return;
		 
		$couponCode=null;
		$customerGroups = Mage::getModel('customer/group')->getCollection();
		$customerGroupIds = array();

		foreach ($customerGroups as $customerGroup) {
		    $customerGroupIds[] = $customerGroup->getId();
		}

		$ruleName = 'Refer-A-Friend';

		$description = 'Refer a friend coupon issued on : '.date('Y-m-d');

		$ruleFromDate = date('Y-m-d');
		$ruleToDate = null;//date('Y-m-d', strtotime("+30 days"));
		$rule = Mage::getModel('salesrule/rule')
            		 ->getCollection()
            		 ->addFieldToFilter('name',array('eq' => $ruleName))
            		 ->getFirstItem();
                if(!$rule->getId())
                {
		     $rule = $this->createReferAFriendRule($ruleName,$description,$customerGroupIds=array(),$couponCode=null,
						$couponPrice,$ruleFromDate,$ruleToDate=null,$conditions=array(),'by_percent');
		}
		else
		{
		    $rule = Mage::getModel('salesrule/rule')->load($rule->getId());
		    $rule->setDiscountAmount($couponPrice);
		    $rule->save();
		}
		$this->_createReferAFriendCoupon($rule,$customer,$email,$couponPrice);
		return true;
	}
	
	public function sendRefereeCoupon($customer)
	{	
		
		$couponPrice=(float)Mage::getStoreConfig('promotion/referafriend/referee_coupon_amount');
		
		
		if($couponPrice<=0)
		 return;
		 
		$couponCode=null;
		$customerGroups = Mage::getModel('customer/group')->getCollection();
		$customerGroupIds = array();

		foreach ($customerGroups as $customerGroup) {
		    $customerGroupIds[] = $customerGroup->getId();
		}

		$ruleName = 'Referee-voucher';

		$description = 'Referee coupon issued on : '.date('Y-m-d');

		$ruleFromDate = date('Y-m-d');
		$ruleToDate = date('Y-m-d', strtotime("+30 days"));
		$rule = Mage::getModel('salesrule/rule')
            		 ->getCollection()
            		 ->addFieldToFilter('name',array('eq' => $ruleName))
            		 ->getFirstItem();
                if(!$rule->getId())
                {
		  $rule = $this->createRule($ruleName,$description,$customerGroupIds,$couponCode,
						$couponPrice,$ruleFromDate,null);
		}
		else
		{
		    $rule = Mage::getModel('salesrule/rule')->load($rule->getId());
		    $rule->setDiscountAmount($couponPrice);
		    $rule->save();
		}
		
		$this->_createRefereeCoupon($rule,$customer,$couponPrice);
		
		return true;
	}
	
	
	public function birthdayCoupons()
	{	
		
		$couponPrice=(float)Mage::getStoreConfig('promotion/birthday/coupon_amount');
		
		
		if($couponPrice<=0)
		 return;
		 
		$dobAttr=Mage::getSingleton('eav/config')->getAttribute('customer', 'dob');
              	
		$resource=Mage::getSingleton('core/resource');
		$dobTable=$resource->getTableName('customer_entity_datetime');
		
              	
		
		$customers = Mage::getModel('customer/customer')->getCollection()
              			    ->addAttributeToSelect('*');
              	
		$customers->getSelect()->joinInner(
					      array('at_dob'=>$dobTable),
					      "at_dob.entity_id=e.entity_id
					      AND at_dob.attribute_id=".$dobAttr->getId(),
						array('at_dob.value'))
					//->where('DATE_FORMAT(at_dob.value,"%m-%d")=?','08-13');
					->where('DATE_FORMAT(at_dob.value,"%m-%d")=?',date('m-d'));
		
              	$customers->load();
              	
		if($customers->count()==0)
		return ;
		
		$couponCode=null;
		$customerGroups = Mage::getModel('customer/group')->getCollection();
		$customerGroupIds = array();

		foreach ($customerGroups AS $customerGroup) {
		    $customerGroupIds[] = $customerGroup->getId();
		}

		$ruleName = 'Birthday-Coupons';

		$description = 'Birthday Coupons issued on : '.date('Y-m-d');

		$ruleFromDate = date('Y-m-d');
		$ruleToDate = null;//date('Y-m-d', strtotime("+30 days"));

		$rule = Mage::getModel('salesrule/rule')
            		 ->getCollection()
            		 ->addFieldToFilter('name',array('eq' => $ruleName))
            		 ->getFirstItem();
                if(!$rule->getId())
                {
		  $rule = $this->createRule($ruleName,$description,$customerGroupIds,$couponCode,
						$couponPrice,$ruleFromDate,null,'by_percent');
		}
		else
		{
		    $rule = Mage::getModel('salesrule/rule')->load($rule->getId());
		    $rule->setDiscountAmount($couponPrice);
		    $rule->save();
		}
		
		$this->_createBirthDayCoupon($rule,$customers);
	
	}

	public function sendFreeShippingCoupons($customer)
	{	
		

		$couponCode=null;
		$customerGroups = Mage::getModel('customer/group')->getCollection();
		$customerGroupIds = array();

		foreach ($customerGroups as $customerGroup) {
		    $customerGroupIds[] = $customerGroup->getId();
		}

		$ruleName = 'Signup-voucher';

		$description = 'Signup coupon issued on : '.date('Y-m-d');

		$ruleFromDate = date('Y-m-d');
		$ruleToDate = date('Y-m-d', strtotime("+30 days"));
		$rule = Mage::getModel('salesrule/rule')
            		 ->getCollection()
            		 ->addFieldToFilter('name',array('eq' => $ruleName))
            		 ->getFirstItem();
                $couponPrice=0;
                if(!$rule->getId())
                {
		  $rule = $this->createFreeShippingRule($ruleName,$description,$customerGroupIds,null,
						$couponPrice,$ruleFromDate);
		}
		else
		{
		    $rule = Mage::getModel('salesrule/rule')->load($rule->getId());
		    $rule->setDiscountAmount($couponPrice);
		    $rule->save();
		}
		
		$this->_createFreeShippingCoupon($rule,$customer);
		
		return true;
	
	}
	
	protected function _createLoyaltyCoupon($rule,$customers)
	{   
		$threshold=(float)Mage::getStoreConfig('promotion/loyalty/threshold');
	    
	    $couponPrice=(float)Mage::getStoreConfig('promotion/loyalty/coupon_amount');
	    
	    $ccEmailsList=Mage::getStoreConfig('promotion/loyalty/cc_emails');
	    $ccEmails=explode(',', $ccEmailsList);
	    foreach($customers as $customer)
	    {   $customerId=$customer->getId();
	        
	        $couponCode = $this->generateCode($this->_codeLength);
		
                while($this->couponExists($couponCode)) {
                   $couponCode = $this->generateCode($this->_codeLength);
                 }
                $ruleToDate = date('Y-m-d', strtotime("+30 days"));
	   			$coupon = Mage::getModel('salesrule/coupon');
            	$coupon->setId(null)
                ->setRuleId($rule->getId())
                ->setUsageLimit(1)
                ->setUsagePerCustomer(1)
                ->setExpirationDate($ruleToDate)
                ->setCreatedAt(Mage::getSingleton('core/date')->gmtTimestamp())
                ->setType(Mage_SalesRule_Helper_Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED)
                ->setCode($couponCode)
                ->save();
                
    	    	$accAmount=(float)$customer->getLoyalty();
    	    	
    	    	//$totalAmount=-1*$accAmount;
    	    	
    	    	$accAmount=(float)$customer->getLoyalty();

    	    	$totalAmount=0;

    	    	$balance=0;
    	    	
    	    	if($accAmount>$threshold)
    	    	{
    	    		$balance=$accAmount-$threshold;
    	    		$totalAmount=-1*$threshold;
    	    	}
    	    	else
    	    	{
    	    		$totalAmount=-1*$accAmount;
    	    	}

    	    	$description=Mage::helper('promotion')->__('Issued coupon code : %s',$couponCode);
    	    	
    	    	//add new entry in account
    	    	$loyalty=Mage::getModel('promotion/loyalty');
    	    	
    	    	$loyalty->setDescription($description);
    	    	
    	    	$loyalty->setAmount($totalAmount);
    	    	
    	    	$loyalty->setCustomerId($customerId);
    	    	
    	    	$loyalty->setOrderId(null);
    	    	
    	    	
    	    	$loyalty->setCreatedTime(date('Y-m-d H:i:s'));
    	    	
    	    	$loyalty->setUpdateTime(date('Y-m-d H:i:s'));
    	    	
    	    	$loyalty->save();
    	    	
    	    	//update total balance in user account
    	    	
    	    	
    	    	$customer->setLoyalty($balance);
    	    	
    	    	$customer->save();
                ///
                $vars=array();
                $vars['name']=$customer->getName();
                $vars['code']=$couponCode;
                $vars['expiry_date']=$rule->getToDate();
                $vars['amount']=Mage::helper('checkout')->formatPrice($couponPrice);
                $this->sendCouponMail($customer->getEmail(),$customer->getName(),$vars,'loyalty_coupon_email_template',$ccEmails);
	    }
	}

	protected function _createReferAFriendCoupon($rule,$customer,$email,$couponPrice)
	{   

	        $customerId=$customer->getId();
	        
	        $couponCode = $this->generateCode($this->_codeLength);

                while($this->couponExists($couponCode)) {
                   $couponCode = $this->generateCode($this->_codeLength);
                 }
                $ruleToDate = date('Y-m-d', strtotime("+30 days"));
	   	$coupon = Mage::getModel('salesrule/coupon');
            	$coupon->setId(null)
                ->setRuleId($rule->getId())
                ->setUsageLimit(1)
                ->setUsagePerCustomer(1)
                ->setExpirationDate($ruleToDate)
                ->setCreatedAt(Mage::getSingleton('core/date')->gmtTimestamp())
                ->setType(Mage_SalesRule_Helper_Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED)
                ->setCode($couponCode)
                ->save();
                
    	    	$referafriend=Mage::getModel('promotion/referafriend');
    	    	$referafriend->setCustomerId($customerId);
    	    	$referafriend->setFriendEmail($email);
    	    	$referafriend->setCouponCode($couponCode);
    	    	$referafriend->setCreatedTime(date('Y-m-d H:i:s'));
    	    	$referafriend->setUpdateTime(date('Y-m-d H:i:s'));
    	    	$referafriend->save();
                ///
                $vars=array();
                $vars['name']=$customer->getName();
                $vars['code']=$couponCode;
                $vars['expiry_date']=$ruleToDate;
                $vars['amount']=Mage::helper('checkout')->formatPrice($couponPrice);
                $this->sendCouponMail($email,$customer->getName(),$vars,'referafriend_coupon_email_template');
	    
	}
	
	protected function _createRefereeCoupon($rule,$customer,$couponPrice)
	{   

	        $customerId=$customer->getId();
	        
	        $couponCode = $this->generateCode($this->_codeLength);

                while($this->couponExists($couponCode)) {
                   $couponCode = $this->generateCode($this->_codeLength);
                 }
                 
                $ruleToDate = date('Y-m-d', strtotime("+30 days"));
	   	$coupon = Mage::getModel('salesrule/coupon');
            	$coupon->setId(null)
                ->setRuleId($rule->getId())
                ->setUsageLimit(1)
                ->setUsagePerCustomer(1)
                ->setExpirationDate($ruleToDate)
                ->setCreatedAt(Mage::getSingleton('core/date')->gmtTimestamp())
                ->setType(Mage_SalesRule_Helper_Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED)
                ->setCode($couponCode)
                ->save();
                
                $vars=array();
                $vars['name']=$customer->getName();
                $vars['code']=$couponCode;
                $vars['expiry_date']=$ruleToDate;
                $vars['amount']=Mage::helper('checkout')->formatPrice($couponPrice);
                $this->sendCouponMail($customer->getEmail(),$customer->getName(),$vars,'referee_coupon_email_template');
	    
	}
	

	protected function _createBirthDayCoupon($rule,$customers)
	{   
	    $couponPrice=(float)Mage::getStoreConfig('promotion/birthday/coupon_amount');
	    
	    $testMode=(float)Mage::getStoreConfig('promotion/birthday/test_mode');
	    $ccEmailsList=Mage::getStoreConfig('promotion/birthday/cc_emails');
	    $ccEmails=explode(',', $ccEmailsList);
	    $testEmails=array();
	    
	    if($testMode==1)
	    {	$mails=Mage::getStoreConfig('promotion/birthday/test_emails');
	    	$testEmails=explode(',', $mails);
	    }

	    foreach($customers as $customer)
	    {   $customerId=$customer->getId();
	        
	        if($testMode==1 /*&& count($testEmails)*/)
	        {
	          if(!in_array($customer->getEmail(),$testEmails))
	          continue;
	        }

	        $couponCode = $this->generateCode($this->_codeLength);

                while($this->couponExists($couponCode)) {
                   $couponCode = $this->generateCode($this->_codeLength);
                 }
	   	$coupon = Mage::getModel('salesrule/coupon');
	   	$ruleToDate = date('Y-m-d', strtotime("+30 days"));
            	$coupon->setId(null)
                ->setRuleId($rule->getId())
                ->setUsageLimit(1)
                ->setUsagePerCustomer(1)
                ->setExpirationDate($ruleToDate)
                ->setCreatedAt(Mage::getSingleton('core/date')->gmtTimestamp())
                ->setType(Mage_SalesRule_Helper_Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED)
                ->setCode($couponCode)
                ->save();
                    	    	
    	    	$description=Mage::helper('promotion')->__('Issued coupon code : %s',$couponCode);
    	    	
    	    	//add new entry in account
    	    	$birthday=Mage::getModel('promotion/birthday');
    	    	
    	    	$birthday->setDescription($description);
    	    	
    	    	$birthday->setAmount($couponPrice);
    	    	
    	    	$birthday->setCustomerId($customerId);
    	    	    	    	
    	    	$birthday->setCouponCode($couponCode);
    	    	
    	    	$birthday->setCreatedTime(date('Y-m-d H:i:s'));
    	    	
    	    	$birthday->setUpdateTime(date('Y-m-d H:i:s'));
    	    	
    	    	$birthday->save();
    	    	
                ///
                $vars=array();
                $vars['name']=$customer->getName();
                $vars['code']=$couponCode;
                $vars['expiry_date']=$rule->getToDate();
                $vars['amount']=Mage::helper('checkout')->formatPrice($couponPrice);
                $this->sendCouponMail($customer->getEmail(),$customer->getName(),$vars,'birthday_coupon_email_template',$ccEmails);
	    }
	}
	
	
	protected function _createFreeShippingCoupon($rule,$customer)
	{   
	       $couponPrice=0;
	
	 
	        $customerId=$customer->getId();
	        
	        $couponCode = $this->generateCode($this->_codeLength);

                while($this->couponExists($couponCode)) {
                   $couponCode = $this->generateCode($this->_codeLength);
                 }
	   	$coupon = Mage::getModel('salesrule/coupon');
	   	$ruleToDate = date('Y-m-d', strtotime("+30 days"));
            	$coupon->setId(null)
                ->setRuleId($rule->getId())
                ->setUsageLimit(1)
                ->setUsagePerCustomer(1)
                ->setExpirationDate($ruleToDate)
                ->setCreatedAt(Mage::getSingleton('core/date')->gmtTimestamp())
                ->setType(Mage_SalesRule_Helper_Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED)
                ->setCode($couponCode)
                ->save();
                    	    	    	    	
    	    	//$customer->setCouponCode($couponCode);
                ///
                $vars=array();
                $vars['name']=$customer->getName();
                $vars['code']=$couponCode;
                $vars['expiry_date']=$ruleToDate;
                //$vars['amount']=Mage::helper('checkout')->formatPrice($couponPrice);
                $this->sendCouponMail($customer->getEmail(),$customer->getName(),$vars,'promotion_new_customer');
	    
	}
	
}

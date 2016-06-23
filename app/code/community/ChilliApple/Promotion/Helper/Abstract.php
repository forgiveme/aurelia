<?php

abstract class  ChilliApple_Promotion_Helper_Abstract extends Mage_Core_Helper_Abstract
{
	protected $_codeLength=12;
	
	
	
	public function createRule($ruleName,$description,$customerGroupIds=array(),$couponCode=null,
						$couponPrice,$ruleFromDate,$ruleToDate=null,$simpleAction='cart_fixed')
	{
	        $rule = Mage::getModel('salesrule/rule');
		$rule->setName($ruleName);
		$rule->setDescription($description);
		$rule->setUsesPerCoupon(1);
		$rule->setUsesPerCustomer(1);
		$rule->setCustomerGroupIds(implode(',', $customerGroupIds));
		$rule->setIsActive(1);
		$rule->setStopRulesProcessing(0);
		$rule->setIsAdvanced(1);
		$rule->setSortOrder('0');
		$rule->setSimpleAction($simpleAction);
		$rule->setDiscountAmount($couponPrice);
		$rule->setDiscountStep(0);
		$rule->setSimpleFreeShipping(0);
		$rule->setApplyToShipping(0);
		$rule->setCouponType(2);
		$rule->setUseAutoGeneration(1);
		$rule->setCouponCode($couponCode);
		$rule->setUsesPerCoupon(1);
		$rule->setTimesUsed(0);
		$rule->setIsRss(0);
		$rule->setWebsiteIds('1');
		$rule->setFromDate($ruleFromDate);
		$rule->setToDate($ruleToDate);
		$rule->save();
		return $rule;
	}

	public function createFreeShippingRule($ruleName,$description,$customerGroupIds=array(),$couponCode=null,
						$couponPrice,$ruleFromDate,$ruleToDate=null,$conditions=array())
	{
	      if(empty($conditions))
	      {
		$conditions=array(
			    'type'=>'salesrule/rule_condition_combine',
			    'attribute'=>null,
			    'operator'=>null,
			    'value'   => 1,
			    'is_value_processed'=>null,
			    'aggregator'=>'all',
			    'conditions' => array(
			    	   		array(
			    	   		'type' => 'salesrule/rule_condition_address',
			    	   		'attribute' => 'country_id',
			    	   	 	'operator' => '==',
			    	   	 	'value'    => 'GB',
			    	   	 	'is_value_processed'=>null	
			    	   	     ),
			    	   )
			 	);
		}
	        $rule = Mage::getModel('salesrule/rule');
		$rule->setName($ruleName);
		$rule->setDescription($description);
		$rule->setUsesPerCoupon(1);
		$rule->setUsesPerCustomer(1);
		$rule->setCustomerGroupIds(implode(',', $customerGroupIds));
		$rule->setIsActive(1);
		$rule->setStopRulesProcessing(0);
		$rule->setIsAdvanced(1);
		$rule->setSortOrder('0');
		$rule->setSimpleAction('cart_fixed');//by_percent
		$rule->setDiscountAmount($couponPrice);
		$rule->setDiscountStep(0);
		$rule->setSimpleFreeShipping(2);
		$rule->setApplyToShipping(1);
		$rule->setCouponType(2);
		$rule->setUseAutoGeneration(1);
		$rule->setCouponCode($couponCode);
		$rule->setUsesPerCoupon(1);
		$rule->setTimesUsed(0);
		$rule->setIsRss(0);
		$rule->setWebsiteIds('1');
		$rule->setFromDate($ruleFromDate);
		$rule->setToDate($ruleToDate);
		if(!empty($conditions))
		{    $serialized=serialize($conditions);
		     $rule->setConditionsSerialized($serialized);
		}
		$rule->save();
		return $rule;
	}

	public function createReferAFriendRule($ruleName,$description,$customerGroupIds=array(),$couponCode=null,
						$couponPrice,$ruleFromDate,$ruleToDate=null,$conditions=array(),$simpleAction='cart_fixed')
	{
	      if(empty($conditions))
	      {
		$conditions=array(
			    'type'=>'salesrule/rule_condition_combine',
			    'attribute'=>null,
			    'operator'=>null,
			    'value'   => 1,
			    'is_value_processed'=>null,
			    'aggregator'=>'all',
			    'conditions' => array(
			    	   		array(
			    	   		'type' => 'salesrule/rule_condition_address',
			    	   		'attribute' => 'base_subtotal',
			    	   	 	'operator' => '>=',
			    	   	 	'value'    => '35',
			    	   	 	'is_value_processed'=>null	
			    	   	     ),
			    	   )
			 	);
		}
	        $rule = Mage::getModel('salesrule/rule');
		$rule->setName($ruleName);
		$rule->setDescription($description);
		$rule->setUsesPerCoupon(1);
		$rule->setUsesPerCustomer(1);
		$rule->setCustomerGroupIds(implode(',', $customerGroupIds));
		$rule->setIsActive(1);
		$rule->setStopRulesProcessing(0);
		$rule->setIsAdvanced(1);
		$rule->setSortOrder('0');
		$rule->setSimpleAction($simpleAction);//by_percent
		$rule->setDiscountAmount($couponPrice);
		$rule->setDiscountStep(0);
		$rule->setSimpleFreeShipping(2);
		$rule->setApplyToShipping(0);
		$rule->setCouponType(2);
		$rule->setUseAutoGeneration(1);
		$rule->setCouponCode($couponCode);
		$rule->setUsesPerCoupon(1);
		$rule->setTimesUsed(0);
		$rule->setIsRss(0);
		$rule->setWebsiteIds('1');
		$rule->setFromDate($ruleFromDate);
		$rule->setToDate($ruleToDate);
		if(!empty($conditions))
		{    $serialized=serialize($conditions);
		     $rule->setConditionsSerialized($serialized);
		}
		$rule->save();
		return $rule;
	}

	public function sendCouponMail($toEmail,$toName,$vars,$template,array $ccMails=array())
	{
	     $emailTemplate  = Mage::getModel('core/email_template')->loadDefault($template);
	     $senderName=Mage::getStoreConfig('trans_email/ident_sales/name');
	     $senderEmail=Mage::getStoreConfig('system/smtpsettings/username');
	     $emailTemplate->setSenderName($senderName);
	     $emailTemplate->setSenderEmail($senderEmail);

	     foreach ($ccMails as $ccmail) {
	     	$emailTemplate->addBcc($ccmail);
	     	}
	     	
	     $emailTemplate->send($toEmail,$toName, $vars);	
	
	}
	
	public function couponExists($couponCode)
	{
	   $coupon = Mage::getModel('salesrule/coupon')
            		 ->getCollection()
            		 ->addFieldToFilter('code',array('eq' => $couponCode))
            		 ->getFirstItem();
            	if ($coupon->getId()) {
            		return true;
        	}

        	return false;
	}
	
	public function generateCode($length=null,$prefix=null)
	{
		if (empty($length)) {
            	$length = $this->_codeLength;
        	}
        	$code = $prefix.crypt(uniqid(rand(),1));
        	$code = strip_tags(stripslashes($code));
        	$code = str_replace(array(".", "$"),"",$code);
        	$code = strrev(str_replace("/","",$code));
        	$couponCode=null;
        	if (!is_null($code)) {
            	$couponCode = strtoupper(substr($code, 0, $length));
        	} else {
            	$couponCode = strtoupper($code);
        	}

       		 return $couponCode;
	}
}

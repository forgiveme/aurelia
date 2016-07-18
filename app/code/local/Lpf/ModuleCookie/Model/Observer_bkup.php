<?php class Lpf_ModuleCookie_Model_Observer
{

     
	  public function customerLogin(Varien_Event_Observer $observer)
            {

                $lastQid = Mage::getSingleton('checkout/session')->getQuoteId(); //quote id during session before login;
                if ($lastQid) { //before login session exists means cart has items
                    $customerQuote = Mage::getModel('sales/quote')
                    ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomerId()); //the cart from last login
                    //set it to the session before login and remove its items if any
					
					
                    $customerQuote->setQuoteId($lastQid);
					
					$ruleId= $customerQuote->getAppliedRuleIds();
					$couponCode= $customerQuote->getCouponCode();
					$coll = Mage::getResourceModel('salesrule/rule_collection')->addFieldToFilter('code', array('in'=>$couponCode));
					 foreach($coll as $_coll){
							$code = $_coll['code'];
							$enddate = $_coll['expiration_date'];
							$actionForFreeProdcuts=$_coll['simple_action'];
							$promoSku = $_coll['promo_sku'];
							
							
						}
				$currentdate = date("Y-m-d");
				$dates = explode(' ',$enddate);
				echo $dates[0];
				
				
				echo $actionForFreeProdcuts;
				echo $promoSku."<br>";
        /* End custom code */
                    $this->_removeAllItems($customerQuote);

                } else { //no session before login, so empty the cart (current cart is the old cart)
                    $quote = Mage::getModel('checkout/session')->getQuote();
                    $this->_removeAllItems($quote);
                }
				
			exit();
                }

				
                protected function _removeAllItems($quote){

                    foreach ($quote->getAllItems() as $item) {
                     echo "<pre>";
					 print_r($item->getName());
                    }
                  
                } //_removeAllItems
	 
	 
	 /**
      * Run couple of 'php' codes after customer logs in
      *
      * @param Varien_Event_Observer $observer
      */
   //  public function customerLogin1($observer)
  //   {
         // Mage::log(__METHOD__ . '() Hello!'); // Remove afterwards. Check your var/log/system.log to see if came to this point
         // $customer = $observer->getCustomer();
		 // print_r($customer);
		 
		  // exit();
		  
		  
		  
		  
		  
		 
		
  //   }

}

?>
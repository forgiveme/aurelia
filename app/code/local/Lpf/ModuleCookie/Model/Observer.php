<?php class Lpf_ModuleCookie_Model_Observer
{

     
	  public function customerLogin(Varien_Event_Observer $observer)
            {

                $lastQid = Mage::getSingleton('checkout/session')->getQuoteId(); //quote id during session before login;
				
                if ($lastQid) :  //before login session exists means cart has items
				
                    $customerQuote = Mage::getModel('sales/quote')
                    ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomerId()); //the cart from last login
                    //set it to the session before login and remove its items if any

                    $customerQuote->setQuoteId($lastQid);
					
					$ruleId= $customerQuote->getAppliedRuleIds();
					$couponCode= $customerQuote->getCouponCode();
					$coll = Mage::getResourceModel('salesrule/rule_collection')->addFieldToFilter('code', array('in'=>$couponCode));
					
					 foreach($coll as $_coll):
						// $data[]=$coll->toArray();
							$code = $_coll['code'];
							$enddate = $_coll['to_date'];
							$actionForFreeProdcuts=$_coll['simple_action'];
							$promoSku = $_coll['promo_sku'];
							
					endforeach;	
							
						//print_r($data);
						$currentdate = date("Y-m-d");
						$currentdate = strtotime($currentdate);
						$dates = explode(' ',$enddate);
					//	echo "------------ ";
					//	print_r($dates);
					//	echo "------------ ";
					//	echo $actionForFreeProdcuts;
					//	echo $promoSku."<br>";
						$time=strtotime($dates[0]);
						
						if($dates[0]!=""):
						if($currentdate > $time):
								
							//	echo "we are going to deletrte the offer and their free produts";

							
							/* End custom code */
							if($actionForFreeProdcuts =="ampromo_product"):
									$this->_removeAllItems($customerQuote);
								
								else:
								
							  $this->_removeAllItems2($customerQuote,$promoSku);
								
								endif;
							//    $this->_removeAllItems($customerQuote);
							//$customerQuote->save();
						
							endif;	
						endif;

						 else: //no session before login, so empty the cart (current cart is the old cart)
							//$quote = Mage::getModel('checkout/session')->getQuote();
							//$this->_removeAllItems($quote);
						endif;
				
			// exit();
		//	Mage::app()->getFrontController()->getResponse()->setRedirect('http://skinaurelia.newsoftdemo.info/index.php/checkout/cart/');
                }

				
                protected function _removeAllItems($quote){

                    foreach ($quote->getAllItems() as $item) :
                     $proData['name']= $item->getName();
					 $proData['price']= $item->getPrice();
					 $categoryId = $item->getProduct()->getCategoryIds();
						if($categoryId[0]==16):
						
						// do nothing 
						
						else:
						$baseAmount = $item->getBasePrice();
						if($baseAmount==0  ):
					
						$itemId = $item->getItemId();
						
						$item->delete();
						
						//Mage::getSingleton('checkout/cart')->removeItem($itemId)->save();
					//	$cartHelper = Mage::helper('checkout/cart');
					//	$cartHelper->getCart()->removeItem($itemId)->save();
						
						endif;
						
						endif;
					 
				//	 echo "<pre>";
					 
					// print_r($proData);
                    endforeach;
                   	Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                } //_removeAllItems
	 
	 
	    protected function _removeAllItems2($quote,$promoSku){

                    foreach ($quote->getAllItems() as $item) {
                     $proData['name']= $item->getName();
					 $proData['price']= $item->getPrice();
					$sku =  $item->getSku();
					 
					$discountProducts =  explode(',',$promoSku);
					if( in_array($sku, $discountProducts)){
					
					//echo $sku;
					//echo "<br>";
					//echo $item->getItemId();
					//echo "<br>";
					
					$item->delete();
					$item->save();
				
					}
				
					 
					
					 
					// echo "<pre>";
					 
					// print_r($proData);
                    }
                 	Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
					$quote->save();
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
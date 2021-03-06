<?php
class As_Customfee_Model_Sales_Quote_Address_Total_Customfee extends Mage_Sales_Model_Quote_Address_Total_Abstract{
    protected $_code = 'customfee';
 
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
 
        $this->_setAmount(0);
        $this->_setBaseAmount(0);
 
        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this; //this makes only address type shipping to come through
        }
 
 
        $quote = $address->getQuote();
 
        if(As_Customfee_Model_Customfee::canApply($address)){ //your business logic
            $exist_amount = $quote->getCustomfeeAmount();
            $customfee = As_Customfee_Model_Customfee::getCustomfee();
			//for compete order fee one time
            $balance = $customfee - $exist_amount;
            $address->setCustomfeeAmount($customfee);
            $address->setBaseCustomfeeAmount($customfee);
                 
            $quote->setCustomfeeAmount($customfee);
 
            $address->setGrandTotal($address->getGrandTotal() + $address->getCustomfeeAmount());
            $address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseCustomfeeAmount());
        }
    }
 
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amt = $address->getCustomfeeAmount();
		if($amt) {
			$address->addTotal(array(
					'code'=>$this->getCode(),
					'title'=>Mage::helper('customfee')->__('Skin Tool Offer'),
					'value'=> $amt
			));
		}
		return $this;
    }
}
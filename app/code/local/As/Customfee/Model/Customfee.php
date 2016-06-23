<?php
class As_Customfee_Model_Customfee extends Varien_Object
{

    /**
     * Fee Amount
     *
     * @var int
     */
    const FEE_AMOUNT = -10;

    /**
     * Retrieve Fee Amount
     *
     * @static
     * @return int
     */
    public static function getCustomFee()
    {
        return self::FEE_AMOUNT;
    }

    /**
     * Check if fee can be apply
     *
     * @static
     * @param Mage_Sales_Model_Quote_Address $address
     * @return bool
     */
    public static function canApply($address)
    {
		/*$quote = $address->getQuote();
		if(!$quote->getIsMultiShipping()) {
			if($quote->getInscription()!='') {
				return true;
			}
		} else {
			$addressData = Mage::getModel('sales/quote_address')->load($address->getId())->getData();
			if($addressData['inscription']!='') {
				return true;
			}
		}*/
		
		$customerData = Mage::getSingleton('customer/session')->getCustomer()->getData();
		if($customerData['entity_id']!='' && $customerData['used_first_order_discount']!='4') {
			
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
			$query = "SELECT * FROM skintools_emails left join skintools_questionsdata on skintools_emails.questiondata_id = skintools_questionsdata.id WHERE skintools_emails.address = '".$customerData['email']."' LIMIT 1";

			$collection = $readConnection->fetchAll($query);
			
			if(count($collection)>0) {
				Mage::helper('checkout/cart')->getQuote()->setData('coupon_code','')->save(); 
				return true;
				
			} else {
				
				return false;
				
			}
		} else {
			return false;
		}
		
    }

}
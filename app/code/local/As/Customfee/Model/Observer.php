<?php
class As_Customfee_Model_Observer
{
    /**
     * Set fee amount invoiced to the order
     *
     * @param Varien_Event_Observer $observer
     * @return As_Customfee_Model_Observer
     */
    public function invoiceSaveAfter(Varien_Event_Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();

        if ($invoice->getBaseCustomfeeAmount()) {
            $order = $invoice->getOrder();
            $order->setCustomfeeAmountInvoiced($order->getCustomfeeAmountInvoiced() + $invoice->getCustomfeeAmount());
            $order->setBaseCustomfeeAmountInvoiced($order->getBaseCustomfeeAmountInvoiced() + $invoice->getBaseCustomfeeAmount());
        }

        return $this;
    }
	
	public function customerAtrributesave(Varien_Event_Observer $observer) {
		
		$order = $observer->getEvent()->getOrder();
		//echo "tertetretet--".$order->getCustomfeeAmount();
			//exit;
		if($order->getCustomfeeAmount()) {
			//echo "ttrtrttetret--".$order->getCustomfeeAmount();
			//exit;
			mail("sanford@123789.org", "test", "test");
			$customer_id = $order->getCustomerId();
			$customerData = Mage::getModel('customer/customer')->load($customer_id);
			$customerData->setData( 'used_first_order_discount', 4 );
			$customerData->save();
		}
		//exit;
	}
}
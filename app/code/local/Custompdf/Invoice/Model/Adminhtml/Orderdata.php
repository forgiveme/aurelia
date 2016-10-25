<?php
Class Custompdf_Invoice_Model_Adminhtml_Orderdata
{
	protected $_orderId;
	
	public function getOrderdata($orderId) {
		
		$this->_orderId = $orderId;
		
		$orderData = Mage::getModel("sales/order")->load($orderId);
		
		return $orderData;

	}
	
}
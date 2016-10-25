<?php
class Custompdf_Invoice_Block_Adminhtml_Custompdf extends Mage_Adminhtml_Block_Template {
	
	public function _construct()
    {
       parent::_construct();
    }
	
	public function getorderData() {
		
		if ($data = $this->getRequest()->getParams()) {
			
			$params = $this->getRequest()->getParams();
			
			$model = Mage::getModel('custompdfinvoice/adminhtml_orderdata');
				
			$orderData = $model->getOrderdata($params['order_id']);
			
			return $orderData;
			
		}
	}
	
	public function getcartproductData($orderobj) {
		
		$retArray = array();
		$i = 0;
		foreach($orderobj->getAllVisibleItems() as $items) {
			
			if(stristr($items->getName(), 'sample') === FALSE) {
				
				$retArray[$i]['name'] = $items->getName();
				$retArray[$i]['qty'] = $items->getqtyOrdered();
				$retArray[$i]['price'] = $items->getPrice();
				$i++;
			}
		}
		return $retArray;
	}
	
	public function getcartsampleData($orderobj) {
		
		$retArray = array();
		$i = 0;
		foreach($orderobj->getAllVisibleItems() as $items) {
			
			if(stristr($items->getName(), 'sample')) {

				$retArray[$i]['name'] = $items->getName();
				$retArray[$i]['qty'] = $items->getqtyOrdered();
				$retArray[$i]['price'] = $items->getPrice();
				$i++;
			}
		
		}
		return $retArray;
	}
	
	public function getconfigData($key) {
		
		return Mage::getStoreConfig('custompdfinvoice/custompdf_group/'.$key);
		
	}
	
	public function getCleanurl($key) {
		return str_replace(array('http://', '/index.php'), array('','', ''), $this->getUrl($key));
	}
	
}
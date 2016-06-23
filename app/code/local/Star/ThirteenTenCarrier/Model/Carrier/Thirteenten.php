<?php
 
class Star_ThirteenTenCarrier_Model_Carrier_ThirteenTen
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface {
    protected $_code = 'ten13';
    protected $_isFixed = true;
 
 	public function isTrackingAvailable() {
        return true;
    }

	//Mandatory function - if excluded the tracking portion of the shipment screen completely fails
    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
    }

 	//Mandatory function - if excluded the tracking portion of the shipment screen completely fails
    public function getAllowedMethods() {
    }

}

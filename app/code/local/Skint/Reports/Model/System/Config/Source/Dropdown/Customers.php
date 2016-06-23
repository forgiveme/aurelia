<?php
class Skint_Reports_Model_System_Config_Source_Dropdown_Customers {
	public function toOptionArray() {
		
		$optionArray = array();
		$optionArray[] = array(
								'value' => 'all',
								'label' => 'Select All',
							);
		$collection = Mage::getResourceModel('customer/customer_collection')->addNameToSelect();
		foreach($collection as $customer) {
			$customerData = $customer->getData();
			$optionArray[] = array(
								'value' => $customerData['entity_id'],
								'label' => $customerData['firstname']." ".$customerData['lastname'].'( '.$customerData['email'].' )',
							);
		}
        return $optionArray;
    }
}
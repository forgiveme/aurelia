<?php
class Skint_Reports_Model_System_Config_Source_Dropdown_Products {
	public function toOptionArray() {
		
		$optionArray = array();
		$optionArray[] = array(
								'value' => 'all',
								'label' => 'Select All',
							);
		$collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('name');
		foreach($collection as $product) {
			$productData = $product->getData();
			$optionArray[] = array(
								'value' => $productData['entity_id'],
								'label' => $productData['name'].'( '.$productData['sku'].' )',
							);
		}
        return $optionArray;
    }
}
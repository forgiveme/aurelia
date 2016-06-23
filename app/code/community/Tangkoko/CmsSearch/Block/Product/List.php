<?php

class Tangkoko_CmsSearch_Block_Product_List extends Mage_Catalog_Block_Product_List
{

	public function setCategoryId($id)
	{
		$category = Mage::getModel('catalog/category')->load($id);
		$this->getLayer()->setCurrentCategory($category);
	}
	
}
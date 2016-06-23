<?php 
class Tangkoko_CmsSearch_Model_Config extends Mage_Core_Model_Abstract
{
	protected $_types;
	protected $_modulesName;
	
	public function getSearchableTypes()
	{
		if(!$this->_types)
		{
			foreach(Mage::getConfig()->getNode('searchables_types')->children() as $type)
			{
				if ($this->verifyIndex($type->suffix))
					$this->_types["$type->class"] = array("suffix" => "$type->suffix", "module_name" => "$type->module_name");
			}
		}
		//Mage::log($this->_types);
		return $this->_types;
	}
	
	public function isActive($moduleName){
		return Mage::helper('core')->isModuleEnabled($moduleName);
	}
	
	public function verifyIndex($module)
	{
		$helper = Mage::helper('cmssearch');
		
		if (strcmp($module, "faq") == 0) 
			if (!($helper->isFaqSearchable()))
				return false;
		if (strcmp($module, "category") == 0)
			if (!($helper->isCategorySearchable()))
				return false;
		if (strcmp($module, "page") == 0)
			if (!($helper->isPageSearchable()))
				return false;
		if (strcmp($module, "blog") == 0)
			if (!($helper->isBlogSearchable()))
				return false;
		return true;
	}
}
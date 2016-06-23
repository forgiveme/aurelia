<?php

class Tangkoko_CmsSearch_Model_Mysql4_Searchable_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct()
    {
		parent::_construct();
        $this->_init('cmssearch/searchable'); 
    }
}
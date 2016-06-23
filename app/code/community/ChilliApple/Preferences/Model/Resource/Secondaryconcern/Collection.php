<?php
class ChilliApple_Preferences_Model_Resource_Secondaryconcern_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	
	/**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('preferences/secondaryconcern');
    }
}
<?php
class ChilliApple_Preferences_Model_Resource_Skincare extends Mage_Core_Model_Resource_Db_Abstract
{

	/**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('preferences/skin_care', 'skin_care_id');
    }

}

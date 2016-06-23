<?php
class ChilliApple_Preferences_Model_Resource_Secondaryconcern extends Mage_Core_Model_Resource_Db_Abstract
{

	/**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('preferences/secondary_concern', 'secondary_concern_id');
    }

}

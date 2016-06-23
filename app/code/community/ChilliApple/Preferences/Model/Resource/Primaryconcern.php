<?php
class ChilliApple_Preferences_Model_Resource_Primaryconcern extends Mage_Core_Model_Resource_Db_Abstract
{

	/**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('preferences/primary_concern', 'primary_concern_id');
    }

}

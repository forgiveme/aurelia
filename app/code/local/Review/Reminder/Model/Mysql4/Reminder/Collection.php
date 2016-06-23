<?php

class Review_Reminder_Model_Mysql4_Reminder_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('reminder/reminder');
    }
}
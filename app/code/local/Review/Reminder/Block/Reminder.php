<?php
class Review_Reminder_Block_Reminder extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getReminder()     
     { 
        if (!$this->hasData('reminder')) {
            $this->setData('reminder', Mage::registry('reminder'));
        }
        return $this->getData('reminder');
        
    }
}
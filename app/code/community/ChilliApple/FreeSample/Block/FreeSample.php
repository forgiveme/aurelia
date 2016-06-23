<?php
class ChilliApple_FreeSample_Block_FreeSample extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getFreeSample()     
     { 
        if (!$this->hasData('freesample')) {
            $this->setData('freesample', Mage::registry('freesample'));
        }
        return $this->getData('freesample');
        
    }
}
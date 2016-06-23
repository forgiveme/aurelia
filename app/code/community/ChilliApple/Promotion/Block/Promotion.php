<?php
class ChilliApple_Promotion_Block_Promotion extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getPromotion()     
     { 
        if (!$this->hasData('promotion')) {
            $this->setData('promotion', Mage::registry('promotion'));
        }
        return $this->getData('promotion');
        
    }
}
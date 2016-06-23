<?php
class As_Emailpreview_Block_Emailpreview extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getEmailpreview()     
     { 
        if (!$this->hasData('emailpreview')) {
            $this->setData('emailpreview', Mage::registry('emailpreview'));
        }
        return $this->getData('emailpreview');
        
    }
}
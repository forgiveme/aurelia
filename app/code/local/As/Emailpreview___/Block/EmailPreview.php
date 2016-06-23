<?php
class As_EmailPreview_Block_EmailPreview extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getEmailPreview()     
     { 
        if (!$this->hasData('emailpreview')) {
            $this->setData('emailpreview', Mage::registry('emailpreview'));
        }
        return $this->getData('emailpreview');
        
    }
}
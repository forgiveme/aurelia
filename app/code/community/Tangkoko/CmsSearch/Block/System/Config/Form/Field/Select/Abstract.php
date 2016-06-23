<?php
    
abstract class Tangkoko_CmsSearch_Block_System_Config_Form_Field_Select_Abstract
    extends Mage_Core_Block_Html_Select
{
    abstract protected function _getSourceModelName();
    
    public function setInputName($value)
    {
        return $this->setName($value);
    }
    
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $options = Mage::getModel("cmssearch/system_config_source_field_block")->toOptionArray();
			
            foreach ($options as $option) {
				$this->addOption($option["value"], $option["label"]);
			}
        }
        return parent::_toHtml();
    }
}
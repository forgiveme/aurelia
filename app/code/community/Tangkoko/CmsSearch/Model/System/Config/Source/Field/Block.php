<?php

class Tangkoko_CmsSearch_Model_System_Config_Source_Field_Block extends mage_Core_Block_Abstract
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = array(array("value" => "title", "label" => $this->__("Title")),
									array("value" => "contents", "label" => $this->__("Contents")),
									array("value" => "description", "label" => $this->__("Description")),
									array("value" => "meta_keyword", "label" => $this->__("Meta Keywords")),
									array("value" => "identifier", "label" => $this->__("Identifier"))								
								);
        }
        return $this->_options;
    }
}
?>
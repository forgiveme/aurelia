<?php
class Tangkoko_CmsSearch_Block_System_Config_Form_Field_Weight
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	protected $_cmsBlockSelectRenderer = null;
	
	protected function _getCmsBlockSelectRenderer()
    {
        if (is_null($this->_cmsBlockSelectRenderer)) {
            $this->_cmsBlockSelectRenderer = $this->getLayout()->createBlock(
                'cmssearch/system_config_form_field_select_cms_block', '',
                array('is_render_to_js_template' => true)
            );
           $this->_cmsBlockSelectRenderer->setExtraParams('style="width:200px"');
        }
        return $this->_cmsBlockSelectRenderer;
    }
	
	protected function _prepareToRender()
	{
		$this->addColumn('attribute', array(
            'label'    =>  $this->__("Page Field"),
            'renderer' => $this->_getCmsBlockSelectRenderer(),
        ));
        $this->addColumn('weight', array(
            'label' => $this->__("Weight"),
            'style' => 'width:40px;',
        ));
        $this->_addButtonLabel = $this->__('Add Field');
	}
	
	protected function _prepareArrayRow(Varien_Object $row)
		{		
			parent::_prepareArrayRow($row);
			$row->setData(
				'option_extra_attr_'.$this->_getCmsBlockSelectRenderer()->calcOptionHash($row->getData('attribute')),
				'selected="selected"'
			);
		}
}
?>
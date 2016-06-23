<?php

class ChilliApple_Preferences_Block_Adminhtml_Skincare_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'preferences';
        $this->_controller = 'adminhtml_skincare';
        
        $this->_updateButton('save', 'label', Mage::helper('preferences')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('preferences')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('skincare_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'skincare_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'skincare_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('skincare_data') && Mage::registry('skincare_data')->getId() ) {
            return Mage::helper('preferences')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('skincare_data')->getTitle()));
        } else {
            return Mage::helper('preferences')->__('Add Item');
        }
    }
}

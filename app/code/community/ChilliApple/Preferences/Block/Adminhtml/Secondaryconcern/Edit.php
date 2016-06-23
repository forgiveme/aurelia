<?php

class ChilliApple_Preferences_Block_Adminhtml_Secondaryconcern_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'preferences';
        $this->_controller = 'adminhtml_secondaryconcern';
        
        $this->_updateButton('save', 'label', Mage::helper('preferences')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('preferences')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('secondaryconcern_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'secondaryconcern_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'secondaryconcern_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('secondaryconcern_data') && Mage::registry('secondaryconcern_data')->getId() ) {
            return Mage::helper('preferences')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('secondaryconcern_data')->getTitle()));
        } else {
            return Mage::helper('preferences')->__('Add Item');
        }
    }
}

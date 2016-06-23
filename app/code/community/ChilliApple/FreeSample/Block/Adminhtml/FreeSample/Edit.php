<?php

class ChilliApple_FreeSample_Block_Adminhtml_FreeSample_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'freesample';
        $this->_controller = 'adminhtml_freesample';
        
        $this->_updateButton('save', 'label', Mage::helper('freesample')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('freesample')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('freesample_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'freesample_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'freesample_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('freesample_data') && Mage::registry('freesample_data')->getId() ) {
            return Mage::helper('freesample')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('freesample_data')->getTitle()));
        } else {
            return Mage::helper('freesample')->__('Add Item');
        }
    }
}
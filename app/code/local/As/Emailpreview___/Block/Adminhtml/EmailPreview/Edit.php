<?php

class As_EmailPreview_Block_Adminhtml_EmailPreview_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'emailpreview';
        $this->_controller = 'adminhtml_emailpreview';
        
        $this->_updateButton('save', 'label', Mage::helper('emailpreview')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('emailpreview')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('emailpreview_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'emailpreview_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'emailpreview_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('emailpreview_data') && Mage::registry('emailpreview_data')->getId() ) {
            return Mage::helper('emailpreview')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('emailpreview_data')->getTitle()));
        } else {
            return Mage::helper('emailpreview')->__('Add Item');
        }
    }
}
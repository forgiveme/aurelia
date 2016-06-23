<?php

class ChilliApple_Promotion_Block_Adminhtml_Promotion_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'promotion';
        $this->_controller = 'adminhtml_promotion';
        
        $this->_updateButton('save', 'label', Mage::helper('promotion')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('promotion')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('promotion_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'promotion_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'promotion_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('promotion_data') && Mage::registry('promotion_data')->getId() ) {
            return Mage::helper('promotion')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('promotion_data')->getTitle()));
        } else {
            return Mage::helper('promotion')->__('Add Item');
        }
    }
}
<?php

class Stardigital_Productsalesreport_Block_Adminhtml_Productsalesreport_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'productsalesreport';
        $this->_controller = 'adminhtml_productsalesreport';

        $this->_updateButton(
            'save',
            'label',
            Mage::helper('productsalesreport')->__('Save Item')
        );

        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('productsalesreport')->__('Delete Item')
        );

        $this->_addButton(
            'saveandcontinue',
            array(
                'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class' => 'save',
            ),
            -100
        );

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('productsalesreport_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'productsalesreport_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'productsalesreport_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if (Mage::registry('productsalesreport_data') && Mage::registry('productsalesreport_data')->getId()) {
            return Mage::helper('productsalesreport')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('productsalesreport_data')->getTitle()));
        }
        else {
            return Mage::helper('productsalesreport')->__('Add Item');
        }
    }
}

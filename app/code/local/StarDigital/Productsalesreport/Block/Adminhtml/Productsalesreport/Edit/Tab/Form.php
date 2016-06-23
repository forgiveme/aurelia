<?php

class Stardigital_Productsalesreport_Block_Adminhtml_Productsalesreport_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'productsalesreport_form',
            array(
                'legend'=>Mage::helper('productsalesreport')->__('Item information')
            )
        );

        $fieldset->addField(
            'title',
            'text',
            array(
                'label' => Mage::helper('productsalesreport')->__('Title'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'title',
            )
        );

        $fieldset->addField(
            'filename',
            'file',
            array(
                'label' => Mage::helper('productsalesreport')->__('File'),
                'required' => false,
                'name' => 'filename',
            )
        );

        $fieldset->addField(
            'status',
            'select',
            array(
                'label' => Mage::helper('productsalesreport')->__('Status'),
                'name' => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('productsalesreport')->__('Enabled'),
                    ),

                    array(
                        'value' => 2,
                        'label' => Mage::helper('productsalesreport')->__('Disabled'),
                    ),
                ),
            )
        );

        $fieldset->addField(
            'content',
            'editor',
            array(
                'name' => 'content',
                'label' => Mage::helper('productsalesreport')->__('Content'),
                'title' => Mage::helper('productsalesreport')->__('Content'),
                'style' => 'width:700px; height:500px;',
                'wysiwyg' => false,
                'required' => true,
            )
        );

        if (Mage::getSingleton('adminhtml/session')->getProductsalesreportData()) {
            $form->setValues(
                Mage::getSingleton('adminhtml/session')->getProductsalesreportData()
            );
            Mage::getSingleton('adminhtml/session')->setProductsalesreportData(null);
        }
        elseif (Mage::registry('productsalesreport_data')) {
            $form->setValues(
                Mage::registry('productsalesreport_data')->getData()
            );
        }

        return parent::_prepareForm();
    }
}

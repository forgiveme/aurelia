<?php

class Stardigital_Productsalesreport_Block_Adminhtml_Productsalesreport_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('productsalesreport_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('productsalesreport')->__('Item Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_section',
            array(
                'label' => Mage::helper('productsalesreport')->__('Item Information'),
                'title' => Mage::helper('productsalesreport')->__('Item Information'),
                'content' => $this->getLayout()->createBlock('productsalesreport/adminhtml_productsalesreport_edit_tab_form')->toHtml(),
            )
        );

        return parent::_beforeToHtml();
    }
}

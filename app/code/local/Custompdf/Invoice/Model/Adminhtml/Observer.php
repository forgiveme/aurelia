<?php
Class Custompdf_Invoice_Model_Adminhtml_Observer
{
    public function adminhtmlWidgetContainerHtmlBefore($event) {
        $block = $event->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {
            $block->addButton('print_custom_pdf', array(
                'label'     => Mage::helper('sales')->__('Print PDF (new)'),
                'onclick'   => "setLocation('{$block->getUrl('*/adminhtml_custompdfinvoice/printpdf')}')",
                'class'     => 'go'
            ));           
        }
    }
}
<?php
class Skint_Reports_Block_Adminhtml_System_Config_Form_Buttoncustomer extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('skint/system/config/buttoncustomer.phtml');
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for button
     *
     * @return string
     */
    public function getAjaxCheckUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/adminhtml_skintreports/skintoolsexpcustomerorproducts');
    }
	
	/**
     * Return file url for button
     *
     * @return string
     */
    public function getFileUrl($filename)
    {
        return Mage::getBaseUrl('media').$filename;
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
            'id'        => 'skintreports_button_customerorproduct',
            'label'     => $this->helper('adminhtml')->__('Export Data'),
            'onclick'   => 'javascript:submit_skintools_exp_customerorproduct(); return false;'
        ));

        return $button->toHtml();
    }
}
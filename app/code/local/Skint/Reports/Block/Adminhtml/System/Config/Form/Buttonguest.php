<?php
class Skint_Reports_Block_Adminhtml_System_Config_Form_Buttonguest extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('skint/system/config/buttonguest.phtml');
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
        return Mage::helper('adminhtml')->getUrl('adminhtml/adminhtml_skintreports/skintoolsexportguests');
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
            'id'        => 'skintreports_guestbutton',
            'label'     => $this->helper('adminhtml')->__('Export Guest Data'),
            'onclick'   => 'javascript:submit_skintools_guest_export(); return false;'
        ));

        return $button->toHtml();
    }
}
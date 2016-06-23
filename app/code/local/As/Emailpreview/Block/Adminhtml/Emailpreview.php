<?php
class As_Emailpreview_Block_Adminhtml_Emailpreview extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_emailpreview';
    $this->_blockGroup = 'emailpreview';
    $this->_headerText = Mage::helper('emailpreview')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('emailpreview')->__('Add Item');
    parent::__construct();
  }
}
<?php
class ChilliApple_FreeSample_Block_Adminhtml_FreeSample extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_freesample';
    $this->_blockGroup = 'freesample';
    $this->_headerText = Mage::helper('freesample')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('freesample')->__('Add Item');
    parent::__construct();
  }
}
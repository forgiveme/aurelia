<?php
class ChilliApple_Preferences_Block_Adminhtml_Preferences extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_preferences';
    $this->_blockGroup = 'preferences';
    $this->_headerText = Mage::helper('preferences')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('preferences')->__('Add Item');
    parent::__construct();
  }
}
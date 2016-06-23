<?php
class ChilliApple_Preferences_Block_Adminhtml_Secondaryconcern extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_secondaryconcern';
    $this->_blockGroup = 'preferences';
    $this->_headerText = Mage::helper('preferences')->__('Secondary concerns');
    $this->_addButtonLabel = Mage::helper('preferences')->__('Add Secondary concern');
    parent::__construct();
  }
}
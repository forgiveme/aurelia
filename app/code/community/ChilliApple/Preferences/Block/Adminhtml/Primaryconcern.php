<?php
class ChilliApple_Preferences_Block_Adminhtml_Primaryconcern extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_primaryconcern';
    $this->_blockGroup = 'preferences';
    $this->_headerText = Mage::helper('preferences')->__('Primary concerns');
    $this->_addButtonLabel = Mage::helper('preferences')->__('Add Primary concern');
    parent::__construct();
  }
}
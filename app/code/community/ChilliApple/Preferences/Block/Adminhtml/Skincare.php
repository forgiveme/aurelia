<?php
class ChilliApple_Preferences_Block_Adminhtml_Skincare extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_skincare';
    $this->_blockGroup = 'preferences';
    $this->_headerText = Mage::helper('preferences')->__('Skincare');
    $this->_addButtonLabel = Mage::helper('preferences')->__('Add Skincare');
    parent::__construct();
  }
}

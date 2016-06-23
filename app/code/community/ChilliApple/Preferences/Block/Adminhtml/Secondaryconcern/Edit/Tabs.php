<?php

class ChilliApple_Preferences_Block_Adminhtml_Secondaryconcern_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('secondaryconcern_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('preferences')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('preferences')->__('Item Information'),
          'title'     => Mage::helper('preferences')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('preferences/adminhtml_secondaryconcern_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}

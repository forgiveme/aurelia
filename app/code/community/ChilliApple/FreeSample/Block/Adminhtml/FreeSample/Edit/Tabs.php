<?php

class ChilliApple_FreeSample_Block_Adminhtml_FreeSample_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('freesample_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('freesample')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('freesample')->__('Item Information'),
          'title'     => Mage::helper('freesample')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('freesample/adminhtml_freesample_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
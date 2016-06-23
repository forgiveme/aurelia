<?php

class As_EmailPreview_Block_Adminhtml_EmailPreview_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('emailpreview_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('emailpreview')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('emailpreview')->__('Item Information'),
          'title'     => Mage::helper('emailpreview')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('emailpreview/adminhtml_emailpreview_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
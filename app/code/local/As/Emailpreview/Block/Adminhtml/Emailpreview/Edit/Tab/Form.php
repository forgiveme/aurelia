<?php

class As_Emailpreview_Block_Adminhtml_Emailpreview_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('emailpreview_form', array('legend'=>Mage::helper('emailpreview')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('emailpreview')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('emailpreview')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('emailpreview')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('emailpreview')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('emailpreview')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('emailpreview')->__('Content'),
          'title'     => Mage::helper('emailpreview')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getEmailpreviewData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getEmailpreviewData());
          Mage::getSingleton('adminhtml/session')->setEmailpreviewData(null);
      } elseif ( Mage::registry('emailpreview_data') ) {
          $form->setValues(Mage::registry('emailpreview_data')->getData());
      }
      return parent::_prepareForm();
  }
}
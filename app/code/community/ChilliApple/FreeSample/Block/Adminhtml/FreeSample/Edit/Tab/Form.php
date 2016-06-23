<?php

class ChilliApple_FreeSample_Block_Adminhtml_FreeSample_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('freesample_form', array('legend'=>Mage::helper('freesample')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('freesample')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('freesample')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('freesample')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('freesample')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('freesample')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('freesample')->__('Content'),
          'title'     => Mage::helper('freesample')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getFreeSampleData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getFreeSampleData());
          Mage::getSingleton('adminhtml/session')->setFreeSampleData(null);
      } elseif ( Mage::registry('freesample_data') ) {
          $form->setValues(Mage::registry('freesample_data')->getData());
      }
      return parent::_prepareForm();
  }
}
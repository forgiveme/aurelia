<?php

class ChilliApple_Preferences_Block_Adminhtml_Preferences_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('preferences_form', array('legend'=>Mage::helper('preferences')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('preferences')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('preferences')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('preferences')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('preferences')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('preferences')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('preferences')->__('Content'),
          'title'     => Mage::helper('preferences')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getPreferencesData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getPreferencesData());
          Mage::getSingleton('adminhtml/session')->setPreferencesData(null);
      } elseif ( Mage::registry('preferences_data') ) {
          $form->setValues(Mage::registry('preferences_data')->getData());
      }
      return parent::_prepareForm();
  }
}
<?php

class As_Brand_Block_Adminhtml_Brand_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('brand_form', array('legend'=>Mage::helper('brand')->__('Award information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('brand')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'image', array(
          'label'     => Mage::helper('brand')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      /*$fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('brand')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('brand')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('brand')->__('Disabled'),
              ),
          ),
      ));*/
      $fieldset->addField('status', 'hidden', array(
          'label'     => Mage::helper('brand')->__('Status'),
          'name'      => 'status',
          'value'    => '1'
      ));
      
     
      if ( Mage::getSingleton('adminhtml/session')->getBrandData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getBrandData());
          Mage::getSingleton('adminhtml/session')->setBrandData(null);
      } elseif ( Mage::registry('brand_data') ) {
          $form->setValues(Mage::registry('brand_data')->getData());
      }
      return parent::_prepareForm();
  }
}
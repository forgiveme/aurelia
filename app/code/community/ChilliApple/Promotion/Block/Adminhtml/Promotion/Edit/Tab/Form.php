<?php

class ChilliApple_Promotion_Block_Adminhtml_Promotion_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('promotion_form', array('legend'=>Mage::helper('promotion')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('promotion')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('promotion')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('promotion')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('promotion')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('promotion')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('promotion')->__('Content'),
          'title'     => Mage::helper('promotion')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getPromotionData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getPromotionData());
          Mage::getSingleton('adminhtml/session')->setPromotionData(null);
      } elseif ( Mage::registry('promotion_data') ) {
          $form->setValues(Mage::registry('promotion_data')->getData());
      }
      return parent::_prepareForm();
  }
}
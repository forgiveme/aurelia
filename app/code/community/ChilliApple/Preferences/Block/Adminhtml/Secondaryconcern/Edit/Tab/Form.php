<?php

class ChilliApple_Preferences_Block_Adminhtml_Secondaryconcern_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('secondaryconcern_form', array('legend'=>Mage::helper('preferences')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('preferences')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      if ( Mage::getSingleton('adminhtml/session')->getSecondaryconcernData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSecondaryconcernData());
          Mage::getSingleton('adminhtml/session')->setPreferencesData(null);
      } elseif ( Mage::registry('secondaryconcern_data') ) {
          $form->setValues(Mage::registry('secondaryconcern_data')->getData());
      }
      return parent::_prepareForm();
  }
}

<?php
class ChilliApple_Preferences_Block_Preferences extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
    
     public function getPreferences()     
     {  
        if (!$this->hasData('preferences')) {
            $this->setData('preferences', Mage::registry('current_preferences'));
        }
        return $this->getData('preferences');
        
    }
    
    public function getPrimaryConcerns()
    {
       $select = $this->getLayout()->createBlock('core/html_select')
                ->setData(array(
                    'id' => 'primary_concern',
                    'class' =>'required-entry'
                ));
      $select->setName('primary_concern')
                    ->addOption('', $this->__('-- Please Select --'));
      $collection=Mage::getModel('preferences/primaryconcern')->getCollection();
      foreach($collection as $row)
      {
        $select->addOption($row->getId(),$row->getTitle());
      }
      $preferences=$this->getPreferences();
      $select->setValue($preferences->getPrimaryConcern());
      return $select->getHtml();
    }
    
    public function getSecondaryConcerns()
    {
      $select = $this->getLayout()->createBlock('core/html_select')
                ->setData(array(
                    'id' => 'primary_concern',
                    'class' =>'required-entry'
                ));
      $select->setName(' secondary_concern')
                    ->addOption('', $this->__('-- Please Select --'));
      $collection=Mage::getModel('preferences/secondaryconcern')->getCollection();
      foreach($collection as $row)
      {
        $select->addOption($row->getId(),$row->getTitle());
      }
      $preferences=$this->getPreferences();
      $select->setValue($preferences->getSecondaryConcern());
      return $select->getHtml();
    }
    
    public function getSkinCares()
    {
      $options=array();
      $collection=Mage::getModel('preferences/skincare')->getCollection();
      foreach($collection as $row)
      {
        $options[]=array('value'=>$row->getId(),'label'=>$row->getTitle());
      }
      return $options;
    }
}

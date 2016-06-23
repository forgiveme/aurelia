<?php

class ChilliApple_Preferences_Model_Preferences extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('preferences/preferences');
    }
    
    public function getCustomerPreferences($customerId)
    {
      return $this->load($customerId,'customer_id');
      
    }
    
    protected function _beforeSave()
    {   $skinCares=$this->getSkinCares();
        $skinCares=is_array($skinCares)?$skinCares:array();
        $skinCareValues=implode(',',$skinCares);
        $this->setSkinCares($skinCareValues);
        parent::_beforeSave();
    }
    
    protected function _afterLoad()
    {
        parent::_afterLoad();
		
		$customer = Mage::registry('current_customer');
		
        $skincareIds=$this->getSkinCares();
        $skincares=array();
        if(!empty($skincareIds))
        { $skincareIds=explode(',',$skincareIds);
          $collection=Mage::getModel('preferences/skincare')->getCollection()
                ->addFieldToFilter('skin_care_id',array('in'=>$skincareIds));
          foreach($collection as $row)
          {
            $skincares[]=array('id'=>$row->getId(),'label'=>$row->getTitle());
          }
        }
        $this->setData('skincare_options',$skincares);
        $hasGlasses=$this->getHasGlasses();
        if($hasGlasses)
        {
            $this->setData('has_glasses_text','Yes');
            
        }
        else
        {
            $this->setData('has_glasses_text','No');
        }
        
        $primaryId=$this->getPrimaryConcern();
        if($primaryId)
        {   $obj=Mage::getModel('preferences/primaryconcern')->load($primaryId);
            if($obj->getId())
            {
                $this->setData('primary_concern_text',$obj->getTitle());
            }
            else
            {
                $this->setData('primary_concern_text',null);
            }
        }
        
        $secondaryId=$this->getSecondaryConcern();
        if($secondaryId)
        {   $obj=Mage::getModel('preferences/secondaryconcern')->load($secondaryId);
            if($obj->getId())
            {
                $this->setData('secondary_concern_text',$obj->getTitle());
            }
            else
            {
                $this->setData('secondary_concern_text',null);
            }
        }
		
		if(!Mage::app()->getStore()->isAdmin()) {
			$customer = Mage::getSingleton('customer/session')->getCustomer();
		}
		if($customer && $customer->getEmail()) {
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
			$query = "SELECT * FROM skintools_emails left join skintools_questionsdata on skintools_emails.questiondata_id = skintools_questionsdata.id WHERE skintools_emails.address = '".$customer->getEmail()."' LIMIT 1";

			$collection = $readConnection->fetchAll($query);
			
			if(count($collection)>0) {
				$this->setData('skintools_data', $collection);
			}
		}
    }
}
<?php

class ChilliApple_Preferences_Model_Observer
{

	public function injectTabs($observer)
	{
	    /* $block = $observer->getEvent()->getBlock();
    		// add tab in customer edit page
           if ($block instanceof Mage_Adminhtml_Block_Customer_Edit_Tabs)
           {
               if ($this->_getRequest()->getActionName() == 'edit' || $this->_getRequest()->getParam('type')) 
                {
                   // $block->addTab('preferences', array('label'=> Mage::helper('customer')->__('Preferences'),
                    // 'url'=> $block->getUrl('//custom', array('_current' => true)),'class'=> 'ajax'));
                }
           }*/
	}

	protected function _getRequest()
	{
           return Mage::app()->getRequest();
    	}
    	
    	/*public function updateGlasses($observer)
    	{   
    	   $request=$this->_getRequest();
    	    
    	    if($request->getParam('has_glasses')=="1")
    	    {
    	      $observer->getEvent()->getOrder()
    	      	       ->setData('has_glasses',"1")
    	      	       ->save();
    	      $hasGlasses="1"
    	      if(Mage::getSingleton('customer/session')->isLoggedIn()) 
    	      {
    	      	$customer=Mage::getSingleton('customer/session')->getCustomer();
		$customerId=$customer->getId();
		$preferences=Mage::getModel('preferences/preferences')->getCustomerPreferences($customerId);
		if(!$preferences->getId())
		{
		  
		  $preferences->setPrimaryConcern('0');
		  $preferences->setSecondaryConcern('0');
		  $preferences->setSkinCares('');
		  $preferences->setotherBrands('');
		  $preferences->setAureliaFeedback('');
		}
		$preferences->setCustomerId($customerId);
		$preferences->setHasGlasses($hasGlasses);
		$preferences->save();
    	      }
    	    }
    	   
    	}*/
    	
    	public function updateGlasses($observer)
  	{   
    	   $request=$this->_getRequest();
    	    $hasGlasses="0";
    	    if($request->getParam('has_glasses')=="1")
    	    {
    	      $observer->getEvent()->getOrder()
    	      	       ->setData('has_glasses',"1")
    	      	       ->save();
    	      $hasGlasses="1";
    	    }
			
		
	         /*if(Mage::getSingleton('customer/session')->isLoggedIn()) 
	    	  {
	    	      $customer=Mage::getSingleton('customer/session')->getCustomer();
				   $customerId=$customer->getId();
			       $preferences=Mage::getModel('preferences/preferences')->getCustomerPreferences($customerId);
					if(!$preferences->getId())
					{
					  
					  $preferences->setPrimaryConcern('0');
					  $preferences->setSecondaryConcern('0');
					  $preferences->setSkinCares('');
					  $preferences->setotherBrands('');
					  $preferences->setAureliaFeedback('');
					}
				
				    $preferences->setCustomerId($customerId);
					$preferences->setHasGlasses($hasGlasses);
					$preferences->save();
	    	 }*/
			
			
    	   
 	 }
  
	public function setAfterLoginSuccessUrl($observer)
	{
	  $url=Mage::getUrl('promotion/index/index');
	  Mage::app()->getResponse()->setRedirect($url);
	  return $this;
	}
	
	public function add_skintool_mass_action($event) {
		//return;
		$block = $event->getBlock();

		if ($block instanceof Mage_Adminhtml_Block_Customer_Grid) {
			
			$block->setMassactionIdField('massexportskintool_id');
			$block->getMassactionBlock()->setFormFieldName('massexportskintool_ids');
			$block->getMassactionBlock()->setUseSelectAll(false);

			$block->getMassactionBlock()->addItem('massexportskintool', array(
				'label' => 'Export Skintools Data',
				'url' => $block->getUrl('preferences/index/exportskintool'),
			));
		
		}
	}
	
}

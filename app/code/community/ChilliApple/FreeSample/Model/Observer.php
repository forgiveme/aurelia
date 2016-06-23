<?php 

class ChilliApple_FreeSample_Model_Observer
{

	public function updateItems($observer)
	{
	
	}
	
	public function initItems($observer)
	{
		$quote = $observer->getQuote();
        	if (!$quote) 
            	return $this;
            	
            	$helper=Mage::helper('freesample');
            	/*foreach ($quote->getItemsCollection() as $item) {
		    if (!$item){
		        continue;
		    }
		     		    
		    $item->isDeleted(true);
		    $item->setData('qty_to_add', '0.0000');
		    $quote->removeItem($item->getId());
		}*/
            	
	}

	public function checkItems($observer)
	{
		$quote = $observer->getQuote();
		if (!$quote) 
            	return $this;
		$i=0;
		foreach ($quote->getItemsCollection() as $item) 
		{
			
			if (!$item){
                	continue;
            		}
            	   	
            	   	$_productId=$item->getProductId();
            	   	
            	   	if (!$_productId)
            	   	{
                	  continue;
            		}
            		
            		$_product=Mage::getModel('catalog/product')->load($_productId);
            		
            		if (!$_product->getId())
            	   	{
                	  continue;
            		}
            		
            		if($_product->getSample())
            		{
            		   $i++;
            		   if($i>3)
            		   {

            		   	$quote->addErrorInfo(
                        'error',
                        'cataloginventory',
                        Mage_CatalogInventory_Helper_Data::ERROR_QTY,
                        Mage::helper('cataloginventory')->__('Not more than 3 samples are allowed'
                    	)) ;

                    	return;

            		   }
            		}
		}
		
	}
}

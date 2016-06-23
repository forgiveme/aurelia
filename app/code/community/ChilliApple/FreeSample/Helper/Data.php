<?php

class ChilliApple_FreeSample_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	
	public function hasSample()
	{
		$quote=Mage::getSingleton('checkout/session')->getQuote();
		
		foreach ($quote->getItemsCollection() as $item) {
			
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
            		   return $_product;
            		}
		}
		
		return false;
	}
	
	public function getSampleProducts()
	{
		$quote=Mage::getSingleton('checkout/session')->getQuote();
		$samples=array();
		foreach ($quote->getItemsCollection() as $item) {
			
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
            		   $samples[$item->getId()]=$_product;
            		}
		}
		
		return $samples;
	}
	
	public function getNonSampleItems()
	{
		$quote=Mage::getSingleton('checkout/session')->getQuote();
		
		$nonSamples=array();
		
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
            		//!$item->isDeleted() && !$item->getParentItemId()
            		if(!$_product->getSample() && !$item->isDeleted())
            		{
            		   $nonSamples[$item->getId()]=$_product;
            		}
		}
	     
	     return $nonSamples;
	}
}

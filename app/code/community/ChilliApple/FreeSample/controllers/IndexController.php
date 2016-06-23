<?php
class ChilliApple_FreeSample_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/freesample?id=15 
    	 *  or
    	 * http://site.com/freesample/id/15 	
    	 */
    	/* 
		$freesample_id = $this->getRequest()->getParam('id');

  		if($freesample_id != null && $freesample_id != '')	{
			$freesample = Mage::getModel('freesample/freesample')->load($freesample_id)->getData();
		} else {
			$freesample = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($freesample == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$freesampleTable = $resource->getTableName('freesample');
			
			$select = $read->select()
			   ->from($freesampleTable,array('freesample_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$freesample = $read->fetchRow($select);
		}
		Mage::register('freesample', $freesample);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}
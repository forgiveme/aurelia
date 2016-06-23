<?php
class As_Emailpreview_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/emailpreview?id=15 
    	 *  or
    	 * http://site.com/emailpreview/id/15 	
    	 */
    	/* 
		$emailpreview_id = $this->getRequest()->getParam('id');

  		if($emailpreview_id != null && $emailpreview_id != '')	{
			$emailpreview = Mage::getModel('emailpreview/emailpreview')->load($emailpreview_id)->getData();
		} else {
			$emailpreview = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($emailpreview == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$emailpreviewTable = $resource->getTableName('emailpreview');
			
			$select = $read->select()
			   ->from($emailpreviewTable,array('emailpreview_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$emailpreview = $read->fetchRow($select);
		}
		Mage::register('emailpreview', $emailpreview);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}
<?php
class Cnc_MarketPlace_Adminhtml_Marketplace_SchedulerController extends Mage_Adminhtml_Controller_Action
{
	public function _initAction()
	{
		Mage::getSingleton( 'core/session' )->getMessages( true );
	}
	public function indexAction()
	{
		$helper = Mage::helper( 'marketplace' );
		$check  = $helper->configCheckerAll();
		if ( !$check )
			$this->_redirect( 'adminhtml/marketplace_configuration' );
		$block = $this->getLayout()->getBlockSingleton( 'marketplace/adminhtml_scheduler' );
		$this->loadLayout()->_setActiveMenu( 'marketplace' );
		$block->getSchedulerDetails();
		$this->_title( $this->__( "Style.com/Scheduler" ) );
		$this->renderLayout();
	}
	public function ajaxSchduleRefreshAction()
	{
		$post  = Mage::app()->getRequest()->getPost();
		$block = $this->getLayout()->getBlockSingleton( 'marketplace/adminhtml_scheduler' );
		if ( $post[ 'type' ] == 'products' ) {
			$prod_crons = $block->getScheduledCronJobs( 'marketplace_prod' );
			echo json_encode( $prod_crons );
		} else {
			$offer_crons = $block->getScheduledCronJobs( 'marketplace_offer' );
			echo json_encode( $offer_crons );
		}
	}
	protected function _isAllowed()
	{
		return Mage::getSingleton( 'admin/session' )->isAllowed( 'marketplace/scheduler' );
	}
	public function deleteCronAction()
	{
		$block = $this->getLayout()->getBlockSingleton( 'marketplace/adminhtml_scheduler' );
		$block->deleteCronJobs();
		$this->_redirect( '*/*/' );
	}
	public function schedulersaveAction()
	{
		$block = $this->getLayout()->getBlockSingleton( 'marketplace/adminhtml_scheduler' );
		$block->insertCronData();
		$this->_redirect( '*/*/' );
	}
}
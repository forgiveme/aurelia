<?php
class Cnc_MarketPlace_Adminhtml_Marketplace_ProductsController extends Mage_Adminhtml_Controller_Action
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
		$block = $this->getLayout()->getBlockSingleton( 'marketplace/adminhtml_products' );
		$this->loadLayout()->_setActiveMenu( 'marketplace' );
		$block->getProductsMapDetails();
		$this->_title( $this->__( "Style.com/Products" ) );
		$this->renderLayout();
	}
	protected function _isAllowed()
	{
		return Mage::getSingleton( 'admin/session' )->isAllowed( 'marketplace/products' );
	}
	public function downloadErrorAction()
	{
		$helper = Mage::helper( 'marketplace' );
		$helper->downloadError_file();
		exit;
	}
	public function getListAction()
	{
		$importshelper = Mage::helper( 'marketplace/imports' );
		$post  = Mage::app()->getRequest()->getPost();
		$helper = Mage::helper( 'marketplace' );
		$idsToRefresh = $post['imports'];
		$page_import_ids = json_encode($importshelper->getPageImportIds('product', $post['prod_imports_page_nr']));
		if($idsToRefresh == "{}"){
			// probably not a refresh ajax call
			// means its a pageload so every import_id on the page should be refreshed
			$idsToRefresh = $page_import_ids;
		}
		$helper->executeImportsUpdate($idsToRefresh, 'product');
		$prod_imports              = $helper->getAllImports( 'product', $page_import_ids);
		$templatePath = 'marketplace/productimports.phtml';
		$output = Mage::app()->getLayout()
			->createBlock("core/template")
			->setData('prod_imports', $prod_imports)
			->setTemplate($templatePath)
			->toHtml();

		$this->getResponse()
			->setHeader('Content-Type', 'text/html')
			->setBody($output);

		return;
	}

	public function productUploadAction()
	{
		$helper = Mage::helper( 'marketplace' );
		$helper->getProductsToUpload();
		Mage::getSingleton( 'core/session' )->addSuccess( "Successfully uploaded product data into Condenast Marketplace" );
		session_write_close();
		$this->_redirect( '*/*/' );
	}
}
<?php
class Cnc_MarketPlace_Adminhtml_Marketplace_OffersController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        session_write_close();
        Mage::getSingleton( 'core/session' )->getMessages( true );
    }
    public function indexAction()
    {
        $helper = Mage::helper( 'marketplace' );
        $check  = $helper->configCheckerAll();
        if ( !$check )
            $this->_redirect( 'adminhtml/system_config' );
        $block = $this->getLayout()->getBlockSingleton( 'marketplace/adminhtml_offers' );
        Mage::getSingleton('core/session')->setMiraklStoreId(Mage::app()->getRequest()->getParam('store'));
        $this->loadLayout()->_setActiveMenu( 'marketplace' );
        $block->getProductsMapDetails();
        $this->_title( $this->__( "Style.com/Offers" ) );
        $this->renderLayout();
    }
    protected function _isAllowed()
    {
        return Mage::getSingleton( 'admin/session' )->isAllowed( 'marketplace/offers' );
    }
    public function downloadErrorAction()
    {
        $helper = Mage::helper( 'marketplace' );
        $helper->downloadError_file();
    }
    public function getListAction()
    {
        $importshelper = Mage::helper( 'marketplace/imports' );
        $post  = Mage::app()->getRequest()->getPost();
        $helper = Mage::helper( 'marketplace' );
        $idsToRefresh = $post['imports'];
        $page_import_ids = json_encode($importshelper->getPageImportIds('offer', $post['offer_imports_page_nr']));
        if($idsToRefresh == "{}"){
            // probably not a refresh ajax call
            // means its a pageload so every import_id on the page should be refreshed
            $idsToRefresh = $page_import_ids;
        }
        $helper->executeImportsUpdate($idsToRefresh, 'offer');
        $offer_imports              = $helper->getAllImports( 'offer', $page_import_ids);
        $templatePath = 'marketplace/offerimports.phtml';
        $output = Mage::app()->getLayout()
            ->createBlock("core/template")
            ->setData('offer_imports', $offer_imports)
            ->setTemplate($templatePath)
            ->toHtml();
        $this->getResponse()
            ->setHeader('Content-Type', 'text/html')
            ->setBody($output);
        return;
    }
    public function offerSingleAction()
    {
        $block = $this->getLayout()->getBlockSingleton( 'marketplace/adminhtml_offers' );
        echo $block->offerSingleGeTAjax();
    }
    public function productUploadAction()
    {
        $helper = Mage::helper( 'marketplace' );
        $helper->getOffersToUpload();
        Mage::getSingleton( 'core/session' )->addSuccess( "Successfully uploaded product data into Condenast Marketplace" );
        session_write_close();
        $this->_redirectReferer();
    }
    public function offerEditSaveAction()
    {
        $block  = $this->getLayout()->getBlockSingleton( 'marketplace/adminhtml_offers' );
        $post   = Mage::app()->getRequest()->getPost();
        $bulk   = $post[ 'bulk' ];
        $helper = Mage::helper( 'marketplace' );
        if ( $bulk == 'true' ) {
            $block->deleteBulkOffer();
        } else {
            $block->saveSingleOffer();
        }
        $this->_redirectReferer();
    }
    public function messageOfferGetAction()
    {
        $post   = Mage::app()->getRequest()->getPost();
        $helper = Mage::helper( 'marketplace' );
        $helper->getInduvidualOrderMessage( $post[ 'offer_id_msg' ], $post[ 'type' ] );
    }
    public function messageOfferAnswerAction()
    {
        $block          = $this->getLayout()->getBlockSingleton( 'marketplace/adminhtml_offers' );
        $post           = Mage::app()->getRequest()->getPost();
        $offer_sku_msg  = $post[ 'offer_sku_msg' ];
        $message_answer = $post[ 'message_answer' ];
        $block->answerMesssages( $offer_sku_msg, $message_answer );
    }
}

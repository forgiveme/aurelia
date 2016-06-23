<?php
class Cnc_MarketPlace_Adminhtml_Marketplace_ConfigurationController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        parent::_initAction();
        Mage::getSingleton( 'core/session' )->getMessages( true );
    }
    protected function _isAllowed()
    {
        return Mage::getSingleton( 'admin/session' )->isAllowed( 'marketplace/configure' );
    }
    public function indexAction()
    {
        $block       = $this->getLayout()->getBlockSingleton( 'marketplace/adminhtml_configuration' );
        $helper      = Mage::helper( 'marketplace' );
        $config_data = Mage::app()->getRequest()->getPost();
        if ( $config_data ) {
            $config_data['credentials']['url'] = rtrim($config_data['credentials']['url'],"/");
            $block->updateConfigurationData( $config_data );
            $checkConnection = $helper->checkCredentials();
            $block->validationMessages( $checkConnection );
        }
        $this->loadLayout()->_setActiveMenu( 'marketplace' );
        $block->fetchConfigurationData();
        $this->_title( $this->__( "Style.com/Configuration" ) );
        $this->renderLayout();
    }

    public function ErrorLogAction()
    {
        $block = $this->getLayout()->getBlockSingleton( 'marketplace/adminhtml_configuration' );
        $block->downloadErrorLog();
    }

    /**
     * From http://stackoverflow.com/questions/15353508/how-to-download-the-files-in-magento
     */
    public function exportAction()
    {
        $block = $this->getLayout()->getBlockSingleton( 'marketplace/adminhtml_configuration' );
        $has_products = $block->createProductsCSV();

        if ($has_products) {

            $filepath  = Mage::helper('marketplace/util')->getProductExportFilename();

            $this->getResponse ()
                ->setHttpResponseCode ( 200 )
                ->setHeader ( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true )
                ->setHeader ( 'Pragma', 'public', true )
                ->setHeader ( 'Content-type', 'text/csv' )
                ->setHeader ( 'Content-Length', filesize($filepath) )
                ->setHeader ( 'Content-disposition', 'attachment' . '; filename=' . basename($filepath) );
            $this->getResponse ()->clearBody ();
            $this->getResponse ()->sendHeaders ();
            readfile ( $filepath );
            return;
        } else {
            Mage::getSingleton( 'core/session' )->addError( "Please add products to style.com first" );
            session_write_close();
        }
    }
}
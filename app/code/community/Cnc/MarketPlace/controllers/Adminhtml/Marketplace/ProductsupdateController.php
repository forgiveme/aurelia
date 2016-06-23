<?php
class Cnc_MarketPlace_Adminhtml_Marketplace_ProductsupdateController extends Mage_Adminhtml_Controller_Action
{
	public function massAddStyleAction()
	{
		$productIds = $this->getRequest()->getParam( 'product' );
		$count      = count( $productIds );
		if ( $count > 0 ) {
			foreach ( $productIds as $productId ) {
				$_product = Mage::getModel( 'catalog/product' )->load( $productId );
				$product  = Mage::getModel( 'catalog/product' )->load( $_product->getEntityId() );
				$product->setData( 'display_style_com', 1 )->getResource()->saveAttribute( $product, 'display_style_com' );
			}
			Mage::getSingleton( 'core/session' )->addSuccess( 'Succesfully updated products to style.com attribute' );
			session_write_close();
		}
		$this->_redirect( 'adminhtml/catalog_product' );
	}
	public function massRemoveStyleAction()
	{
		$productIds = $this->getRequest()->getParam( 'product' );
		$count      = count( $productIds );
		if ( $count > 0 ) {
			foreach ( $productIds as $productId ) {
				$_product = Mage::getModel( 'catalog/product' )->load( $productId );
				$product  = Mage::getModel( 'catalog/product' )->load( $_product->getEntityId() );
				$product->setData( 'display_style_com', 0 )->getResource()->saveAttribute( $product, 'display_style_com' );
			}
			Mage::getSingleton( 'core/session' )->addSuccess( 'Succesfully removed products from style.com attribute' );
			session_write_close();
		}
		$this->_redirect( 'adminhtml/catalog_product' );
	}
}
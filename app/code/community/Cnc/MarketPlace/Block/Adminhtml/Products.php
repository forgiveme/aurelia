<?php
class Cnc_MarketPlace_Block_Adminhtml_Products extends Mage_Adminhtml_Block_Template
{
	protected $helper;
	protected $importshelper;
	public function __construct()
	{
		parent::__construct();
		$this->setFormAction( Mage::getUrl( '*/*/productUpload' ) );
		$this->setListAction( Mage::getUrl( '*/*/getList' ) );
		$this->helper = Mage::helper( 'marketplace' );
		$this->importshelper = Mage::helper( 'marketplace/imports' );
	}
	public function getProductsMapDetails()
	{
		$post = Mage::app()->getRequest()->getPost();
		$field_attributes          = $this->helper->getDefaultProductAttributes( 'product' );
		$selected_field_attributes = $this->helper->getConfigurationData( 'stored_fields' );
		$block                     = $this->getLayout()->getBlock( 'products' );
		$block->setData( 'product_fields', $field_attributes );
		$block->setData( 'selected_field_attributes', $selected_field_attributes );

		$block->setData( 'prod_import_total_pages', $this->importshelper->getLastPageNr('product'));
		$prod_imports_page_nr = isset($post[ 'prod_imports_page_nr' ]) ? $post[ 'prod_imports_page_nr' ] : 1;
		$prod_import_ids = $this->importshelper->getPageImportIds('product', $prod_imports_page_nr);
		$block->setData( 'prod_imports_page_nr', $prod_imports_page_nr);
		$block->setData( 'prod_import_ids', $prod_import_ids );

	}
}

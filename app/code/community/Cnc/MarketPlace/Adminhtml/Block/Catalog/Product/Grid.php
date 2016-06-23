<?php
class Cnc_MarketPlace_Adminhtml_Block_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
	protected function _prepareColumns()
	{
		$this->addColumn( 'display_style_com', array(
			'header' => Mage::helper( 'catalog' )->__( 'Visible in style.com' ),
			'width' => '20px',
			'index' => 'display_style_com',
			'type' => 'options',
			'options' => array(
				'1' => 'Yes',
				'0' => 'No'
			)
		) );
		return parent::_prepareColumns();
	}
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField( 'entity_id' );
		$this->getMassactionBlock()->setFormFieldName( 'product' );
		$this->getMassactionBlock()->addItem( 'addtostyle', array(
			'label' => Mage::helper( 'catalog' )->__( 'Add Products to style.com' ),
			'url' => $this->getUrl( 'adminhtml/marketplace_productsupdate/massAddStyle' )
		) );
		$this->getMassactionBlock()->addItem( 'removetostyle', array(
			'label' => Mage::helper( 'catalog' )->__( 'Remove Products from style.com' ),
			'url' => $this->getUrl( 'adminhtml/marketplace_productsupdate/massRemoveStyle' )
		) );
		return parent::_prepareMassaction();
	}
	protected function _prepareCollection()
	{
		$store      = $this->_getStore();
		$collection = Mage::getModel( 'catalog/product' )->getCollection()->addAttributeToSelect( 'sku' )->addAttributeToSelect( 'name' )->addAttributeToSelect( 'attribute_set_id' )->addAttributeToSelect( 'type_id' );
		$collection->addAttributeToSelect( 'display_style_com' );
		if ( Mage::helper( 'catalog' )->isModuleEnabled( 'Mage_CatalogInventory' ) ) {
			$collection->joinField( 'qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left' );
		}
		if ( $store->getId() ) {
			$adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
			$collection->addStoreFilter( $store );
			$collection->joinAttribute( 'name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore );
			$collection->joinAttribute( 'custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId() );
			$collection->joinAttribute( 'status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId() );
			$collection->joinAttribute( 'visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId() );
			$collection->joinAttribute( 'price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId() );
		} else {
			$collection->addAttributeToSelect( 'price' );
			$collection->joinAttribute( 'status', 'catalog_product/status', 'entity_id', null, 'inner' );
			$collection->joinAttribute( 'visibility', 'catalog_product/visibility', 'entity_id', null, 'inner' );
		}
		$this->setCollection( $collection );
		Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
		$this->getCollection()->addWebsiteNamesToResult();
		return $this;
	}
}
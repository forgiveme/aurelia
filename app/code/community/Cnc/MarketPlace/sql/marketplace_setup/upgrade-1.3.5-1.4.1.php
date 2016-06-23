<?php
$installer = $this;
$installer->startSetup();

// NB: 'default' for export_dir_location will use Mage::getBaseDir('var')/cnc_marketplace.  Otherwise
// value supplied will be used.
$installer->run( '
REPLACE INTO ' . $this->getTable( 'product_to_mirakle' ) . ' (id, meta_key, meta_value) VALUES
(11, "export_dir_location", "default"),
(12, "shop_info", "");
' );
$installer->endSetup();
?>
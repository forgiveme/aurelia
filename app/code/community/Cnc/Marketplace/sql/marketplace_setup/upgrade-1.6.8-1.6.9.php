<?php
/**
 * Created by Daniel Rafique.
 * For: Style.com
 * Date: 22/07/2016
 * Time: 10:22
 * Copyright all rights reserved to author of this content.
 */
$installer = $this;
 
$installer->startSetup();
 
$setup = new Mage_Core_Model_Config();

//Set content type for get all shipping options
$setup->saveConfig('carriers/dhlint/content_type', 'D', 'default', 0);
 
$installer->endSetup();

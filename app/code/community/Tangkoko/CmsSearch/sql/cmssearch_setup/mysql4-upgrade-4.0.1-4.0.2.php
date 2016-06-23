<?php
/**
 * Tangkoko Cms Search Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@tangkoko.com  and you will be sent
 * a copy immediately.
 *
 * @category   Tangkoko
 * @package    CmsSearch
 * @author     Vincent Decrombecque
 * @copyright  Copyright (c) 2012 Tangkoko sarl (http://www.tangkoko.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
$installer->startSetup();


$connection = $installer->getConnection();

if(!mage::helper("cmssearch")->cleverCmsIsActive()){
	$connection->addColumn($installer->getTable('cms/page'), 'is_searchable', 'tinyint(1) DEFAULT 1');
}else{
	$connection->addColumn($installer->getTable('cms_page_tree'), 'is_searchable', 'tinyint(1) DEFAULT 1');
}
$installer->endSetup();

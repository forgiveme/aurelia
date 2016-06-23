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
$connection->addColumn($installer->getTable('catalogsearch/search_query'), 'is_cmsprocessed', 'tinyint(1) DEFAULT 0 AFTER `is_processed`');
$connection->addColumn($installer->getTable('catalogsearch/search_query'), 'is_categoryprocessed', 'tinyint(1) DEFAULT 0 AFTER `is_cmsprocessed`');


$entityTypeId     = $installer->getEntityTypeId('catalog_category');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$installer->addAttribute('catalog_category', 'is_searchable',  array(
		'type'          => 'int',
        'group'         => 'General information',
        'label'         => '[CMS Search] Is searchable',
        'input'         => 'select',       
		'source'        => 'eav/entity_attribute_source_boolean',
		'visible'       => true,
		'required'      => true,
		'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
		'default'       => '1',
)
);

$attributeId = $installer->getAttributeId($entityTypeId, 'is_searchable');

$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('cmssearch/fulltext_page')}`;
DROP TABLE IF EXISTS `{$installer->getTable('cmssearch/result_page')}`;
DROP TABLE IF EXISTS `{$installer->getTable('cmssearch/fulltext_category')}`;
DROP TABLE IF EXISTS `{$installer->getTable('cmssearch/result_category')}`;
DROP TABLE IF EXISTS `{$installer->getTable('cmssearch/issearchable')}`;

DROP TABLE IF EXISTS `{$installer->getTable('cmssearch/issearchable')}`;
CREATE TABLE `{$installer->getTable('cmssearch/issearchable')}` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `entity` varchar(255) NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELETE FROM `{$installer->getTable('catalog_category_entity_int')}`
WHERE`attribute_id`='{$attributeId}';

INSERT INTO `{$installer->getTable('catalog_category_entity_int')}`
(`entity_type_id`, `attribute_id`, `entity_id`, `value`)
    SELECT '{$entityTypeId}', '{$attributeId}', `entity_id`, '1'
        FROM `{$installer->getTable('catalog_category_entity')}`;

");

$installer->endSetup();

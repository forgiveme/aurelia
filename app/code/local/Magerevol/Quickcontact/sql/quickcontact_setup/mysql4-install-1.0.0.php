<?php
/**
 * Magerevol
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Magerevol
 * @package    Magerevol_Quickcontact
 * @author     Magerevol Development Team
 * @copyright  Copyright (c) 2013 Magerevol. (http://www.magerevol.com)
 * @license    http://opensource.org/licenses/osl-3.0.php
 */
$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('mr_quickcontact')};
CREATE TABLE {$this->getTable('mr_quickcontact')} (
                   `qc_id` bigint(20) NOT NULL AUTO_INCREMENT,                   
                   `name` varchar(100) DEFAULT NULL,                             
                   `email` varchar(100) NOT NULL,                                
                   `subject` varchar(255) DEFAULT NULL,                          
                   `phone` varchar(20) DEFAULT NULL,                             
                   `message` text NOT NULL,                                      
                   `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  
                   PRIMARY KEY (`qc_id`)                                         
                 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;   
    ");

$installer->endSetup(); 
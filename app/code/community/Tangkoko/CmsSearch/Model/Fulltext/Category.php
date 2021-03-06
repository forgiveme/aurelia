<?php
/**
 * 
 * Tangkoko Cms Search Extension
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
 * @category Tangkoko
 * @package  CmsSearch
 * @author Nicolas RENAULT
 * @copyright  Copyright (c) 2011 Tangkoko sarl (http://www.tangkoko.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tangkoko_CmsSearch_Model_Fulltext_Category extends Tangkoko_CmsSearch_Model_Fulltext_Abstract
{
	/**
	 * Initialize category ressource model (Tangkoko_CmsSearch_Model_Mysql4_Fulltext_Category)
	 * @see Varien_Object::_construct() 
	 */
	protected function _construct()
    {
        $this->_init('cmssearch/fulltext_category');
    }
}
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
class Magerevol_Quickcontact_Block_Quickcontact extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getQuickcontact()     
     { 
        if (!$this->hasData('quickcontact')) {
            $this->setData('quickcontact', Mage::registry('quickcontact'));
        }
        return $this->getData('quickcontact');
        
    }
}
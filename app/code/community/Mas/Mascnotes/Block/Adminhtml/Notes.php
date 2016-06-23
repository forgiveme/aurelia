<?php
/**
 * Mas_Mascnotes extension by Makarovsoft.com
 * 
 * @category   	Mas
 * @package		Mas_Mascnotes
 * @copyright  	Copyright (c) 2014
 * @license		http://makarovsoft.com/license.txt
 * @author		makarovsoft.com
 */
/**
 * Note tab on product edit form
 *
 * @category	Mas
 * @package		Mas_Mascnotes
 * 
 */
class Mas_Mascnotes_Block_Adminhtml_Notes extends Mas_Mascnotes_Block_Notes {
	/**
	 * Set grid params
	 * @access protected
	 * @return void
	 * 
	 */
	public function __construct(){
		parent::__construct();        
	}
	
	public function getHeaderText()
    {
        return Mage::helper('mascnotes')->__('Notes on Customer');
    }
 
    public function getAddUrl()
    {
    	return Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit', array('back' => 'edit', 'id' => $this->getCustomerId(), 'tab' => 'customer_info_tabs_customer_notes'));
    }
	
	public function getCustomerId() {		
		return Mage::getSingleton('adminhtml/session_quote')->getCustomerId();
	}
	
}
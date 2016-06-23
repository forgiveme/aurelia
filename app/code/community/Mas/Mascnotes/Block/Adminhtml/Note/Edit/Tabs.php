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
 * Note admin edit tabs
 *
 * @category	Mas
 * @package		Mas_Mascnotes
 * 
 */
class Mas_Mascnotes_Block_Adminhtml_Note_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{
	/**
	 * constructor
	 * @access public
	 * @return void
	 * 
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('note_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('mascnotes')->__('Note'));
	}
	/**
	 * before render html
	 * @access protected
	 * @return Mas_Mascnotes_Block_Adminhtml_Note_Edit_Tabs
	 * 
	 */
	protected function _beforeToHtml(){
		$this->addTab('form_note', array(
			'label'		=> Mage::helper('mascnotes')->__('Note'),
			'title'		=> Mage::helper('mascnotes')->__('Note'),
			'content' 	=> $this->getLayout()->createBlock('mascnotes/adminhtml_note_edit_tab_form')->toHtml(),
		));

		$this->addTab('products', array(
			'label' => Mage::helper('mascnotes')->__('Associated Customers'),
			'url'   => $this->getUrl('*/*/customers', array('_current' => true)),
   			'class'	=> 'ajax'
		));
		return parent::_beforeToHtml();
	}
}
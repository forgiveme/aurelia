<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_SearchController extends Fishpig_Wordpress_Controller_Abstract
{
	/**
	 * If Integrated search is installed, redirect if enabled
	 *
	 * @return $this
	 */
	public function preDispatch()
	{
		if (Mage::helper('wordpress')->isAddonInstalled('IntegratedSearch') && Mage::getStoreConfigFlag('wordpress/integratedsearch/blog')) {
			$this->_forceForwardViaException('index', 'result', 'catalogsearch', array(
				'q' => $this->getRequest()->getParam('s'),
			));
		}
		
		return parent::preDispatch();
	}

	/**
	  * Initialise the current category
	  */
	public function indexAction()
	{
		$this->_rootTemplates[] = 'post_list';
		
		$this->_addCustomLayoutHandles(array(
			'wordpress_search_index',
			'wordpress_post_list',
		));
		
		$this->_initLayout();

		$helper = $this->getRouterHelper();

		$searchTerm = Mage::helper('core')->escapeHtml($helper->getSearchTerm());
		
		$this->_title($this->__("Search results for: '%s'", $searchTerm));
		
		$this->addCrumb('search_label', array('link' => '', 'label' => $this->__('Search')));
		$this->addCrumb('search_value', array('link' => '', 'label' => $searchTerm));
		
		$this->renderLayout();
	}
}

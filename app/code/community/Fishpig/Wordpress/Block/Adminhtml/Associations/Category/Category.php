<?php
/**
 * @category		Fishpig
 * @package		Fishpig_Wordpress
 * @license		http://fishpig.co.uk/license.txt
 * @author		Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Adminhtml_Associations_Category_Category extends Fishpig_Wordpress_Block_Adminhtml_Associations_Abstract
{
	/**
	 * Retrieve the association type for this grid
	 *
	 * @return string
	 */
	public function getAssociationType()
	{
		return 'category/category';
	}
}

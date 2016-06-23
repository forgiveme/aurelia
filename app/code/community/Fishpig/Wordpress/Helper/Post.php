<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Post extends Fishpig_Wordpress_Helper_Abstract
{
	/**
	 * Get the permalink structure as a string
	 *
	 * @return string
	 */
	public function getPermalinkStructure()
	{
		$cacheKey = 'permalink_structure';
		
		if ($this->_isCached($cacheKey)) {
			return $this->_cached($cacheKey);
		}

		$permalink = ltrim(str_replace('index.php/', '', ltrim($this->getWpOption('permalink_structure'), ' -/')), '/');

		$this->_cache($cacheKey, $permalink);
		
		return $permalink;
	}
	
	/**
	 * Returns TRUE is ?p=id links are being used
	 *
	 * @return bool
	 */
	public function useGuidLinks()
	{
		return !$this->getPermalinkStructure();
	}
	
	/**
	 * Retrieve the permalink structure in array format
	 *
	 * @return false|array
	 */
	public function getExplodedPermalinkStructure()
	{
		$cacheKey = 'permalink_structure_exploded';
		
		if ($this->_isCached($cacheKey)) {
			return $this->_cached($cacheKey);
		}
		
		if ($this->useGuidLinks()) {
			$this->_cache($cacheKey, array());
			
			return array();
		}
		
		$structure = $this->getPermalinkStructure();
		$parts = preg_split("/(\/|-)/", $structure, -1, PREG_SPLIT_DELIM_CAPTURE);
		$structure = array();

		foreach($parts as $part) {
			if ($result = preg_split("/(%[a-zA-Z0-9_]{1,}%)/", $part, -1, PREG_SPLIT_DELIM_CAPTURE)) {
				$results = array_filter(array_unique($result));

				foreach($results as $result) {
					array_push($structure, $result);
				}
			}
			else {
				$structure[] = $part;
			}
		}

		$this->_cache($cacheKey, $structure);
		
		return $structure;
	}
	
	/**
	 * Retrieve the pattern used to match the URL to the permalink structure
	 *
	 * @return string
	 */
	protected function _getPermalinkPattern()
	{
		$routerHelper = Mage::helper('wordpress/router');
		
		if ($structure = $this->_getExplodedPermalinkStructure()) {
			$lastPiece = $structure[count($structure)-1];

			if ($lastPiece === '/') {
				array_pop($structure);
			}
			
			foreach($structure as $i => $part) {
				if (preg_match('/^\%[a-zA-Z0-9_-]{1,}\%$/', $part)) {
					$part = trim($part, '%');
					
					if ($part === 'year') {
				 		$part = '[1-2]{1}[0-9]{3}';
				 	}
					else if ($part === 'monthnum') {
						$part = '[0-1]{1}[0-9]{1}';
					}
					else if ($part === 'day') {
						$part = '[0-3]{1}[0-9]{1}';
					}
					else if ($part === 'hour') {
						$part = '[0-2]{1}[0-9]{1}';
					}
					else if ($part === 'minute') {
						$part = '[0-5]{1}[0-9]{1}';
					}
					else if ($part === 'second') {
						$part = '[0-5]{1}[0-9]{1}';
					}
					else if ($part === 'post_id') {
						$part = '[0-9]{1,}';
					}
					else if ($part === 'postname') {
						$part = $routerHelper->getPermalinkStringRegex();
					}
					else if ($part === 'category') {
						$part = $routerHelper->getPermalinkStringRegex('\/');
					}
					else if ($part === 'author') {
						$part = $routerHelper->getPermalinkStringRegex();
					}
					else {
						$response = new Varien_Object(array('value' => false));
						
						Mage::dispatchEvent('wordpress_permalink_segment_unknown_pattern', array('response' => $response, 'segment' => $part));

						if ($response->getValue() !== false) {
							$part = $response->getValue();
						}
					}
					
					$part = '(' . $part . ')';
				}
				else {
					$part = preg_replace('/([.|\\\|\/]{1})/i', '\\\$1', $part);
				}

				$structure[$i] = $part;
			}

			return '^' . implode('', $structure) . '$';
		}
		
		return false;
	}

	/**
	 * Retrieve an array of the 'tokens' from the permalink structure
	 * A token is part of the structure that relates to dynamic post informat
	 * eg. %post_id% or %postname% etc
	 *
	 * @return false|array
	 */
	protected function _getPermalinkTokens()
	{
		if ($format = $this->_getExplodedPermalinkStructure()) {
			foreach($format as $i => $part) {
				if (!preg_match("/^\%([a-zA-Z0-9_\-]{1,})\%$/", $part)) {
					unset($format[$i]);
				}
				else {
					$format[$i] = trim($part, '%');
				}
			}
		
			return array_values($format);
		}
		
		return false;
	}
	
	/**
	 * Determine whether the URI is a post URI
	 * This function accepts URI's generated by Fishpig_Wordpress_Helper_Router::getBlogUri
	 *
	 * @param string
	 * @return bool
	 */
	public function isPostUri($uri, $returnParts = false)
	{
		if ($uri === 'index.php') {
			return false;
		}

		if ($pattern = $this->_getPermalinkPattern()) {
			$results = array();

			if (preg_match("/" . $pattern . "/iu", $uri, $results)) {
				array_shift($results);
				return $returnParts ? $results : true;
			}
		}

		return $this->getPostId();
	}

	/**
	 * Determine whether the URI is a post URI
	 * This function accepts URI's generated by Fishpig_Wordpress_Helper_Router::getBlogUri
	 *
	 * @param string
	 * @return bool
	 */
	public function isPostAttachmentUri($uri)
	{
		if (strpos($uri, '/') !== false) {
			$postUri = substr($uri, 0, strrpos($uri, '/'));
			
			if ($this->isPostUri($postUri)) {
				$attachmentUri = trim(substr($uri, strrpos($uri, '/')), '/');

				return Mage::getResourceModel('wordpress/image')->isImagePostName($attachmentUri);
			}
		}
		
		return false;
	}
	
	/**
	 * Retrieve an associative array of token => value for loading a post by it's permalink
	 *
	 * @param string $uri
	 * @return array
	 */
	protected function _getTokenValueArray($uri)
	{
		if (($tokens = $this->_getPermalinkTokens()) && ($values = (array)$this->isPostUri($uri, true))) {
			if (count($tokens) == count($values)) {
				$loadValues = array();
				
				foreach($tokens as $i => $token) {
					$loadValues[$token] = $values[$i];
				}
				
				return $loadValues;
			}
		}
		
		return false;
	}
	
	/**
	 * Retrieve the post ID from the query string
	 *
	 * @return string
	 */
	public function getPostId()
	{
		$postId = Mage::app()->getRequest()->getParam($this->getPostIdVar(), false);
		
		if ($postId === false && $this->isPreview()) {
			$postId = Mage::app()->getRequest()->getParam('preview_id', false);
		}
		
		return $postId;
	}
	
	/**
	 * Determine whether we are previewing a post
	 *
	 * @return bool
	 */
	public function isPreview()
	{
		return Mage::app()->getRequest()->getParam('preview', false);
	}
	
	/**
	 * Retrieve the variable used for post ID's when using Guid links
	 *
	 * @return string
	 */
	public function getPostIdVar()
	{
		return 'p';
	}
	
	/**
	 * Retrieve the URL for the tags page
	 *
	 * @return string
	 */
	public function getTagsUrl()
	{
		return $this->getUrl('tags');
	}

	/**
	 * Retrieve the number of comments to display per page
	 *
	 * @return int
	 */
	public function getCommentsPerPage()
	{
		return $this->getWpOption('page_comments') ? Mage::helper('wordpress')->getWpOption('comments_per_page', 50) : 0;
	}
	
	/**
	 * Determine whether the permalink has a trailing slash
	 *
	 * @return bool
	 */
	public function permalinkHasTrainingSlash()
	{
		return substr($this->getWpOption('permalink_structure'), -1) == '/';
	}
	
	
	/**
	 * Deprecated
	 */
	protected function _getRawPermalinkStructure()
	{
		return $this->getPermalinkStructure();
	}

	/**
	 * Deprecated
	 */
	protected function _getPermalinkStructure()
	{
		return $this->getPermalinkStructure();
	}
	
	protected function _getExplodedPermalinkStructure()
	{
		return $this->getExplodedPermalinkStructure();
	}
}

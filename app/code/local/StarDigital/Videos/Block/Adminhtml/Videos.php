<?php


class StarDigital_Videos_Block_Adminhtml_Videos extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_videos";
	$this->_blockGroup = "videos";
	$this->_headerText = Mage::helper("videos")->__("Videos Manager");
	$this->_addButtonLabel = Mage::helper("videos")->__("Add New Item");
	parent::__construct();
	
	}

}
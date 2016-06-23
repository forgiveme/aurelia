<?php

class StarDigital_Videos_Block_Adminhtml_Videos_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("videosGrid");
				$this->setDefaultSort("video_id");
				$this->setDefaultDir("ASC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("videos/videos")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("video_id", array(
				"header" => Mage::helper("videos")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "video_id",
				));
                
				$this->addColumn("video_name", array(
				"header" => Mage::helper("videos")->__("Video Name"),
				"index" => "video_name",
				));
				$this->addColumn("video_link", array(
				"header" => Mage::helper("videos")->__("Video Link"),
				"index" => "video_link",
				));				
		 $this->addRssList('videos/adminhtml_rss_rss/videos', Mage::helper('videos')->__('RSS'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		

}
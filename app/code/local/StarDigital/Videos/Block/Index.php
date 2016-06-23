<?php   
class StarDigital_Videos_Block_Index extends Mage_Core_Block_Template{   


	public function getVideos(){
		
		$videos = Mage::getModel('videos/videos')->getCollection()->addFieldToSelect('video_link')->addFieldToSelect('video_name')->addFieldToSelect('video_description')->addFieldToSelect('thumbnail');
					
		return $videos;
		
	}//get Videos



}
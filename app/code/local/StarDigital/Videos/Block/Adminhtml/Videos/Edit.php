<?php
	
class StarDigital_Videos_Block_Adminhtml_Videos_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "video_id";
				$this->_blockGroup = "videos";
				$this->_controller = "adminhtml_videos";
				$this->_updateButton("save", "label", Mage::helper("videos")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("videos")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("videos")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("videos_data") && Mage::registry("videos_data")->getId() ){

				    return Mage::helper("videos")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("videos_data")->getId()));

				} 
				else{

				     return Mage::helper("videos")->__("Add Item");

				}
		}
}
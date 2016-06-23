<?php
class StarDigital_Videos_Block_Adminhtml_Videos_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("videos_form", array("legend"=>Mage::helper("videos")->__("Item information")));

				
						$fieldset->addField("video_name", "text", array(
						"label" => Mage::helper("videos")->__("Video Name"),
						"name" => "video_name",
						));
					
						$fieldset->addField("video_description", "textarea", array(
						"label" => Mage::helper("videos")->__("Vide Description"),
						"name" => "video_description",
						));
									
						$fieldset->addField('thumbnail', 'image', array(
						'label' => Mage::helper('videos')->__('Thumbnail'),
						'name' => 'thumbnail',
						'note' => '(*.jpg, *.png, *.gif)',
						));
						$fieldset->addField("video_link", "text", array(
						"label" => Mage::helper("videos")->__("Video Link"),
						"name" => "video_link",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getVideosData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getVideosData());
					Mage::getSingleton("adminhtml/session")->setVideosData(null);
				} 
				elseif(Mage::registry("videos_data")) {
				    $form->setValues(Mage::registry("videos_data")->getData());
				}
				return parent::_prepareForm();
		}
}

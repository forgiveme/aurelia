<?php

class StarDigital_Videos_Adminhtml_VideosController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("videos/videos")->_addBreadcrumb(Mage::helper("adminhtml")->__("Videos  Manager"),Mage::helper("adminhtml")->__("Videos Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Videos"));
			    $this->_title($this->__("Manager Videos"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Videos"));
				$this->_title($this->__("Videos"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("videos/videos")->load($id);
				if ($model->getId()) {
					Mage::register("videos_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("videos/videos");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Videos Manager"), Mage::helper("adminhtml")->__("Videos Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Videos Description"), Mage::helper("adminhtml")->__("Videos Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("videos/adminhtml_videos_edit"))->_addLeft($this->getLayout()->createBlock("videos/adminhtml_videos_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("videos")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Videos"));
		$this->_title($this->__("Videos"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("videos/videos")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("videos_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("videos/videos");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Videos Manager"), Mage::helper("adminhtml")->__("Videos Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Videos Description"), Mage::helper("adminhtml")->__("Videos Description"));


		$this->_addContent($this->getLayout()->createBlock("videos/adminhtml_videos_edit"))->_addLeft($this->getLayout()->createBlock("videos/adminhtml_videos_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						
				 //save image
		try{

if((bool)$post_data['thumbnail']['delete']==1) {

	        $post_data['thumbnail']='';

}
else {

	unset($post_data['thumbnail']);

	if (isset($_FILES)){

		if ($_FILES['thumbnail']['name']) {

			if($this->getRequest()->getParam("id")){
				$model = Mage::getModel("videos/videos")->load($this->getRequest()->getParam("id"));
				if($model->getData('thumbnail')){
						$io = new Varien_Io_File();
						$io->rm(Mage::getBaseDir('media').DS.implode(DS,explode('/',$model->getData('thumbnail'))));	
				}
			}
						$path = Mage::getBaseDir('media') . DS . 'videos' . DS .'videos'.DS;
						$uploader = new Varien_File_Uploader('thumbnail');
						$uploader->setAllowedExtensions(array('jpg','png','gif'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$destFile = $path.$_FILES['thumbnail']['name'];
						$filename = $uploader->getNewFileName($destFile);
						$uploader->save($path, $filename);

						$post_data['thumbnail']='videos/videos/'.$filename;
		}
    }
}

        } catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
        }
//save image


						$model = Mage::getModel("videos/videos")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Videos was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setVideosData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setVideosData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("videos/videos");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
}

<?php
  class As_Emailpreview_Model_Email_Template_Mailer extends  Mage_Core_Model_Email_Template_Mailer
  {
  
        public function send()
    	{
		/** @var $emailTemplate Mage_Core_Model_Email_Template */
		$emailTemplate = Mage::getModel('emailpreview/email_template');
		// Send all emails from corresponding list
		while (!empty($this->_emailInfos)) {
		    $emailInfo = array_pop($this->_emailInfos);
		    // Handle "Bcc" recipients of the current email
		    $emailTemplate->addBcc($emailInfo->getBccEmails());
		    // Set required design parameters and delegate email sending to Mage_Core_Model_Email_Template
		    $emailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $this->getStoreId()))
		        ->setQueue($this->getQueue())
		        ->sendTransactional(
		            $this->getTemplateId(),
		            $this->getSender(),
		            $emailInfo->getToEmails(),
		            $emailInfo->getToNames(),
		            $this->getTemplateParams(),
		            $this->getStoreId()
		    );
        	}
        	return $this;
    	}
  
  }  

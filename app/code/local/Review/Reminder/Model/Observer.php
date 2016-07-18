<?php
class Review_Reminder_Model_Observer {
	
	const XML_PATH_NUM_OF_DAYS_AFTER_ORDER = 'reminder/general_settings/number_of_days';
	const XML_PATH_EMAIL_SENDER     = 'reminder/general_settings/sender_email_identity';
	const XML_PATH_EMAIL_TEMPLATE   = 'reminder/general_settings/email_template';
	 
	public function sendReviewReminder() {	
		//check is extension enabled
		if (!Mage::helper('reminder')->isExtensionEnabled()) {
			return;
		}
		$storeId = Mage::app()->getStore()->getStoreId();

		$date=date("Y-m-d h:i:s");
		$days = Mage::getStoreConfig(self::XML_PATH_NUM_OF_DAYS_AFTER_ORDER, $storeId);

		$differenceDate = date('Y-m-d',(strtotime ( '-'.$days.' day' , strtotime ( $date) ) ));
		$fromDate = $differenceDate ." 00:00:00";
		$toDate = $differenceDate ." 23:59:59";
		$salesCollection =Mage::getModel('sales/order')->getCollection()
			->addAttributeToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate))
			->setOrder('created_at','DESC');
		
		foreach($salesCollection as $order) {
	
			$createdDate=$order->getCreatedAt();
			$orderId=$order->getIncrementId();
			
			$configNumOfDaysAfterOrder= Mage::getStoreConfig(self::XML_PATH_NUM_OF_DAYS_AFTER_ORDER, $storeId);

			$baseUrl= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN);
			$imgUrl=$baseUrl."frontend/default/star-aurelia-responsive/images/email/star-img.jpg";
		
			$firstName=$order->getCustomerFirstname();
			$customerEmail = $order->getCustomerEmail();
			$items=$order->getAllItems();
			$productForReview="";
			$allProducts="";
			$total=1;
			
			foreach ($items as $itemId => $item) {
				$productName=$item->getName();
				$productId=$item->getProductId();
				$productObj = Mage::getModel('catalog/product')->load($productId);
				$categoryIds = $productObj->getCategoryIds();
				if($categoryIds && in_array('16', $categoryIds)) {
					continue;
				}
				$productUrl = $productObj->getProductUrl();
				
				if($total==1){
					$allProducts.= $productName;
				} else {
					$allProducts.= " and ". $productName;
				}
				
				$productForReview.='<tr>
					  <td style="text-align:center"><img src="'.$imgUrl.'" width="102" height="17" alt=""/></td>
					</tr>
					<tr>
					  <td height="10"></td>
					</tr>
					<tr>
					<td style="text-align:center; font-family:Times New Roman;"> <span style="text-transform:uppercase; letter-spacing:0.5px; color:#4d4d4d;font-size:16px;">'.$productName.' </span> <br/> <a href="'.$productUrl.'?openreview=1"  style="color:#4d4d4d; text-decoration:underline; font-size:16px; padding-top:4px">Please click here to write your review</a></td>
					</tr>
					<tr>
					  <td height="50"></td>
					</tr>';				 
				$total++;
			}
			$translate = Mage::getSingleton('core/translate');
			/* @var $translate Mage_Core_Model_Translate */
			$translate->setTranslateInline(false);
			try {
				$mailTemplate = Mage::getModel('core/email_template');
				/* @var $mailTemplate Mage_Core_Model_Email_Template */

			  // get configured email template
				$template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, Mage::app()->getStore()->getId());

				$mailSender = Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER, Mage::app()->getStore()->getId());
				
				$mailTemplate->sendTransactional(
					$template,
					$mailSender,
					$customerEmail,
					$firstName,
					array(
						'firstName' => $firstName,
						'allProducts'=> $allProducts,
						'reviewProducts'=>$productForReview
					)
				);
				
				if (!$mailTemplate->getSentSuccess()) {
					throw new Exception();
				}
					
				$translate->setTranslateInline(true);
				return true;
			} catch (Exception $ex) {
				$translate->setTranslateInline(true);
				return false;
			}
		}
	}
}
?>
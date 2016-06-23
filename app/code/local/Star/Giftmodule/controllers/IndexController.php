<?php

class Star_Giftmodule_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	if (Mage::app()->getRequest()->getPost()) {
    		$cart = Mage::getSingleton('checkout/cart');
    		$quote = $cart->getQuote();
    		$_productId = Mage::getStoreConfig('onestepcheckout/extra_products/product_ids');
			
			//Add Gift Message details to OSC
            $giftmessage = Mage::app()->getRequest()->getParam('gift_message');
            $giftSender = Mage::app()->getRequest()->getParam('gift_sender');
            $giftRecipient = Mage::app()->getRequest()->getParam('gift_recipient');
	
			$giftMessage = Mage::app()->getRequest()->getParam('gift_message_check');
			
			if ($giftMessage) {
				if (trim($giftmessage) !== '') {
										
					$giftMessage = Mage::getModel('giftmessage/message');
					$giftMessage->setCustomerId(Mage::getSingleton('customer/session')->getCustomer()->getId());
					$giftMessage->setSender($giftSender);
					$giftMessage->setRecipient($giftRecipient);					
					$giftMessage->setMessage($giftmessage);
					$giftObj = $giftMessage->save();
					$quote->setGiftMessageId($giftObj->getId());
					$quote->collectTotals()->save();					
            	}

			} else {
				
					$giftMessage = Mage::getModel('giftmessage/message');
					$giftMessage->setCustomerId(Mage::getSingleton('customer/session')->getCustomer()->getId());
					$giftMessage->setSender('fsdfsdf');
					$giftMessage->setRecipient('fdsfs');
					$giftMessage->setMessage($giftmessage);
					try {
						$giftObj = $giftMessage->save($giftmessage);
						$quote->setGiftMessageId($giftObj->getId());
					}
					catch(Exception $e) {
						//echo "<br>-------".$e->getMessage();
					}
					
					$quote->collectTotals()->save();

			}
    	}
    	else {
    		$this->_redirect('/');
    		return;
    	}

    	Mage::app()->getResponse()->setRedirect(Mage::getUrl('checkout/cart/'));
    	return;
    }


	public function giftWrapAction() { 
            $param = $this->getRequest()->getParam('gift_wrap_check');
            $cart = Mage::getSingleton('checkout/cart');
            $quote = $cart->getQuote();
            $_productId = Mage::getStoreConfig('onestepcheckout/extra_products/product_ids');

            if ($param == 'gift_wrap1') {
                $_product = Mage::getModel('catalog/product')->load($_productId);

                if ($_product->getId()) {
                    $cart->addProduct($_product, array('qty' => '1'));
                    $quote->collectTotals()->save();
                    Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                    $return['success'] = true;
                }
            }else {
                foreach($quote->getAllVisibleItems() AS $_item) {
                    if ($_item->getProduct()->getId() == $_productId) {
                        Mage::getSingleton('checkout/cart')->removeItem($_item->getId())->save();
                        $quote->collectTotals()->save();
                        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                        $return['success'] = true;
                    }
                }
            }
            echo json_encode($return);
            return;
        }

}

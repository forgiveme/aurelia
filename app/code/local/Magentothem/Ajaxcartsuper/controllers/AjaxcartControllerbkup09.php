<?php
require_once "Mage/Checkout/controllers/CartController.php";
class Magentothem_Ajaxcartsuper_AjaxcartController extends Mage_Checkout_CartController
{
      /**
     * override Add product to shopping cart action
     */
    public function addAction()
    {
		header("Content-type: application/json");
        if($this->getRequest()->getParam('callback')) {
             $cart   = $this->_getCart();
            $ajaxData = array();
            $productInfo = array();
            $params = $this->getRequest()->getParams();
             $qt=0; 
             $oldQty=0;
            try {
                if (isset($params['qty'])) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    $params['qty'] = $filter->filter($params['qty']);
		   //$qt=$params['qty'] ;
                }
                
                $product = $this->_initProduct();
                if($params['type_product']==1) {
                    $productInfo['type_product'] = $product->getTypeId();
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($productInfo));
                     return ;
                }
                $related = $this->getRequest()->getParam('related_product');
                
                /**
                 * Check product availability
                 */
                if (!$product) {
                    	$ajaxData['status'] = 0;
                        $ajaxData['message'] = $this->__('Unable to find Product ID');
                }

		if(Mage::helper('checkout/cart')->getQuote()->hasProductId($product->getId()))
		{  $item=Mage::helper('checkout/cart')->getQuote()->getItemByProduct($product);
		   $oldQty=$item->getQty();
		}
		
                $cart->addProduct($product, $params);
                if (!empty($related)) {
                    $cart->addProductsByIds(explode(',', $related));
                }

                $cart->save();

                $this->_getSession()->setCartWasUpdated(true);

                /**
                 * @todo remove wishlist observer processAddToCart
                 */
                Mage::dispatchEvent('checkout_cart_add_product_complete',
                    array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
                );

                if (!$this->_getSession()->getNoCartRedirect(true)) {
                    if (!$cart->getQuote()->getHasError()){
                        $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                       // $this->_getSession()->addSuccess($message);
                        $ajaxData['status'] = 1;
                        $this->loadLayout();
                        $sidebarCart = "";
                        $mini_cart = "";
                        $toplink = "";
                        if ($this->getLayout()->getBlock('cart_sidebar')) {
                            $sidebarCart = $this->getLayout()->getBlock('cart_sidebar')->toHtml();
                        }
                        if ($this->getLayout()->getBlock('cart_sidebar_mini')) {
                            $mini_cart = $this->getLayout()->getBlock('cart_sidebar_mini')->toHtml();
                        }
                        if ($this->getLayout()->getBlock('top.links')) {
                            $toplink = $this->getLayout()->getBlock('top.links')->toHtml();
                        }
                        $pimage = Mage::helper('catalog/image')->init($product, 'small_image')->resize(200);
                        $ajaxData['sidebar_cart'] = $sidebarCart;
                        $ajaxData['top_link'] = $toplink;
                        $ajaxData['mini_cart'] = $mini_cart;
			$count = Mage::helper('checkout/cart')->getSummaryCount();  //get total items in cart
			$total = Mage::helper('checkout/cart')->getQuote()->getGrandTotal();
			$item=Mage::helper('checkout/cart')->getQuote()->getItemByProduct($product);
			$qt=$item->getQty()-$oldQty;
						if (strpos($number,'.') !== false) {

								$total=$total;
							}else {
								$total=$total.".00";
							}
                        //show or hide cofirmbox when add product to cart
                        if (Mage::getStoreConfig('ajaxcartsuper/ajaxcartsuper_config/show_confirm')) {
                            //$ajaxData['product_info'] = Mage::helper('ajaxcartsuper/data')->productHtml($product->getName(), $product->getProductUrl(), $pimage);
                        $volume=$product->getVolume();
                        $namevolume='';
                        $formattedPrice = Mage::helper('core')->currency($product->getFinalPrice(), true, false);
                        if($volume) { $volume=$volume;} else { $volume='';}
                        if($volume) { $namevolume='<span class="popup-product-volume">'.$volume.'</span>';}
						$ajaxData['product_info']	=	 "<div class='left-panel'>";
						$ajaxData['product_info']	.=	"<div id='product_info_box'>  <h2>ADDED TO YOUR SHOPPING BAG</h2>";
						$ajaxData['product_info']	.=	 "<div class='p_image'><img src='" .$pimage." '></div>";
						$ajaxData['product_info']	.= "<div class='p_name'><a href=' ".$product->getProductUrl()."'>".$product->getName()."<span class='popup-product-price'>".$formattedPrice."</span>".$namevolume."</a></div>";
						$ajaxData['product_info']	.= "</div><div id='product_cart_info'><div class='product-cart-detail'><p> Quantity Added ".$qt."</p>
																			<p> Items In Bag ".$count." &nbsp; | &nbsp; Bag Total  &pound;".$total."</p>
																				  </div>
																				</div>
																			  </div>";
							$ajaxData['shopping_bag_link']='SHOPPING BAG '.$count ;
                        }
						
						 $RelProduct = Mage::getModel('catalog/product')->load($product->getId())->getRelatedProductIds();
						 
						  
				$ajaxData['related_pro']= "<h2>WE RECOMMEND TO USE WITH</h2>";
				$ajaxData['related_pro'].= "<a id='close_popup'>close</a>";
				$ajaxData['related_pro'].= "<div class='product clearfix'>";

							$limit=1;
							foreach ($RelProduct as $id) {
							if($limit<3){
							$relatedProduct = Mage::getModel('catalog/product')->load($id);
							$relImage=Mage::helper('catalog/image')->init($relatedProduct, 'small_image')->resize(100,120);
								
								if($limit!=1){
							  $ajaxData['related_pro'].=  "<div class='product__item last'>";
							 }else{
							 $ajaxData['related_pro'].=  "<div class='product__item'>";
							 }
							 $ajaxData['related_pro'].= " <div class='product__img'><img src='".$relImage."'></div>";
							 $ajaxData['related_pro'].=   "<div class='related-container'><span>".$relatedProduct->getName()."</span>";
							 $desc=$relatedProduct->getDescription();
							$pos=strpos($desc, ' ', 140);
							$shortDesc=substr($desc,0,$pos ); 
							 $ajaxData['related_pro'].=   " <div class='related-desc'><p>".$shortDesc."</p>";
							 $ajaxData['related_pro'].=    "</div> <div class='qty-detail'><input type='hidden' value='1' id='qty' name='qty'></div>";
							 $proUrl=$relatedProduct->getProductUrl();
							 $ajaxData['related_pro'].= " <a class='addtobag' href='".Mage::helper('checkout/cart')->getAddUrl($relatedProduct)."'>Add to Bag</a> </div> </div> ";
							 
					
							 }
							 $limit=$limit+1;
								}
						$ajaxData['related_pro'].=	"</div>"; 
						
						
                    }
                }
              
            } catch (Mage_Core_Exception $e) {
                
                $msg = "";
                if ($this->_getSession()->getUseNotice(true)) {
                    $msg = $e->getMessage();
                } else {
                    $messages = array_unique(explode("\n", $e->getMessage()));
                    foreach ($messages as $message) {
                        $msg .= $message . '<br/>';
                    }
                }
                $ajaxData['status'] = 0;
                $ajaxData['message'] = $msg;
                $ajaxData['type_product_ajax'] = 1;
                
            } catch (Exception $e) {
                $ajaxData['status'] = 0;
                $ajaxData['message'] = $this->__('Cannot add the this product to shopping cart.');
            }
           $this->getResponse()->setBody($this->getRequest()->getParam('callback').'('.Mage::helper('core')->jsonEncode($ajaxData).')');
           return;

        }  else {
            parent::addAction();
        }
    }
     /**
     * override Delete shoping cart item action
     */
    public function deleteAction() {
       
        if ($this->getRequest()->getParam('callback')) {
            $id = (int) $this->getRequest()->getParam('id');
            if ($id) {
                try {
                    $this->_getCart()->removeItem($id)
                            ->save();
                } catch (Exception $e) {
                    $this->_getSession()->addError($this->__('Cannot remove the item.'));
                    Mage::logException($e);
                }
            }
            $this->loadLayout();
            $sidebarCart = "";
            $mini_cart = "";
            $toplink = "";
            if($this->getLayout()->getBlock('cart_sidebar')) {
                $sidebarCart = $this->getLayout()->getBlock('cart_sidebar')->toHtml();
            }
            if($this->getLayout()->getBlock('cart_sidebar_mini')){
                $mini_cart =  $this->getLayout()->getBlock('cart_sidebar_mini')->toHtml();
            }
            if($this->getLayout()->getBlock('top.links')){
                $toplink = $this->getLayout()->getBlock('top.links')->toHtml();
            }

            if($this->getRequest()->getParam('type_del')=='cart' & $this->getRequest()->getParam('count_remove')==1) {
               $cartEmpty = Mage::helper('ajaxcartsuper/data')->getEmptyCartHtml();
            }
            $ajaxData['status'] = 1;
            $ajaxData['top_link'] = $toplink;
            $ajaxData['sidebar_cart'] = $sidebarCart;
            $ajaxData['checkout_cart'] = $cartEmpty;
            $ajaxData['mini_cart'] = $mini_cart;
            $this->getResponse()->setBody($this->getRequest()->getParam('callback').'('.Mage::helper('core')->jsonEncode($ajaxData).')');
            return;
        } else {
            $id = (int) $this->getRequest()->getParam('id');
            if ($id) {
                try {
                    $this->_getCart()->removeItem($id)
                            ->save();
                } catch (Exception $e) {
                    $this->_getSession()->addError($this->__('Cannot remove the item.'));
                    Mage::logException($e);
                }
            }
            $this->_redirectReferer(Mage::getUrl('*/*'));
        }
    }
    
     /**
     * override Update product configuration for a cart item
     */
    public function updateItemOptionsAction()
    {
             if($this->getRequest()->getParam('callback')) {
                    $ajaxData = array();
                    $productInfo = array();
                    $cart   = $this->_getCart();
                    $id = (int) $this->getRequest()->getParam('id');
                    $params = $this->getRequest()->getParams();

                    if (!isset($params['options'])) {
                        $params['options'] = array();
                    }
                    try {
                        if (isset($params['qty'])) {
                            $filter = new Zend_Filter_LocalizedToNormalized(
                                array('locale' => Mage::app()->getLocale()->getLocaleCode())
                            );
                            $params['qty'] = $filter->filter($params['qty']);
                        }

                        $quoteItem = $cart->getQuote()->getItemById($id);
                        if (!$quoteItem) {
                            Mage::throwException($this->__('Quote item is not found.'));
                        }

                        $item = $cart->updateItem($id, new Varien_Object($params));
                        if (is_string($item)) {
                            Mage::throwException($item);
                        }
                        if ($item->getHasError()) {
                            Mage::throwException($item->getMessage());
                        }

                        $related = $this->getRequest()->getParam('related_product');
                        if (!empty($related)) {
                            $cart->addProductsByIds(explode(',', $related));
                        }

                        $cart->save();

                        $this->_getSession()->setCartWasUpdated(true);

                        Mage::dispatchEvent('checkout_cart_update_item_complete',
                            array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
                        );
                        if (!$this->_getSession()->getNoCartRedirect(true)) {
                            if (!$cart->getQuote()->getHasError()){
                                $message = $this->__('%s was updated in your shopping cart.', Mage::helper('core')->htmlEscape($item->getProduct()->getName()));
                               // $this->_getSession()->addSuccess($message);
                            }
                        }
                    } catch (Mage_Core_Exception $e) {
                        if ($this->_getSession()->getUseNotice(true)) {
                            $this->_getSession()->addNotice($e->getMessage());
                        } else {
                            $messages = array_unique(explode("\n", $e->getMessage()));
                            foreach ($messages as $message) {
                                $this->_getSession()->addError($message);
                            }
                        }

                        $url = $this->_getSession()->getRedirectUrl(true);
             
                    } catch (Exception $e) {
                        $this->_getSession()->addException($e, $this->__('Cannot update the item.'));
                        Mage::logException($e);
                    }
                    $this->loadLayout();
                if ($this->getLayout()->getBlock('cart_sidebar')) {
                    $sidebarCart = $this->getLayout()->getBlock('cart_sidebar')->toHtml();
                }
                if ($this->getLayout()->getBlock('cart_sidebar_mini')) {
                    $mini_cart = $this->getLayout()->getBlock('cart_sidebar_mini')->toHtml();
                }
                if ($this->getLayout()->getBlock('top.links')) {
                    $toplink = $this->getLayout()->getBlock('top.links')->toHtml();
                }
                $ajaxData['status'] = 1;
                $ajaxData['top_link'] = $toplink;
                $ajaxData['sidebar_cart'] = $sidebarCart;
                $ajaxData['mini_cart'] = $mini_cart;
                $this->getResponse()->setBody($this->getRequest()->getParam('callback').'('.Mage::helper('core')->jsonEncode($ajaxData).')');
        } else {
            parent::updateItemOptionsAction();
        }
    }
     /**
     * override Delete shoping cart item action
     */ 
    
      public function updatePostAction() {

        if ($this->getRequest()->getParam('callback')) {
            $updateAction = (string) $this->getRequest()->getParam('update_cart_action');

            switch ($updateAction) {
                case 'empty_cart':
                    $this->_emptyShoppingCart();
                    break;
                case 'update_qty':
                    $this->_updateShoppingCart();
                    break;
                default:
                    $this->_updateShoppingCart();
            }
            $this->loadLayout();
            $updateAction = $this->getRequest()->getParams();
 
            if($updateAction == 'empty_cart') {
            }
            $toplink = $this->getLayout()->getBlock('top.links')->toHtml();
            $cartEmpty = Mage::helper('ajaxcartsuper/data')->getEmptyCartHtml();
            $ajaxData['status'] = 1;
            $ajaxData['top_link'] = $toplink;
           // $ajaxData['checkout_cart'] = $cartEmpty;
            $this->getResponse()->setBody($this->getRequest()->getParam('callback').'('.Mage::helper('core')->jsonEncode($ajaxData).')'); 
        } else {
            $updateAction = (string) $this->getRequest()->getParam('update_cart_action');
            switch ($updateAction) {
                case 'empty_cart':
                    $this->_emptyShoppingCart();
                    break;
                case 'update_qty':
                    $this->_updateShoppingCart();
                    break;
                default:
                    $this->_updateShoppingCart();
            }
            $this->_goBack();
        }
    }

}

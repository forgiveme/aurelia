<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

if(!isset($_GET['key']) OR $_GET['key']!='22cz3c4JHdcfxcfCZncxc2sejg21v') exit(403);

include_once dirname(__FILE__).'/app/Mage.php';

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Tue, 01 Jan 2013 00:00:00 GMT"); // Date in the past
header('Content-Type: text/xml; charset=utf-8');
//header('Content-Type: text/plain; charset=utf-8');
//header('Content-Disposition: attachment; filename=feedoptimise.com.xml');

if(!defined('STORE_ID'))
{
	define('STORE_ID',1);
}

Mage::app()->setCurrentStore(STORE_ID);
$store = Mage::getModel('core/store')->load(STORE_ID);

set_time_limit(10000);

$products = Mage::getModel('catalog/product')->setStoreId(STORE_ID)->getCollection()->setOrder('entity_id', 'asc')->setPageSize(100);
//$products = Mage::getModel('catalog/product')->getCollection();

//$products->addFieldToFilter('entity_id', array('eq' => '146'));

$products->addAttributeToSelect('entity_id');
$products->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));


$visibility = array(
		Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
		Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
);

$products->addAttributeToFilter('visibility', $visibility);

$baseUrl = 'https://www.aureliaskincare.com/';

$pages = $products->getLastPageNumber();
$currentPage = 1;

echo '<?xml version="1.0" encoding="utf-8"?>
<items created_by="www.feedoptimise.com" created_at="'.date(DATE_ATOM).'">';

do {

	$products->setCurPage($currentPage);
	$products->load();

	$currentPage++;

	foreach ($products as $id => $product):

		if (isset($_GET['id']))
		{
			$p = Mage::getModel('catalog/product')->load($_GET['id']);
		}
		else
		{
			$p = Mage::getModel('catalog/product')->load($id);
		}

		if (isset($_GET['dump']) && $_GET['dump'] == 'true')
		{
			header('Content-Type: text/plain; charset=utf-8');
			echo"<pre>";var_dump($p);
			die;
		}


		$price = $p->getPrice();
		$special_price = $p->getFinalPrice();
		$sspecial = Mage::getModel('catalogrule/rule')->calcProductPriceRule($p,$p->getPrice());

		if ($sspecial > 0 && $sspecial<$price && (!$special_price ||  $sspecial < $special_price))
		{
			$special_price =  round($sspecial,2);
		}

		if(!$special_price || $special_price == $price)
		{
			$special_price = '';
		}

		if(!$p['is_in_stock'] OR $p->getSku()=='test' OR $p['image'] == 'no_selection') continue;

		$variants = '';
		$images = '';

		$delivery_price = 0;
		if($p->getTypeId() == 'configurable')
		{
			$qty = 0;
			$price = 10000000000000;

			$options = options($p, $special_price);

			$variants = '<variantsJson><![CDATA['.json_encode($options).']]></variantsJson>';

			foreach ($options as $option) {

				$qty += $option['qty'];
				if($option['price']<$price)
				{
					$price = $option['price'];
				}

			}
		}
		else
		{
			//$price = round(Mage::helper('tax')->getPrice($p, $p->getFinalPrice(), true),2);
			$price = $p['price'];
			$qty   = Mage::getModel('cataloginventory/stock_item')->loadByProduct($p)->getQty();

		}

		//header('Content-Type: text/plain; charset=utf-8');
		//echo"<pre>";var_dump($qty, $p->isAvailable(), $p->isSaleable(), $price);die;

		if(!$p->isAvailable() OR !$p->isSaleable() OR !$price)
		{
			$product->clearInstance();
			$p->clearInstance();
			continue;
		}

		$coptions = array();

		foreach ($p->getOptions() as $o) {
			$optionType = $o->getType();

			$title = $o->getDefaultTitle();

			if ($optionType == 'drop_down') {
				$values = $o->getValues();

				foreach ($values as $k => $v)
				{

					//print_r($v);
					//exit;

					$optionPrice = $v->getPrice();
					$coptions[$title][] = array(
							'value' => $v->getTitle(),
							'id'    => strtolower($p['entity_id'].'-'.trim($v->getTitle())),
							'price' => round(Mage::helper('tax')->getPrice($p, $p->getFinalPrice()+$optionPrice, true),2)
					);
				}
			}
			else {
				//print_r($o);
			}
		}


		if(count($coptions))
		{
			$coptions = '<coptions><![CDATA['. json_encode($coptions) .']]></coptions>';
		}
		else
		{
			$coptions = '';
		}

		$categoryIds  = $p->getCategoryIds();
		$categories   = array();

		foreach($categoryIds as $categoryId)
		{
			$category = Mage::getModel('catalog/category')->load($categoryId);

			if(!$category->getName() ||$category->getName() == 'Default Category') continue;
			$categories[$category->getLevel()] = $category->getName();
		}

		ksort($categories);
		$parentCategories = reset($categories);

		unset($categoryIds);

		$url = $baseUrl.$p['url_path'];
		$url = trim ($url);

		$meta = array();
		$meta['weight'] 							= @$p['weight'] ?: '';
		$meta['volume'] 			= @$p['volume'] ?: '';

		echo '
  <item>
   <id>'. $p['entity_id'].'</id>
   <name><![CDATA['. clearStr($p['name']) .']]></name>
   <brand><![CDATA['. (@$p['stylecom_brandname'] ?: 'Aurelia Probiotic Skincare') .']]></brand>
   <sku><![CDATA['. $p['sku'] .']]></sku>
   <mpn><![CDATA['. $p['sku'] .']]></mpn>
   <gtin><![CDATA['. $p['ean'] .']]></gtin>
   <description><![CDATA['.mb_convert_encoding(clearStr($p->getDescription()) ,'UTF-8','UTF-8') .']]></description>
   <price><![CDATA['. $price .']]></price>

   <special_price><![CDATA['. $special_price .']]></special_price>

   <msrp><![CDATA['.$p->getMsrp().']]></msrp>
   <rrp><![CDATA['. $p['msrp'] .']]></rrp>
   <categories><![CDATA['. getMostSpecificCategory($p) .']]></categories>
   <category><![CDATA['. implode(' > ' ,$categories) .']]></category>
   <url><![CDATA['. $url .']]></url>

   <parentCategories><![CDATA['. $parentCategories .']]></parentCategories>
   <is_in_stock><![CDATA['.$p['is_in_stock'].']]></is_in_stock>
   <image_url_main><![CDATA['. $baseUrl ."media/catalog/product". $p['image'] .']]></image_url_main>
   <weight><![CDATA['. $p['weight'] .']]></weight>

   <qty><![CDATA['.$qty.']]></qty>
   <meta><![CDATA['.json_encode($meta).']]></meta>
   '.$coptions.'

   '.$variants.'
   '.$images.'
  </item>';

		$product->clearInstance();
		$p->clearInstance();

		unset($categories);
		unset($p);
		unset($product);

		flush();
		if (isset($_GET['id']))
		{
			break;
		}
	endforeach;

	$products->clear();
	if (isset($_GET['id']))
	{
		break;
	}
} while ($currentPage <= $pages);

echo '</items>';



function options(&$_product, $parent_special_price)
{
	global $baseUrl;
	//Load all simple products
	$products = array();

	$allProducts = $_product->getTypeInstance(true)->getUsedProducts(null, $_product);
	foreach ($allProducts as $product) {
		if ($product->isSaleable())
		{
			$products[] = $product;
		}
		else
		{
			$products[] = $product;
		}
	}

	//Load all used configurable attributes
	$configurableAttributeCollection = $_product->getTypeInstance()->getConfigurableAttributes();

	$result = array();
	//Get combinations
	foreach ($products as $product_)
	{
		$product = Mage::getModel('catalog/product')->load($product_->getId());

		$items = array();
		$pricing =0;
		$attrOptionValueArr = [];
		$urlParams = array();

		if(!$product->isAvailable() OR !$product->isSaleable())
		{
			continue;
		}

		foreach($configurableAttributeCollection as $attribute)
		{
			$attrValue = $product->getResource()->getAttribute($attribute->getProductAttribute()->getAttributeCode())->getFrontend();
			//$attrCode = $attribute->getProductAttribute()->getAttributeCode();
			$attrCode = $attribute->getProductAttribute()->getStoreLabel();
			$value = $attrValue->getValue($product);
			$items['theme_'.$attrCode] = $value;

			foreach($attrValue->getSelectOptions() as $attrOption)
			{
				if($attrOption['label'] == $value )
				{
					$urlParams[] = $attribute["attribute_id"].'='.$attrOption['value'];
					$attrOptionValueArr[] = $attrOption['value'];
				}
			}

			foreach ($attribute->getPrices() as $attrPrice)
			{
				//header('Content-Type: text/plain; charset=utf-8');
				//echo"<pre>";var_dump($attrPrice["value_index"], $attrOptionValueArr);die;

				if(in_array($attrPrice["value_index"], $attrOptionValueArr))
				{
					$pricing += $attrPrice['pricing_value'] ?: 0;
				}
			}
		}

		if(count($urlParams))
		{
			$urlParams = '#'.implode('&',$urlParams);
		}
		else
		{
			$urlParams = '';
		}

		$items['url'] = $baseUrl.$_product['url_path'].$urlParams;
		$items['is_in_stock'] = (int) $product['is_in_stock'];
		$items['qty'] = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();;
		$items['price'] = $_product->getFinalPrice()+$pricing;
		$items['name'] = $product->getName();
		//$items['msrp'] = $product->getMsrp();

		$special_price = getSpecialPrice($product);
		$items['special_price'] = $special_price ?  $special_price : $parent_special_price;



		$items['sku']= $product->getSku();
		//$items['gr_ean']= $product['gr_ean'];
		$items['id']= $product['entity_id'];

		$product->load('media_gallery');

		if(isset($product['media_gallery']['images']) && is_array($product['media_gallery']['images']))
		{
			$images = array();
			foreach($product['media_gallery']['images'] as $image)
			{
				if($image['disabled']) continue;

				$images[] = $baseUrl ."media/catalog/product". $image['file'];
			}

			$items['images']=$images;

		}

		$result[] = $items;

		$product->clearInstance();
		$product_->clearInstance();
	}

	unset($configurableAttributeCollection);
	unset($products);
	return $result;
}

function clearStr($str)
{
	return str_replace(array(
			"\x00","\x01","\x02","\x03","\x04","\x05","\x06","\x07","\x08","\t",
			"\n","\x0B","\x0C","\r","\x0E","\x0F",
			"\x10","\x11","\x12","\x13","\x14","\x15","\x16","\x17","\x18","\x19",
			"\x1A","\x1B","\x1C","\x1D","\x1E","\x1F"
	),' ',$str);
}

function getSpecialPrice($p)
{
	$price    = $p->getPrice();
	$final    = $p->getFinalPrice();

	if ($final > 0 && $final<$price) {
		return round($final,2);
	} else {
		return "";
	}
}

function &getParentTopCategory($catId,$digg=true)
{
	$id               = $catId;

	$category = Mage::getModel('catalog/category')->load($id);
	$parentId = $category->getParentId();

	if(isset($category['level']) && $category['level']<3)
	{
		$parentCategories[] = $category->getName();
		$child = $category->getChildren();
		$category->clearInstance();

		$child = explode(',',$child);
		if($digg)
		{
			$parentCategories = getParentTopCategory(trim($child[0]),false);
		}

		return $parentCategories;
	}

	$category->clearInstance();

	while(true)
	{
		$category = Mage::getModel('catalog/category')->load($parentId);
		if(!isset($category['level'])){$category->clearInstance();break;}

		$parentId            = $category->getParentId();
		$parentCategories[] = $category->getName();


		$category->clearInstance();
		if($category['level']<3){$category->clearInstance();break;}
	}

	$parentCategories = array_reverse($parentCategories);
	return $parentCategories;
}
function getMostSpecificCategory($p)
{
	$categoryIds  = $p->getCategoryIds();
	$categories   = array();
	$level = -1;
	$catPath = array();

	foreach($categoryIds as $categoryId)
	{
		$category = Mage::getModel('catalog/category')->load($categoryId);

		if($category->getLevel() > $level )
		{
			$level = $category->getLevel();
			$catPath = $category->getPathIds();
		}
		$category->clearInstance();
	}

	if(count($catPath))
	{
		foreach ($catPath as $catId)
		{
			$category = Mage::getModel('catalog/category')->load($catId);
			if($category->getLevel() > 1 && $category->getName() !='')
				$categories[] = $category->getName();
			$category->clearInstance();
		}
	}

	return implode(' > ', $categories);
}
?>
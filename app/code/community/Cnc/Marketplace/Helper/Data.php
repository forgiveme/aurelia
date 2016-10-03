<?php

class Cnc_Marketplace_Helper_Data extends Mage_Core_Helper_Abstract
{
    const NO_SELECTION = 'no_selection';
    const NO_CATEGORY_SELECTED_MSG = 'category not selected';

    protected $_readAdapter = null;
    protected $_defaultAttributes = null;
    protected $_defaultProductAttributes = null;
    protected $_attributeList = array();

    protected function _getReadAdapter()
    {
        if (is_null($this->_readAdapter)) {
            $this->_readAdapter = Mage::getSingleton('core/resource')->getConnection('core_read');
        }

        return $this->_readAdapter;
    }

    protected function _getCategoryConfig()
    {
        $categories = explode(',', Mage::getStoreConfig('marketplace/product_settings/categories'));
        $output = [];
        foreach ($categories as $category) {
            if (!empty($category) && !in_array($category, $output)) {
                $output[] = $category;
            }
        }
        return $output;
    }

    public function getConfigurationData($meta_key)
    {
        $configuration = Mage::getModel('marketplace/configurationtable')->load($meta_key);
        $data = $configuration->getData();
        return $data['meta_value'];
    }

    public function configCheckerAll()
    {
        $callApi = Mage::helper('marketplace/callapi');
        $checkConnection = $callApi->getCarriers(1);
        $this->addConfigurationData('config_valid_data', $checkConnection);
        if (!$checkConnection) {

            $message = "Error: ";

            if ($this->object_is_nonempty($this->getStyledotComProducts())) {
                $message .= "Please check the URL and API key entered";
            } else {
                $message .= "Please complete the style.com configuration steps";
            }

            Mage::getSingleton('core/session')->getMessages(true);
            Mage::getSingleton('core/session')->addError($message);
            session_write_close();
            Mage::app()->getCacheInstance()->flush();
            return 0;
        }
        return 1;
    }

    public function addConfigurationData($meta_key, $meta_value)
    {
        $update_data = array(
            'meta_value' => $meta_value
        );
        $model = Mage::getModel('marketplace/configurationtable')->load($meta_key)->addData($update_data);
        try {
            $model->setId($meta_key)->save();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getMagentoCarriers($isMultiSelect = false)
    {
        $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
        $options = array();
        foreach ($methods as $_code => $_method) {
            if (!$_title = Mage::getStoreConfig("carriers/$_code/title")) {
                $_title = $_code;
            }
            $options[] = array(
                'value' => $_code,
                'label' => $_title . " ($_code)"
            );
        }
        if ($isMultiSelect) {
            array_unshift($options, array(
                'value' => '',
                'label' => Mage::helper('adminhtml')->__('--Please Select--')
            ));
        }
        return $options;
    }

    public function checkCredentials()
    {
        $checkConnection = $this->getConfigurationData('config_valid_data');
        return $checkConnection;
    }

    /*
     * Returns a list of default attributes, combined from results from various sources,
     * for either a product or offer.
     *
     * @param String $type Either 'product' or 'offer'
     */
    public function getDefaultProductAttributes($type = 'offer')
    {
        if (is_null($this->_defaultAttributes)) {
            Mage::helper('marketplace/logger')->log('type is ', $type);

            //Get attributes from various sources
            $attributes = $this->getAttributes();
            $stock_attributes = $this->getStockAttributes($type);

            // Artificially add a group id field, as it is required by Mirakl.
            $styledotcom_attributes['gid']['field_code'] = '_styledotcom_group_id';
            $styledotcom_attributes['gid']['field_name'] = 'Style.com Group Id';

            // Merge them together & sort alphabetically
            $this->_defaultAttributes = array_merge($stock_attributes, $attributes, $styledotcom_attributes);
            uasort($this->_defaultAttributes, function ($a, $b) {
                return strcmp($a['field_code'], $b['field_code']);
            });

            if ($this->_defaultProductAttributes) {
                $this->_attributeList[] = '_styledotcom_group_id';
                foreach ($this->_defaultProductAttributes as $attribute) {
                    $this->_attributeList[] = $attribute->getAttributeCode();
                }
            }

            Mage::helper('marketplace/logger')->log('returning: ', $this->_defaultAttributes);
        }
        return $this->_defaultAttributes;
    }

    /*
     * Retrieves all the product attributes.
     */
    public function getAttributes()
    {
        $fields_data = array();
        $attributesCollection = Mage::getResourceModel('catalog/product_attribute_collection');
        $ignoredAttributes = explode(',', Mage::getStoreConfig('marketplace/product_settings/ignored_attributes', Mage::app()->getStore()->getStoreId()));

        if (is_array($ignoredAttributes) && count($ignoredAttributes) > 0) {
            $attributesCollection->addFieldToFilter('attribute_code', array('nin' => $ignoredAttributes));
        }

        $this->_defaultProductAttributes = $attributesCollection->getItems();
        foreach ($this->_defaultProductAttributes as $key => $attribute) {
            $fields_data[$key]['field_code'] = $attribute->getAttributecode();
            $fields_data[$key]['field_name'] = $attribute->getFrontendLabel();
        }

        Mage::helper('marketplace/logger')->log('returning: ', $fields_data);
        return $fields_data;
    }

    /*
     * Retrieves Stock Attributes.
     *
     * If type is 'product' we only retrieve attributes relevant to product CSV.
     * Else if it is 'offer' we retrieve all stock attributes.
     *
     * @param String $type Either 'product' or 'offer'
     */
    public function getStockAttributes($type)
    {
        $dbName = (string)Mage::getConfig()->getResourceConnectionConfig("default_setup")->dbname;
        $query = "
            SELECT `COLUMN_NAME`
            FROM `INFORMATION_SCHEMA`.`COLUMNS`
            WHERE
                `TABLE_SCHEMA`='$dbName'
                AND `TABLE_NAME`='cataloginventory_stock_item';
        ";
        $stock_maker = $this->_getReadAdapter()->fetchCol($query);

        Mage::helper('marketplace/logger')->log('stock_maker is ', $stock_maker);
        $stock_field_list = array();
        foreach ($stock_maker as $key => $stock_make) {
            if ($type == 'offer' ||
                ($type == 'product' && ($stock_make == 'item_id' || $stock_make == 'product_id'))
            ) {
                $stock_field_list[$key]['field_code'] = $stock_make;
                $parts = explode('_', $stock_make);
                $parts = array_map('ucfirst', $parts);
                $stock_field_list[$key]['field_name'] = implode(' ', $parts);
            }
        }

        Mage::helper('marketplace/logger')->log('returning: ', $stock_field_list);
        return $stock_field_list;
    }

    public function getAllImports($type, $ajax_import_ids)
    {
        $ajax_import_ids = isset($ajax_import_ids) ? $ajax_import_ids : '';
        $type = isset($type) ? $type : '';
        $model = Mage::getModel('marketplace/importtable');
        $collection = $model->getCollection();
        $collection->setOrder('date_created', 'DESC');
        if ($type) {
            $collection->getType($type);
        }
        if ($ajax_import_ids) {
            $collection->getImportIds($ajax_import_ids);
        }
        $data = array();
        foreach ($collection as $item) {
            $data[] = $item->getData();
        }
        return $data;
    }

    public function getImportStatusByType($type, $import_data)
    {
        if ($type == 'product') {
            $status = isset($import_data->import_status) ? $import_data->import_status : '';
        } else {
            $status = isset($import_data->status) ? $import_data->status : '';
        }
        return $status;
    }

    public function executeImportsUpdate($import_ids, $type)
    {
        $report['type'] = $type;
        $all_imports = $this->getAllImports($type, $import_ids);
        $callApi = Mage::helper('marketplace/callapi');
        $url_between = $callApi->getImportPathByType($report['type']);
        foreach ($all_imports as $import) {
            $import_id = isset($import['import_id']) ? $import['import_id'] : '';
            if ($import['status'] != 'COMPLETE') {
                $response = $callApi->getCurlResponse($url_between, $import_id);
                $import_data = json_decode($response);
                $report['status'] = $callApi->getImportStatusByType($report['type'], $import_data);
                $date_created = isset($import_data->date_created) ? $import_data->date_created : '';
                $import_status_file = $callApi->getImportStatusFile($url_between, $import_data, $import_id);
                $report['transformed_file'] = isset($import_status_file['transformed_file']) ? $import_status_file['transformed_file'] : '';
                $report['error_file'] = isset($import_status_file['error_file']) ? $import_status_file['error_file'] : '';
                $report['transformation_error_report'] = isset($import_status_file['transformation_error_report']) ? $import_status_file['transformation_error_report'] : '';
                $this->updateImportData($report, $date_created, $import_id, $import['id']);
            }
        }
    }

    public function updateImportData($report, $date_created, $import_id, $id)
    {
        $transformation_error_report = !empty($report['transformation_error_report']) ? 'Download' : '';
        $error_file = !empty($report['error_file']) ? 'Download' : '';
        $transformed_file = !empty($report['transformed_file']) ? 'Download' : '';
        $type = !empty($report['type']) ? $report['type'] : '';
        $status = !empty($report['status']) ? $report['status'] : '';
        $data = array(
            'import_id'       => $import_id,
            'type'            => $type,
            'date_created'    => $date_created,
            'status'          => $status,
            'error'           => $error_file,
            'transform'       => $transformed_file,
            'transform_error' => $transformation_error_report
        );
        $model = Mage::getModel('marketplace/importtable')->load($id)->addData($data);
        try {
            $model->setId($id)->save();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function addImportData($report, $date_created, $import_id)
    {
        $error_file = !empty($report['error_file']) ? 'Download' : '';
        $transformed_file = !empty($report['transformed_file']) ? 'Download' : '';
        $type = !empty($report['type']) ? $report['type'] : '';
        $status = !empty($report['status']) ? $report['status'] : '';
        $data = array(
            'import_id' => $import_id,
            'type' => $type,
            'date_created' => $date_created,
            'status' => $status,
            'error' => $error_file,
            'transform' => $transformed_file
        );
        $model = Mage::getModel('marketplace/importtable')->setData($data);
        try {
            $insertId = $model->save()->getId();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function downloadError_file()
    {
        $data = Mage::app()->getRequest()->getPost();
        $type = isset($data['err_type']) ? $data['err_type'] : '';
        $type_po = isset($data['type']) ? $data['type'] : '';
        $import_id = isset($data['import_id']) ? $data['import_id'] : '';
        $callApi = Mage::helper('marketplace/callapi');
        $url_between = $callApi->getImportPathByType($type_po);
        if ($type == 'transformation_error_report') {
            $import_path = '/transformation_error_report';
            $report = $callApi->getCurlResponse($url_between, $import_id, $import_path, 'csv');
        } else if ($type == 'transformed_file') {
            $import_path = '/transformed_file';
            $report = $callApi->getCurlResponse($url_between, $import_id, $import_path, 'csv');
        } else if ($type == 'error_report') {
            $import_path = '/error_report';
            $report = $callApi->getCurlResponse($url_between, $import_id, $import_path, 'csv');
        }
        return $report;
        exit;
    }

    public function getStockDataByProductId($product)
    {
        $prostock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
        $stock_list = $prostock->getData();
        return $stock_list;
    }

    public function getProductValuesForAttrFields($fields_selected, $all_product_with_catIds, $cron)
    {
        $product_field_values = array();
        $separator = ';';
        foreach ($fields_selected as $fields_select) {
            $selector_with_field = isset($all_product_with_catIds[$fields_select]) ? $all_product_with_catIds[$fields_select] : '';
            $product_field_values[] = '"' . urldecode(html_entity_decode(strip_tags($selector_with_field))) . '"';
        }
        $product_field_value = implode($separator, $product_field_values);
        return $product_field_value;
    }

    public function getOfferValuesForAttrFields($fields_selected, $all_product_with_catIds)
    {
        $mapped_fields_offer = Mage::app()->getRequest()->getPost();
        $product_field_values = array();
        $seprator = ';';
        foreach ($fields_selected as $fields_select) {
            $selector_with_field = $fields_select['value'];
            $product_field_values[] = '"' . urldecode(html_entity_decode(strip_tags($all_product_with_catIds[$selector_with_field]))) . '"';
        }
        $product_field_value = implode($seprator, $product_field_values);
        return $product_field_value;
    }

    public function setImportData($import_id, $type)
    {
        $obj = json_decode($import_id);
        $import_id = $obj->import_id;
        $report['type'] = isset($type) ? $type : '';
        $callApi = Mage::helper('marketplace/callapi');
        $url_between = $callApi->getImportPathByType($report['type']);
        $response = $callApi->getCurlResponse($url_between, $import_id);
        $import_data = json_decode($response);
        $report['status'] = $this->getImportStatusByType($report['type'], $import_data);
        $this->addImportData($report, $import_data->date_created, $import_id);
    }

    public function getOffersAttrHeadings($existing_fields, $mapped_fields, $cron = false)
    {
        $existing_field = json_decode($existing_fields);
        $selected_fields = array();
        $field_headings = array();
        foreach ($existing_field as $key => $existing_field) {
            $field_headings[] = $existing_field->name;
            $selected_fields[$key]['name'] = $existing_field->name;
            if ($cron)
                $selected_fields[$key]['value'] = $existing_field->value;
            else
                $selected_fields[$key]['value'] = $mapped_fields[$key];
        }
        // 2016-02-18 state will default to '11', i.e. New
        $field_headings[] = "state";
        $field_headings[] = "update-delete";
        $field_heading = implode(';', $field_headings);
        if (!$cron) {
            $selected_fields_json = json_encode($selected_fields);
            $this->addConfigurationData('product_map', $selected_fields_json, '0');
        }
        return array(
            $field_heading,
            $selected_fields
        );
    }

    public function getOffersToUpload()
    {
        $productIds = $this->getStyledotComProducts(array(), true);
        if (count($productIds) > 0) {
            $dbConfig = Mage::getConfig()->getResourceConnectionConfig("default_setup");
            $offersQuery = "
                SELECT
                    cpe.sku AS sku,
                    cpe.sku AS 'product-id',
                    'SHOP_SKU' AS 'product-id-type',
                    ROUND(cpip.price,0) AS price,
                    ROUND(csi.qty,0) AS quantity,
                    '11' AS state
                FROM catalog_product_entity AS cpe
                LEFT JOIN catalog_product_index_price AS cpip ON cpip.entity_id = cpe.entity_id
                LEFT JOIN cataloginventory_stock_item AS csi ON csi.product_id = cpe.entity_id
                WHERE
                  cpip.price > 0
                  AND csi.qty > 0
                  AND cpe.entity_id IN (" . implode(",", $productIds) . ")
                GROUP BY cpe.entity_id
            ";

            $exportDir = Mage::getBaseDir() . '/var/cnc_marketplace/';
            if (!file_exists($exportDir)) {
                mkdir($exportDir, 0777, true);
            }

            $csvFilename = 'offer';
            $tmpOutputFile = $exportDir . $csvFilename . '_tmp.csv';
            $outputFile = $exportDir . $csvFilename . '.csv';
            @unlink($outputFile);

            $cmd = "mysql --host=" . $dbConfig->host . " -u" . $dbConfig->username . " -p" . $dbConfig->password . " " . $dbConfig->dbname . " -B -e \"$offersQuery\" | tr '\\t' ';' > $tmpOutputFile";
            shell_exec($cmd);
            shell_exec('sed \'s,\\\\\\\\,\\\\,g\' ' . $tmpOutputFile . ' > ' . $outputFile);
            @unlink($tmpOutputFile);

            $callApi = Mage::helper('marketplace/callapi');
            $callApi->uploadOfferData();
        } else {
            Mage::log('OFFERS : no product selection specified', null, 'cnc_marketplace_errors.log', true);
        }
    }

    public function getProductAttrHeadings($fields_selected, $cron = false, $productIds = array())
    {
        $_products = $this->getStyledotComProducts($productIds);
        $field_headings = array();
        foreach ($fields_selected as $fields_select) {
            $field_headings[] = $fields_select;
        }
        if (!$cron) {
            $field_heading_json = json_encode($field_headings);
            $this->addConfigurationData('stored_fields', $field_heading_json, 'products');
        }

        // Add picture's columns
        for ($j = 1; $j <= 12; $j++) {
            $field_headings[] = 'Picture URL ' . $j;
        }

        $field_heading = implode(";", $field_headings);
        return array(
            $_products,
            $field_heading
        );
    }

    public function getProductIdsQuery()
    {
        $categoryIds = $this->_getCategoryConfig();
        if (count($categoryIds) > 0) {
            $whereConditionsArray = [];
            foreach ($categoryIds as $category) {
                $whereConditionsArray[] = "path LIKE '%/$category/%'";
            }

            $query = "SELECT entity_id FROM catalog_category_entity";
            if (count($whereConditionsArray) > 0) {
                $query .= " WHERE " . implode("OR ", $whereConditionsArray);
            }

            // Get all children categories of the configured categories
            $childrenCategoryIds = $this->_getReadAdapter()->fetchCol($query);
            if (count($childrenCategoryIds)) {
                $categoryIds = array_merge($childrenCategoryIds, $categoryIds);
            }

            return "
                SELECT DISTINCT child_id
                FROM catalog_category_product AS ccp
                LEFT JOIN catalog_product_relation AS cpr ON cpr.parent_id = ccp.product_id
                WHERE category_id IN (" . implode(",", $categoryIds) . ")
            ";
        }

        return null;
    }

    /**
     * Retrieves all 'simple' products which are set to display in Style.com
     * @param array $productIds
     * @param bool $outputCollectionIds
     * @return Mage_Catalog_Model_Resource_Collection_Abstract
     */
    public function getStyledotComProducts($productIds = array(), $outputCollectionIds = false)
    {
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect((empty($this->_attributeList) ? '*' : $this->_attributeList))
            ->addAttributeToFilter('type_id', array('eq' => 'simple'))
            ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->setStoreId(Mage::app()->getRequest()->getParam('store'))
            ->addAttributeToSort('created_at', 'DESC');

        // No manual selection of products through the GUI ?
        if (count($productIds) === 0) {
            // Try to get a batch of product restricted by system config
            $productQuery = $this->getProductIdsQuery();
            if (null !== $productQuery) {
                $productIds = array_values(array_unique($this->_getReadAdapter()->fetchCol($this->getProductIdsQuery())));
            }
        }

        // No product specific selection? Fallback to style.com default selection (flag to Yes)
        if (count($productIds) === 0) {
            $collection->addAttributeToFilter('display_style_com', 1);
        } // Else, select only our products
        else {
            $collection->addAttributeToFilter('entity_id', array('in' => $productIds));
        }

        if ($outputCollectionIds) {
            return $collection->getAllIds();
        }

        return $collection;
    }

    public function object_is_nonempty($obj)
    {
        foreach ($obj as $x) return true;
        return false;
    }

    protected function _getProductMediaGalleryImages($productId)
    {
        $baseUrl = Mage::getBaseUrl('media');
        return $this->_getReadAdapter()->fetchCol("
            SELECT REPLACE(CONCAT(?, cpemg.value), 'media//', 'media/')
            FROM catalog_product_entity_media_gallery AS cpemg
            LEFT JOIN catalog_product_entity_media_gallery_value AS cpemgv ON cpemgv.value_id = cpemg.value_id
            WHERE entity_id = ?
            ORDER BY cpemgv.position ASC
        ", array($baseUrl, $productId));
    }

    protected function _getProductName($product)
    {
        return $product->getData('name');
    }

    /**
     * $cron = whether initiated from the scheduler (i.e. use defaults)
     * $upload = whether to actually upload. If not, just create product.csv
     * $outputInShell = print in shell's console export status
     * @param bool $cron
     * @param bool $upload
     * @param bool $outputInShell
     * @param array $productIds
     * @return bool
     */
    public function getProductsToUpload($cron = true, $upload = true, $outputInShell = false, $productIds = array())
    {
        $configurableAtttributes = explode(',', Mage::getStoreConfig('marketplace/product_settings/attributes_from_configurable', Mage::app()->getStore()->getStoreId()));
        $default_attributes = $this->getDefaultProductAttributes('product');
        if ($cron) {
            if (!$this->_attributeList) {
                $fields_selected = json_decode($this->getConfigurationData('stored_fields'));
            } else {
                $fields_selected = $this->_attributeList;
            }
        } else {
            if (Mage::app()->getRequest()->isPost()) {
                $fields_selected = Mage::app()->getRequest()->getPost();
                $fields_selected = $fields_selected['field_key_values'];
            }
        }

        // if fields_selected is empty or
        // just has the single _styledotcom_group_id field (from bug API-494), use defaults
        $has_fields_selected = $this->object_is_nonempty($fields_selected);
        if (!$has_fields_selected || (count($fields_selected) == 1 && $fields_selected[0] == "_styledotcom_group_id")) {
            Mage::helper('marketplace/logger')->log('Detected that product fields for CSV not stored, get defaults');
            $default_attributes = $this->getDefaultProductAttributes('product');
            $default_attributes_arr = array();

            foreach ($default_attributes as $key => $default_attribute) {
                array_push($default_attributes_arr, $default_attribute['field_code']);
            }
            $fields_selected = $default_attributes_arr;
        }

        list($_products, $field_heading) = $this->getProductAttrHeadings($fields_selected, $cron, $productIds);

        // do we have products?
        $totalProducts = $_products->getSize();
        $has_products = $totalProducts > 0;
        if ($has_products) {
            if ($outputInShell) {
                echo "$totalProducts products to export to CSV" . PHP_EOL;
            }

            // Create string to store CSV file contents
            $file_string = $field_heading . "\n";

            $idx = 0;
            $image = Mage::getModel('catalog/product_media_config');
            foreach ($_products as $product) {
                $productId = $product->getId();
                if ($outputInShell) {
                    echo "#" . $idx++ . " - " . $product->getSku() . PHP_EOL;
                }

                $prod_attributes = $product->getData();

                // Name
                $prod_attributes['name'] = $this->_getProductName($product);

                // Base image
                $baseImage = $product->getImage();

                $thumbnail_path_url = $image->getMediaUrl($product->getThumbnail());
                $smallimage_path_url = $image->getMediaUrl($product->getSmallImage());
                $image_path_url = $image->getMediaUrl($baseImage);
                $prod_attributes['thumbnail'] = $thumbnail_path_url;
                $prod_attributes['small_image'] = $smallimage_path_url;
                $prod_attributes['image'] = $image_path_url;
                $prod_attributes['url_path'] = $this->getProductUrl($prod_attributes['sku']);
                $prod_attributes['category_ids'] = $this->getCategoryPath($prod_attributes['sku']);

                if (!$prod_attributes['category_ids']) {
                    $prod_attributes['category_ids'] = self::NO_CATEGORY_SELECTED_MSG;
                }

                // Get parent IDS (just one time)
                $parent_ids = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
                $parentId = $parent_ids[0];
                $parentProduct = Mage::getModel('catalog/product')->load($parentId);

                // if image is empty or has 'no_selection', the product is likely
                // to be a simple product if so, use configurable product image
                if (empty($baseImage) || $baseImage == self::NO_SELECTION) {
                    if (count($parent_ids) >= 1) {
                        if ($parentProductId = $parentProduct->getSku()) {
                            $prod_attributes['image'] = $parentProduct->getImage();
                        }
                    }
                }

                if (count($parent_ids) > 0) {
                    try {
                        $parent_skus = $this->_getReadAdapter()->fetchCol("SELECT sku FROM catalog_product_entity WHERE entity_id IN (" . implode(",", $parent_ids) . ");");
                    } catch (Exception $e) {
                        Mage::logException($e);
                        continue;
                    }

                    // This product has a parent, so use its sku as _styledotcom_group_id.
                    // Only take the first parent sku as system cannot handle multiple parents
                    $prod_attributes['_styledotcom_group_id'] = $parent_skus[0];
                    if (count($parent_skus) > 1) {
                        // The product has multiple parents.  We might have selected the 'wrong' one
                        // so let's at least log what we have done.
                        Mage::helper('marketplace/logger')->log('SKU ' . $prod_attributes['sku'] . ' has ' . count($parent_skus), 'Only use ' . $parent_skus[0] . ' ignoring the others');
                    }
                } else {
                    // This is a simple product which does not have a 'parent'.  Use it's SKU to create its
                    // own unique group.
                    $prod_attributes['_styledotcom_group_id'] = $prod_attributes['sku'];
                }

                foreach ($fields_selected as $fields_title) {
                    $attribute = $product->getData($fields_title);
                    if (!empty($attribute)) {
                        $check = $product->getAttributeText($fields_title);
                        if ((!is_array($check)) && $check && trim($check) != '') {
                            unset($prod_attributes[$fields_title]);
                            $prod_attributes[$fields_title] = $product->getAttributeText($fields_title);
                        } else if (is_array($check) && count($check) > 0) {
                            $check = implode(",", $check);
                            unset($prod_attributes[$fields_title]);
                            $prod_attributes[$fields_title] = $check;
                        }
                    }
                }

                // Override data with parent's data if necessary
                if (count($configurableAtttributes) > 0) {
                    if ($parentProductId = $parentProduct->getSku()) {
                        foreach ($configurableAtttributes as $configurableAtttribute) {
                            $prod_attributes[$configurableAtttribute] = $parentProduct->getData($configurableAtttribute);
                        }
                    }
                }

                /*$stock_list = $this->getStockDataByProductId($product);
                $array_all_attr = array_merge($stock_list, $prod_attributes);*/

                // Strip default category from categories
                $prod_attributes['category_ids'] = str_replace('defaultcategory>', '', $prod_attributes['category_ids']);

                $product_field_value = $this->getProductValuesForAttrFields($fields_selected, $prod_attributes, $cron);

                // Handle media gallery
                $mediaGallery = $this->_getProductMediaGalleryImages($productId);
                $mediaGalleryArray = [];
                if (count($mediaGallery)) {
                    foreach ($mediaGallery as $mediaGalleryImage) {
                        $mediaGalleryArray[] = '"' . $mediaGalleryImage . '"';
                    }
                }

                if (count($mediaGalleryArray) > 0) {
                    $product_field_value .= ";" . implode(";", $mediaGalleryArray);
                }

                $file_string .= $product_field_value . "\n";


                // If individual products selected or upload from category config selection set attribute to Yes
                $product->setData('display_style_com', 1)->getResource()->saveAttribute($product, 'display_style_com');

                // Clear memory
                unset($mediaGallery, $mediaGalleryArray, $prod_attributes);
            }

            // Create CSV file
            Mage::helper('marketplace/util')->writeExportFile('product', utf8_encode($file_string));

            if ($upload) {
                $callApi = Mage::helper('marketplace/callapi');
                $callApi->uploadProductData();
            }
        } else {
            Mage::helper('marketplace/logger')->log('No products: ', $_products);
        }

        return $has_products;
    }

    public function getCategoryPath($sku)
    {
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

        $parentIdArray = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());

        // check if the product is a simple that belongs to a configurable. If so, use configurable
        if (sizeof($parentIdArray) == 1 &&
            Mage::getModel('catalog/product')->load($parentIdArray[0])->getTypeId() == 'configurable'
        ) {
            $product = Mage::getModel('catalog/product')->load($parentIdArray[0]);
        }

        $pathArray = array();
        $collection1 = $product->getCategoryCollection()->setStoreId(Mage::app()->getStore()->getId())->addAttributeToSelect('path')->addAttributeToSelect('is_active');
        foreach ($collection1 as $cat1) {
            $pathIds = explode('/', $cat1->getPath());
            $collection = Mage::getModel('catalog/category')->getCollection()->setStoreId(Mage::app()->getStore()->getId())->addAttributeToSelect('name')->addAttributeToSelect('is_active')->addFieldToFilter('entity_id', array(
                'in' => $pathIds
            ));
            $pathByName = '';
            $i = 1;
            foreach ($collection as $cat) {
                if ($i != 1) {
                    $sep = ($i != 2) ? '>' : '';
                    $pathByName .= $sep . $cat->getName();
                }
                $i++;
            }
            $pathArray[] = $pathByName;
        }
        $final_categories = array_pop($pathArray);
        return $final_categories;
    }

    public function getProductUrl($sku)
    {
        $url = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku)->getProductUrl();
        return $url;
    }

    public function getOrderOfferMessages()
    {
        $results = Mage::getModel('marketplace/messagetable')->getCollection()->getData();
        return $results;
    }

    public function getSingleOrder($orderid, $mgnto)
    {
        $mgnto = isset($mgnto) ? $mgnto : 0;
        $prefix = Mage::getConfig()->getTablePrefix();
        $select = $this->_getReadAdapter()->select()->from($prefix . 'cn_cmi_orders', array(
            '*'
        ));
        if ($mgnto) {
            $select->where('m_order_id=?', $orderid);
        } else {
            $select->where('orderid=?', $orderid);
        }
        $rowArray = $this->_getReadAdapter()->fetchRow($select);
        return $rowArray;
    }

    public function setMirakleOrders($activity, $pass_order_id, $order_line)
    {
        $fields_selected = Mage::app()->getRequest()->getPost();
        $order_line = isset($order_line) ? $order_line : '';
        $url_api = Mage::getStoreConfig('marketplace/configuration/api_url');
        if (isset($fields_selected["activity"]) && trim($fields_selected["activity"]) != '') {
            if ($fields_selected["activity"] == 'reject' || $fields_selected["activity"] == 'reject_manual') {
                $reject = true;
            }
            if ($fields_selected["activity"] == 'accept' || $fields_selected["activity"] == 'reject' || $fields_selected["activity"] == 'reject_manual') {
                $fields_selected["activity"] = 'confirm';
            }
        }
        $action = isset($fields_selected["activity"]) ? $fields_selected["activity"] : $activity;
        $callApi = Mage::helper('marketplace/callapi');
        switch ($action) {
            case "confirm":
                $result = $callApi->executeConfirmOrderStatus($url_api, $fields_selected, $pass_order_id, $reject);
                break;
            case "confirm_multiple":
                $result = $callApi->executeMultipleConfirmOrderStatus($url_api, $pass_order_id, $order_line);
                break;
            case "tracking":
                $result = $callApi->executeTrackingOrderStatus($url_api, $fields_selected, $pass_order_id);
                break;
            case "shipped":
                $result = $callApi->executeShippedOrderStatus($url_api, $fields_selected, $pass_order_id);
                break;
            case "refund":
                $result = $callApi->executeRefundOrderStatus($url_api, $fields_selected);
                break;
        }
        return $result;
    }

    public function updateCustomOrderTable($order_id, $mgnto_order_id, $message)
    {
        $has_message = (isset($message) && !empty($message)) ? '1' : '0';
        $check = $this->getSingleOrder($order_id, '');
        if ($check['orderid'] && isset($check['orderid'])) {
            $id = $check['id'];
            if (!$check['m_order_id']) {
                $data = array(
                    'orderid'           => $order_id,
                    'last_updated_date' => $check['last_updated_date'],
                    'total_price'       => $check['total_price'],
                    'order_state'       => $check['order_state'],
                    'all_fields'        => $check['all_fields'],
                    'm_order_id'        => $mgnto_order_id,
                    'message'           => $message,
                    'message_read'      => $has_message
                );
                $model = Mage::getModel('marketplace/ordertable')->load($id)->addData($data);
                try {
                    $model->setId($id)->save();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
        return $check['orderid'];
    }

    public function saveOrderMessages()
    {
        $orderId = isset($orderId) ? $orderId : '';
        $callApi = Mage::helper('marketplace/callapi');
        $all_messages = $callApi->getAllMessages();
        $message_data = json_decode($all_messages, true);
        $message_id_array = array();
        foreach ($message_data['messages'] as $message) {
            $all_fields = json_encode($message);
            if ($message['order_id']) {
                $message['type_msg'] = 'order';
                $order_offer_id = $message['order_id'];
            } else {
                $message['type_msg'] = 'offer';
                $order_offer_id = $message['offer_id'];
            }
            $message_id_array[] = $message['id'];
            $check = Mage::getModel('marketplace/messagetable')->getCollection()->getByMessageID($message['id'])->getData();
            $check_offer = Mage::getModel('marketplace/offertable')->getCollection()->getOfferByOfferId($order_offer_id)->getData();
            /*$date_created = date('Y-m-d H:i:s', strtotime($message['date_created']));
            if (isset($check[0]['id']) && $check[0]['id']) {
                if (!$check_offer) {
                    Mage::getModel('marketplace/messagetable')->getResource()->deleteByCondition($order_offer_id, 'offer');
                }
            } else {
                if ($message['type_msg'] == 'offer') {
                    if ($check_offer) {
                        $this->insertOrderMessages($message, $order_offer_id, $all_fields, $date_created);
                    }
                } else {
                    $this->insertOrderMessages($message, $order_offer_id, $all_fields, $date_created);
                }
            }*/
        }
        $existing_db_messages = Mage::getModel('marketplace/messagetable')->getCollection()->getData();
        foreach ($existing_db_messages as $existing_db_message) {
            if (!in_array($existing_db_message['message_id'], $message_id_array)) {
                $this->deleteOfferMessages($existing_db_message['id']);
            }
        }
    }

    public function deleteOfferMessages($id)
    {
        $id = isset($id) ? $id : '';
        if ($id) {
            $model = Mage::getModel('marketplace/messagetable');
            try {
                $model->setId($id)->delete();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    public function insertOrderMessages($message, $order_offer_id, $order_all_fields, $date_created)
    {
        $data = array(
            'message_id'     => $message['id'],
            'order_offer_id' => $order_offer_id,
            'type_user'      => $message['from_type'],
            'type_msg'       => $message['type_msg'],
            'all_fields'     => $order_all_fields,
            'date_created'   => $date_created,
            'read_msg'       => 0
        );
        $model = Mage::getModel('marketplace/messagetable')->setData($data);
        try {
            $insertId = $model->save()->getId();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getInduvidualOrderMessage($order_id, $type)
    {
        $order_id = isset($order_id) ? $order_id : '';
        $type = isset($type) ? $type : '';
        $callApi = Mage::helper('marketplace/callapi');
        $output = $callApi->getInduvidualMessage($order_id, $type);
        $checks = Mage::getModel('marketplace/messagetable')->getCollection()->getByOrderOfferID($order_id)->getData();
        foreach ($checks as $key => $check) {
            $id = $check['id'];
            $data = array(
                'read_msg' => 1
            );
            $model = Mage::getModel('marketplace/messagetable')->load($id)->addData($data);
            try {
                $model->setId($id)->save();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        echo $output;
    }

    /**
     * @return mixed
     */
    public function createProductsCSV()
    {
        Mage::helper('marketplace/logger')->log('export', 'Downloaded CSV for Mirakl mapping');
        $has_products = $this->getProductsToUpload(true, false);
        return $has_products;
    }

    public function createCSVFromProductSelection($products)
    {
        Mage::helper('marketplace/logger')->log('export', 'Downloaded CSV for Mirakl mapping');
        $has_products = $this->getProductsToUpload(true, true, false, $products);
        return $has_products;
    }
}

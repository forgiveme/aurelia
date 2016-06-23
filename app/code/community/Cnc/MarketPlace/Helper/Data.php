<?php
class Cnc_MarketPlace_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getConfigurationData( $meta_key )
    {
        $configuration = Mage::getModel( 'marketplace/configurationtable' )->load( $meta_key );
        $data          = $configuration->getData();
        return $data[ 'meta_value' ];
    }
    public function configCheckerAll()
    {
        $callApi         = Mage::helper( 'marketplace/callapi' );
        $checkConnection = $callApi->getCarriers( 1 );
        $this->addConfigurationData( 'config_valid_data', $checkConnection );
        if ( !$checkConnection ) {

            $message = "Error: ";

            if($this->object_is_nonempty($this->getStyledotComProducts())) {
                $message .= "Please check the URL and API key entered";
            } else {
                $message .= "Please complete the style.com configuration steps";
            }

            Mage::getSingleton( 'core/session' )->getMessages( true );
            Mage::getSingleton( 'core/session' )->addError($message);
            session_write_close();
            Mage::app()->getCacheInstance()->flush();
            return 0;
        }
        return 1;
    }
    public function addConfigurationData( $meta_key, $meta_value )
    {
        $update_data = array(
             'meta_value' => $meta_value
        );
        $model       = Mage::getModel( 'marketplace/configurationtable' )->load( $meta_key )->addData( $update_data );
        try {
            $model->setId( $meta_key )->save();
        }
        catch ( Exception $e ) {
            echo $e->getMessage();
        }
    }
    public function getMagentoCarriers( $isMultiSelect = false )
    {
        $methods = Mage::getSingleton( 'shipping/config' )->getActiveCarriers();
        $options = array();
        foreach ( $methods as $_code => $_method ) {
            if ( !$_title = Mage::getStoreConfig( "carriers/$_code/title" ) ) {
                $_title = $_code;
            }
            $options[ ] = array(
                 'value' => $_code,
                'label' => $_title . " ($_code)"
            );
        }
        if ( $isMultiSelect ) {
            array_unshift( $options, array(
                 'value' => '',
                'label' => Mage::helper( 'adminhtml' )->__( '--Please Select--' )
            ) );
        }
        return $options;
    }
    public function checkCredentials()
    {
        $checkConnection = $this->getConfigurationData( 'config_valid_data' );
        return $checkConnection;
    }

    /*
     * Retrieves all the product attributes.
     */
    public function getAttributes()
    {
        $fields_data = array();
        $attributes  = Mage::getResourceModel( 'catalog/product_attribute_collection' )->getItems();
        foreach ( $attributes as $key => $attribute ) {
            $fields_data[ $key ][ 'field_code' ] = $attribute->getAttributecode();
            $fields_data[ $key ][ 'field_name' ] = $attribute->getFrontendLabel();
        }

        return $fields_data;
    }

    public function getItemIdFromStockAttributes()
    {
        $stock_lists = Mage::getResourceModel( 'cataloginventory/stock_item_collection' )->getItems();
        $stock_maker = array();
        $first       = true;
        $parts       = '';
        foreach ( $stock_lists as $stock_list ) {
            if ( $first ) {
                $stock_maker = array_keys( $stock_list->getData() );
                $first       = false;
            }
        }
        $stock_field_list = array();
        foreach ( $stock_maker as $key => $stock_make ) {
            if ( $stock_make == 'item_id' || $stock_make == 'product_id' ) {
                $stock_field_list[ $key ][ 'field_code' ] = $stock_make;
                $parts                                    = explode( '_', $stock_make );
                $parts                                    = array_map( 'ucfirst', $parts );
                $stock_field_list[ $key ][ 'field_name' ] = implode( ' ', $parts );
            }
        }

        return $stock_field_list;
    }
    public function getStockAttributes()
    {
        $stock_lists = Mage::getResourceModel( 'cataloginventory/stock_item_collection' )->getItems();
        $stock_maker = array();
        $first       = true;
        $parts       = '';
        foreach ( $stock_lists as $stock_list ) {
            if ( $first ) {
                $stock_maker = array_keys( $stock_list->getData() );
                $first       = false;
            }
        }
        $stock_field_list = array();
        foreach ( $stock_maker as $key => $stock_make ) {
            $stock_field_list[ $key ][ 'field_code' ] = $stock_make;
            $parts                                    = explode( '_', $stock_make );
            $parts                                    = array_map( 'ucfirst', $parts );
            $stock_field_list[ $key ][ 'field_name' ] = implode( ' ', $parts );
        }
        return $stock_field_list;
    }

    public function getDefaultProductAttributes( $type = '' )
    {
        $attributes = $this->getAttributes();
        if ( $type == 'product' ) {
            $stock_attributes = $this->getItemIdFromStockAttributes();
        } else {
            $stock_attributes = $this->getStockAttributes();
        }

        // Artificially add a group id field, as it is required by Mirakl. 
        $styledotcom_attributes['gid']['field_code'] = '_styledotcom_group_id';
        $styledotcom_attributes['gid']['field_name'] = 'Style.com Group Id';

        $fields_data = array_merge( $stock_attributes, $attributes, $styledotcom_attributes );

        // sort alphabetically
        uasort($fields_data, function($a, $b) {
            return strcmp($a['field_code'], $b['field_code']);
        });
        return $fields_data;
    }

    public function getAllImports( $type, $ajax_import_ids )
    {
        $ajax_import_ids = isset( $ajax_import_ids ) ? $ajax_import_ids : '';
        $type            = isset( $type ) ? $type : '';
        $model           = Mage::getModel( 'marketplace/importtable' );
        $collection      = $model->getCollection();
        $collection->setOrder( 'date_created', 'DESC' );
        if ( $type ) {
            $collection->getType( $type );
        }
        if ( $ajax_import_ids ) {
            $collection->getImportIds( $ajax_import_ids );
        }
        $data = array();
        foreach ( $collection as $item ) {
            $data[ ] = $item->getData();
        }
        return $data;
    }
    public function getImportStatusByType( $type, $import_data )
    {
        if ( $type == 'product' ) {
            $status = isset( $import_data->import_status ) ? $import_data->import_status : '';
        } else {
            $status = isset( $import_data->status ) ? $import_data->status : '';
        }
        return $status;
    }
    public function executeImportsUpdate($import_ids, $type)
    {
        $report[ 'type' ] = $type;
        $all_imports = $this->getAllImports( $type, $import_ids );
        $callApi     = Mage::helper( 'marketplace/callapi' );
        $url_between = $callApi->getImportPathByType( $report[ 'type' ] );
        foreach ( $all_imports as $import ) {
            $import_id = isset( $import[ 'import_id' ] ) ? $import[ 'import_id' ] : '';
            if ( $import[ 'status' ] != 'COMPLETE' ) {
                $response                                 = $callApi->getCurlResponse( $url_between, $import_id );
                $import_data                             = json_decode( $response );
                $report[ 'status' ]                      = $callApi->getImportStatusByType( $report[ 'type' ], $import_data );
                $date_created                            = isset( $import_data->date_created ) ? $import_data->date_created : '';
                $import_status_file                      = $callApi->getImportStatusFile( $url_between, $import_data, $import_id );
                $report[ 'transformed_file' ]            = isset( $import_status_file[ 'transformed_file' ] ) ? $import_status_file[ 'transformed_file' ] : '';
                $report[ 'error_file' ]                  = isset( $import_status_file[ 'error_file' ] ) ? $import_status_file[ 'error_file' ] : '';
                $report[ 'transformation_error_report' ] = isset( $import_status_file[ 'transformation_error_report' ] ) ? $import_status_file[ 'transformation_error_report' ] : '';
                $this->updateImportData( $report, $date_created, $import_id, $import[ 'id' ] );
            }
        }
    }
    public function updateImportData( $report, $date_created, $import_id, $id )
    {
        $transformation_error_report = !empty( $report[ 'transformation_error_report' ] ) ? 'Download' : '';
        $error_file                  = !empty( $report[ 'error_file' ] ) ? 'Download' : '';
        $transformed_file            = !empty( $report[ 'transformed_file' ] ) ? 'Download' : '';
        $type                        = !empty( $report[ 'type' ] ) ? $report[ 'type' ] : '';
        $status                      = !empty( $report[ 'status' ] ) ? $report[ 'status' ] : '';
        $data                        = array(
             'import_id' => $import_id,
            'type' => $type,
            'date_created' => $date_created,
            'status' => $status,
            'error' => $error_file,
            'transform' => $transformed_file,
            'transform_error' => $transformation_error_report
        );
        $model                       = Mage::getModel( 'marketplace/importtable' )->load( $id )->addData( $data );
        try {
            $model->setId( $id )->save();
        }
        catch ( Exception $e ) {
            echo $e->getMessage();
        }
    }
    public function addImportData( $report, $date_created, $import_id )
    {
        $error_file       = !empty( $report[ 'error_file' ] ) ? 'Download' : '';
        $transformed_file = !empty( $report[ 'transformed_file' ] ) ? 'Download' : '';
        $type             = !empty( $report[ 'type' ] ) ? $report[ 'type' ] : '';
        $status           = !empty( $report[ 'status' ] ) ? $report[ 'status' ] : '';
        $data             = array(
             'import_id' => $import_id,
            'type' => $type,
            'date_created' => "$date_created",
            'status' => $status,
            'error' => $error_file,
            'transform' => $transformed_file
        );
        $model            = Mage::getModel( 'marketplace/importtable' )->setData( $data );
        try {
            $insertId = $model->save()->getId();
        }
        catch ( Exception $e ) {
            echo $e->getMessage();
        }
    }
    public function downloadError_file()
    {
        $data        = Mage::app()->getRequest()->getPost();
        $type        = isset( $data[ 'err_type' ] ) ? $data[ 'err_type' ] : '';
        $type_po     = isset( $data[ 'type' ] ) ? $data[ 'type' ] : '';
        $import_id   = isset( $data[ 'import_id' ] ) ? $data[ 'import_id' ] : '';
        $callApi     = Mage::helper( 'marketplace/callapi' );
        $url_between = $callApi->getImportPathByType( $type_po );
        if ( $type == 'transformation_error_report' ) {
            $import_path = '/transformation_error_report';
            $report      = $callApi->getCurlResponse( $url_between, $import_id, $import_path, 'csv' );
        } else if ( $type == 'transformed_file' ) {
            $import_path = '/transformed_file';
            $report      = $callApi->getCurlResponse( $url_between, $import_id, $import_path, 'csv' );
        } else if ( $type == 'error_report' ) {
            $import_path = '/error_report';
            $report      = $callApi->getCurlResponse( $url_between, $import_id, $import_path, 'csv' );
        }
        return $report;
        exit;
    }
    public function getStockDataByProductId( $product )
    {
        $prostock   = Mage::getModel( 'cataloginventory/stock_item' )->loadByProduct( $product );
        $stock_list = $prostock->getData();
        return $stock_list;
    }
    public function getProductValuesForAttrFields( $fields_selected, $all_product_with_catIds, $cron )
    {
        $product_field_values = array();
        $seprator             = ';';
        foreach ( $fields_selected as $fields_select ) {
            
            $selector_with_field     = isset( $all_product_with_catIds[ $fields_select ] ) ? $all_product_with_catIds[ $fields_select ] : '';
            $product_field_values[ ] = '"' . urldecode( html_entity_decode( strip_tags( $selector_with_field ) ) ) . '"';
        }
        $product_field_value = implode( $seprator, $product_field_values );
        return $product_field_value;
    }
    public function getOfferValuesForAttrFields( $fields_selected, $all_product_with_catIds )
    {
        $mapped_fields_offer  = Mage::app()->getRequest()->getPost();
        $product_field_values = array();
        $seprator             = ';';
        foreach ( $fields_selected as $fields_select ) {
            $selector_with_field     = $fields_select[ 'value' ];
            $product_field_values[ ] = '"' . urldecode( html_entity_decode( strip_tags( $all_product_with_catIds[ $selector_with_field ] ) ) ) . '"';
        }
        $product_field_value = implode( $seprator, $product_field_values );
        return $product_field_value;
    }
    public function setImportData( $import_id, $type )
    {
        $obj                = json_decode( $import_id );
        $import_id          = $obj->import_id;
        $report[ 'type' ]   = isset( $type ) ? $type : '';
        $callApi            = Mage::helper( 'marketplace/callapi' );
        $url_between        = $callApi->getImportPathByType( $report[ 'type' ] );
        $response           = $callApi->getCurlResponse( $url_between, $import_id );
        $import_data        = json_decode( $response );
        $report[ 'status' ] = $this->getImportStatusByType( $report[ 'type' ], $import_data );
        $this->addImportData( $report, $import_data->date_created, $import_id );
    }
    public function getOffersAttrHeadings( $existing_fields, $mapped_fields, $cron = false )
    {
        $existing_field  = json_decode( $existing_fields );
        $selected_fields = array();
        $field_headings  = array();
        foreach ( $existing_field as $key => $existing_field ) {
            $field_headings[ ]                 = $existing_field->name;
            $selected_fields[ $key ][ 'name' ] = $existing_field->name;
            if ( $cron )
                $selected_fields[ $key ][ 'value' ] = $existing_field->value;
            else
                $selected_fields[ $key ][ 'value' ] = $mapped_fields[ $key ];
        }
        // 2016-02-18 state will default to '11', i.e. New
        $field_headings[ ] = "state";
        $field_headings[ ] = "update-delete";
        $field_heading     = implode( ';', $field_headings );
        if ( !$cron ) {
            $selected_fields_json = json_encode( $selected_fields );
            $this->addConfigurationData( 'product_map', $selected_fields_json, '0' );
        }
        return array(
             $field_heading,
            $selected_fields
        );
    }
    public function getOffersToUpload( $cron = false )
    {
        if ( $cron ) {
            $mapped_fields = json_decode( $this->getConfigurationData( 'product_map' ) );
        } else {
            $mapped_fields = Mage::app()->getRequest()->getPost();
            $mapped_fields = $mapped_fields[ 'map_value' ];
        }
        if ( count( $mapped_fields ) > 0 && $mapped_fields ) {
            $existing_fields = $this->getConfigurationData( 'product_map' );
            list( $field_heading, $selected_fields ) = $this->getOffersAttrHeadings( $existing_fields, $mapped_fields, $cron );
            $file = Mage::helper('marketplace/util')->getOfferExportFilename();
            file_put_contents( $file, $field_heading . "\n" );
            $_products = $this->getStyledotComProducts();
            $image     = Mage::getModel( 'catalog/product_media_config' );
            $logMessages = array();
            foreach ( $_products as $_product ) {
                $_product->getId();
                $product                           = Mage::getModel( 'catalog/product' )->load( $_product->getId() );
                $prod_attributes                   = $_product->getData();
                $thumbnail_path_url                = $image->getMediaUrl( $product->getThumbnail() );
                $smallimage_path_url               = $image->getMediaUrl( $product->getSmallImage() );
                $image_path_url                    = $image->getMediaUrl( $product->getImage() );
                $prod_attributes[ 'thumbnail' ]    = $thumbnail_path_url;
                $prod_attributes[ 'small_image' ]  = $smallimage_path_url;
                $prod_attributes[ 'image' ]        = $image_path_url;
                $prod_attributes[ 'url_path' ]     = $this->getProductUrl( $prod_attributes[ 'sku' ] );
                $prod_attributes[ 'category_ids' ] = $this->getCategoryPath( $prod_attributes[ 'sku' ] );
                $stock_list                        = $this->getStockDataByProductId( $product );
                foreach ( $mapped_fields as $fields_title ) {
                    if ( $cron )
                        $fields_title = $fields_title->value;
                    $attribute = $_product->getResource()->getAttribute( $fields_title );
                    if ( $attribute ) {
                        $check = $_product->getAttributeText( $fields_title );
						            if ((!is_array($check)) && $check && trim( $check ) != '' ) {
							              unset( $prod_attributes[ $fields_title ] );
							              $prod_attributes[ $fields_title ] = $_product->getAttributeText( $fields_title );
						            } else if(is_array($check)){
							              $check = implode(",", $check);
							              unset( $prod_attributes[ $fields_title ] );
							                    $prod_attributes[ $fields_title ] = $check;
						            }
                    }
                }
                $all_product         = array_merge( $prod_attributes, $stock_list );
                $product_field_value = $this->getOfferValuesForAttrFields( $selected_fields, $all_product );
                // tag on '11' for the 'state' field
                $product_field_value .= ';"11"';
                $string              = $product_field_value . "\n";
                file_put_contents( $file, $string, FILE_APPEND );
                array_push($logMessages, $string);
            }
            if(count($logMessages)>0){
                Mage::helper('marketplace/logger')->log( 'getOffersToUpload', $logMessages );
            }
            $callApi = Mage::helper( 'marketplace/callapi' );
            $callApi->uploadOfferData();
        }
    }
    public function getProductAttrHeadings( $fields_selected, $cron = false )
    {
        $_products      = $this->getStyledotComProducts();
        $field_headings = array();
        foreach ( $fields_selected as $fields_select ) {
            $_products->addAttributeToSelect( $fields_select );
            $field_headings[ ] = $fields_select;
        }
        if ( !$cron ) {
            $field_heading_json = json_encode( $field_headings );
            $this->addConfigurationData( 'stored_fields', $field_heading_json, 'products' );
        }
        $seprator      = ";";
        $field_heading = implode( $seprator, $field_headings );
        return array(
             $_products,
            $field_heading
        );
    }

    /**
     * Retrives all 'simple' products which are set to display in Style.com
     */
    public function getStyledotComProducts() { 

        // Retrieve all attributes for all products to be shown in Style.com, but only retrive
        // what Magento refers to as 'simple' products. 
        return Mage::getModel( 'catalog/product' )->getCollection()
                                ->addAttributeToSelect( '*' )
                                ->addAttributeToFilter( 'display_style_com', 1 )
                                ->addAttributeToFilter('type_id', array('eq' => 'simple'));
    }

    function object_is_nonempty( $obj ){
        foreach( $obj as $x ) return true;
        return false;
    }
    /**
     * $cron = whether initiated from the scheduler (i.e. use defaults)
     * $upload = whether to actually upload. If not, just create product.csv
     */
    public function getProductsToUpload( $cron = false, $upload = true )
    {
        if ( $cron ) {
            $fields_selected = json_decode( $this->getConfigurationData( 'stored_fields' ) );
        } else {
            $fields_selected = Mage::app()->getRequest()->getPost();
            $fields_selected = $fields_selected[ 'field_key_values' ];
        }

        // if fields_selected is empty or just has the single _styledotcom_group_id field (from bug API-494), use defaults
        $has_fields_selected = $this->object_is_nonempty($fields_selected);
        if (! $has_fields_selected or ( count($fields_selected) == 1 and $fields_selected[0] == "_styledotcom_group_id")) {
                
            Mage::helper('marketplace/logger')->log( 'we are inside if loop! ' );    
            $default_attributes = $this->getDefaultProductAttributes( 'product' );
            $default_attributes_arr = array();
            foreach ($default_attributes as $key => $default_attribute) {
                array_push($default_attributes_arr, $default_attribute['field_code']);
            }
            Mage::helper('marketplace/logger')->log('getProductsToUpload, using default attributes', $default_attributes_arr);
            $fields_selected = $default_attributes_arr;
        }

        list( $_products, $field_heading ) = $this->getProductAttrHeadings( $fields_selected, $cron );

        // do we have products?
        $has_products = $this->object_is_nonempty($_products);
        if ($has_products) {
            $file = Mage::helper('marketplace/util')->getProductExportFilename();
            file_put_contents( $file, $field_heading . "\n" );
            $image = Mage::getModel( 'catalog/product_media_config' );
            $logMessages = array();
            foreach ( $_products as $_product ) {

                $_product->getId();
                $product                           = Mage::getModel( 'catalog/product' )->load( $_product->getId() );
                $prod_attributes                   = $_product->getData();
                $thumbnail_path_url                = $image->getMediaUrl( $product->getThumbnail() );
                $smallimage_path_url               = $image->getMediaUrl( $product->getSmallImage() );
                $image_path_url                    = $image->getMediaUrl( $product->getImage() );
                $prod_attributes[ 'thumbnail' ]    = $thumbnail_path_url;
                $prod_attributes[ 'small_image' ]  = $smallimage_path_url;
                $prod_attributes[ 'image' ]        = $image_path_url;
                $prod_attributes[ 'url_path' ]     = $this->getProductUrl( $prod_attributes[ 'sku' ] );
                $prod_attributes[ 'category_ids' ] = $this->getCategoryPath( $prod_attributes[ 'sku' ] );


                // Set Group Id field.  
                // First, get SKUs of any configurable parent products
                $parent_ids = Mage::getModel('catalog/product_type_configurable')
                                                     ->getParentIdsByChild($product->getId());
                $parent_collection = Mage::getResourceModel('catalog/product_collection')
                                            ->addFieldToFilter('entity_id', array('in'=>$parent_ids))
                                            ->addAttributeToFilter('type_id', array('eq' => 'configurable'))
                                            ->addAttributeToSelect('sku');
                $parent_skus = $parent_collection->getColumnValues('sku');

                if (count($parent_skus) > 0) {

                    // This product has a parent, so use its sku as _styledotcom_group_id.
                    // Only take the first parent sku as system cannot handle multiple parents
                    $prod_attributes[ '_styledotcom_group_id' ] = $parent_skus[0];

                    if (count($parent_skus) > 1) {
                        // The product has multiple parents.  We might have selected the 'wrong' one
                        // so let's at least log what we have done.
                        Mage::helper('marketplace/logger')->log('SKU '.$prod_attributes[ 'sku' ].
                                                                ' has '. count($parent_skus),
                                                                'Only use '.$parent_skus[0].' ignoring the others');
                    }
                } else {
                    // This is a simple product which does not have a 'parent'.  Use it's SKU to create its 
                    // own unique group.
                    $prod_attributes[ '_styledotcom_group_id' ] = $prod_attributes[ 'sku' ];
                }
                
                foreach ( $fields_selected as $fields_title ) {
                    $attribute = $_product->getResource()->getAttribute( $fields_title );
                    if ( $attribute ) {
                        $check = $_product->getAttributeText( $fields_title );
                        if ((!is_array($check)) && $check && trim( $check ) != '' ) {
                            unset( $prod_attributes[ $fields_title ] );
                            $prod_attributes[ $fields_title ] = $_product->getAttributeText( $fields_title );
                        } else if(is_array($check) && count($check) > 0){
                            $check = implode(",", $check);
                            unset( $prod_attributes[ $fields_title ] );
                            $prod_attributes[ $fields_title ] = $check;
                        }
                    }
                }
                $stock_list          = $this->getStockDataByProductId( $product );
                $array_all_attr      = array_merge( $stock_list, $prod_attributes );
                $product_field_value = $this->getProductValuesForAttrFields( $fields_selected, $array_all_attr, $cron );
                $string              = $product_field_value . "\n";
                file_put_contents( $file, $string, FILE_APPEND );

                array_push($logMessages, 'product=' . $string);
            }

            Mage::helper('marketplace/logger')->log( 'getProductsToUpload', $logMessages);

            if ($upload) {
                $callApi = Mage::helper( 'marketplace/callapi' );
                $callApi->uploadProductData();
            }
        } else {
            Mage::helper('marketplace/logger')->log( 'No products: ', $_products);
        }
        return $has_products;
    }
    public function getCategoryPath( $sku )
    {
        $product     = Mage::getModel( 'catalog/product' )->loadByAttribute( 'sku', $sku );
        $pathArray   = array();
        $collection1 = $product->getCategoryCollection()->setStoreId( Mage::app()->getStore()->getId() )->addAttributeToSelect( 'path' )->addAttributeToSelect( 'is_active' );
        foreach ( $collection1 as $cat1 ) {
            $pathIds    = explode( '/', $cat1->getPath() );
            $collection = Mage::getModel( 'catalog/category' )->getCollection()->setStoreId( Mage::app()->getStore()->getId() )->addAttributeToSelect( 'name' )->addAttributeToSelect( 'is_active' )->addFieldToFilter( 'entity_id', array(
                 'in' => $pathIds
            ) );
            $pahtByName = '';
            $i          = 1;
            foreach ( $collection as $cat ) {
                if ( $i != 1 ) {
                    $sep = ( $i != 2 ) ? '>' : '';
                    $pahtByName .= $sep . $cat->getName();
                }
                $i++;
            }
            $pathArray[ ] = $pahtByName;
        }
        $final_categories = array_pop( $pathArray );
        return $final_categories;
    }
    public function getProductUrl( $sku )
    {
        $url = Mage::getModel( 'catalog/product' )->loadByAttribute( 'sku', $sku )->getProductUrl();
        return $url;
    }
    public function getOrderOfferMessages()
    {
        $results = Mage::getModel( 'marketplace/messagetable' )->getCollection()->getData();
        return $results;
    }
    public function getSingleOrder( $orderid, $mgnto )
    {
        $mgnto      = isset( $mgnto ) ? $mgnto : 0;
        $connection = Mage::getSingleton( 'core/resource' )->getConnection( 'core_read' );
        $prefix     = Mage::getConfig()->getTablePrefix();
        $select     = $connection->select()->from( $prefix . 'cn_cmi_orders', array(
             '*'
        ) );
        if ( $mgnto ) {
            $select->where( 'm_order_id=?', $orderid );
        } else {
            $select->where( 'orderid=?', $orderid );
        }
        $rowArray = $connection->fetchRow( $select );
        return $rowArray;
    }
    public function setMirakleOrders( $activity, $pass_order_id, $order_line )
    {
        $fields_selected = Mage::app()->getRequest()->getPost();
        $order_line      = isset( $order_line ) ? $order_line : '';
        $url_api         = json_decode( $this->getConfigurationData( 'api_url' ) );
        if ( isset( $fields_selected[ "activity" ] ) && trim( $fields_selected[ "activity" ] ) != '' ) {
            if ( $fields_selected[ "activity" ] == 'reject' || $fields_selected[ "activity" ] == 'reject_manual' ) {
                $reject = true;
            }
            if ( $fields_selected[ "activity" ] == 'accept' || $fields_selected[ "activity" ] == 'reject' || $fields_selected[ "activity" ] == 'reject_manual' ) {
                $fields_selected[ "activity" ] = 'confirm';
            }
        }
        $action  = isset( $fields_selected[ "activity" ] ) ? $fields_selected[ "activity" ] : $activity;
        $callApi = Mage::helper( 'marketplace/callapi' );
        switch ( $action ) {
            case "confirm":
                $result = $callApi->executeConfirmOrderStatus( $url_api, $fields_selected, $pass_order_id, $reject );
                break;
            case "confirm_multiple":
                $result = $callApi->executeMultipleConfirmOrderStatus( $url_api, $pass_order_id, $order_line );
                break;
            case "tracking":
                $result = $callApi->executeTrackingOrderStatus( $url_api, $fields_selected, $pass_order_id );
                break;
            case "shipped":
                $result = $callApi->executeShippedOrderStatus( $url_api, $fields_selected, $pass_order_id );
                break;
            case "refund":
                $result = $callApi->executeRefundOrderStatus( $url_api, $fields_selected );
                break;
        }
        return $result;
    }
    public function updateCustomOrderTable( $order_id, $mgnto_order_id, $message )
    {
        $has_message = ( isset( $message ) && !empty( $message ) ) ? '1' : '0';
        $check       = $this->getSingleOrder( $order_id, '' );
        if ( $check[ 'orderid' ] && isset( $check[ 'orderid' ] ) ) {
            $id = $check[ 'id' ];
            if ( !$check[ 'm_order_id' ] ) {
                $data  = array(
                     'orderid' => $order_id,
                    'last_updated_date' => $check[ 'last_updated_date' ],
                    'total_price' => $check[ 'total_price' ],
                    'order_state' => $check[ 'order_state' ],
                    'all_fields' => $check[ 'all_fields' ],
                    'm_order_id' => $mgnto_order_id,
                    'message' => $message,
                    'message_read' => $has_message
                );
                $model = Mage::getModel( 'marketplace/ordertable' )->load( $id )->addData( $data );
                try {
                    $model->setId( $id )->save();
                }
                catch ( Exception $e ) {
                    echo $e->getMessage();
                }
            }
        }
        return $check[ 'orderid' ];
    }
    public function saveOrderMessages()
    {
        $orderId          = isset( $orderId ) ? $orderId : '';
        $callApi          = Mage::helper( 'marketplace/callapi' );
        $all_messages     = $callApi->getAllMessages();
        $message_data     = json_decode( $all_messages, true );
        $message_id_array = array();
        foreach ( $message_data[ 'messages' ] as $message ) {
            $all_fields = json_encode( $message );
            if ( $message[ 'order_id' ] ) {
                $message[ 'type_msg' ] = 'order';
                $order_offer_id        = $message[ 'order_id' ];
            } else {
                $message[ 'type_msg' ] = 'offer';
                $order_offer_id        = $message[ 'offer_id' ];
            }
            $message_id_array[ ] = $message[ 'id' ];
            $check               = Mage::getModel( 'marketplace/messagetable' )->getCollection()->getByMessageID( $message[ 'id' ] )->getData();
            $check_offer         = Mage::getModel( 'marketplace/offertable' )->getCollection()->getOfferByOfferId( $order_offer_id )->getData();
            $date_created        = date( 'Y-m-d H:i:s', strtotime( $message[ 'date_created' ] ) );
            if ( isset( $check[ 0 ][ 'id' ] ) && $check[ 0 ][ 'id' ] ) {
                if ( !$check_offer ) {
                    Mage::getModel( 'marketplace/messagetable' )->getResource()->deleteByCondition( $order_offer_id, 'offer' );
                }
            } else {
                if ( $message[ 'type_msg' ] == 'offer' ) {
                    if ( $check_offer ) {
                        $this->insertOrderMessages( $message, $order_offer_id, $all_fields, $date_created );
                    }
                } else {
                    $this->insertOrderMessages( $message, $order_offer_id, $all_fields, $date_created );
                }
            }
        }
        $existing_db_messages = Mage::getModel( 'marketplace/messagetable' )->getCollection()->getData();
        foreach ( $existing_db_messages as $existing_db_message ) {
            if ( !in_array( $existing_db_message[ 'message_id' ], $message_id_array ) ) {
                $this->deleteOfferMessages( $existing_db_message[ 'id' ] );
            }
        }
    }
    public function deleteOfferMessages( $id )
    {
        $id = isset( $id ) ? $id : '';
        if ( $id ) {
            $model = Mage::getModel( 'marketplace/messagetable' );
            try {
                $model->setId( $id )->delete();
            }
            catch ( Exception $e ) {
                echo $e->getMessage();
            }
        }
    }
    public function insertOrderMessages( $message, $order_offer_id, $order_all_fields, $date_created )
    {
        $data  = array(
             'message_id' => $message[ 'id' ],
            'order_offer_id' => $order_offer_id,
            'type_user' => $message[ 'from_type' ],
            'type_msg' => $message[ 'type_msg' ],
            'all_fields' => $order_all_fields,
            'date_created' => $date_created,
            'read_msg' => 0
        );
        $model = Mage::getModel( 'marketplace/messagetable' )->setData( $data );
        try {
            $insertId = $model->save()->getId();
        }
        catch ( Exception $e ) {
            echo $e->getMessage();
        }
    }
    public function getInduvidualOrderMessage( $order_id, $type )
    {
        $order_id = isset( $order_id ) ? $order_id : '';
        $type     = isset( $type ) ? $type : '';
        $callApi  = Mage::helper( 'marketplace/callapi' );
        $output   = $callApi->getInduvidualMessage( $order_id, $type );
        $checks   = Mage::getModel( 'marketplace/messagetable' )->getCollection()->getByOrderOfferID( $order_id )->getData();
        foreach ( $checks as $key => $check ) {
            $id    = $check[ 'id' ];
            $data  = array(
                 'read_msg' => 1
            );
            $model = Mage::getModel( 'marketplace/messagetable' )->load( $id )->addData( $data );
            try {
                $model->setId( $id )->save();
            }
            catch ( Exception $e ) {
                echo $e->getMessage();
            }
        }
        echo $output;
    }
}

<?php
class Cnc_MarketPlace_Block_Adminhtml_Configuration extends Mage_Adminhtml_Block_Template
{
    protected $helper;
	protected $configurationValidator;
	public function __construct()
	{
		parent::__construct();
		$this->setFormAction( Mage::getUrl( '*/*/index' ) );
		$this->setErrorLogAction( Mage::getUrl( '*/*/ErrorLog' ) );
		$this->setExportAction( Mage::getUrl( '*/*/export' ) );
		$this->configurationValidator = Mage::helper( 'marketplace/magentoValidator' );
        $this->helper = Mage::helper( 'marketplace' );
		$this->callApi = Mage::helper( 'marketplace/callapi' );
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
			Mage::getSingleton( 'core/session' )->addError( $e->getMessage() );
            Mage::helper('marketplace/logger')->log( 'addConfigurationData', array('metakey' => $meta_key, 'metavalue' => $meta_value, 'error' => $e->getMessage()));
			session_write_close();
		}
	}
	public function updateConfigurationData( $config_data )
	{
		if ( is_array( $config_data ) && !empty( $config_data ) ) {
			$ship_state_mapping  = isset( $config_data[ 'ship_state_mapping' ] ) ? $config_data[ 'ship_state_mapping' ] : '';
			$track_state_mapping = isset( $config_data[ 'track_state_mapping' ] ) ? $config_data[ 'track_state_mapping' ] : '';
			$order_status_label  = isset( $config_data[ 'order_status_label' ] ) ? $config_data[ 'order_status_label' ] : array();
			$magento_carrier     = isset( $config_data[ 'magento_carrier' ] ) ? $config_data[ 'magento_carrier' ] : array();
			$order_acceptance    = isset( $config_data[ 'accept' ] ) ? $config_data[ 'accept' ] : '';
			$state_mapping       = array(
				 'ship_state_mapping' => $ship_state_mapping,
				'track_state_mapping' => $track_state_mapping
			);
			$status_mapping      = array();
			foreach ( $order_status_label as $key => $order_status_labels ) {
				$status_mapping[ ] = array(
					 $order_status_labels => $config_data[ 'status_mapping' ][ $key ]
				);
			}
			$carrier_maps = array();
			foreach ( $magento_carrier as $k => $magento_carrier ) {
				$carrier_maps[ ] = array(
					 $magento_carrier => $config_data[ 'mirakle_carrier' ][ $k ]
				);
			}
			$this->addConfigurationData( 'api_url', json_encode( array_map( 'trim', $config_data[ 'credentials' ] ) ), 'credentials' );
			$this->addConfigurationData( 'order_acceptance', $order_acceptance );
			$this->addConfigurationData( 'state_mapping', json_encode( $state_mapping ) );
			$this->addConfigurationData( 'status_mapping', json_encode( $status_mapping ) );
			$this->addConfigurationData( 'carrier_mapping', json_encode( $carrier_maps ) );
			$checkConnection = $this->callApi->getCarriers( 1 );
			$this->addConfigurationData( 'config_valid_data', $checkConnection );
			if($checkConnection == 1 ) {
				$this->updateShopInfo();
			}
        }

		$config_data = Mage::getModel( 'marketplace/configurationtable' )->getCollection()->getData();
		Mage::helper('marketplace/logger')->log( 'updateConfigurationData config_data',  $config_data);
	}

	public function updateShopInfo() {
		$shop_info = $this->callApi->getShopInfo();
		if(isset($shop_info)) {
			$this->addConfigurationData( 'shop_info', $shop_info );
		}
	}

	public function fetchConfigurationData()
	{
		session_write_close();
		$config_data = Mage::getModel( 'marketplace/configurationtable' )->getCollection()->getData();
		$block       = $this->getLayout()->getBlock( 'configuration' );
		$block->setData( 'magento_configuration_errors',  $this->configurationValidator->fireValidations());
		foreach ( $config_data AS $value ) {
			$block->setData( $value[ 'meta_key' ], json_decode( $value[ 'meta_value' ] ) );
		}
		$magento_order_status = Mage::getModel( 'sales/order_status' )->getResourceCollection()->getData();
		$magento_order_states = $this->getMagentoOrderStates();
		$carriers             = $this->callApi->getCarriers();
        $magentoCarriers      = $this->helper->getMagentoCarriers();
		$block->setData( 'magento_carriers', $magentoCarriers );
		$block->setData( 'magento_order_status', $magento_order_status );
		$block->setData( 'magento_order_states', $magento_order_states );
		$block->setData( 'carriers', $carriers );

        // check if the export has been run before
        $filepath = Mage::helper('marketplace/util')->getProductExportFilename();
        $exported = is_file ( $filepath ) && is_readable ( $filepath );
        $block->setData('exported', $exported);

        // get a link to the product admin
        // thanks http://alanstorm.com/magento_admin_hello_world_revisited
        $product_admin_url = Mage::getModel('adminhtml/url')->getUrl('adminhtml/catalog_product/index');
        $block->setData('product_admin_url', $product_admin_url);

        // product count check
        // TODO just do a product count check - the method below still creates the CSV
        // a hack would be to only execute the below 2 lines if the CSV doesn't exist.
        $has_products = $this->helper->getProductsToUpload($cron = true, $upload = false);
        $block->setData('has_products', $has_products);

		$post = Mage::app()->getRequest()->getPost();
		if($post && isset($post['credentials'])) {
			$block->setData('config_post', true);
		}

	}
	public function validationMessages( $checkConnection )
	{
		session_write_close();
		if ( $checkConnection == 1 ) {
			Mage::getSingleton( 'core/session' )->addSuccess( 'Successfully validated and saved configuration data' );
            foreach(array('layout', 'block_html') as $val) {
                Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => $val));
            }
		} else if ( $checkConnection != 1 ) {
			Mage::getSingleton( 'core/session' )->addError( 'Error: Please check the URL and API key entered.' );
		}
		session_write_close();
	}
	public function getMagentoOrderStates()
	{
		$order_states = Mage::getModel( 'sales/order_status' )->getCollection()->joinStates()->getData();
		foreach ( $order_states as $states ) {
			$states_list[ ] = $states[ 'state' ];
		}
		$magento_order_states = array_filter( array_unique( $states_list ) );
		return $magento_order_states;
	}
	public function getOptionsStateMapping( $magento_order_states, $state_mapping_value )
	{
		$option = '';
		foreach ( $magento_order_states as $magento_order_state ) {
			$option .= '<option';
			if ( $state_mapping_value == $magento_order_state ) {
				$option .= ' selected';
			}
			$option .= ' value = "' . $magento_order_state . '">' . $magento_order_state . '</option>';
		}
		return $option;
	}
	public function downloadErrorLog()
	{
		$log_file  = Mage::getBaseDir( 'log' ) . DIRECTORY_SEPARATOR . 'cnc_marketplace.log';
		$full_file = file_get_contents( $log_file, FILE_USE_INCLUDE_PATH );
		header( 'Content-disposition: attachment; filename=cnc_marketplace.log' );
		header( 'Content-type: application/log' );
		var_dump( $full_file );
		exit;
	}
    public function createProductsCSV() {
        Mage::helper('marketplace/logger')->log('export', 'Downloaded CSV for Mirakl mapping');
        $has_products = $this->helper->getProductsToUpload($cron = true, $upload = false);
        return $has_products;
    }
}
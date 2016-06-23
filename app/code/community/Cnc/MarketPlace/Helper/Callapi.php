<?php
/**
 * Fetch, post and put data from and to mirakl API
 **/
class Cnc_MarketPlace_Helper_Callapi extends Mage_Core_Helper_Abstract
{
    function __construct()
    {
        $this->helper = Mage::helper( 'marketplace' );
    }
    /**
     * Fetch data using GET CURL from API
     *
     * @param string $url_between Containing the middle path of the URL
     * @param int $import_id
     * @param string $import_path
     * @param string $output_type Type CSV or other
     * @param string $qstring String passed after the API key (ex: &max=100)
     *
     * @return json
     */
    public function getCurlResponse( $url_between, $import_id = '', $import_path = '', $output_type = '', $qstring = '' )
    {
        $url_api = json_decode( $this->helper->getConfigurationData( 'api_url' ) );
        $url     = $url_api->url;
        $apiKey  = $url_api->api_key;
        $ch      = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url . $url_between . $import_id . $import_path . "?api_key=" . $apiKey . $qstring );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        $response = curl_exec( $ch );
        if ( curl_errno( $ch ) ) {
            $error = 'Curl error: ' . curl_error( $ch );
            Mage::getSingleton( 'core/session' )->addError( "Please check your internet connection and click on the back button " . $error );
        }
        curl_close( $ch );
        if ( $output_type == 'csv' ) {
            $file_name = str_replace( '/', '', $import_path );
            header( "Content-type: text/csv" );
            header( 'Content-Disposition: attachment; filename="' . $file_name . '_' . $import_id . '.csv"' );
            echo $response;
        } else {
            return $response;
        }
    }
    /**
     * Put data using PUT CURL to API
     *
     * @param mixed[] $url_api Array object which contains URL and API Key
     * @param string $url_between Containing the middle path of the URL
     * @param string $data_json Json which need to be PUT to the API
     * @param string $offers To check if the function is called for offer or order
     *
     * @return json
     */
    public function putCurlResponse( $url_api, $url_between, $data_json = '', $offers = '' )
    {
        $ch     = curl_init();
        $offers = ( isset( $offers ) && !empty( $offers ) ) ? $offers : 'orders';
        curl_setopt( $ch, CURLOPT_URL, $url_api->url . "/api/" . $offers . "/" . $url_between . "?api_key=" . $url_api->api_key );
        if ( !empty( $data_json ) ) {
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
                 'Content-Type: application/json'
            ) );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_json );
        }
        if ( $offers == 'orders' ) {
            curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
        }
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        $response = curl_exec( $ch );
        if ( curl_errno( $ch ) ) {
            $error = curl_error( $ch );
            Mage::getSingleton( 'core/session' )->addError( $error );
        } else {
            if ( !empty( $offers ) && $offers != 'orders' ) {
                Mage::getSingleton( 'core/session' )->addSuccess( "An Import has been succesfully created for the offer updated/deleted. Please check the import status in the previous imports section." );
            } else {
                Mage::getSingleton( 'core/session' )->addSuccess( "Order Updated succesfully" );
            }
        }
        curl_close( $ch );
        $logMessages = array(
        'response' => $response,
        'url_api' => $url_api,
        'url_between' => $url_between,
        'data_json' => $data_json,
        'offers' => $offers);
        Mage::helper('marketplace/logger')->log( 'putCurlResponse', $logMessages );
        return $response;
    }
    /**
     * POST data using PUT CURL to API
     *
     * @param string $url_between Containing the middle path of the URL
     * @param string $existing_file File name with full absolute path
     * @param string $filename File name to be passed in the API
     * @param string $type To determine if Json or CSV
     *
     * @return json
     */
    public function postCurlResponse( $url_between, $existing_file, $filename, $type = '' )
    {
        $url_api = json_decode( $this->helper->getConfigurationData( 'api_url' ) );
        $url     = $url_api->url;
        $apiKey  = $url_api->api_key;
        $request = curl_init( $url . $url_between . "?api_key=" . $apiKey );
        if ( $type == 'json' ) {
            curl_setopt( $request, CURLOPT_HTTPHEADER, array(
                 'Content-Type: application/json'
            ) );
        } else {
            if ( function_exists( 'curl_file_create' ) ) {
                $cfile    = curl_file_create( $existing_file, 'text/csv', $filename );
                $filename = array(
                     'file' => $cfile,
                    'import_mode' => 'NORMAL'
                );
            } else {
                $filename = array(
                     'file' => "@" . $existing_file . ';type=text/csv',
                    'import_mode' => 'NORMAL'
                );
            }
        }
        curl_setopt( $request, CURLOPT_POST, true );
        curl_setopt( $request, CURLOPT_POSTFIELDS, $filename );
        curl_setopt( $request, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $request, CURLOPT_SSL_VERIFYPEER, FALSE );
        $response = curl_exec( $request );
        if ( curl_errno( $request ) ) {
            $error = 'Curl error: ' . curl_error( $request );
            Mage::getSingleton( 'core/session' )->addError( "Please check your internet connection and click on the back button " . $error );
        }
        curl_close( $request );

		$logMessages = array(
			'response' => $response,
			'url_between' => $url_between,
			'existing_file' => $existing_file,
			'filename' => $filename,
			'type' => $type);
		Mage::helper('marketplace/logger')->log( 'postCurlResponse', $logMessages );
		return $response;
	}
	/**
	 * Get carrier data from API - Also used to check connectivity with internet and API (SH21)
	 *
	 * @param int $check Check if the function is used for getting carrier or to check connectivity
	 *
	 * @return json
	 */
	public function getCarriers( $check = '' )
	{
		$url_api = json_decode( $this->helper->getConfigurationData( 'api_url' ) );
		$url     = $url_api->url;
		$apiKey  = $url_api->api_key;
		if ( $url && $apiKey ) {
			$request = curl_init( $url . "/api/shipping/carriers?api_key=" . $apiKey );
			curl_setopt( $request, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $request, CURLOPT_SSL_VERIFYPEER, FALSE );
			curl_setopt( $request, CURLOPT_SSL_VERIFYHOST, FALSE );
			$res = curl_exec( $request );
			if ( $check ) {
				$result = json_decode( $res );
				$value  = isset( $result->carriers[ 0 ]->code ) ? $result->carriers[ 0 ]->code : '';
				if ( curl_errno( $request ) || $value == '' ) {
					return 0;
				} else {
					return 1;
				}
			} else {
				if ( curl_errno( $request ) ) {
					$error = 'Curl error: ' . curl_error( $request );
					Mage::getSingleton( 'core/session' )->addError( "Please check your internet connection and the credentials entered in your config. " . $error );
				}
				return json_decode( $res );
			}
			curl_close( $request );
		}
	}

	/**
	 * Gets Shop Info (id + Name) from Mirakl-
	 *
	 * @return string
	 */
	public function getShopInfo()
	{
		$url_api = json_decode( $this->helper->getConfigurationData( 'api_url' ) );
		$url     = $url_api->url;
		$apiKey  = $url_api->api_key;
		if ( $url && $apiKey ) {
			$request = curl_init( $url . "/api/account?api_key=" . $apiKey );
			curl_setopt( $request, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $request, CURLOPT_SSL_VERIFYPEER, FALSE );
			curl_setopt( $request, CURLOPT_SSL_VERIFYHOST, FALSE );
			$res = curl_exec( $request );
			if ( curl_errno( $request ) ) {
				$error = 'Curl error: ' . curl_error( $request );
				Mage::getSingleton( 'core/session' )->addError( "Please check your internet connection and the credentials entered in your config. " . $error );
			} else {
				$result = json_decode( $res );
				$shopId = $result->shop_id;
				$shopName  = $result->shop_name;
				return "$shopId-$shopName";
			}
			curl_close( $request );
		}
	}

	/**
	 * Get URL Status of import
	 *
	 * @param string $url_between Containing the middle path of the URL
	 * @param mixed $import_data Array containing entire import data for one import
	 * @param int $import_id Current import ID
	 *
	 * @return string Status of the import
	 */
	public function getImportStatusFile( $url_between, $import_data, $import_id )
	{
		//Transformed file
		$report = array();
		if ( isset( $import_data->has_transformed_file ) && $import_data->has_transformed_file ) {
			$import_path                  = '/transformed_file';
			$report[ 'transformed_file' ] = $this->getCurlResponse( $url_between, $import_id, $import_path );
		}
		//Error file
		if ( isset( $import_data->has_error_report ) && $import_data->has_error_report ) {
			$import_path            = '/error_report';
			$report[ 'error_file' ] = $this->getCurlResponse( $url_between, $import_id, $import_path );
		}
		//Transformed Error file
		if ( isset( $import_data->has_transformation_error_report ) && $import_data->has_transformation_error_report ) {
			$import_path                             = '/transformation_error_report';
			$report[ 'transformation_error_report' ] = $this->getCurlResponse( $url_between, $import_id, $import_path );
		}
		return $report;
	}
	/**
	 * If product, API gives status in "import_status". If offer, API gives status in "status".
	 * Function used to give out status of import
	 *
	 * @param string $type Product (or) Offer
	 * @param mixed $import_data Array containing entire import data
	 *
	 * @return string Status of the current import
	 */
	public function getImportStatusByType( $type, $import_data )
	{
		if ( $type == 'product' ) {
			$status = isset( $import_data->import_status ) ? $import_data->import_status : '';
		} else {
			$status = isset( $import_data->status ) ? $import_data->status : '';
		}
		return $status;
	}
	/**
	 * Get URL path between the URL and API Key
	 *
	 * @param string $type Product (or) Offer
	 *
	 * @return string
	 */
	public function getImportPathByType( $type = '' )
	{
		if ( $type == 'product' ) {
			$path = "/api/products/imports/";
		} else {
			$path = "/api/offers/imports/";
		}
		return $path;
	}
	/**
	 * Upload products to mirakl API (P41)
	 */
	public function uploadProductData()
	{
		$file        = Mage::helper('marketplace/util')->getProductExportFilename();
		$url_between = '/api/products/imports/';
		$res         = $this->postCurlResponse( $url_between, $file, 'product.csv' );
		$this->helper->setImportData( $res, 'product' );
	}
	/**
	 * Upload offers to mirakl API (OF01)
	 */
	public function uploadOfferData()
	{
		$file        = Mage::helper('marketplace/util')->getOfferExportFilename();
		$url_between = '/api/offers/imports/';
		$res         = $this->postCurlResponse( $url_between, $file, 'offer.csv' );
		//print_r($res);exit;
		$this->helper->setImportData( $res, 'offer' );
	}
	/**
	 * Get All Messages from mirakl API (M01)
	 */
	public function getAllMessages()
	{
		$qstring = '&max=100&visible=ALL';
		return $this->getCurlResponse( '/api/messages/', '', '', '', $qstring );
	}
	/**
	 * Get All offers from mirakl API (OF21)
	 */
	public function getAllOffers()
	{
		return $this->getCurlResponse( '/api/offers', '', '', '', '&max=100' );
	}
	/**
	 * Used to create Offer Json
	 *
	 * @param $offer_ids
	 *
	 * return mixed Full offer json
	 */
	public function deleteBulkOffersApi( $offer_data )
	{
		$offer_ids       = json_decode( $offer_data[ 'offer_ids' ], true );
		$multiple_offers = $this->createOffersJson( $offer_ids );
		$url_api         = json_decode( $this->helper->getConfigurationData( 'api_url' ) );
		return $response = $this->putCurlResponse( $url_api, '', json_encode( $multiple_offers ), 'offers' );
	}
	public function createOffersJson( $offer_ids )
	{
		$multiple_offers = array();
		$create_json     = (object) array();
		$key             = 0;
		foreach ( $offer_ids as $offer_id ) {
			$create_json->offers[ $key ] = $this->createSingleOfferJson( $offer_id, $key );
			$multiple_offers             = $create_json;
			$key++;
		}
        Mage::helper('marketplace/logger')->log( 'createOffersJson - multiple_offers: ', $multiple_offers );
        return $multiple_offers;
    }
    /**
     * Used to create Offer Json - Sub (Used to create one Offer Json)
     *
     * @param $offer_id
     *
     * return mixed One Offer Json
     */
    public function createSingleOfferJson( $offer_id = '' )
    {
        if ( !empty( $offer_id ) ) {
            $collection = Mage::getModel( 'marketplace/offertable' )->getCollection()->getOfferById( $offer_id )->getData();
            $offer_data = json_decode( $collection[ 0 ][ 'all_fields' ], true );
        } else {
            $offer_data = Mage::app()->getRequest()->getPost();
        }
        $create_json                       = (object) array();
        $create_json->available_ended      = !empty( $offer_data[ 'available_ended' ] ) ? $offer_data[ 'available_ended' ] : null;
        $create_json->available_started    = !empty( $offer_data[ 'available_started' ] ) ? $offer_data[ 'available_started' ] : null;
        $create_json->description          = !empty( $offer_data[ 'description' ] ) ? trim( $offer_data[ 'description' ] ) : null;
        $create_json->discount->end_date   = !empty( $offer_data[ 'end_date' ] ) ? $offer_data[ 'end_date' ] : null;
        $create_json->discount->start_date = !empty( $offer_data[ 'start_date' ] ) ? $offer_data[ 'start_date' ] : null;
        $create_json->discount->price      = !empty( $offer_data[ 'origin_price' ] ) ? $offer_data[ 'origin_price' ] : null;
        $create_json->internal_description = !empty( $offer_data[ 'internal_description' ] ) ? trim( $offer_data[ 'internal_description' ] ) : null;
        $create_json->logistic_class       = !empty( $offer_data[ 'logistic_class' ][ 'code' ] ) ? $offer_data[ 'logistic_class' ][ 'code' ] : null;
        $create_json->min_quantity_alert   = !empty( $offer_data[ 'min_quantity_alert' ] ) ? $offer_data[ 'min_quantity_alert' ] : null;
        if ( isset( $offer_data[ 'offer_additional_fields' ] ) && count( $offer_data[ 'offer_additional_fields' ] ) > 0 ) // Delete - getting data from DB
            {
            foreach ( $offer_data[ 'offer_additional_fields' ] as $ky => $offer_additional_fields ) {
                $create_json->offer_additional_fields[ $ky ]->code  = !empty( $offer_additional_fields[ 'code' ] ) ? $offer_additional_fields[ 'code' ] : null;
                $create_json->offer_additional_fields[ $ky ]->value = !empty( $offer_additional_fields[ 'value' ] ) ? $offer_additional_fields[ 'value' ] : null;
            }
        } else {
            foreach ( $offer_data[ 'offer_additional_field_code' ] as $kk => $offer_additional_fields ) // Update - Getting data from hidden fields
                {
                $create_json->offer_additional_fields[ $kk ]->code  = !empty( $offer_additional_fields ) ? $offer_additional_fields : null;
                $create_json->offer_additional_fields[ $kk ]->value = !empty( $offer_data[ 'offer_additional_field_value' ][ $kk ] ) ? $offer_data[ 'offer_additional_field_code' ][ $kk ] : null;
            }
        }
        $create_json->price                 = !empty( $offer_data[ 'price_offer_cmi' ] ) ? $offer_data[ 'price_offer_cmi' ] : null;
        $create_json->price_additional_info = !empty( $offer_data[ 'price_additional_info' ] ) ? $offer_data[ 'price_additional_info' ] : null;
        $create_json->quantity              = !empty( $offer_data[ 'quantity' ] ) ? $offer_data[ 'quantity' ] : null;
        $create_json->product_id            = !empty( $offer_data[ 'product_id' ] ) ? $offer_data[ 'product_id' ] : null;
        $create_json->product_id_type       = !empty( $offer_data[ 'product_id_type' ] ) ? $offer_data[ 'product_id_type' ] : null;
        $create_json->shop_sku              = !empty( $offer_data[ 'shop_sku' ] ) ? $offer_data[ 'shop_sku' ] : null;
        $create_json->state_code            = !empty( $offer_data[ 'state_code' ] ) ? $offer_data[ 'state_code' ] : null;
        $create_json->update_delete         = !empty( $offer_data[ 'update_delete' ] ) ? $offer_data[ 'update_delete' ] : 'delete';
        Mage::helper('marketplace/logger')->log( 'createSingleOfferJson - create_json: ', $create_json );
        return $create_json;
    }
    /**
     * Used to create Offer Json - Sub (Used to create one Offer Json)
     *
     * @param $offer_id
     *
     * return mixed One Offer Json
     */
    public function updateOffer()
    {
        $create_json              = (object) array();
        $create_json->offers[ 0 ] = $this->createSingleOfferJson();
        $url_api                  = json_decode( $this->helper->getConfigurationData( 'api_url' ) );
        return $this->putCurlResponse( $url_api, '', json_encode( $create_json ), 'offers' );
    }
    /**
     * Get message for a single message ID
     *
     * @param $order_id order (or) offer id
     *
     * return mixed
     */
    public function getInduvidualMessage( $order_id, $type )
    {
        $order_id    = isset( $order_id ) ? $order_id : '';
        $type        = isset( $type ) ? $type : '';
        $url_between = "/api/" . $type . "/" . $order_id . "/messages/";
        return $this->getCurlResponse( $url_between, '', '', '', '&visible=ALL&sort=dateCreated&order=asc&max=100' );
    }
    /**
     * Send offer message using message ID
     *
     * @param $msg_ids Message ID of the message thread
     * @param $body Content of the message
     * @param $offerID
     *
     * return mixed
     */
    public function answerOfferMessages( $msg_ids, $body, $offerID )
    {
        $messageID             = $msg_ids[ 0 ][ 'message_id' ];
        $dataJson              = array();
        $dataJson[ 'body' ]    = $body;
        $dataJson[ 'visible' ] = true;
        $url_between           = "/api/offers/" . $offerID . "/messages/" . $messageID;
        return $response = $this->postCurlResponse( $url_between, '', json_encode( $dataJson, true ), 'json' );
    }
    /**
     * Get the reasons stored in mirakle which can be used in refund pop up
     *
     * return mixed
     */
    public function getRefundReasons()
    {
        $response = $this->getCurlResponse( '/api/reasons/refund' );
        return json_decode( $response );
    }
    /**
     * Get All orders OR11
     * Since mirakl allows only 100 orders at a time to be fetched, we loop 100 orders at a time.
     *
     * @param $offset offset is the limitstart(To start from which number)
     * @param $max Max is set to 100 as mirakl allows max only upto 100
     * @param $recursive Check to see if we should fetch orders again
     * @param $date The max date from which the orders should be fetched
     *
     * return mixed
     */
    public function getMirakleDataCurlResponse( $offset, $max, $recursive, $date )
    {
        $max            = isset( $max ) ? $max : 100;
        $recursive      = isset( $recursive ) ? $recursive : '';
        $offset         = isset( $offset ) ? $offset : '';
        $offset_qstring = '';
        $date_qstring   = '';
        $url_api        = json_decode( $this->helper->getConfigurationData( 'api_url' ) );
        $url            = $url_api->url;
        $apiKey         = $url_api->api_key;
        $ch             = curl_init();
        if ( trim( $date ) != 'TZ' && $recursive == '' ) {
            $date_qstring = '&start_update_date=' . $date;
        }
        if ( $offset ) {
            $offset_qstring = '&offset=' . $offset;
        }
        curl_setopt( $ch, CURLOPT_URL, $url . "/api/orders?api_key=" . $apiKey . "&max=" . $max . $date_qstring . $offset_qstring );
        curl_setopt( $ch, CURLOPT_HTTPGET, TRUE );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        $output = curl_exec( $ch );
        curl_close( $ch );
        return $output;
    }
    /**
     * Send order messages
     *
     * @param $orderId order ID of the order
     * @param $message_details Array containing the message content
     *
     * return mixed
     */
    public function answerOrderMessages( $orderId, $message_details )
    {
        $datajson                         = array();
        $datajson[ 'body' ]               = $message_details[ 'message_answer' ];
        $datajson[ 'customer_email' ]     = $orderId;
        $datajson[ 'customer_firstname' ] = 'Operator';
        $datajson[ 'customer_id' ]        = $orderId;
        $datajson[ 'customer_lastname' ]  = '';
        $datajson[ 'subject' ]            = $message_details[ 'subject' ];
        $datajson[ 'to_customer' ]        = false;
        $datajson[ 'to_operator' ]        = true;
        $datajson[ 'to_shop' ]            = false;
        $url_between                      = "/api/orders/" . $orderId . "/messages";
        return $this->postCurlResponse( $url_between, '', json_encode( $datajson, true ), 'json' );
    }
    /**
     * Accept order
     *
     * @param $url_api
     * @param $fields_selected Array containing order details
     * @param $pass_order_id Check to see if function is called for automation
     * @param $reject Reject or Accept the order
     *
     * return mixed
     */
    public function executeConfirmOrderStatus( $url_api, $fields_selected, $pass_order_id, $reject )
    {
        $orderID_checker = isset( $fields_selected[ "orderId" ] ) ? $fields_selected[ "orderId" ] : '';
        $orderID         = json_decode( $orderID_checker );
        if ( isset( $pass_order_id ) && trim( $pass_order_id ) != '' ) {
            $orderID_checker = $pass_order_id;
        }
        if ( $orderID ) {
            $response_arr = array();
            foreach ( $orderID as $value ) {
                $datajson                                     = array();
                $datajson[ "order_lines" ][ 0 ][ "accepted" ] = true;
                $datajson[ "order_lines" ][ 0 ][ "id" ]       = $value . "-1";
                $data_json                                    = json_encode( $datajson );
                $response                                     = $this->putCurlResponse( $url_api, $value . "/accept", $data_json, '0' );
                $response_arr[ ]                              = $response;
            }
            echo $response_arr[ 0 ];
        } else {
            $datajson = array();
            $reject   = isset( $reject ) ? $reject : '';
            if ( $reject == true ) {
                $datajson[ "order_lines" ][ 0 ][ "accepted" ] = false;
            } else {
                $datajson[ "order_lines" ][ 0 ][ "accepted" ] = true;
            }
            $datajson[ "order_lines" ][ 0 ][ "id" ] = $orderID_checker . "-1";
            $data_json                              = json_encode( $datajson );
            echo $response = $this->putCurlResponse( $url_api, $orderID_checker . "/accept", $data_json, '0' );
        }
    }
    /**
     * Accept order - multiple orders in loop
     *
     * @param $url_api
     * @param $pass_order_id Check to see if function is called for automation
     * @param $order_lines Order deatails array
     *
     * return mixed
     */
    public function executeMultipleConfirmOrderStatus( $url_api, $pass_order_id, $order_lines )
    {
        $datajson = array();
        foreach ( $order_lines as $key => $order_line_json ) {
            $order_line = json_decode( $order_line_json );
            if ( isset( $order_line->reject ) ) {
                $datajson[ "order_lines" ][ $key ][ "accepted" ] = false;
                $datajson[ "order_lines" ][ $key ][ "id" ]       = $order_line->reject;
            } else if ( isset( $order_line->accept ) ) {
                $datajson[ "order_lines" ][ $key ][ "accepted" ] = true;
                $datajson[ "order_lines" ][ $key ][ "id" ]       = $order_line->accept;
            }
        }
        $data_json = json_encode( $datajson );
        $response  = $this->putCurlResponse( $url_api, $pass_order_id . "/accept", $data_json, '0' );
        if ( $response == '' ) {
            //Mage::getSingleton('core/session')->addSuccess("Order Updated succesfully"); // Added in putCurlResponse
        } else {
            Mage::getSingleton( 'core/session' )->addError( 'Error with submission' );
        }
    }
    /**
     * Send Tracking information
     *
     * @param $url_api
     * @param $fields_selected Order deatails array
     * @param $pass_order_id Check to see if function is called for automation
     *
     * return mixed
     */
    public function executeTrackingOrderStatus( $url_api, $fields_selected, $pass_order_id )
    {
        if ( isset( $pass_order_id[ 'orderId' ] ) && $pass_order_id[ 'orderId' ] ) {
            $fields_selected = $pass_order_id;
        }
        $orderID                       = $fields_selected[ "orderId" ];
        $datajson                      = array();
        $datajson[ "carrier_code" ]    = $fields_selected[ "carriercode" ];
        $datajson[ "carrier_name" ]    = $fields_selected[ "courier" ];
        $datajson[ "tracking_number" ] = $fields_selected[ "trackingNumber" ];
        $data_json                     = json_encode( $datajson );
        $response                      = $this->putCurlResponse( $url_api, $orderID . "/tracking", $data_json );
        if ( isset( $pass_order_id[ 'orderId' ] ) && $pass_order_id[ 'orderId' ] ) {
            return $response;
        } else {
            echo $response;
        }
    }
    /**
     * Send Shipping confirmation
     *
     * @param $url_api
     * @param $fields_selected Order deatails array
     * @param $pass_order_id Check to see if function is called for automation
     *
     * return mixed
     */
    public function executeShippedOrderStatus( $url_api, $fields_selected, $pass_order_id )
    {
        $orderID_checker = isset( $fields_selected[ "orderId" ] ) ? $fields_selected[ "orderId" ] : '';
        $orderID         = json_decode( $orderID_checker );
        if ( $pass_order_id ) {
            $orderID_checker = $pass_order_id;
        }
        if ( $orderID ) {
            $response_arr = array();
            foreach ( $orderID as $value ) {
                $response = $this->putCurlResponse( $url_api, $value . "/ship", '' );
                if ( $response )
                    $response_arr[ ] = json_decode( $response );
            }
            if ( $response_arr[ 0 ]->message )
                echo $response_arr[ 0 ]->message;
        } else {
            $response = $this->putCurlResponse( $url_api, $orderID_checker . "/ship", '' );
            if ( $pass_order_id != '' ) {
                return $response;
            } else {
                echo $response;
            }
        }
    }
    /**
     * Send Refund information
     *
     * @param $url_api
     * @param $fields_selected Order deatails array
     *
     * return mixed
     */
    public function executeRefundOrderStatus( $url_api, $fields_selected )
    {
        $datajson = array();
        foreach ( $fields_selected[ 'order_line_refunds' ] as $key => $order_line ) {
            $datajson[ "refunds" ][ $key ][ "amount" ]          = $fields_selected[ "refundAmount" ][ $key ];
            $datajson[ "refunds" ][ $key ][ "order_line_id" ]   = $order_line;
            $datajson[ "refunds" ][ $key ][ "quantity" ]        = $fields_selected[ "quantity" ][ $key ];
            $datajson[ "refunds" ][ $key ][ "reason_code" ]     = $fields_selected[ "reason" ][ $key ];
            $datajson[ "refunds" ][ $key ][ "shipping_amount" ] = $fields_selected[ "shipAmount" ][ $key ];
        }
        $data_json = json_encode( $datajson );
        echo $response = $this->putCurlResponse( $url_api, "refund", $data_json );
    }
}
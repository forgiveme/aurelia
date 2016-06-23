<?php
class Cnc_MarketPlace_Block_Adminhtml_Scheduler extends Mage_Adminhtml_Block_Template
{
	protected $helper;
	public function __construct()
	{
		parent::__construct();
		$this->setFormAction( Mage::getUrl( '*/*/schedulersave' ) );
		$this->setDeleteCronAction( Mage::getUrl( '*/*/deleteCron' ) );
		$this->setScheduleRefreshAction( Mage::getUrl( '*/*/ajaxSchduleRefresh' ) );
		$this->helper = Mage::helper( 'marketplace' );
	}
	public function getSchedulerDetails()
	{
		$cron_data   = $this->getAllCron();
		$prod_crons  = $this->getScheduledCronJobs( 'marketplace_prod' );
		$offer_crons = $this->getScheduledCronJobs( 'marketplace_offer' );
		$block       = $this->getLayout()->getBlock( 'scheduler' );
		$block->setData( 'cron_data', $cron_data );
		$block->setData( 'prod_crons', $prod_crons );
		$block->setData( 'offer_crons', $offer_crons );
	}
	public function getAllCron()
	{
		$model      = Mage::getModel( 'marketplace/crontable' );
		$collection = $model->getCollection();
		$crons      = array();
		foreach ( $collection as $item ) {
			$crons[ ] = $item->getData();
		}
		return $crons;
	}
	public function getScheduledCronJobs( $jobCode )
	{
		$job_code       = isset( $jobCode ) ? $jobCode : '';
		$resource       = Mage::getSingleton( 'core/resource' );
		$readConnection = $resource->getConnection( 'core_read' );
		$query          = 'SELECT * FROM ' . $resource->getTableName( 'cron/schedule' ) . ' where job_code = "' . $job_code . '" order by schedule_id desc limit 0,20';
		$results        = $readConnection->fetchAll( $query );
        Mage::helper('marketplace/logger')->log( 'getScheduledCronJobs - results: ', $results );
		return $results;
	}
	public function insertCronData()
	{
		$value             = Mage::app()->getRequest()->getPost();
		$type              = isset( $value[ 'type' ] ) ? $value[ 'type' ] : '';
		$details           = $this->getSchedulerDetailsFromPost( $value );
		$details[ 'name' ] = $value[ 'name' ];
		$details[ 'type' ] = $type;
		if ( ( $type == 'daily' && $details[ 'date_specified' ] == '' ) || ( $type == 'weekly' && $details[ 'date_specified' ] == '' ) || ( $type == 'custom' && ( $details[ 'recursive_value' ] == '' || $details[ 'date_specified' ] == '' ) ) ) {
			Mage::getSingleton( 'core/session' )->addError( "Please check the fields you have entered. Date field (or) The Custom option cannot be left empty" );
		} else if ( strtotime( $details[ 'date_specified' ] ) < strtotime( 'now' ) ) {
			Mage::getSingleton( 'core/session' )->addError( "Date and time should be greater than current date and time" );
		} else {
			$details[ 'timescheduled' ] = strftime( "%Y-%m-%d %H:%M:%S", mktime( date( "H", strtotime( $details[ 'date_specified' ] ) ), date( "i", strtotime( $details[ 'date_specified' ] ) ), date( "s", strtotime( $details[ 'date_specified' ] ) ), date( "m", strtotime( $details[ 'date_specified' ] ) ), date( "d", strtotime( $details[ 'date_specified' ] ) ), date( "Y", strtotime( $details[ 'date_specified' ] ) ) ) );
			$custom_cron                = $this->getExisitingCustomCronByName( $value[ 'name' ] );
			if ( isset( $custom_cron[ 'checker' ] ) && $custom_cron[ 'checker' ] ) {
				$this->deleteExistingMagentoCrons( $custom_cron[ 'jobCode' ] );
				$this->updateCustomCron( $details, $custom_cron[ 'id' ] );
			} else {
				$this->insertCustomCron( $details );
			}
            Mage::helper('marketplace/logger')->log( 'insertCronData - details: ', $details );
		}
	}
	public function getSchedulerDetailsFromPost( $value )
	{
		$weekdays        = 0;
		$recursive_value = '1440';
		$days            = isset( $value[ 'daysofweek' ] ) ? implode( ',', $value[ 'daysofweek' ] ) : '';
		$date_specified  = $value[ 'date_sp_week' ];
		switch ( $value[ 'type' ] ) {
			case 'daily':
				if ( isset( $value[ 'every_day' ] ) && $value[ 'every_day' ] == 'weekday' ) {
					$weekdays = 1;
					$days     = 'Monday,Tuesday,Wednesday,Thursday,Friday';
				} else {
					$days = 'Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday';
				}
				$date_specified = $value[ 'date_sp' ];
				break;
			case 'custom':
				$days            = 'Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday';
				$recursive_value = $value[ 'minutes' ];
				$date_specified  = $value[ 'date_sp_custom' ];
				break;
		}
		return array(
			 'weekdays' => $weekdays,
			'days' => $days,
			'recursive_value' => $recursive_value,
            'date_specified' => $date_specified
		);
	}
	public function getExisitingCustomCronByName( $name )
	{
		$custom_cron = array();
		if ( $name == 'product_upload' ) {
			$custom_cron[ 'jobCode' ] = 'marketplace_prod';
			$select                   = 'product_upload';
		} else {
			$custom_cron[ 'jobCode' ] = 'marketplace_offer';
			$select                   = 'offer_upload';
		}
		$collections = Mage::getModel( 'marketplace/crontable' )->getCollection()->addCronSelect( $select );
		$result      = $collections->getData();
		if ( count( $result ) > 0 ) {
			$custom_cron[ 'checker' ] = $result[ 0 ][ 'name' ];
			$custom_cron[ 'id' ]      = $result[ 0 ][ 'id' ];
		}
		return $custom_cron;
	}
	public function deleteExistingMagentoCrons( $jobCode )
	{
		if ( $jobCode ) {
			try {
				$coreResource  = Mage::getSingleton( 'core/resource' );
				$write         = $coreResource->getConnection( 'core_write' );
				$cron_schedule = $coreResource->getTableName( 'cron_schedule' );
				$write->query( "DELETE FROM  $cron_schedule WHERE  job_code =  '" . $jobCode . "'" );
			}
			catch ( Exception $e ) {
				Mage::getSingleton( 'core/session' )->addError( $e->getMessage() );
                Mage::helper('marketplace/logger')->log( 'deleteExistingMagentoCrons', array('jobCode' => $jobCode, 'Error' => $e->getMessage()));
				session_write_close();
			}
		}
	}
	public function updateCustomCron( $details, $id )
	{
		$data  = array(
			 'name' => $details[ 'name' ],
			'type' => $details[ 'type' ],
			'date' => $details[ 'date_specified' ],
			'executed' => 'false',
			'next_time' => $details[ 'timescheduled' ],
			'recursive_value' => $details[ 'recursive_value' ],
			'days' => $details[ 'days' ],
            'weekdays' => $details[ 'weekdays' ]
		);
		$model = Mage::getModel( 'marketplace/crontable' )->load( $id )->addData( $data );
		try {
			$model->setId( $id )->save();
		}
		catch ( Exception $e ) {
			Mage::getSingleton( 'core/session' )->addError( $e->getMessage() );
            Mage::helper('marketplace/logger')->log( 'updateCustomCron - Error: ', $e->getMessage() );
			session_write_close();
		}
	}
	public function insertCustomCron( $details )
	{
		$data  = array(
			 'name' => $details[ 'name' ],
			'type' => $details[ 'type' ],
			'date' => $details[ 'date_specified' ],
			'executed' => 'false',
			'next_time' => $details[ 'timescheduled' ],
			'recursive_value' => $details[ 'recursive_value' ],
			'days' => $details[ 'days' ],
            'weekdays' => $details[ 'weekdays' ]
		);
		$model = Mage::getModel( 'marketplace/crontable' )->setData( $data );
		try {
			$insertId = $model->save()->getId();
		}
		catch ( Exception $e ) {
			Mage::getSingleton( 'core/session' )->addError( $e->getMessage() );
            Mage::helper('marketplace/logger')->log( 'insertCustomCron - Error: ', $e->getMessage() );
			session_write_close();
		}
	}
	public function deleteCronJobs()
	{
		$data = Mage::app()->getRequest()->getPost();
		$name = isset( $data[ 'cron_type' ] ) ? $data[ 'cron_type' ] : '';
		if ( $name != '' ) {
			$custom_cron = $this->getExisitingCustomCronByName( $name );
			$model       = Mage::getModel( 'marketplace/crontable' );
			try {
				$model->setId( $custom_cron[ 'id' ] )->delete();
			}
			catch ( Exception $e ) {
				Mage::getSingleton( 'core/session' )->addError( $e->getMessage() );
                Mage::helper('marketplace/logger')->log( 'deleteCronJobs - Error: ', $e->getMessage() );
				session_write_close();
			}
			$this->deleteExistingMagentoCrons( $custom_cron[ 'jobCode' ] );
		}
	}
}
<?php
class Cnc_MarketPlace_Model_Observer
{
	public function setStatus()
	{
		$model      = Mage::getModel( 'marketplace/crontable' );
		$collection = $model->getCollection();
		foreach ( $collection as $item ) {
			$values       = $item->getData();
			$recursive    = isset( $values[ 'recursive_value' ] ) ? $values[ 'recursive_value' ] : '';
			$timecreated  = strftime( "%Y-%m-%d %H:%M:%S", mktime( date( "H" ), date( "i" ), date( "s" ), date( "m" ), date( "d" ), date( "Y" ) ) );
			$next_time    = $values[ 'next_time' ];
			$new_nexttime = date( "Y-m-d H:i:s", strtotime( '+' . $recursive . ' minutes', strtotime( $next_time ) ) );
			$days_arr     = explode( ',', $values[ 'days' ] );
			$dw           = date( "l", strtotime( $values[ 'next_time' ] ) );
			if ( $values[ 'name' ] == 'product_upload' ) {
				$jobCode     = 'marketplace_prod';
				$collections = Mage::getModel( 'marketplace/crontable' )->getCollection()->addCronSelect( 'product_upload' );
				$result      = $collections->getData();
				if ( $result[ 0 ] && isset( $result[ 0 ] ) ) {
					$id = $result[ 0 ][ 'id' ];
				}
				$data  = array(
					 'name' => $values[ 'name' ],
					'type' => $values[ 'type' ],
					'date' => $values[ 'date_sp' ],
					'executed' => 'True',
					'next_time' => $new_nexttime,
					'recursive_value' => $values[ 'minutes' ],
					'days' => $values[ 'days' ],
					'weekdays' => $values[ 'weekdays' ] 
				);
				$model = Mage::getModel( 'marketplace/crontable' )->load( $id )->addData( $data );
				try {
					$model->setId( $id )->save();
				}
				catch ( Exception $e ) {
					echo $e->getMessage();
				}
			} else {
				$jobCode     = 'marketplace_offer';
				$collections = Mage::getModel( 'marketplace/crontable' )->getCollection()->addCronSelect( 'offer_upload' );
				$result      = $collections->getData();
				if ( $result[ 0 ] && isset( $result[ 0 ] ) ) {
					$id = $result[ 0 ][ 'id' ];
				}
				$data  = array(
					 'name' => $values[ 'name' ],
					'type' => $values[ 'type' ],
					'date' => $values[ 'date_sp' ],
					'executed' => 'True',
					'next_time' => $new_nexttime,
					'recursive_value' => $values[ 'minutes' ],
					'days' => $values[ 'days' ],
					'weekdays' => $values[ 'weekdays' ] 
				);
				$model = Mage::getModel( 'marketplace/crontable' )->load( $id )->addData( $data );
				try {
					$model->setId( $id )->save();
				}
				catch ( Exception $e ) {
					echo $e->getMessage();
				}
			}
			if ( in_array( $dw, $days_arr ) ) {
				$timecreated    = strftime( "%Y-%m-%d %H:%M:%S", mktime( date( "H" ), date( "i" ), date( "s" ), date( "m" ), date( "d" ), date( "Y" ) ) );
				$timescheduled  = $next_time;
				$resource       = Mage::getSingleton( 'core/resource' );
				$readConnection = $resource->getConnection( 'core_read' );
				$query          = 'SELECT * FROM ' . $resource->getTableName( 'cron/schedule' ) . ' where job_code = "' . $jobCode . '" AND status = "pending"';
				$results        = $readConnection->fetchAll( $query );
				$size           = count( $results );
				if ( $size <= 5 ) {
					try {
						$schedule = Mage::getModel( 'cron/schedule' );
						$schedule->setJobCode( $jobCode )->setCreatedAt( $timecreated )->setScheduledAt( $timescheduled )->setStatus( Mage_Cron_Model_Schedule::STATUS_PENDING )->save();
					}
					catch ( Exception $e ) {
						throw new Exception( Mage::helper( 'cron' )->__( 'Unable to save Cron expression' ) );
					}
				}
			}
		}
	}
}
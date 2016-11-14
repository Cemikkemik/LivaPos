<?php
global $Options;
$this->load->config( 'rest' );
?>
<?php include_once( MODULESPATH . '/nexo/inc/angular/order-list/directives/loading-spinner.php' );?>
<script>
tendooApp.filter('fillZero', function () {
    return function (n, len) {
        var num = parseInt(n, 10);
        len = parseInt(len, 10);
        if (isNaN(num) || isNaN(len)) {
            return n;
        }
        num = ''+num;
        while (num.length < len) {
            num = '0'+num;
        }
        return num;
    };
});
tendooApp.filter('secondsToDateTime', [function() {
    return function(seconds) {
		seconds 	=	typeof seconds == 'undefined' ? 0 : seconds;
        return new Date(1970, 0, 1).setSeconds(seconds);
    };
}])
</script>
<script>
"use strict";

tendooApp.controller( 'nrPackage', [ '$scope', '$http', '$timeout', '$compile', '$interval', function( $scope, $http, $timeout, $compile, $interval ) {

	var counter_interval;
	$scope.buttonText			=	'<?php echo __( 'Start', 'nexo-playground-manager' );?>';
	$scope.buttonClass			=	'primary';
	$scope.contentHeight		=	angular.element( '.content-wrapper' ).height() - 75;
	$scope.serverDate			=	moment( '<?php echo date_now( DATE_ATOM );?>' );
	$scope.searchOrder			=	'';
	$scope.selectedOrderIndex	=	'';
	$scope.spinner				=	{
		order_details			:	false
	};
	$scope.buttonDisabled			=	true;

	/**
	 * Current Order
	**/

	$scope.currentOrder			=	function() {
		return $scope.orders[ $scope.selectedOrderIndex ];
	}

	/**
	 * Edit Order
	**/

	$scope.editOrder			=	function( order_code ){

		if( angular.isDefined( $scope.orders[ $scope.selectedOrderIndex ] ) ) {
			if( order_code == $scope.orders[ $scope.selectedOrderIndex ].CODE ) {
				return false;
			}
		}

		$scope.stopTimer();

		_.each( $scope.orders, function( value, key ) {
			if( value.CODE == order_code ) {
				// $scope.selectedOrderCode	=	order_code;
				$scope.selectedOrderIndex		=	key;
			}
		});

		$scope.setActive( order_code );
		$scope.getOrderDetails( order_code );

	}

	/**
	 * Search
	**/

	$scope.getOrders			=	function( action ) {

		if( action == 'search' ) {
			if( $scope.searchOrder == '' ) {
				NexoAPI.Notify().warning( '<?php echo _s( 'Warning', 'nexo-playground-manager' );?>', '<?php echo _s( 'You must input something for search', 'nexo-playground-manager' );?>' );
				return false;
			}
			var url		=	$scope.searchOrder + '/search?<?php echo store_get_param( null );?>';
		} else {
			var url		=	'?<?php echo store_get_param( null );?>';
		}

		$http.get( '<?php echo site_url( array( 'rest', 'np_manager', 'orders' ) );?>/' + url, {
			headers			:	{
				'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
			}
		}).then( function( returned ){
			$scope.orders	=	returned.data;
		});
	}

	/**
	 * Get OrderItems
	**/

	$scope.getOrderDetails		=	function( order_code ) {

		if( $scope.selectedOrderIndex < 0 ) {
			console.log( 'error occured while trying to load order' );
			return false;
		}

		$scope.spinner.order_details	=	true;

		// Only when the order is open the first time
		// Then at this moment we add overalltime var

		if( ! angular.isDefined( $scope.orders[ $scope.selectedOrderIndex ].overallTime ) ) {

			$scope.orders[ $scope.selectedOrderIndex ]	=	_.extend( $scope.orders[ $scope.selectedOrderIndex ],{
				overallTime					:	0,
				remainingTime				:	0,
				usedSecondsIsUpdated		:	false
			});

		}

		$http.get( '<?php echo site_url( array( 'rest', 'np_manager', 'orders_items' ) );?>/' + order_code + '?<?php echo store_get_param( null );?>', {
			headers			:	{
				'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
			}
		}).then( function( returned ){

			$scope.spinner.order_details	=	false;
			$scope.orderDetails				=	returned.data;

			// Only if overallTime equals "0" then you can calculate the order overall time.

			if( $scope.orders[ $scope.selectedOrderIndex ].overallTime 	==	 0 ) {

                _.each( $scope.orderDetails, function( value ) {
                    if( value.NP_ENABLED == '1' ) {
                        var HoursToSeconds      =   value.NP_HOURS * 3600;
                        var MinutesToSeconds    =   value.NP_MINUTES * 60;
                        var TimeToSeconds       =   HoursToSeconds + MinutesToSeconds;
                        value.NP_TIME           =   TimeToSeconds;

                        $scope.orders[ $scope.selectedOrderIndex ].overallTime		+=	( parseInt( TimeToSeconds ) * parseInt( value.QUANTITE ) );
                    }
                });
			}

			if( $scope.orders[ $scope.selectedOrderIndex ].TIMER_ON == '0' ) {

				$scope.orders[ $scope.selectedOrderIndex ].remainingTime		=
				parseInt( $scope.orders[ $scope.selectedOrderIndex ].overallTime ) -
				parseInt( $scope.orders[ $scope.selectedOrderIndex ].USED_SECONDS );

				if( $scope.orders[ $scope.selectedOrderIndex ].remainingTime > 0 ) {
					$scope.buttonDisabled											=	false;
					$scope.buttonText												=	'<?php echo __( 'Start', 'nexo-playground-manager' );?>';
					$scope.buttonClass												=	'primary';
				} else {
					$scope.buttonDisabled											=	true;
					$scope.buttonText												=	'<?php echo __( 'Stop', 'nexo-playground-manager' );?>';
					$scope.buttonClass												=	'warning';
				}

			} else {

				var durationSoFar	= 	moment( $scope.serverDate ).diff( moment( $scope.orders[ $scope.selectedOrderIndex ].START_TIME ), 'seconds' );
				var realDuration	=	durationSoFar	+	parseInt( $scope.orders[ $scope.selectedOrderIndex ].USED_SECONDS );

				if( realDuration <  $scope.orders[ $scope.selectedOrderIndex ].overallTime ) {

					$scope.buttonDisabled		=	false;
					$scope.buttonText			=	'<?php echo __( 'Stop', 'nexo-playground-manager' );?>';
					$scope.buttonClass			=	'default';

					if( $scope.orders[ $scope.selectedOrderIndex ].usedSecondsIsUpdated == false ) {
						$scope.orders[ $scope.selectedOrderIndex ].USED_SECONDS			=	realDuration;
						$scope.orders[ $scope.selectedOrderIndex ].usedSecondsIsUpdated	=	true;
					}

					$scope.orders[ $scope.selectedOrderIndex ].remainingTime		=	$scope.orders[ $scope.selectedOrderIndex ].overallTime - realDuration;
					$scope.triggerTimer();

				} else {
					$scope.orders[ $scope.selectedOrderIndex ].remainingTime		=	0;
					$scope.orders[ $scope.selectedOrderIndex ].USED_SECONDS			=	$scope.orders[ $scope.selectedOrderIndex ].overallTime;
					$scope.buttonDisabled					=	true;
					$scope.buttonText						=	'<?php echo __( 'Stop', 'nexo-playground-manager' );?>';
					$scope.buttonClass						=	'default';
				}

			}
		});
	}

	/**
	 * Trigger Timer
	**/

	$scope.triggerTimer			=	function() {
		$scope.orders[ $scope.selectedOrderIndex ].TIMER_ON					=	'1';
		$scope.orders[ $scope.selectedOrderIndex ].interval				=	$interval( function(){
			if( $scope.orders[ $scope.selectedOrderIndex ].remainingTime > 0 ) {
				$scope.orders[ $scope.selectedOrderIndex ].remainingTime -= 1;
				$scope.orders[ $scope.selectedOrderIndex ].USED_SECONDS	=	parseInt( $scope.orders[ $scope.selectedOrderIndex ].USED_SECONDS ) + 1;
			} else {
				$interval.cancel( $scope.orders[ $scope.selectedOrderIndex ].interval );
				$scope.orders[ $scope.selectedOrderIndex ].interval		=	undefined;

				$scope.buttonDisabled			=	true;
				$scope.buttonText			=	'<?php echo __( 'Start', 'nexo-playground-manager' );?>';
				$scope.buttonClass			=	'primary';

				// Launch query to stop timer on server
				$http.post( '<?php echo site_url( array( 'rest', 'np_manager', 'end_timer' ) );?>?<?php echo store_get_param( null );?>', {
					order_id		:	$scope.orders[ $scope.selectedOrderIndex ].ID,
					date			:	$scope.serverDate.format(),
					overall_time	:	$scope.orders[ $scope.selectedOrderIndex ].overallTime
				},{
					headers			:	{
						'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
					}
				}).then( function( returned ){
				});
			}
		}, 1000 );
	}

	/**
	 * Reset
	**/

	$scope.reset				=	function() {
		$scope.getOrders();
		$scope.orders[ $scope.selectedOrderIndex ]	=	{};
	}

	/**
	 * Run Time
	**/

	$scope.runTime				=	function( time ) {
		$interval( function(){
			$scope.serverDate.add( 1, 's' );
		}, 1000 );
	};

	/**
	 * Run Timer
	**/

	$scope.runTimer				=	function() {

		if( ! _.isEmpty( $scope.orders[ $scope.selectedOrderIndex ] ) ) {

			if( $scope.orders[ $scope.selectedOrderIndex ].TIMER_ON	==	'0' ) {

				if( parseInt( $scope.orders[ $scope.selectedOrderIndex ].USED_SECONDS ) >= parseInt( $scope.orders[ $scope.selectedOrderIndex ].overallTime ) ) {
					NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo-playground-manager' );?>', '<?php echo _s( 'There is no more remaning time for this order', 'nexo-playground-manager' );?>' );
					return false;
				}

				$scope.orders[ $scope.selectedOrderIndex ].TIMER_ON	=	'1';

				// Query to run active
				$http.post( '<?php echo site_url( array( 'rest', 'np_manager', 'start_timer' ) );?>?<?php echo store_get_param( null );?>', {
						order_id		:	$scope.orders[ $scope.selectedOrderIndex ].ID,
						date			:	$scope.serverDate.format(),
						overall_time	:	$scope.orders[ $scope.selectedOrderIndex ].overallTime
				},{
					headers			:	{
						'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
					},
					method			:	'POST'
				}).then( function( returned ){
				});

				// Launch Time Runner on server for selected order
				$scope.buttonText							=	'<?php echo __( 'Stop', 'nexo-playground-manager' );?>';
				$scope.buttonClass							=	'warning';
				$scope.triggerTimer();

			} else {

				$scope.orders[ $scope.selectedOrderIndex ].TIMER_ON	=	'0';

				// Query to stop timer on server
				$http.post( '<?php echo site_url( array( 'rest', 'np_manager', 'end_timer' ) );?>?<?php echo store_get_param( null );?>', {
						order_id		:	$scope.orders[ $scope.selectedOrderIndex ].ID,
						date			:	$scope.serverDate.format(),
						overall_time	:	$scope.orders[ $scope.selectedOrderIndex ].overallTime
				},{
					headers			:	{
						'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
					}
				}).then( function( returned ){
				});
				// Stop Time Runner for selected Order
				$scope.stopTimer();
				$scope.buttonText							=	'<?php echo __( 'Start', 'nexo-playground-manager' );?>';
				$scope.buttonClass							=	'primary';
			}
		} else {
			NexoAPI.Notify().warning( '<?php echo _s( 'Warning', 'nexo-playground-manager' );?>', '<?php echo _s( 'You must select an order first before running the timer', 'nexo-playground-manager' );?>' );
			return false;
		}
	}

	/**
	 * Set Active
	**/

	$scope.setActive			=	function( order_code ){
		_.each( $scope.orders, function( value, key ) {
			if( value.CODE	==	order_code ) {
				value.active	=	true;
			} else {
				value.active	=	false;
			}
		});
	}

	/**
	 * Select Order
	**/

	$scope.selectOrder			=	function( order_code ) {
		_.each( $scope.orders, function( value, key ) {
			if( value.CODE	==	order_code ) {
				return $scope.orders[ key ];
			}
		});
	}

	/**
	 * Stop Timer
	**/

	$scope.stopTimer			=	function() {
		if( angular.isDefined( $scope.orders[ $scope.selectedOrderIndex ] ) ) {
			// $scope.orders[ $scope.selectedOrderIndex ].TIMER_ON			=	'0';
			$interval.cancel( $scope.orders[ $scope.selectedOrderIndex ].interval );
			$scope.orders[ $scope.selectedOrderIndex ].interval			=	undefined;
		}
	}

	$scope.runTime();
	$scope.getOrders();
}]);
</script>

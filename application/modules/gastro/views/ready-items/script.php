<script>
tendooApp.directive( 'readyItems', function(){
	return {
		templateUrl    :    '<?php echo site_url([ 'dashboard', 'gastro', 'templates', 'ready_items']);?>'
	}
})
tendooApp.controller( 'readyMealCTRL', [ '$rootScope', '$scope', '$http', '$interval', '$timeout', '$compile',
function( $rootScope, $scope, $http, $interval, $timeout, $compile ){
	$scope.items   =    [];
	
	/**
	* Open Ready Item Windows
	* @return void
	**/

	$scope.openReadyMeals    =    function(){
		NexoAPI.Bootbox().confirm({
			message 		:	'<div class="meal-selection"><ready-items></ready-items></div>',
			title          :	'<?php echo _s( 'Ready Meals', 'nexo' );?>',
			buttons: {
				cancel: {
					label: '<?php echo _s( 'Close', 'nexo' );?>',
					className: 'btn-default'
				}
			},
			callback		:	function( action ) {
				
			},
			className       :   'ready-meals'
		});
		$scope.windowHeight				=	window.innerHeight;
		$scope.wrapperHeight			=	$scope.windowHeight - ( ( 56 * 2 ) + 30 );
		$timeout( function(){
			angular.element( '.ready-meals .modal-dialog' ).css( 'width', '80%' );
			angular.element( '.ready-meals .modal-body' ).css( 'padding-top', '0px' );
			angular.element( '.ready-meals .modal-body' ).css( 'padding-bottom', '0px' );
			angular.element( '.ready-meals .modal-body' ).css( 'padding-left', '0px' );
			angular.element( '.ready-meals .modal-body' ).css( 'padding-right', '0px' );
			angular.element( '.ready-meals .modal-body' ).css( 'height', $scope.wrapperHeight );
			angular.element( '.ready-meals .modal-body' ).css( 'overflow-x', 'hidden' );
			$( '.meal-selection' ).html( $compile( $( '.meal-selection').html() )( $scope ) );
		}, 200 );
	}

	$rootScope.$on( 'getOrders.withMetas', function( scope, data ) {
		$scope.items        =    [];
		data.forEach( order => {
			if( order.REAL_TYPE == 'dinein' ) {
				order.items.forEach( item => {
					if( item.FOOD_STATUS == 'ready' ) {
						item.TABLE_NAME     =    order.TABLE_NAME;
						item.AUTHOR_NAME    =    order.AUTHOR_NAME;
						item.ORDER_CODE     =    order.CODE
						$scope.items.push( item );
					}
				});
			}
		});
	});

	$rootScope.$on( 'open-waiter-screen', function( scope, data ) {
		$scope.openReadyMeals();
	});

	$scope.collectMeal       =    function( meal_id, index ) {
			$http.post( '<?php echo site_url([ 'api', 'gastro', 'tables', 'collect', store_get_param( '?' ) ]);?>', {
			meal_id
		}, {
			headers			:	{
				'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo get_option( 'rest_key' );?>'
			}
		}).then(function( returned ) {
			$( '#item-' + meal_id ).fadeOut(500, function(){
				$scope.items.splice( index, 1 );
			});
			$rootScope.$broadcast( 'meal.collected', returned.data );
		}, function( returned ){
			$rootScope.$broadcast( 'meal.notServed', returned.data );
		});
	}
}])
</script>
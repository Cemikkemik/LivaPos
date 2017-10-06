<script>
tendooApp.directive( 'readyOrders', function(){
	return {
		templateUrl         :    '<?php echo site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'templates', 'ready-orders' ] );?>',
		restrict            :   'E'
	}
});

tendooApp.controller( 'readerOrdersCTRL', [ 
'$scope', '$timeout', '$compile', '$rootScope', '$interval', '$http', '$filter', 
function( $scope, $timeout, $compile, $rootScope, $interval, $http, $filter ){
	$scope.$watch( 'newOrders', function( previous, current ){
		if( $scope.newOrders > 0 && previous < current ){
			NexoAPI.Toast()( '<?php echo _s( 'A new order is ready', 'nexo-restaurant' );?>' );
		}
	});

	// $( '.new-orders-button' ).append( '<span class="badge badge-warning">42</span>' );
	$scope.newOrders         	=    0;  

	$scope.selectedOrder 		=	null;
	
	/**
	* Open Waiter Screen
	* @return void
	**/
	
	$scope.openReadyOrders       =    function(){
		NexoAPI.Bootbox().alert({
			message 		:	'<div class="ready-orders"><ready-orders></ready-orders></div>',
			title          :	'<?php echo _s( 'Ready orders', 'nexo' );?>',
			buttons 		:	{
				ok: {
					label: '<span ><?php echo _s( 'Close', 'nexo-restaurant' );?></span></span>',
					className: 'btn-default'
				},
			}, 
			callback 		:	function( action ) {
				$rootScope.$broadcast( 'open-select-order-type' );
			},
			className 	:	'ready-orders-box col-md-8 col-lg-12'
		});
		
		$scope.windowHeight           =	window.innerHeight;
		$scope.wrapperHeight          =	$scope.windowHeight - ( ( 56 * 2 ) + 30 );
		
		$timeout( function(){
			angular.element( '.ready-orders-box .modal-dialog' );
			angular.element( '.ready-orders-box .modal-body' ).css( 'padding-top', '0px' );
			angular.element( '.ready-orders-box .modal-body' ).css( 'padding-bottom', '0px' );
			angular.element( '.ready-orders-box .modal-body' ).css( 'padding-left', '0px' );
			angular.element( '.ready-orders-box .modal-body' ).css( 'padding-right', '0px' );
			angular.element( '.ready-orders-box .modal-body' ).css( 'height', $scope.wrapperHeight );
			angular.element( '.ready-orders-box .modal-body' ).css( 'overflow-x', 'hidden' );
		}, 150 );
		
		$( '.ready-orders-box' ).last().find( '.modal-footer' )
		.prepend( '<a ng-show="selectedOrder != null && selectedOrder.TYPE != \'nexo_order_comptant\'" ng-click="runPayment()" class="btn btn-primary"><?php echo _s( 'Pay that order', 'nexo-restarant' );?></a>' );
		// $( '.ready-orders-box' ).last().find( '.modal-footer' )
		// .prepend( '<a ng-show="selectedOrder.STATUS == \'ready\'" ng-click="setAsServed()" class="btn btn-primary"><?php echo _s( 'Serve', 'nexo-restarant' );?></a>' );
		
		$( '.ready-orders' ).html( 
			$compile( $( '.ready-orders').html() )( $scope ) 
		);
		$( '.ready-orders-box' )
		.find( '.modal-footer' )
		.html( $compile( $( '.ready-orders-box' ).find( '.modal-footer' ).html() )( $scope ) );
	}

	/**
	* Payement
	* @return void
	**/
	
	$scope.runPayment             =    function(){
		
		v2Checkout.From 				=	'readerOrdersCTRL.runPayment';
		v2Checkout.emptyCartItemTable();
		// since by default the query don't return the meta. We can the force meta when the order is being open
		// on the ready order screen.
		// the modifier will be available on the cart
		$scope.selectedOrder.items.forEach( ( item ) => {
			item.metas 						=	new Object;
			item.metas[ 'modifiers' ] 			=	JSON.parse( item.MODIFIERS );
			item.metas[ 'restaurant_food_issue' ] 	=	item.FOOD_ISSUE;
			item.metas[ 'restaurant_food_status' ] 	=	item.FOOD_STATUS;
			item.metas[ 'restaurant_note' ]		=	item.NOTE;
		});

		v2Checkout.CartItems 			=	$scope.selectedOrder.items;

		if( $scope.selectedOrder.REMISE_TYPE != '' ) {
			v2Checkout.CartRemiseType			=	$scope.selectedOrder.REMISE_TYPE;
			v2Checkout.CartRemise				=	NexoAPI.ParseFloat( $scope.selectedOrder.REMISE );
			v2Checkout.CartRemisePercent			=	NexoAPI.ParseFloat( $scope.selectedOrder.REMISE_PERCENT );
			v2Checkout.CartRemiseEnabled			=	true;
		}

		if( parseFloat( $scope.selectedOrder.GROUP_DISCOUNT ) > 0 ) {
			v2Checkout.CartGroupDiscount 			=	parseFloat( $scope.selectedOrder.GROUP_DISCOUNT ); // final amount
			v2Checkout.CartGroupDiscountAmount 	=	parseFloat( $scope.selectedOrder.GROUP_DISCOUNT ); // Amount set on each group
			v2Checkout.CartGroupDiscountType 		=	'amount'; // Discount type
			v2Checkout.CartGroupDiscountEnabled 	=	true;
		}

		v2Checkout.CartCustomerID 			=	$scope.selectedOrder.REF_CLIENT;
		// @since 2.7.3
		v2Checkout.CartNote 				=	$scope.selectedOrder.DESCRIPTION;
		v2Checkout.CartTitle 				=	$scope.selectedOrder.TITRE;

		// @since 3.1.2
		v2Checkout.CartShipping 				=	parseFloat( $scope.selectedOrder.SHIPPING_AMOUNT );
		$scope.price 						=	v2Checkout.CartShipping; // for shipping directive
		$( '.cart-shipping-amount' ).html( $filter( 'moneyFormat' )( $scope.price ) );

		// Restore Custom Ristourne
		v2Checkout.restoreCustomRistourne();

		// Refresh Cart
		// Reset Cart state
		v2Checkout.buildCartItemTable();
		v2Checkout.refreshCart();
		v2Checkout.refreshCartValues();
		v2Checkout.ProcessURL				=	"<?php echo site_url(array( 'rest', 'nexo', 'order' ));?>" + '/<?php echo User::id();?>/' + $scope.selectedOrder.ID + "?store_id=<?php echo get_store_id();?>";
		v2Checkout.ProcessType				=	'PUT';
		// convert type to terminated
		// if( _.indexOf( [ 'dinein', 'takeaway', 'delivery', 'booking' ], $scope.selectedOrder.REAL_TYPE ) != -1 ) {
		// 	v2Checkout.CartType 				=	'nexo_order_' + $scope.selectedOrder.REAL_TYPE + '_paid';
		// } else {
		// 	// $scope.openReadyOrders();
		// 	v2Checkout.resetCart();
		// 	v2Checkout.ProcessURL 		=	"<?php echo site_url(array( 'rest', 'nexo', 'order', User::id() ));?>?store_id=<?php echo get_store_id();?>";
		// 	v2Checkout.ProcessType 		=	'POST';
		// 	return NexoAPI.Toast()( '<?php echo _s( 'The order type is not supported.', 'nexo-restaurant' );?>' );
		// }

		// Restore Shipping
		// @since 3.1
		_.each( $scope.selectedOrder.shipping, ( value, key ) => {
			$scope[ key ] 	=	value;
		});
		
		$rootScope.$broadcast( 'filter-selected-order-type', {
			namespace 	:	$scope.selectedOrder.RESTAURANT_ORDER_TYPE
		});
		$rootScope.$emit( 'payBox.openPayBox' );
	}

	/**
	 * Unselect all orders
	**/

	$scope.unselectAllOrders 	=	function(){
		_.each( $scope.orders, function( _order ) {
			_order.active       =    false;
		});
	}
	
	/***
	* Select Order
	* @return void
	**/
	
	$scope.selectOrder            =    function( order ) {
		// unselect all orders
		$scope.unselectAllOrders();
		
		order.active             =    true;
		$scope.selectedOrder 	=	order;
	}

	/**
	 * Resume Orders
	 * @return string
	**/

	$scope.resumeItems 		=	function( items ) {
		let itemNames 		=	[];
		_.each( items, function( item ){
			itemNames.push( item.DESIGN );
		});
		return itemNames.toString();
	}

	/**
	 * Time SPan
	 * @param object order
	 * @return string
	**/

	$scope.timeSpan 		=	function( order ){
		return moment( order.DATE_CREATION ).from( tendoo.date );
	}

	$scope.rawOrders 			=	[];
	$scope.rawOrdersCodes 		=	[];
	$scope.orders 				=	[];
	$scope.ordersCodes  		=	[];
	
	$interval( function(){
		$http.get( '<?php echo site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'get_orders_with_meals' ]);?>' )
		.then( function( returned ){
			
			// filter only ready items
			let indexToRemove 		=	[];
			let dineinOrders 		=	[];
			returned.data.forEach( ( order, index ) => {
				// if order is not ready or collected
				if( _.indexOf([ 'ready', 'collected' ], order.STATUS ) == -1 ) {
					indexToRemove.push( index );
				}

				if( order.TYPE == 'REAL_TYPE' ) {
					dineinOrders.push( order );
				}
			});

			// Just emit what the server returns
			$rootScope.$emit( 'getOrders.withMetas', angular.copy( returned.data ) );

			// Clear all unused index
			indexToRemove.forEach( index => {
				returned.data.splice( index, 1 );
			});

			$scope.rawOrders		=	[];
			$scope.rawOrdersCodes 	=	[];
			
			_.each( returned.data, function( order ) {
				$scope.rawOrders.unshift( order );
				$scope.rawOrdersCodes.unshift( order.CODE );
			});

			let indexToKill 		=	[];
			let indexCodeToKill 	=	[];
			// remove order which aren't not more listed
			_.each( $scope.orders, ( order, index ) => {
				if( _.indexOf( $scope.rawOrdersCodes, order.CODE ) == -1 ) {
					indexToKill.push( index );
					indexCodeToKill.push( _.indexOf( $scope.ordersCodes, order.CODE ) );
				}
			});

			_.each( $scope.rawOrdersCodes, function( rawCode, index ){
				if( _.indexOf( $scope.ordersCodes, rawCode ) == -1 ) {
					$scope.orders.unshift( $scope.rawOrders[ index ] );
					$scope.ordersCodes.unshift( rawCode );
					$scope.newOrders++;
				}
			});

			if( indexToKill.length > 0 ){
				// kill indexes
				indexToKill.forEach( index => {
					$scope.orders.splice( index, 1 );
					if( $scope.newOrders > 0 ) {
						$scope.newOrders--;
					}
				});
			}

			if( indexCodeToKill.length > 0 ) {
				indexCodeToKill.forEach( index => {
					$scope.ordersCodes.splice( index, 1 );
					if( $scope.newOrders > 0 ) {
						$scope.newOrders--;
					}
				});
			}

		});
	}, 8000 );

	NexoAPI.events.addAction( 'close_paybox', function(){
		// if current order is a modification
		// we can cancel that.
		if( v2Checkout.ProcessType == 'PUT' && v2Checkout.From == 'readerOrdersCTRL.runPayment' ) {
			// $scope.openReadyOrders();
			v2Checkout.resetCart();
			v2Checkout.ProcessURL 		=	"<?php echo site_url(array( 'rest', 'nexo', 'order', User::id() ));?>?store_id=<?php echo get_store_id();?>";
			v2Checkout.ProcessType 		=	'POST';
		}
		$scope.selectedOrder 	=	null;
		$scope.unselectAllOrders();
	});

	/**
	 *  Remove only served order from the list
	**/

	NexoAPI.events.addFilter( 'test_order_type', function( data ){
		if( $( '.ready-orders-box' ).length == 1 ) {
			$rootScope.$broadcast( 'open-select-order-type' );
			// close ready order when payment is loaded
			// $( '.ready-orders-box' ).find( '[data-dismiss="modal"]' ).trigger( 'click' );			
		}
		return data;
	});

	/**
	 * Watch Events
	**/

	$scope.$on( 'open-ready-orders', function(){
		$scope.openReadyOrders();
	});
}]);
</script>
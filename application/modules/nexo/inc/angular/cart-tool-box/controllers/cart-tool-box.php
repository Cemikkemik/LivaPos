<?php global $Options;?>
<script>
tendooApp.directive( 'shipping', function(){
	return {
		restrict 		:	'E',
		templateUrl 	:	'<?php echo site_url([ 'dashboard', store_slug(), 'nexo_templates', 'shippings']);?>',
		controller 		:	[ '$scope', function( $scope ) {
			$scope.optionShowed 	=	false;

			// Check whether the current customer has valid informations
			$scope.isAddressValid	=	false;
			$scope.currentCustomer 	=	new Object;

			_.each( v2Checkout.customers.list, ( customer ) => {
				if( customer.ID == parseInt( v2Checkout.CartCustomerID ) ) {
					if( typeof customer.shipping_name != 'undefined' ) {
						$scope.isAddressValid	=	true;
						$scope.currentCustomer	=	customer;
					}
				}
			});

			/**
			 * Toggle Options
			**/

			$scope.toggleOptions	=	function(){
				$scope.optionShowed  	=	!$scope.optionShowed;
			}

			/**
			 * toggleFillShippingInfo
			 * @param boolean
			 * @return void
			**/

			$scope.toggleFillShippingInfo 	=	function( bool ) {
				if( bool ) {

					_.each( $scope.currentCustomer, ( customer_fields, key ) => {
						if( key.substr( 0, 9 ) == 'shipping_' && _.indexOf([ 
							'name', 'enterprise', 'address_1',
							'city', 'country', 'pobox',
							'state', 'surname',
							'address_2'
						], key.substr( 9 ) ) != -1 ) {
							$scope[ key.substr( 9 ) ] 	=	customer_fields;
						}
					});

					console.log( $scope );
				} else {
					_.each([ 
						'name', 'enterprise', 'address_1',
						'city', 'country', 'pobox',
						'state', 'surname',
						'address_2'
					], ( field ) => {
						$scope[ field ] 	=	'';
					});
				}
			}

			console.log( v2Checkout );
		}]
	}
});

tendooApp.directive('validNumber', function() {
	return {
	require: '?ngModel',
	link: function(scope, element, attrs, ngModelCtrl) {
		if(!ngModelCtrl) {
		return; 
		}

		ngModelCtrl.$parsers.push(function(val) {
		if (angular.isUndefined(val)) {
			var val = '';
		}
		
		var clean = val.replace(/[^-0-9\.]/g, '');
		var negativeCheck = clean.split('-');
		var decimalCheck = clean.split('.');
		if(!angular.isUndefined(negativeCheck[1])) {
			negativeCheck[1] = negativeCheck[1].slice(0, negativeCheck[1].length);
			clean =negativeCheck[0] + '-' + negativeCheck[1];
			if(negativeCheck[0].length > 0) {
				clean =negativeCheck[0];
			}
			
		}
			
		if(!angular.isUndefined(decimalCheck[1])) {
			decimalCheck[1] = decimalCheck[1].slice(0,2);
			clean =decimalCheck[0] + '.' + decimalCheck[1];
		}

		if (val !== clean) {
			ngModelCtrl.$setViewValue(clean);
			ngModelCtrl.$render();
		}
		return clean;
		});

		element.bind('keypress', function(event) {
		if(event.keyCode === 32) {
			event.preventDefault();
		}
		});
	}
	};
});
</script>

<script>
tendooApp.controller( 'cartToolBox', [ '$http', '$compile', '$scope', '$timeout', 'hotkeys', 
	function( $http, $compile, $scope, $timeout, hotkeys ) {

	$scope.loadedOrders				=	new Object;
	$scope.orderDetails				=	null;
	let default_orderType			=	{
		nexo_order_devis			:	{
			title					:	'<?php echo _s( 'En attente', 'nexo' );?>',
			active					:	false
		}
	}

	$scope.orderStatusObject		=	NexoAPI.events.applyFilters( 'history_orderType', default_orderType );
	$scope.theSpinner				=	new Object;
	$scope.theSpinner[ 'mspinner' ]	=	false;
	$scope.theSpinner[ 'rspinner' ]	=	true;
	$scope.windowHeight				=	window.innerHeight;
	$scope.wrapperHeight			=	$scope.windowHeight - ( ( 56 * 2 ) + 30 );

	// reset default URL when cart is reset
	NexoAPI.events.addAction( 'reset_cart', function(){
		NexoAPI.events.removeFilter( 'process_data' );
	});

	/**
	 * Since the button has been moved to the pos header. It's not dynamically loaded
	 * @since 3.0.22
	**/

	$( '.history-box-button' ).replaceWith( $compile( $( '.history-box-button' )[0].outerHTML )( $scope ) );

	/**
	 * Load order for
	**/

	$scope.loadOrders			=	function( namespace ){

		$scope.theSpinner[ 'mspinner' ]	=	true;

		$http.get( '<?php echo site_url( array( 'rest', 'nexo', 'order_with_status' ) );?>' + '/' + namespace + '?<?php echo store_get_param( null );?>', {
			headers			:	{
				'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
			}
		}).then(function( returned ){
			$scope.theSpinner[ 'mspinner' ]		=	false;
			$scope.loadedOrders[ namespace ]	=	returned.data;
		});
	};

	/**
	 * Open History Box
	**/

	$scope.openHistoryBox		=	function(){
		if( ! v2Checkout.isCartEmpty() ) {
			NexoAPI.Bootbox().confirm( '<?php echo _s( 'Une commande est déjà en cours, souhaitez vous la supprimer ?', 'nexo' );?>', function( action ){
				if( action ) {
					v2Checkout.resetCart();
					$scope.openHistoryBox();
				}
			});
			return false;
		}

		NexoAPI.Bootbox().confirm({
			message 		:	'<div class="historyboxwrapper"><history-content/></div>',
			title			:	'<?php echo _s( 'Historique des commandes', 'nexo' );?>',
			buttons: {
				confirm: {
					label: '<?php echo _s( 'Ouvrir la commande', 'nexo' );?>',
					className: 'btn-success'
				},
				cancel: {
					label: '<?php echo _s( 'Fermer', 'nexo' );?>',
					className: 'btn-default'
				}
			},
			callback		:	function( action ) {
				return $scope.openOrderOnPOS( action );
			}
		});

		$( '.historyboxwrapper' ).html( $compile( $( '.historyboxwrapper' ).html() )($scope) );

		$timeout( function(){
			angular.element( '.modal-dialog' ).css( 'width', '90%' );
			angular.element( '.modal-body' ).css( 'padding-top', '0px' );
			angular.element( '.modal-body' ).css( 'padding-bottom', '0px' );
			angular.element( '.modal-body' ).css( 'padding-left', '0px' );
			angular.element( '.modal-body' ).css( 'height', $scope.wrapperHeight );
			angular.element( '.modal-body' ).css( 'overflow-x', 'hidden' );
			angular.element( '.middle-content' ).attr( 'style', 'border-left:solid 1px #DEDEDE;overflow-y:scroll;height:' + $scope.wrapperHeight + 'px' );
			angular.element( '.order-details' ).attr( 'style', 'overflow-y:scroll;height:' + $scope.wrapperHeight + 'px' );
			angular.element( '.middle-content' ).css( 'padding', 0 );
		}, 150 );


		// Select first option
		$scope.selectHistoryTab( _.keys( $scope.orderStatusObject )[0] );
	};

	/**
	 * Open Order Details
	**/

	$scope.openOrderDetails			=	function( order_id ) {
		$scope.theSpinner[ 'rspinner' ]			=	true;
		$http.get( '<?php echo site_url( array( 'rest', 'nexo', 'order_with_item' ) );?>' + '/' + order_id + '?<?php echo store_get_param( null );?>', {
			headers			:	{
				'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
			}
		}).then(function( returned ){
			$scope.theSpinner[ 'rspinner' ]		=	false;
			$scope.orderDetails					=	returned.data;
		});
	};

	/**
	 * Open Order On POS
	**/

	$scope.openOrderOnPOS			=	function( action ){
		if( action ) {
			if( $scope.orderDetails == null ) {
				NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Vous devez choisir une commande avant de l\'ouvrir.', 'nexo' );?>' );
				return false;
			}

			NexoAPI.events.addFilter( 'process_data', function( data ){
				data.url			=	"<?php echo site_url(array( 'rest', 'nexo', 'order', User::id() ) );?>/" + $scope.orderDetails.order.ID + "?store_id=<?php echo get_store_id();?>";

				data.type			=	'PUT';
				return data;
			});

			/**
			 * Overrite open order on cart
			 * A script can then handle the way order are added to the cart
			 * @since 3.0.22
			**/

			if( NexoAPI.events.applyFilters( 'override_open_order', $scope.orderDetails ) ) {
				return true;
			}

			v2Checkout.emptyCartItemTable();
			v2Checkout.CartItems			=	$scope.orderDetails.items;

			_.each( v2Checkout.CartItems, function( value, key ) {
				value.QTE_ADDED		=	value.QUANTITE;
			});

			// @added CartRemisePercent
			// @since 2.9.6

			if( $scope.orderDetails.order.REMISE_TYPE != '' ) {
				v2Checkout.CartRemiseType			=	$scope.orderDetails.order.REMISE_TYPE;
				v2Checkout.CartRemise				=	NexoAPI.ParseFloat( $scope.orderDetails.order.REMISE );
				v2Checkout.CartRemisePercent		=	NexoAPI.ParseFloat( $scope.orderDetails.order.REMISE_PERCENT );
				v2Checkout.CartRemiseEnabled		=	true;
			}

			if( parseFloat( $scope.orderDetails.order.GROUP_DISCOUNT ) > 0 ) {
				v2Checkout.CartGroupDiscount				=	parseFloat( $scope.orderDetails.order.GROUP_DISCOUNT ); // final amount
				v2Checkout.CartGroupDiscountAmount			=	parseFloat( $scope.orderDetails.order.GROUP_DISCOUNT ); // Amount set on each group
				v2Checkout.CartGroupDiscountType			=	'amount'; // Discount type
				v2Checkout.CartGroupDiscountEnabled			=	true;
			}

			v2Checkout.CartCustomerID						=	$scope.orderDetails.order.REF_CLIENT;

			// @since 2.7.3
			v2Checkout.CartNote								=	$scope.orderDetails.order.DESCRIPTION;

			v2Checkout.CartTitle							=	$scope.orderDetails.order.TITRE;

			// Restore Custom Ristourne
			v2Checkout.restoreCustomRistourne();

			// Refresh Cart
			// Reset Cart state
			v2Checkout.buildCartItemTable();
			v2Checkout.refreshCart();
			v2Checkout.refreshCartValues();
		}
	};

	/**
	 * Select History Tab
	**/

	$scope.selectHistoryTab			=	function( namespace ) {
		_.each( $scope.orderStatusObject, function( value, key ) {
			value.active	=	false;
		});

		_.propertyOf( $scope.orderStatusObject )( namespace ).active	=	true;

		$scope.loadOrders( namespace );

		$scope.theSpinner[ 'rspinner' ]			=	true;
		$scope.orderDetails						=	null;
	}

	/**
	 * Creating Customer
	 * @since 3.1
	**/

	$scope.calling 					= 	0;
	$scope.openCreatingUser 		=	function(){

		// create cache
		if( $( 'div.customers-directive-cache' ).length == 0 ) {
			angular.element( 'body' ).append( '<div class="customers-directive-cache" style="display:none;"></div>' );
		}

		NexoAPI.Bootbox().alert({
			message 		:	'<div class="customerwrapper"></div>',
			title			:	'<?php echo _s( 'Créer un nouveau client', 'nexo' );?>',
			buttons: {
				ok: {
					label: '<?php echo _s( 'Fermer', 'nexo' );?>',
					className: 'btn-default'
				}
			},
			callback		:	function( action ) {
				$( 'customers-main' ).appendTo( '.customers-directive-cache' );
				$scope.model        =   new Object;
			}
		});
		
		$timeout( function(){

			if( $( 'customers-main' ).length > 0 ) {
				$( '.customerwrapper' ).html( '' );
				$( 'customers-main' ).appendTo( '.customerwrapper' );
			} else {
				$( '.customerwrapper' ).append( '<customers-main></customers-main>' );
				$( 'customers-main' ).replaceWith( $compile( 
					$( 'customers-main' )[0].outerHTML )($scope) 
				);
			}

			angular.element( '.modal-dialog' ).css( 'width', '90%' );
			angular.element( '.modal-body' ).css( 'height', $scope.wrapperHeight );
			angular.element( '.modal-body' ).css( 'background', '#f9f9f9' );
			angular.element( '.modal-body' ).css( 'overflow-x', 'hidden' );
			angular.element( '.middle-content' ).attr( 'style', 'border-left:solid 1px #DEDEDE;overflow-y:scroll;height:' + $scope.wrapperHeight + 'px' );
			angular.element( '.order-details' ).attr( 'style', 'overflow-y:scroll;height:' + $scope.wrapperHeight + 'px' );
			angular.element( '.middle-content' ).css( 'padding', 0 );
			angular.element( '.modal-footer' ).append( '<a class="btn btn-primary create-customer-footer-btn" href="javascript:void(0)" ng-click="submitForm()"><?php echo _s( 'Ajouter un client', 'nexo' );?></a>')
			
			$( '.create-customer-footer-btn' ).replaceWith( $compile( 
				$( '.create-customer-footer-btn' )[0].outerHTML )($scope) 
			);

		}, 150 );

		setTimeout( () => {
			$( '.customer-save-btn' ).remove();
			$( '.name-input-group' ).removeClass( 'input-group' );
		}, 600 );
	}

	/**
	 * Get Customer
	 * @return void
	**/

	$scope.getCustomers 			=	function(){
		$http.get( '<?php echo site_url( [ 'rest', 'nexo', 'customers', store_get_param( '?' ) ]);?>', {
			headers	:	{
				'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo get_option( 'rest_key' );?>'
			}
		}).then( ( returned ) => {
			$( '.customers-list' ).selectpicker('destroy');
			// Empty list first
			$( '.customers-list' ).children().each(function(index, element) {
				$( this ).remove();
			});;

			let customers	=	NexoAPI.events.applyFilters( 'customers_dropdown', returned.data );

			_.each( customers, function( value, key ){
				if( parseInt( v2Checkout.CartCustomerID ) == parseInt( value.ID ) ) {

					$( '.customers-list' ).append( '<option value="' + value.ID + '" selected="selected">' + value.NOM + '</option>' );
					// Fix customer Selection
					NexoAPI.events.doAction( 'select_customer', [ value ] );

				} else {
					$( '.customers-list' ).append( '<option value="' + value.ID + '">' + value.NOM + '</option>' );
				}
			});

			// @since 3.0.16
			v2Checkout.customers.list 	=	customers;

			$( '.customers-list' ).selectpicker( 'refresh' );
		});
	}

	/**
	 * Open Delivery
	**/

	$scope.openDelivery 			=	function(){
		NexoAPI.Bootbox().confirm({
			message 		:	'<div class="shippingwrapper"><shipping></shipping></div>',
			title			:	'<?php echo _s( 'Livraison', 'nexo' );?>',
			buttons: {
				confirm: {
					label: '<?php echo _s( 'Confirmer', 'nexo' );?>',
					className: 'btn-primary'
				},
				cancel: {
					label: '<?php echo _s( 'Fermer', 'nexo' );?>',
					className: 'btn-default'
				}
			},
			callback		:	function( action ) {
			}
		});
		
		$timeout( function(){

			angular.element( '.modal-dialog' ).css( 'width', '50%' );
			angular.element( '.modal-body' ).css( 'height', $scope.wrapperHeight - 100 );
			angular.element( '.modal-body' ).css( 'background', '#f9f9f9' );
			angular.element( '.modal-body' ).css( 'overflow-x', 'hidden' );
			angular.element( '.middle-content' ).attr( 'style', 'border-left:solid 1px #DEDEDE;overflow-y:scroll;height:' + $scope.wrapperHeight + 'px' );
			angular.element( '.modal-body' ).attr( 'style', 'overflow-y:scroll;height:' + $scope.wrapperHeight + 'px' );
			angular.element( '.middle-content' ).css( 'padding', 0 );

			$( '.shippingwrapper' ).replaceWith( $compile( 
				$( '.shippingwrapper' )[0].outerHTML )($scope) 
			);
			
		}, 150 );
	}

	$scope.getCustomers();

	hotkeys.add({
		combo: '<?php echo @$Options[ 'pending_order' ] == null ? "shift+s" : @$Options[ 'pending_order' ];?>',
		description: 'This one goes to 11',
		allowIn: ['INPUT', 'SELECT', 'TEXTAREA'],
		callback: function() {
			$scope.openHistoryBox()
		}
	});
	
}]);
</script>

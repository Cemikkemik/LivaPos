<?php global $Options;?>
<script>
tendooApp.controller( 'cartToolBox', [ '$http', '$compile', '$scope', '$timeout', 'hotkeys', 
	function( $http, $compile, $scope, $timeout, hotkeys ) {

	$scope.loadedOrders				=	new Object;
	$scope.orderDetails				=	null;
	$scope.orderStatusObject		=	{
		nexo_order_devis			:	{
			title					:	'<?php echo _s( 'En attente', 'nexo' );?>',
			active					:	false
		}
	}
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

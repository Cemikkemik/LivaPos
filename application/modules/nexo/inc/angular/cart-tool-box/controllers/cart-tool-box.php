<script>
tendooApp.controller( 'cartToolBox', [ '$http', '$compile', '$scope', function( $http, $compile, $scope ) {
	
	$scope.loadedOrders				=	new Object;
	$scope.windowHeight				=	window.innerHeight;
	$scope.wrapperHeight			=	$scope.windowHeight - ( ( 56 * 2 ) + 30 );
	$scope.theSpinner				=	new Object;
	$scope.theSpinner[ 'mspinner' ]	=	false;
	
	$scope.orderStatusObject		=	{
		nexo_order_devis			:	{
			title					:	'<?php echo _s( 'En attente', 'nexo' );?>',
			active					:	false
		}
	}
	
	/**
	 * Load order for
	**/
	
	$scope.loadOrders			=	function( namespace ){
		
		$scope.theSpinner[ 'mspinner' ]		=	true;
	
		$http.get( '<?php echo site_url( array( 'rest', 'nexo', 'order_with_status' ) );?>' + '/' + namespace + '?<?php echo store_get_param( null );?>', {
			headers			:	{
				'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
			}
		}).then(function( returned ){
			$scope.loadedOrders[ namespace ]	=	returned.data;
		});
	};
	
	/**
	 * Open History Box
	**/
	
	$scope.openHistoryBox		=	function(){
		if( ! v2Checkout.isCartEmpty() ) {
			NexoAPI.Bootbox().confirm( '<?php echo _s( 'Une commande est déjà en cours, souhaitez vous la supprimer', 'nexo' );?>', function( action ){
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
					label: '<?php echo _s( 'Valider la commande', 'nexo' );?>',
					className: 'btn-success'
				},
				cancel: {
					label: '<?php echo _s( 'Fermer', 'nexo' );?>',
					className: 'btn-default'
				}
			},
			callback		:	function( action ) {
				return action;
			}
		});
		
		$( '.historyboxwrapper' ).html( $compile( $( '.historyboxwrapper' ).html() )($scope) );
		
		angular.element( '.modal-dialog' ).css( 'width', '90%' );
		angular.element( '.modal-body' ).css( 'padding-top', '0px' );
		angular.element( '.modal-body' ).css( 'padding-bottom', '0px' );
		angular.element( '.modal-body' ).css( 'padding-left', '0px' );
		angular.element( '.modal-body' ).css( 'height', $scope.wrapperHeight );
		angular.element( '.modal-body' ).css( 'overflow-x', 'hidden' );
		
		angular.element( '.middle-content' ).attr( 'style', 'border-left:solid 1px #DEDEDE;overflow-y:scroll;height:' + $scope.wrapperHeight + 'px' );	
		angular.element( '.middle-content' ).css( 'padding', 0 );
		// Select first option
		$scope.selectHistoryTab( _.keys( $scope.orderStatusObject )[0] );		
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
	}
}]);
</script>
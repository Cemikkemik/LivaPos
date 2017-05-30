<script>
/**
 * Save Order NexoPOS
**/

tendooApp.controller( 'saveBox', [ '$compile', '$http', '$scope', 'hotkeys', function( $compile, $http, $scope, hotkeys ) {

	/**
	 * confirmSaveOrder
	 **/

	$scope.confirmSaveOrder		=	function( action ){
		if( action ) {
			if( <?php echo $this->events->apply_filters( 'saveorder_confirm_condition', '$scope.orderName 	==	""' );?> ) {
				NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Veuillez renseigner un titre pour cette commande. Choissez un titre qui vous permettra de distinguer cette commande des autres.', 'nexo' )?>' );
				return false;
			}

			v2Checkout.CartTitle	=	$scope.orderName;
			v2Checkout.cartSubmitOrder( 'cash' );
		}
	}

	/**
	 * OpenSaveBox
	 **/

	$scope.openSaveBox			=		function(){

		$scope.orderName			=	typeof v2Checkout.CartTitle == 'undefined' ? '' : v2Checkout.CartTitle;
		$scope.cart					=	new Object;
		$scope.cart.value			=	v2Checkout.CartValue;
		$scope.cart.netPayable		=	v2Checkout.CartToPay;

		if( v2Checkout.isCartEmpty() ) {
			NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Vous ne pouvez pas sauvegarder une commande qui ne contient aucun produit.', 'nexo' );?>' );
			return false;
		}

		// If order has at least one item
		NexoAPI.Bootbox().confirm({
			message 		:	'<div class="saveboxwrapper"><save-order-content/></div>',
			title			:	'<?php echo _s( 'Mettre en attente', 'nexo' );?>',
			buttons: {
				confirm: {
					label: '<?php echo _s( 'Mettre en attente', 'nexo' );?>',
					className: 'btn-info'
				},
				cancel: {
					label: '<?php echo _s( 'Fermer', 'nexo' );?>',
					className: 'btn-default'
				}
			},
			callback		:	function( action ) {
				return $scope.confirmSaveOrder( action );
			}
		});

		$( '.saveboxwrapper' ).html( $compile( $( '.saveboxwrapper' ).html() )($scope) );

		angular.element( '.modal-dialog' ).css( 'width', '50%' );
		/*angular.element( '.modal-body' ).css( 'padding-top', '0px' );
		angular.element( '.modal-body' ).css( 'padding-bottom', '0px' );
		angular.element( '.modal-body' ).css( 'padding-left', '0px' );*/
		angular.element( '.modal-body' ).css( 'height', $scope.wrapperHeight );
		angular.element( '.modal-body' ).css( 'overflow-x', 'hidden' );
	}
	
	hotkeys.add({
		combo: 'shift+h',
		description: 'Hold Order',
		callback: function() {
			$scope.openSaveBox();
		}
	});
}]);
</script>

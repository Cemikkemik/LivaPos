<?php $this->config->load( 'nexo' );?>
<script>

/**
 *	Stripe Service
**/

tendooApp.service( '__stripeCheckout', [ '__windowSplash', function( __windowSplash ){
	
	this.getDescription	=	function( items ){
		return	items.length + '<?php echo _s(': produit(s) acheté(s)', 'nexo');?>';
	}

	this.run			=	function( cashPaymentAmount, orderCode, $scope ){
		
		this.scope		=	$scope;
		
		<?php if (@$Options[ store_prefix() . 'nexo_enable_stripe' ] != 'no'):?>
		if( typeof StripeCheckout != 'undefined' ) {
			<?php if (empty($Options[ store_prefix() . 'nexo_stripe_publishable_key' ])):?>
			NexoAPI.Notify().warning( '<?php echo _s('Une erreur s\'est produite', 'nexo');?>', '<?php echo _s('Vous n\'avez pas définit la "publishable key" dans les réglages stripe. Le paiement par ce moyen ne fonctionnera pas.', 'nexo');?>' );
			<?php endif;?>
			this.handler = StripeCheckout.configure({
				key: '<?php echo @$Options[ store_prefix() . 'nexo_stripe_publishable_key' ];?>',
				image: '<?php echo img_url('nexo') . '/nexopos-logo.png';?>',
				locale: 'auto',
				token: function(token) {
					$this.proceedPayment( token, cashPaymentAmount, orderCode );
				}
				<?php if ($this->config->item('nexo_test_mode') == false):?>
				,zipCode : true,
				billingAddress : true
				<?php endif;?>
			});
		} else {
			NexoAPI.Notify().warning( '<?php echo _s('Une erreur s\'est produite', 'nexo');?>', '<?php echo _s('Stripe ne s\'est pas chargé correctement. Le paiement via ce dernier ne fonctionnera pas. Veuillez rafraichir la page.', 'nexo');?>' );
		}
		<?php endif;?>
	}

	/**
	 * Proceed Payment
	 * @param object
	 * @return void
	**/

	this.proceedPayment		=	function( token, cashPaymentAmount, orderCode ) {
			
		token				=	_.extend( token, {
			'apiKey' 		: 	'<?php echo @$Options[ store_prefix() . 'nexo_stripe_secret_key' ];?>' ,
			'currency'		:	'<?php echo @$Options[ store_prefix() . 'nexo_currency_iso' ];?>' ,
			'amount'		:	cashPaymentAmount,
			'description'	:	'<?php echo _s( 'Compléter le paiement d\'une commande : ', 'nexo' );?>' + orderCode
		});

		$.ajax( '<?php echo site_url(array( 'rest', 'nexo', 'stripe', store_get_param( '?' ) ) );?>', {
			beforeSend : 	function(){
				
				__windowSplash.showSplash();
				
				NexoAPI.Notify().success( '<?php echo _s('Veuillez patienter', 'nexo');?>', '<?php echo _s('Paiement en cours...', 'nexo');?>' );
			},
			type		:	'POST',
			dataType	:	"json",
			data		:	token,
			success		: 	function( data ) {
				if( data.status == 'payment_success' ) {
					$this.scope.proceedPayment( 'stripe', false, function(){
						__windowSplash.hideSplash();
						NexoAPI.Notify().success( '<?php echo _s('Paiement effectué', 'nexo');?>', '<?php echo _s('Le paiement a été effectué.', 'nexo');?>' );
					});
				}
			},
			error		:	function( data ){
				data			=	$.parseJSON( data.responseText );

				if( typeof data.error != 'undefined' ) {
					var message		=	data.error.message;
				} else if( typeof data.httpBody != 'undefined' ) {
					var message		=	data.jsonBody.error.message;
				} else {
					var message		=	'N/A';
				}

				__windowSplash.hideSplash();
				
				NexoAPI.Notify().warning( '<?php echo _s('Une erreur s\'est produite', 'nexo');?>', '<?php echo _s('Le paiement n\'a pu être effectuée. Une erreur s\'est produite durant la facturation de la carte de crédit.<br>Le serveur à retourner cette erreur : ', 'nexo');?>' + message );
			}
		});
	}
	
	$this	=	this;
}]);

</script>
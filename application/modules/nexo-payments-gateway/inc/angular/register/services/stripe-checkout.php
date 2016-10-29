<?php
global $Options;
$this->config->load( 'nexo' );

/**
 '<?php echo @$Options[ store_prefix() . 'nexo_stripe_secret_key' ];?>' ,
			'currency'		:	'<?php echo @$Options[ store_prefix() . 'nexo_currency_iso' ];?>' ,
			**/
?>
<script>
/**
 *	Stripe Service
**/

tendooApp.service( '__stripeCheckout', [ '__windowSplash', function( __windowSplash ){
	this.open			=	function( name, description, amount, currency ){

		StripeHandler.open({
			name			: 	name,
			description		: 	description,
			amount			: 	amount,
			currency		:	currency
		});
	}
}]);

</script>

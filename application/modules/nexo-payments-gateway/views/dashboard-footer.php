<script>
NexoAPI.events.addFilter( 'nexo_payments_types_object', function( object ) {
		
	object		=	_.extend( object, _.object( [ 'stripe' ], [{
		text		:	'<?php echo _s( 'Stripe', 'nexo-payments-gateway' );?>',
		active		:	false,
		isCustom	:	true	
	}] ) );
	
	return object;
	
});

var	previous_text	=	null;

NexoAPI.events.addAction( 'pos_select_payment', function( data ) {
	
	if( previous_text == null ) {
		previous_text	=	data[0].defaultAddPaymentText;
	}
	
	if( data[1] == 'stripe' ) {
		// Disable payment for Stripe
		data[0].defaultAddPaymentText	=	'<?php echo _s( 'Facturer une carte', 'nexo' );?>';
	} else {
		data[0].defaultAddPaymentText	=	previous_text;
	}	
	
});

// Disable payment edition for Stripe
NexoAPI.events.addFilter( 'allow_payment_edition', function( data ) {
	if( data[1] == 'stripe' ) {
		NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Vous ne pouvez pas modifier un paiement déjà effectué, car une carte a déjà été débitée.', 'nexo' );?>' );
		
		return [ false, data[1] ];
	}
	
	return data;
});
</script>
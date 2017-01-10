<?php global $Options;?>

<script type="text/javascript">
    "use strict";
    <?php
    if (@$Options[ store_prefix() . 'disable_coupon' ] != 'yes' ):
    ?>

    NexoAPI.events.addFilter( 'nexo_payments_types_object', function( object ) {

    	object		=	_.extend( object, _.object( [ 'coupon' ], [{
    		text		:	'<?php echo _s( 'Coupon', 'nexo-payments-gateway' );?>',
    		active		:	false,
    		isCustom	:	true
    	}] ) );

    	return object;

    });

    NexoAPI.events.addAction( 'pos_select_payment', function( data ) {

    	var previous_text	=	data[0].defaultAddPaymentText;

    	if( data[1] == 'coupon' ) {
    		// Disable payment for Stripe
    		data[0].defaultAddPaymentText	=	'<?php echo _s( 'Utiliser un coupon', 'nexo-payments-gateway' );?>';
    	} else {
    		data[0].defaultAddPaymentText	=	previous_text;
    	}

    });

    // Disable payment edition for Stripe
    NexoAPI.events.addFilter( 'allow_payment_edition', function( data ) {
    	if( data[1] == 'coupon' ) {
    		NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo-payments-gateway' );?>', '<?php echo _s( 'Vous ne pouvez pas modifier la valeur d\'un coupon.', 'nexo' );?>' );

    		return [ false, data[1] ];
    	}

    	return data;
    });

    <?php endif;?>
</script>

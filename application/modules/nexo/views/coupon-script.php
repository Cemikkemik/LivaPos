<?php global $Options;?>
<script type="text/javascript">
    "use strict";

    var previous_text;

    <?php
    if (@$Options[ store_prefix() . 'disable_coupon' ] != 'yes' ):
    ?>

    NexoAPI.events.addAction( 'pay_box_loaded', function( $scope ){
        $scope.usedCoupon       =   [];
    });

    NexoAPI.events.addFilter( 'nexo_payments_types_object', function( object ) {

    	object		=	_.extend( object, _.object( [ 'coupon' ], [{
    		text		:	'<?php echo _s( 'Coupon', 'nexo' );?>',
    		active		:	false,
    		isCustom	:	true
    	}] ) );

    	return object;

    });

    NexoAPI.events.addAction( 'pos_select_payment', function( data ) {

    	previous_text	=	angular.isUndefined( previous_text ) ? data[0].defaultAddPaymentText : previous_text;

    	if( data[1] == 'coupon' ) {

    		// Disable payment for Stripe
    		data[0].defaultAddPaymentText	=	'<?php echo _s( 'Charger un coupon', 'nexo' );?>';
            data[0].couponCode              =   '';
            data[0].couponDetails           =   [];
            setTimeout(function(){
                angular.element( '.coupon-field' ).focus();
            },100);
    	} else {
    		data[0].defaultAddPaymentText	=	previous_text;
    	}

    });

    // Disable payment edition for Stripe
    NexoAPI.events.addFilter( 'allow_payment_edition', function( data ) {
    	if( data[1] == 'coupon' ) {
    		NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Vous ne pouvez pas modifier la valeur d\'un coupon.', 'nexo' );?>' );

    		return [ false, data[1] ];
    	}

    	return data;
    });

    NexoAPI.events.addAction( 'cart_remove_payment', function( data ) {
        if( data[3].namespace == 'coupon' ) {
            var index       =   _.indexOf( data[2].usedCoupon, parseInt( data[3].meta.coupon_id ) );

            // Delete coupon id
            data[2].usedCoupon.splice( index, 1 );

            // Delete coupon from used coupon details
            _.each( data[2].allCouponDetails, function( value, index ){
                if( value.ID == data[3].meta.coupon_id ) {
                    data[2].allCouponDetails.splice( index, 1 );
                }
            });
        };
    });

    // before submiting

    <?php endif;?>
</script>

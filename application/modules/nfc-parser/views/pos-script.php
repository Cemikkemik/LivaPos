<?php global $PageNow; ?>
<?php
if( $PageNow == 'nexo/registers/__use' ):
    $NFC        =   new NFC_Library;
?>
<script type="text/javascript">
function NFC_Cart_Listener() {
    this.items              =   <?php echo json_encode( $NFC->load_cart() );?>;
    this.saveCartStatus     =   function( items ){
        $.ajax({
            url             :   '<?php echo site_url([ 'rest', 'nfc_parser', 'update' ] );?>',
            type            :   'POST',
            data            :   {
                items       :   items,
                user_id     :   <?php echo User::id();?>
            }
        })
    }
    this.getCartStatus      =   function(){
        setInterval( function(){
            $.ajax({
                url             :   '<?php echo site_url([ 'rest', 'nfc_parser', 'retreive', '?user_id=' . User::id() ] );?>',
                type            :   'GET',
                success         :   function( data ){
                    _.each( data, function( value, key ){
                        var promo_start			= 	moment( value.SPECIAL_PRICE_START_DATE );
        				var promo_end			= 	moment( value.SPECIAL_PRICE_END_DATE );
                        value.PROMO_ENABLED	=	false;

        				if( promo_start.isBefore( v2Checkout.CartDateTime ) ) {
        					if( promo_end.isSameOrAfter( v2Checkout.CartDateTime ) ) {
        						value.PROMO_ENABLED	=	true;
        						MainPrice			=	NexoAPI.ParseFloat( value.PRIX_PROMOTIONEL );
        						Discounted			=	'<small><del>' + NexoAPI.DisplayMoney( NexoAPI.ParseFloat( value.PRIX_DE_VENTE ) ) + '</del></small>';
        						CustomBackground	=	'background:#DFF0D8';
        					}
        				}

                        value.DISCOUNT_TYPE		=	'percentage'; // has two type, "percent" and "flat";
    					value.DISCOUNT_AMOUNT	=	0;
    					value.DISCOUNT_PERCENT	=	0;
                    });

                    // console.log( _.difference( _.keys( v2Checkout.CartItems[0] ), _.keys( data[0] ) ) );
                    v2Checkout.CartItems    =   data;
                    v2Checkout.buildCartItemTable();
                    v2Checkout.refreshCart();
                }
            })
        },1000);
    }

    /**
     *  Delete Cache
     *  @param
     *  @return
    **/

    this.deleteCache        =   function(){
        $.ajax({
            url             :   '<?php echo site_url([ 'rest', 'nfc_parser', 'cache', '?user_id=' . User::id() ] );?>',
            type            :   'DELETE',
        });
    }

    // Auto Run
    this.getCartStatus();
}

$( document ).ready( function(){
    var NFC         =  new NFC_Cart_Listener;
    NexoAPI.events.addAction( 'add_to_cart', function( v2Checkout ){
        NFC.saveCartStatus( v2Checkout.CartItems );
    });

    NexoAPI.events.addAction( 'reduce_from_cart', function( v2Checkout ){
        NFC.saveCartStatus( v2Checkout.CartItems );
    });

    NexoAPI.events.addAction( 'submit_order', function(){
        NFC.deleteCache();
    });
});
</script>
<?php endif;?>

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

global $PageNow, $Options;

if( $PageNow == 'nexo/register/__use' || true ) {
?>
<script type="text/javascript">
"use strict";

NexoAPI.events.addAction( 'cart_refreshed', function(){
    $.ajax({
        url     :   '<?php echo site_url( array( 'rest', 'customer_display', 'save_data', store_get_param( '?' ) ) );?>',
        method  :   'POST',
        data    :   {
            items       :   v2Checkout.CartItems,
            register_id :   '<?php echo $this->uri->segment( $this->uri->total_segments() );?>',
            store_id    :   '<?php echo get_store_id();?>',
            vat         :   v2Checkout.CartVAT,
            paidSoFar   :   0,
            balance     :   0
        }
    })
});

var doThisAction    =   function( data ) {
    $.ajax({
        url     :   '<?php echo site_url( array( 'rest', 'customer_display', 'save_data', store_get_param( '?' ) ) );?>',
        method  :   'POST',
        data    :   {
            items       :   v2Checkout.CartItems,
            register_id :   '<?php echo $this->uri->segment( $this->uri->total_segments() );?>',
            store_id    :   '<?php echo get_store_id();?>',
            vat         :   v2Checkout.CartVAT,
            paidSoFar   :   data[0],
            balance     :   data[1]
        }
    })
}

NexoAPI.events.addAction( 'cart_remove_payment', doThisAction );

NexoAPI.events.addAction( 'cart_add_payment', doThisAction );



</script>
<?php
}

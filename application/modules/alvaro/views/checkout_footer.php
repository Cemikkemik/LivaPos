<?php
global $PageNow, $Options;
if( $PageNow == 'nexo/registers/__use' ):
?>
<script type="text/javascript">
var cartItems;
var commission_order;
var order_details        =    {
    appointment_id          :   '<?php echo $this->input->get( 'appointment_id' );?>',
    is_appointment          :   '<?php echo @$_GET[ 'appointment_id' ] != null ? 'yes' : 'no';?>'
};
    NexoAPI.events.addFilter( 'before_submit_order', function( order ){
        cartItems           =   v2Checkout.CartItems;
        commission_order    =   order;
        return order;
    });

    NexoAPI.events.addFilter( 'test_order_type', function( data ){
        var percentage  =   0;
        var items_ids   =   [];
        _.each( cartItems, function( item ) {
            items_ids.push({
                id      :   item.ID,
                qte     :   item.QTE_ADDED,
                price   :   item.PROMO_ENABLED ? item.PRIX_PROMOTIONEL : ( v2Checkout.CartShadowPriceEnabled ? item.SHADOW_PRICE : item.PRIX_DE_VENTE )

            });
            // if( typeof item.COMMISSION != 'undefined'  ) {
            //     percentage  +=  ( parseFloat( item.COMMISSION ) * item.QTE_ADDED );
            //     totalTime   +=  ( parseFloat( item.TIME ) * item.QTE_ADDED );
            // }
        });

        order_details.commission_amount       =   ( ( parseFloat( percentage ) * commission_order.TOTAL ) / 100 ),
        order_details.ref_author              =     '<?php echo User::id();?>',
        order_details.ref_order               =     data[1].order_id,
        order_details.date_creation           =     commission_order.DATE_CREATION,
        order_details.order_code                =     data[1].order_code,
        order_details.items                     =   items_ids;

        $.ajax( '<?php echo site_url([ 'rest', 'alvaro_rest', 'submit_commission?store_id=' . get_store_id() ] );?>', {
            type    :   'POST',
            data    :   order_details,
            success     :   function(){
                order_details.is_appointment = 'no';
            }
        });
        return data;
    })
</script>
<?php
endif;
?>

<?php
global $PageNow, $Options;
if( $PageNow == 'nexo/registers/__use' ):
?>
<script type="text/javascript">
var cartItems;
var commission_order;

    NexoAPI.events.addFilter( 'before_submit_order', function( order ){
        cartItems           =   v2Checkout.CartItems;
        commission_order    =   order;
        return order;
    });

    NexoAPI.events.addFilter( 'test_order_type', function( data ){

        let order_data        =    {
            appointment_id          :   '<?php echo $this->input->get( 'appointment_id' );?>',
            is_appointment          :   '<?php echo @$_GET[ 'appointment_id' ] != null ? 'yes' : 'no';?>',
            loaded_order            :   '<?php echo @$_GET[ 'load-order' ];?>'
        };

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

        order_data.commission_amount     =   ( ( parseFloat( percentage ) * commission_order.TOTAL ) / 100 ),
        order_data.ref_author            =   '<?php echo User::id();?>',
        order_data.ref_order             =   data[1].order_id,
        order_data.date_creation         =   commission_order.DATE_CREATION,
        order_data.order_code            =   data[1].order_code,
        order_data.items                 =   items_ids;
        order_data.order_type            =   data[1].order_type;
        order_data.ref_client           =   commission_order.REF_CLIENT;

        $.ajax( '<?php echo site_url([ 'rest', 'alvaro_rest', 'submit_commission?store_id=' . get_store_id() ] );?>', {
            type        :   'POST',
            data        :   order_data,
            success     :   function(){
                order_data.is_appointment = 'no';
            }
        });

        return data;
    });

    NexoAPI.events.addFilter( 'customers_dropdown', ( customers ) => {
        customers.forEach( ( customer ) => {
            customer.NOM    +=  ( customer.TEL != 0 ? ' &mdash; ' + customer.TEL : '' );
        });

        console.log( customers );
        return customers;
    })

    v2Checkout.checkItemsStock			=	function( items ) {

		var stockToReport			=	new Array;
		var minPercentage			=	100;
		var isEnabled				=	'<?php echo @$Options[ store_prefix() . 'nexo_enable_stock_warning' ];?>';

		if( isEnabled == 'yes' ) {
            console.log( items );
			_.each( items, function( value, key ) {
				if( parseInt( value.QUANTITE_RESTANTE ) <= parseInt( value.STOCK_ALERT ) ) {
					stockToReport.push({
						'id'		:	value.ID,
						'design'	:	value.DESIGN
					});
				}
			});

			if( stockToReport.length > 0 ) {
				$.ajax({
					url		:	'<?php echo site_url([ 'rest', 'nexo', 'stock_report', store_get_param( '?' )]);?>',
					method	:	'POST',
					data	:	{
						'reported_items'	:	stockToReport
					}
				});
			}
		}
	}

    
</script>
<?php
endif;
?>

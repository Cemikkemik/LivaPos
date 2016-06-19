<?php global $Options;?>
<script type="text/javascript">
"use strict";

var NexoSMS			=	new Object;
	NexoSMS.__CustomerNumber	=	'';
	NexoSMS.__SendSMSInvoice	=	null;
<?php if( in_array( 'twilio', array_keys( $this->config->item( 'nexo_sms_providers' ) ) ) && @$Options[ 'nexo_sms_service' ] == 'twilio' ):?>

NexoAPI.events.addAction( 'is_cash_order', function( data ) {
	if( NexoSMS.__SendSMSInvoice == true ) {
		if( NexoSMS.__CustomerNumber != '' ) {

			var v2Checkout		=	data[0];
			var order_details	=	data[1];
			var ItemsDetails	=	v2Checkout.CartTotalItems + '<?php echo _s(': produit(s) acheté(s)', 'nexo_sms');?>';

			var	message			=	'<?php echo @$Options[ 'site_name' ];?>\n' + ItemsDetails + '<?php echo _s( 'Total : ', 'nexo_sms' );?> <?php echo @$Options[ 'nexo_currency_iso' ];?>' + NexoAPI.Format( v2Checkout.CartValue ) + '\n<?php echo _s('Ref : ', 'nexo_sms' );?>' + order_details.order_code;
			var phones			=	[ NexoSMS.__CustomerNumber ];
			var from_number		=	'<?php echo @$Options[ 'nexo_twilio_from_number' ];?>';
			var	post_data		=	_.object( [ 'message', 'phones', 'from_number' ], [ message, phones, from_number ] );

			$.ajax( '<?php echo site_url( array( 'rest', 'twilio', 'send_sms' ) );?>/' +
				'<?php echo @$Options[ 'nexo_twilio_account_sid' ];?>/' +
				'<?php echo @$Options[ 'nexo_twilio_account_token' ];?>', {
				success	:	function( returned ) {
					if( _.isObject( returned ) ) {
						if( returned.status == 'success' ) {
							tendoo.notify.success( '<?php echo _s( 'La facture par SMS a été envoyée', 'nexo_sms' );?>', '<?php echo _s( 'Un exemplaire de la facture a été envoyée au numéro spécifié.', 'nexo_sms' );?>' );
						}
					}
				},
				error	:	function( returned ) {
					returned		=	$.parseJSON( returned.responseText );
					NexoAPI.Notify().warning( '<?php echo _s( 'Une erreur s\'est produite.', 'nexo_sms' );?>', '<?php echo _s( 'Le serveur à renvoyé une erreur durant l\'envoi du SMS :', 'nexo_sms' );?>' + returned.error.message );
				},
				type	:	'POST',
				data	:	post_data
			});
		} else {
			NexoAPI.Notify().warning( '<?php echo _s( 'Une erreur s\'est produite.', 'nexo_sms' );?>', '<?php echo _s( 'Vous devez specifier un numéro de téléphone. La facture par SMS n\'a pas pu être envoyée.', 'nexo_sms' );?>' );
		}
	} 
});

<?php endif;?>

/**
 * Set customer Number
**/

NexoAPI.events.addAction( 'select_customer', function( data ) {
	if( _.isObject( data ) ) {
		NexoSMS.__CustomerNumber		=	data[0].TEL;
	}
});

/**
 * Display Toggle
**/

NexoAPI.events.addFilter( 'pay_box_footer', function( data ) {
	return 	data + '<input type="checkbox" <?php echo @$Options[ 'nexo_enable_smsinvoice'] == 'yes' ? 'checked="checked"' : '';?> name="send_sms" send-sms-invoice data-toggle="toggle" data-width="150" data-height="35">';
});

/**
 * Load Paybox
**/

NexoAPI.events.addAction( 'pay_box_loaded', function( data ) {
	$('[send-sms-invoice]').bootstrapToggle({
      on: '<?php echo _s( 'Activer les SMS', 'nexo_sms' );?>',
      off: '<?php echo _s( 'Désactiver les SMS', 'nexo_sms' );?>'
    });
	
	// Ask whether to change customer number
	
	$( '[send-sms-invoice]' ).bind( 'change', function(){
		if( typeof $(this).attr( 'checked' ) != 'undefined' ) {
			NexoAPI.Bootbox().prompt({
			  title: "<?php echo _s( 'Veuillez définir le numéro à utiliser pour la facture par SMS', 'nexo_sms' );?>",
			  value: typeof NexoSMS.__CustomerNumber != 'undefined' ? NexoSMS.__CustomerNumber : '',
			  callback: function(result) {
				if (result !== null) {
				  NexoSMS.__CustomerNumber	=	result;
				}
			  }
			});
		}
	});
});

/**
 * Before Subiting order
**/

NexoAPI.events.addAction( 'submit_order', function() {
	NexoSMS.__SendSMSInvoice	=	typeof $( '[send-sms-invoice]').attr( 'checked' ) != 'undefined' ? true : false;
})

/** 
 * When Cart is Reset
**/

NexoAPI.events.addAction( 'reset_cart', function( v2Checkout ) {
	NexoSMS.__CustomerNumber	=	'';
	NexoSMS.__SendSMSInvoice	=	null;
});
</script>

<?php global $Options;?>
<?php if (@$Options[ store_prefix() . 'nexo_sms_service' ] == 'plivo'):?>
<script type="text/javascript" src="https://s3.amazonaws.com/plivosdk/web/plivo.min.js"/></script>
<script type="text/javascript">
"use strict";
// These two listeners are required before init
Plivo.onWebrtcNotSupported = webrtcNotSupportedAlert;
Plivo.onReady = onReady;
Plivo.init();
// Credentials
var username = 'johndoe12345';
var pass = 'XXXXXXXX';
Plivo.conn.login(username, pass);
</script>
<?php endif;?>
<script type="text/javascript">
"use strict";

var NexoSMS			=	new Object;
	NexoSMS.__CustomerNumber	=	'';
	NexoSMS.__SendSMSInvoice	=	null;
	NexoSMS.__CustomerName		=	'';
<?php if (in_array('twilio', array_keys($this->config->item('nexo_sms_providers'))) && @$Options[ store_prefix() . 'nexo_sms_service' ] == 'twilio'):?>

NexoAPI.events.addAction( 'is_cash_order', function( data ) {
	if( NexoSMS.__SendSMSInvoice == true ) {
		if( NexoSMS.__CustomerNumber != '' ) {

			var v2Checkout		=	data[0];
			var order_details	=	data[1];
			var ItemsDetails	=	v2Checkout.CartTotalItems + '<?php echo _s(': produit(s) acheté(s)', 'nexo_sms');?>';

			_.templateSettings = {
			  interpolate: /\{\{(.+?)\}\}/g
			};

			var	message			=	_.template( '<?php echo @$Options[ store_prefix() . 'nexo_sms_invoice_template' ];?>' );

			var SMS_object		=	{
				'site_name'		:	'<?php echo @$Options[ store_prefix() . 'site_name' ];?>',
				'order_code'	:	order_details.order_code,
				'order_topay'	:	'<?php echo @$Options[ store_prefix() . 'nexo_currency_iso' ];?> ' + NexoAPI.Format( v2Checkout.CartValue ),
				'name'			:	NexoSMS.__CustomerName
			};

			var SMS				=	message( SMS_object );

			var phones			=	[ NexoSMS.__CustomerNumber ];
			var from_number		=	'<?php echo @$Options[ store_prefix() . 'nexo_twilio_from_number' ];?>';
			var	post_data		=	_.object( [ 'message', 'phones', 'from_number' ], [ SMS, phones, from_number ] );
			var twilioUrl		=	'<?php echo site_url(array( 'rest', 'twilio', 'send_sms' ));?>/';

			$.ajax( twilioUrl +
				'<?php echo @$Options[ store_prefix() . 'nexo_twilio_account_sid' ];?>/' +
				'<?php echo @$Options[ store_prefix() . 'nexo_twilio_account_token' ];?>', {
				success	:	function( returned ) {
					if( _.isObject( returned ) ) {
						if( returned.status == 'success' ) {
							tendoo.notify.success( '<?php echo _s('La facture par SMS a été envoyée', 'nexo_sms');?>', '<?php echo _s('Un exemplaire de la facture a été envoyée au numéro spécifié.', 'nexo_sms');?>' );
						}
					}
				},
				error	:	function( returned ) {
					returned		=	$.parseJSON( returned.responseText );
					NexoAPI.Notify().warning( '<?php echo _s('Une erreur s\'est produite.', 'nexo_sms');?>', '<?php echo _s('Le serveur à renvoyé une erreur durant l\'envoi du SMS :', 'nexo_sms');?>' + returned.error.message );
				},
				type	:	'POST',
				data	:	post_data
			});
		} else {
			NexoAPI.Notify().warning( '<?php echo _s('Une erreur s\'est produite.', 'nexo_sms');?>', '<?php echo _s('Vous devez specifier un numéro de téléphone. La facture par SMS n\'a pas pu être envoyée.', 'nexo_sms');?>' );
		}
	}
});

<?php elseif (in_array('bulksms', array_keys($this->config->item('nexo_sms_providers'))) && @$Options[ store_prefix() . 'nexo_sms_service' ] == 'bulksms'):?>

NexoAPI.events.addAction( 'is_cash_order', function( data ) {
	if( NexoSMS.__SendSMSInvoice == true ) {
		if( NexoSMS.__CustomerNumber != '' ) {

			var v2Checkout		=	data[0];
			var order_details	=	data[1];
			var ItemsDetails	=	v2Checkout.CartTotalItems + '<?php echo _s(': produit(s) acheté(s)', 'nexo_sms');?>';

			_.templateSettings = {
			  interpolate: /\{\{(.+?)\}\}/g
			};

			var	message			=	_.template( '<?php echo @$Options[ store_prefix() . 'nexo_sms_invoice_template' ];?>' );
			var SMS_object		=	{
				'site_name'		:	'<?php echo @$Options[ store_prefix() . 'site_name' ];?>',
				'order_code'	:	order_details.order_code,
				'order_topay'	:	'<?php echo @$Options[ store_prefix() . 'nexo_currency_iso' ];?> ' + NexoAPI.Format( v2Checkout.CartValue ),
				'name'			:	NexoSMS.__CustomerName
			};

			var SMS				=	message( SMS_object );

			var phones			=	[ NexoSMS.__CustomerNumber ];
			var from_number		=	'<?php echo @$Options[ store_prefix() . 'nexo_twilio_from_number' ];?>';
			var	post_data		=	_.object( [
				'message',
				'phones',
				'user_name',
				'user_pwd',
				'http_url',
				'port'
			], [
				SMS,
				phones,
				'<?php echo @$Options[ store_prefix() . 'nexo_bulksms_username' ];?>',
				'<?php echo @$Options[ store_prefix() . 'nexo_bulksms_password' ];?>',
				'<?php echo @$Options[ store_prefix() . 'nexo_bulksms_url' ];?>',
				'<?php echo @$Options[ store_prefix() . 'nexo_bulksms_port' ];?>'
			 ] );
			var url				=	'<?php echo site_url(array( 'rest', 'bulksms', 'send_sms' ));?>/';

			$.ajax( url, {
				success	:	function( returned ) {
					if( _.isObject( returned ) ) {
						if( returned.status == 'success' ) {
							tendoo.notify.success( '<?php echo _s('La facture par SMS a été envoyée', 'nexo_sms');?>', '<?php echo _s('Un exemplaire de la facture a été envoyée au numéro spécifié.', 'nexo_sms');?>' );
						}
					}
				},
				error	:	function( returned ) {
					returned		=	$.parseJSON( returned.responseText );
					NexoAPI.Notify().warning( '<?php echo _s('Une erreur s\'est produite.', 'nexo_sms');?>', '<?php echo _s('Le serveur à renvoyé une erreur durant l\'envoi du SMS :', 'nexo_sms');?>' + returned.error.message );
				},
				type	:	'POST',
				data	:	post_data
			});
		} else {
			NexoAPI.Notify().warning( '<?php echo _s('Une erreur s\'est produite.', 'nexo_sms');?>', '<?php echo _s('Vous devez specifier un numéro de téléphone. La facture par SMS n\'a pas pu être envoyée.', 'nexo_sms');?>' );
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
		NexoSMS.__CustomerName			=	data[0].NOM
	}
});

/**
 * Display Toggle
**/

NexoAPI.events.addFilter( 'pay_box_footer', function( data ) {
	return 	data + '<input type="checkbox" <?php echo @$Options[ store_prefix() . 'nexo_enable_smsinvoice'] == 'yes' ? 'checked="checked"' : '';?> name="send_sms" send-sms-invoice data-toggle="toggle" data-width="150" data-height="35">';
});

/**
 * Load Paybox
**/

NexoAPI.events.addAction( 'pay_box_loaded', function( data ) {
	$('[send-sms-invoice]').bootstrapToggle({
      on: '<?php echo _s('Activer les SMS', 'nexo_sms');?>',
      off: '<?php echo _s('Désactiver les SMS', 'nexo_sms');?>'
    });

	// Ask whether to change customer number

	$( '[send-sms-invoice]' ).bind( 'change', function(){
		if( typeof $(this).attr( 'checked' ) != 'undefined' ) {
			NexoAPI.Bootbox().prompt({
			  title: "<?php echo _s('Veuillez définir le numéro à utiliser pour la facture par SMS', 'nexo_sms');?>",
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

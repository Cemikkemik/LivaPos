<?php
global $Options;

if (@$Options[ store_prefix() . 'nexo_enable_stripe' ] != 'no'):
?>
/**
 * Load Stripe Payment
**/

$scope.openStripePayment	=	function(){

	if( parseFloat( $scope.paidAmount ) == isNaN() || parseFloat( $scope.paidAmount ) <= 0 || typeof $scope.paidAmount == 'undefined' ) {
    	NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo-payments-gateway' );?>', '<?php echo _s( 'Le montant spécifié est incorrecte.', 'nexo-payments-gateway' );?>' )
        return false;
    }

    <?php if( in_array(strtolower(@$Options[ store_prefix() . 'nexo_currency_iso' ]), $this->config->item('nexo_supported_currency')) ) {
		?>
	var	CartToPayLong		=	numeral( $scope.paidAmount ).multiply(100).value();
		<?php
	} else {
		?>
	var	CartToPayLong		=	NexoAPI.Format( $scope.paidAmount, '0.00' );
		<?php
	};?>

    $scope.stripeDetails	=	{
    	name				:	'<?php echo _s( 'Paiement d\'une commande', 'nexo-payments-gateway' );?>',
        description			:	'<?php echo sprintf( _s( 'Vous venez d\'effectuer un paiement dans la boutique %s. Merci', 'nexo-payments-gateway' ), $Options[ store_prefix() . 'site_name' ] );?>',
        amount				:	CartToPayLong,
        currency			:	'<?php echo @$Options[ store_prefix() . 'nexo_currency_iso' ];?>'
    };

    __stripeCheckout.open(
    	$scope.stripeDetails.name,
        $scope.stripeDetails.description,
        $scope.stripeDetails.amount,
        $scope.stripeDetails.currency
	);
};

// Register events when payment is proceeded

NexoAPI.events.addAction( 'stripe_charged', function( token ) {

    token.apiKey		=	'<?php echo @$Options[ store_prefix() . 'nexo_stripe_secret_key' ];?>';
    token				=	_.extend( token, $scope.stripeDetails );

	$.ajax( '<?php echo site_url(array( 'rest', 'nexo', 'stripe', store_get_param( '?' ) ) );?>', {
        beforeSend : 	function(){

            __windowSplash.showSplash();

            NexoAPI.Notify().success( '<?php echo _s('Veuillez patienter', 'nexo-payments-gateway');?>', '<?php echo _s('Paiement en cours...', 'nexo-payments-gateway');?>' );
        },
        type		:	'POST',
        dataType	:	"json",
        data		:	token,
        success		: 	function( data ) {
            if( data.status == 'payment_success' ) {

                $scope.addPayment( 'stripe', $scope.paidAmount );
                $scope.refreshBox();

                __windowSplash.hideSplash();
				NexoAPI.Notify().success( '<?php echo _s('Paiement effectué', 'nexo-payments-gateway');?>', '<?php echo _s('Le paiement a été effectué.', 'nexo-payments-gateway');?>' );
            }
        },
        error		:	function( data ){
            data			=	$.parseJSON( data.responseText );

            if( typeof data.error != 'undefined' ) {
                var message		=	data.error.message;
            } else if( typeof data.httpBody != 'undefined' ) {
                var message		=	data.jsonBody.error.message;
            } else {
                var message		=	'N/A';
            }

            __windowSplash.hideSplash();

            NexoAPI.Notify().warning( '<?php echo _s('Une erreur s\'est produite', 'nexo-payments-gateway');?>', '<?php echo _s('Le paiement n\'a pu être effectuée. Une erreur s\'est produite durant la facturation de la carte de crédit.<br>Le serveur à retourner cette erreur : ', 'nexo-payments-gateway');?>' + message );
        }
    });
});
<?php endif; ?>

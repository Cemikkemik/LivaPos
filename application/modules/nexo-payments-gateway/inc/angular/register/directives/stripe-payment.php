<script>
tendooApp.directive( 'stripePayment', function(){

	HTML.body.add( 'angular-cache' );

	HTML.query( 'angular-cache' )
	.add( 'h3.text-center' )
	.each( 'style', 'margin:0px;margin-bottom:10px;' )
	.textContent	=	'<?php echo _s( 'Stripe Payment', 'nexo-payments-gateway' );?>';

	HTML.query( 'h3.text-center' )
	.add( 'span.status' )
	.each( 'ng-show', 'editModeEnabled' )

	HTML.query( 'angular-cache' )
	.add( 'div.input-group.input-group-lg.payment-field-wrapper>span.input-group-addon' )
	.textContent	=	'<?php echo _s( 'Montant du paiement', 'nexo-payments-gateway' );?>';

	HTML.query( '.payment-field-wrapper' )
	.add( 'input.form-control.stripe-field' )
	.each( 'ng-model', 'paidAmount' )
	.each( 'ng-focus', 'bindKeyBoardEvent( $event )' )
	.each( 'placeholder', '<?php echo _s( 'Définir un montant', 'nexo-payments-gateway' );?>' );

	HTML.query( '.payment-field-wrapper' )
	.add( 'span.input-group-btn.paymentButtons>button.btn.addPaymentButton' )
	.each( 'ng-click', 'openStripePayment()' )
	.each( 'ng-disabled', 'addPaymentDisabled' )
	.textContent	=	'{{ defaultAddPaymentText }}';

	HTML.query( '.paymentButtons' )
	.add( 'button.btn.btn-default' )
	.each( 'ng-show', 'showCancelEditionButton' )
	.each( 'ng-click', 'cancelPaymentEdition()' )
	.add( 'i.fa.fa-remove' );

	// Stripe content
	HTML.query( 'angular-cache' )
	.add( 'br' );

	HTML.query( 'angular-cache' )
	.add( 'div.alert.alert-info.text-center' )
	.textContent		=	'<?php echo _s( 'Vous devez facturer une carte avec Stripe, avant de l\'ajouter comme paiement', 'nexo-payments-gateway' );?>';

	HTML.query( 'angular-cache' )
	.add( 'keyboard' )
	.each( 'input_name', 'stripe-field' )
	.each( 'keyinput', 'keyboardInput' );

	HTML.query( '.pay-wrapper' )
	.add( 'input.form-control.pay-field' )
	.each( 'ng-model', 'cashPaymentAmount' )
	.each( 'ng-change', 'controlCashAmount()' );

	angular.element( '.addPaymentButton' )
	.addClass( 'btn-{{defaultAddPaymentClass}}' );

	var DOM		=	angular.element( 'angular-cache' ).html();

	angular.element( 'angular-cache' ).remove();

	return {
		template 	:	DOM
	}
});
</script>

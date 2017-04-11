<script>
tendooApp.directive( 'defaultPayment', function(){

	HTML.body.add( 'angular-cache' );

	HTML.query( 'angular-cache' )
	.add( 'h3.text-center' )
	.each( 'style', 'margin:0px;margin-bottom:10px;' )
	.textContent	=	'{{ defaultSelectedPaymentText }}';

	HTML.query( 'h3.text-center' )
	.add( 'span.status' )
	.each( 'ng-show', 'editModeEnabled' )

	HTML.query( 'angular-cache' )
	.add( 'div.input-group.input-group-lg.payment-field-wrapper>span.input-group-addon.hidden-sm.hidden-xs' )
	.textContent	=	'<?php echo _s( 'Montant du paiement', 'nexo' );?>';

	HTML.query( '.payment-field-wrapper' )
	.add( 'input.form-control' )
	.each( 'ng-model', 'paidAmount' )
	.each( 'ng-focus', 'bindKeyBoardEvent( $event )' )
	.each( 'placeholder', '<?php echo _s( 'Définir un montant', 'nexo' );?>' );

	HTML.query( '.payment-field-wrapper' )
	.add( 'span.input-group-btn.paymentButtons>button.btn.addPaymentButton' )
	.each( 'ng-click', 'addPayment( defaultSelectedPaymentNamespace, paidAmount )' )
	.each( 'ng-disabled', 'addPaymentDisabled' )
	.textContent	=	'{{ defaultAddPaymentText }}';

	HTML.query( '.paymentButtons' )
	.add( 'button.btn.btn-default' )
	.each( 'ng-show', 'showCancelEditionButton' )
	.each( 'ng-click', 'cancelPaymentEdition()' )
	.add( 'i.fa.fa-remove' );

	angular.element( '.addPaymentButton' ).addClass( 'btn-{{defaultAddPaymentClass}}' );

	var DOM		=	angular.element( 'angular-cache' ).html();

	angular.element( 'angular-cache' ).remove();

	return {
		template 	:	DOM,
		scope		:	{
			payment							:	'=',
			paidAmount						:	'=',
			addPayment						:	'=',
			bindKeyBoardEvent				:	'=',
			cancelPaymentEdition			:	'=',
			defaultAddPaymentText			:	'=',
			data 							:	'=',
			defaultAddPaymentClass			:	'=',
			defaultSelectedPaymentText		:	'=',
			defaultSelectedPaymentNamespace	:	'=',
			showCancelEditionButton			:	'='
		}
	}
});
</script>

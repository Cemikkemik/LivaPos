<script>
tendooApp.directive( 'couponPayment', function(){

    HTML.body.add( 'angular-cache' );

	HTML.query( 'angular-cache' )
	.add( 'h3.text-center' )
	.each( 'style', 'margin:0px;margin-bottom:10px;' )
	.textContent	=	'<?php echo _s( 'Appliquer un coupon', 'nexo-payments-gateway' );?>';

    HTML.query( 'angular-cache' )
	.add( 'div.input-group.input-group-lg.payment-field-wrapper>span.input-group-addon' )
	.textContent	=	'<?php echo _s( 'Code du coupon', 'nexo-payments-gateway' );?>';

    HTML.query( '.payment-field-wrapper' )
	.add( 'input.form-control.stripe-field' )
	.each( 'ng-model', 'couponCode' )
	.each( 'ng-focus', 'bindKeyBoardEvent( $event )' )
	.each( 'placeholder', '<?php echo _s( 'Spécifier le code du coupon', 'nexo-payments-gateway' );?>' );

    HTML.query( '.payment-field-wrapper' )
	.add( 'span.input-group-btn.paymentButtons>button.btn.addPaymentButton' )
	.each( 'ng-click', 'checkCoupon()' )
	.each( 'ng-disabled', 'addPaymentDisabled' )
	.textContent	=	'{{ defaultAddPaymentText }}';

    angular.element( '.addPaymentButton' )
	.addClass( 'btn-{{defaultAddPaymentClass}}' );

    var DOM		=	angular.element( 'angular-cache' ).html();
	angular.element( 'angular-cache' ).remove();

    return {
		template 	:	DOM
	}

    // ,
    // scope		:	{
    // 	payment							:	'=',
    // 	paidAmount						:	'=',
    // 	addPayment						:	'=',
    // 	bindKeyBoardEvent				:	'=',
    // 	cancelPaymentEdition			:	'=',
    // 	defaultAddPaymentText			:	'=',
    // 	defaultAddPaymentClass			:	'=',
    // 	defaultSelectedPaymentText		:	'=',
    // 	defaultSelectedPaymentNamespace	:	'=',
    // 	showCancelEditionButton			:	'='
    // }
});
</script>

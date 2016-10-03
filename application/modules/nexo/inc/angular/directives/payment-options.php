<script>
/**
 * Create Controller
**/

tendooApp.directive( 'cashPayment', function() {
	
	HTML.add('div.cache-angular');		
	
	HTML.query( '.cache-angular' )
		.add( 'form>div.input-group.pay-wrapper>span.input-group-addon{<?php echo _s( 'Valeur du paiement', 'nexo' );?>}' );
	
	HTML.query( '.cache-angular' )
		.add( 'br' );
		
	HTML.query( '.cache-angular' )
		.add( 'input.btn.btn-primary' )
		.each( 'value', '<?php echo _s( 'Payer', 'nexo' );?>' )
		.each( 'type', 'button' )
		.each( 'ng-disabled', 'paymentDisabled' )
		.each( 'ng-click', 'proceedPayment( "cash" )' );
		
	HTML.query( '.cache-angular' )
		.add( 'br' );
		
	HTML.query( '.cache-angular' )
		.add( 'br' );
		
	HTML.query( '.pay-wrapper' )
		.add( 'input.form-control.pay-field' )
		.each( 'ng-model', 'cashPaymentAmount' )
		.each( 'ng-change', 'controlCashAmount()' );
	
	var template	=	$( '.cache-angular' ).html();
	
	$( '.cache-angular' ).remove();
	
	return {
		template	:	template
	}
});

tendooApp.directive( 'bankPayment', function() {
	
	HTML.add('div.cache-angular');		
	
	HTML.query( '.cache-angular' )
		.add( 'form>div.input-group.pay-wrapper>span.input-group-addon{<?php echo _s( 'Valeur du virement', 'nexo' );?>}' );
	
	HTML.query( '.cache-angular' )
		.add( 'br' );
		
	HTML.query( '.cache-angular' )
		.add( 'input.btn.btn-primary' )
		.each( 'value', '<?php echo _s( 'Valider le virement', 'nexo' );?>' )
		.each( 'type', 'button' )
		.each( 'ng-disabled', 'paymentDisabled' )
		.each( 'ng-click', 'proceedPayment( "bank" )' );
		
	HTML.query( '.cache-angular' )
		.add( 'br' );
		
	HTML.query( '.cache-angular' )
		.add( 'br' );
		
	HTML.query( '.pay-wrapper' )
		.add( 'input.form-control.pay-field' )
		.each( 'ng-model', 'cashPaymentAmount' )
		.each( 'ng-change', 'controlCashAmount()' );
	
	var template	=	$( '.cache-angular' ).html();
	
	$( '.cache-angular' ).remove();
	
	return {
		template	:	template
	}
});

tendooApp.directive( 'stripePayment', function() {
	
	HTML.add('div.cache-angular');		
	
	HTML.query( '.cache-angular' )
		.add( 'form>div.input-group.pay-wrapper>span.input-group-addon{<?php echo _s( 'Facturer une carte', 'nexo' );?>}' );
	
	HTML.query( '.cache-angular' )
		.add( 'br' );
		
	HTML.query( '.cache-angular' )
		.add( 'input.btn.btn-primary' )
		.each( 'value', '<?php echo _s( 'Facturer', 'nexo' );?>' )
		.each( 'type', 'button' )
		.each( 'ng-disabled', 'paymentDisabled' )
		.each( 'ng-click', 'loadStripeCheckout()' );
		
	HTML.query( '.cache-angular' )
		.add( 'br' );
		
	HTML.query( '.cache-angular' )
		.add( 'br' );
		
	HTML.query( '.pay-wrapper' )
		.add( 'input.form-control.pay-field' )
		.each( 'ng-model', 'cashPaymentAmount' )
		.each( 'ng-change', 'controlCashAmount()' );
	
	var template	=	$( '.cache-angular' ).html();
	
	$( '.cache-angular' ).remove();
	
	return {
		template	:	template
	}
});
</script>
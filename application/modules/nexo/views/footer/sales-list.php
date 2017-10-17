<?php if (@$Options[ store_prefix() . 'nexo_enable_stripe' ] != 'no'):?>
<script type="text/javascript" src="https://checkout.stripe.com/checkout.js"></script>
<script type="text/javascript">
	'use strict';
	// Close Checkout on page navigation:
	$(window).on('popstate', function() {
		// alert( 'POP' );
		//get your angular element
		  var elem = angular.element(document.querySelector('[ng-controller="nexo_order_list"]'));

		  //get the injector.
		  var injector = elem.injector();

		  //get the service.
		  // var __stripeCheckout = injector.get( '__stripeCheckout' );

		  //update the service.
		  // __stripeCheckout.handler.close();

		  // elem.scope().$apply();
	});
</script>
<?php endif;?>

<?php include_once( MODULESPATH . '/nexo/inc/angular/order-list/include.php' );?>
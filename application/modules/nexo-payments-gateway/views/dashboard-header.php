<?php
global $Options;
?>
<?php if (@$Options[ store_prefix() . 'nexo_enable_stripe' ] != 'no'):?>
<script src="https://checkout.stripe.com/checkout.js"></script>

<script type="text/javascript">

var StripeHandler 	= 	StripeCheckout.configure({
	key: '<?php echo @$Options[ store_prefix() . 'nexo_stripe_publishable_key' ];?>',
	image: '<?php echo img_url('nexo') . '/nexopos-logo.png';?>',
	locale: 'auto',
	token: function(token) {
		NexoAPI.events.doAction( 'stripe_charged', token  );
	}
	<?php if ($this->config->item('nexo_test_mode') == false):?>
	,zipCode : true,
	billingAddress : true
	<?php endif;?>
});

// Close Checkout on page navigation:
window.addEventListener( 'popstate', function() {
	StripeHandler.close();
});
</script>

<?php endif;?>

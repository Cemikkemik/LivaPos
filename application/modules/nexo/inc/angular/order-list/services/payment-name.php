<?php $this->config->load( 'nexo' );?>
<script>
tendooApp.service( '__paymentName', function(){
	this.func		=	function( x ){
		var payment_name	=	<?php echo json_encode( $this->config->item( 'nexo_payments_types' ) );?>;
		return _.propertyOf( payment_name )( x );
	};
});
</script>
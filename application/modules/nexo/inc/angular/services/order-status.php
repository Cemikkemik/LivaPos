<?php $this->config->load( 'nexo' );?>
<script>
tendooApp.service( '__orderStatus', function(){
	this.func		=	function( x ){
		var orders_status	=	<?php echo json_encode( $this->config->item( 'nexo_order_types' ) );?>;
		return _.propertyOf( orders_status )( x );
	};
});
</script>
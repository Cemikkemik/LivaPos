<script>
tendooApp.filter( 'moneyFormat', function(){
	return function( input ) {
		return NexoAPI.DisplayMoney( input );
	};
});
</script>
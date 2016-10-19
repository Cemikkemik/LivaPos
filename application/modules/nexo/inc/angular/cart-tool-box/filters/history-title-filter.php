<script>
/***
 * Order History Filter
 * Checks whether a title is empty an return a text
**/

tendooApp.filter( 'titleFilter', function(){
	return function( filter ) {
		if( typeof filter == 'undefined' || filter == '' ) {			
			return '<?php echo _s( 'Commande sans désignation', 'nexo' );?>';
		}
		return filter;
	}
});
</script>
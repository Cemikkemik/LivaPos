<div id="tutorial-wrapper" class="box-body"  style="padding:0;">
	<p class="text-center" style="line-height:45px;"><?php _e('Chargement...', 'nexo');?></p>
</div>
<script>
"use strict";

var NexoTutorials	=	new function(){
	this.Get		=	function(){
		$.ajax( '<?php echo site_url(array( 'nexo', 'feed' ));?>', {
			error		: 	function(){
				$( '#tutorial-wrapper' ).find( 'p' ).html( '<?php echo addslashes(__('Une erreur s\'est produite durant la récupération des données', 'nexo'));?>' );
			},
			success 	:	function( data ) {
				if( ! _.isUndefined( data.item ) ) {
					$( '#tutorial-wrapper' ).find( 'p' ).remove();
					$( '#tutorial-wrapper' ).append( '<ul class="nav nav-stacked"></ul>' );
					_.each( data.item, function( value, key ) {
						if( key <= 4 ) {
							$( '#tutorial-wrapper ul' ).append( '<li><a href="' + value.link + '">' + value.title + '</a></li>' );
						} else {
							return;
						}
					});
				}
			}
		});
	};
};
// When doc is ready
$( document ).ready(function(e) {
    NexoTutorials.Get();
});
</script>
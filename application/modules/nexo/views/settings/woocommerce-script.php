<?php
global $Options;
?>
<hr>
<h4><?php echo sprintf( 
	__( 'Status de la connectivité : %s', 'nexo' ), 
	in_array( @$Options[ 'nexo_woo_connected' ], array( null, false ) ) ? __( 'Déconnecté', 'nexo' ) : __( 'Connecté', 'nexo' ) 
);?></h4>

<input class="btn btn-primary connect-to-woocommerce" value="<?php _e( 'Connection', 'nexo' );?>">
<br>
<br>
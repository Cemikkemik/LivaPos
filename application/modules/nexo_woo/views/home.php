<?php
$this->Gui->col_width( 1, 2 );
$this->Gui->col_width( 2, 2 );

$this->Gui->add_meta( array(
	'col_id'	=>	1,
	'type'		=>	'box',
	'title'		=>	__( 'Réglages', 'nexo_woo' ),
	'gui_saver'	=>	true,
	'namespace'	=>	'nexo_woo_settings',
	'footer'	=>	array(
		'submit'	=>	array(
			'label'	=>	__( 'Sauvegarder les réglages', 'nexo_woo' )
		)
	)
) );

$this->Gui->add_meta( array(
	'col_id'	=>	2,
	'type'		=>	'box',
	'title'		=>	__( 'Synchronisation', 'nexo_woo' ),
	'gui_saver'	=>	true,
	'namespace'	=>	'nexo_woo_settings_2'
) );

$this->Gui->add_item( array(
	'type'	=>	'text',
	'name'	=>	'woocommerce_url',
	'label'	=>	__( 'Url vers le site web', 'nexo_woo' ),
	'description'	=>	__( 'Veuillez fournir l\'adresse principale du site web utilisant WooCommerce', 'nexo_woo' )
), 'nexo_woo_settings', 1 );

$this->Gui->add_item( array(
	'type'	=>	'text',
	'name'	=>	'woocommerce_cusommer_key',
	'label'	=>	__( 'Clé du consommateur', 'nexo_woo' ),
	'description'	=>	sprintf( __( 'Veuillez fournir la clé du consommateur. <a href="%s">Lisez la documentation</a> de WooCommerce pour générer de nouvelles clés.', 'nexo_woo' ), 'https://docs.woothemes.com/document/woocommerce-rest-api/' )
), 'nexo_woo_settings', 1 );

$this->Gui->add_item( array(
	'type'	=>	'text',
	'name'	=>	'woocommerce_secret_key',
	'label'	=>	__( 'Clé secrète', 'nexo_woo' ),
	'description'	=>	sprintf( __( 'Veuillez fournir la clé secrète. <a href="%s">Lisez la documentation</a> de WooCommerce pour générer de nouvelles clés.', 'nexo_woo' ), 'https://docs.woothemes.com/document/woocommerce-rest-api/' )

), 'nexo_woo_settings', 1 );

ob_start();

$this->load->config( 'nexo_woo' );

global $Options;
if( @$Options[ 'woocommerce_url' ] != null && @$Options[ 'woocommerce_url' ] != null && @$Options[ 'woocommerce_url' ] != null ) {
?>
<input type="button" value="<?php _e( 'Synchroniser avec la boutique' );?>" class="sync_btn btn btn-primary" />
<script>
var WooSync		=	new function(){
	/**
	 * Sync Categories
	**/
	
	this.syncCategories	=	function(){
		$.ajax( '<?php echo site_url( array( 'rest', 'woocommerce', 'sync_categories' ) );?>', {
			beforeSend: function(xhr) { 
				xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_url_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_url' ];?>'); 
				xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_key_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_cusommer_key' ];?>'); 
				xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_secret_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_secret_key' ];?>'); 
			},
			type		:	'GET'
		}); 	
	}

	/**
	 * Boot Syncing
	**/
	
	this.boot	=	function(){
		this.syncCategories();
	}
}
$( document ).ready(function(e) {
	WooSync.syncCategories();
	$( '.sync_btn' ).bind( 'click', function(){
		WooSync.syncCategories();
	});
	
});	
</script>
<?php
}

$content	=	ob_get_clean();

$this->Gui->add_item( array(
	'type'	=>	'dom',
	'content'	=>	$content
), 'nexo_woo_settings_2', 2 );

$this->Gui->output();
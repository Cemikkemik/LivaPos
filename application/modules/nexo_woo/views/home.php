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
	'title'		=>	__( 'NexoPOS &rarr; WooCommerce', 'nexo_woo' ),
	'gui_saver'	=>	true,
	'namespace'	=>	'nexo_woo_settings_2'
) );

/*$this->Gui->add_meta( array(
	'col_id'	=>	2,
	'type'		=>	'box',
	'title'		=>	__( 'WooCommerce &rarr; NexoPOS', 'nexo_woo' ),
	'gui_saver'	=>	true,
	'namespace'	=>	'nexo_woo_settings_3'
) );*/

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
$this->load->model( 'Nexo_Shipping' );

$shippings		=	get_instance()->Nexo_Shipping->get_shipping();


global $Options;
if( @$Options[ 'woocommerce_url' ] != null && @$Options[ 'woocommerce_url' ] != null && @$Options[ 'woocommerce_url' ] != null ) {
?>
<form>
<div class="input-group">
  <span class="input-group-addon" id="basic-addon1"><?php _e( 'Collection a exclure', 'nexo_woo' );?></span>
  <select name="exclude" class="form-control" placeholder="Username" aria-describedby="basic-addon1">
	<?php if( ! $shippings ):?>
    	<option value="false"><?php _e( 'Aucune collection disponible', 'nexo_woo' );?></option>
	<?php else:?>
    	<?php if( is_array( $shippings ) ):?>
        	<option value="all"><?php _e( 'Tout inclure', 'nexo_woo' );?></option>
        	<?php foreach( $shippings as $ship ):?>
            	<option value="<?php echo $ship[ 'ID' ];?>"><?php echo $ship[ 'TITRE' ];?></option>
            <?php endforeach;?>
        <?php endif;?>
    <?php endif;?>
  </select>
</div>

<div class="checkbox">
<label>
  <input type="checkbox" class="delete_on_woocommerce" value="true"> <?php _e( 'Supprimer tout le contenu de la boutique', 'nexo_woo' );?>
</label>
</div>
<?php echo tendoo_info( __( 'Si vous ne supprimez pas le contenu existant, vous devez-vous assurez qu\'il n\'existe pas de catégorie ou de produit similaire', 'nexo_woo' ) );?>
<input type="button" value="<?php _e( 'Synchroniser avec la boutique', 'nexo_woo' );?>" class="sync_btn btn btn-primary" />
</form>
<br />
<br />
<ul class="list-group sync_list" style="margin-bottom:0;">
</ul>
<script>

"use strict";

var NexoSync		=	new function(){
	
	this.proceedDelete	=	function(){
		
		this.Exclude		=	$( '[name="exclude"]' ).val();		
		var WooCategories	=	[];
		var WooItems		=	[];
		var ItemWave		=	0;
		
		if( this.Exclude  == 'false' ) {
			bootbox.alert( '<?php echo _s( 'La synchronisation ne peut pas fonctionner, car NexoPOS ne contient aucune données exploitables', 'nexo_woo' );?>' );
			return false;
		}
		
		
		
		/**
		 * Get WooCategories
		**/
		
		var GetWooCategories	=	function(){
			$.ajax( '<?php echo site_url( array( 'rest', 'woocommerce', 'sync_get_woo_categories' ) );?>', {
				beforeSend: function(xhr) { 
					xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_url_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_url' ];?>'); 
					xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_key_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_cusommer_key' ];?>'); 
					xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_secret_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_secret_key' ];?>'); 
				},
				type		:	'GET',
				success		:	function( data ) {				
					WooCategories		=	data;
					
					if( ! _.isEmpty( data ) && $( '.delete_on_woocommerce' ).is( ':checked' ) ) {
						RunWooCategoriesDelete();
					} else {
						// Now Get WooItems
						GetWooItems();
					}
					
					
				},
				error		:	function(){
					$( '.sync_list' ).append( '<li class="list-group-item"><?php echo _s( 'Une erreur s\'est produite... ', 'nexo_woo' );?></li>' );
				},
				dataType	:	"json"
			});
		}
		
		/**
		 * Get Items
		**/
		
		var GetWooItems		=	function(){
			ItemWave++;
			$.ajax( '<?php echo site_url( array( 'rest', 'woocommerce', 'sync_get_woo_items' ) );?>', {
				beforeSend: function(xhr) { 
					xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_url_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_url' ];?>'); 
					xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_key_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_cusommer_key' ];?>'); 
					xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_secret_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_secret_key' ];?>'); 
				},
				type		:	'GET',
				success		:	function( data ) {				
					
					WooItems		=	data.products;
					
					if( ! _.isEmpty( data ) && $( '.delete_on_woocommerce' ).is( ':checked' ) ) {
						RunWooItemDelete();
					} else {
						NexoSync.syncCategories();
					}
					
				},
				error		:	function(){
					$( '.sync_list' ).append( '<li class="list-group-item"><?php echo _s( 'Une erreur s\'est produite... ', 'nexo_woo' );?></li>' );
				},
				dataType	:	"json"
			});
		};
		
		var RunWooCategoriesDelete		=	function(){
						
			if( $( '.delete_category_notice' ).length == 0 ){
				$( '.sync_list' ).append( '<li class="list-group-item delete_category_notice"><?php echo _s( 'Suppression des categories. Restant = ', 'nexo_woo' );?> ' + WooCategories.length + '</li>' );
			} else {
				$( '.delete_category_notice' ).html( '<?php echo _s( 'Suppression des categories. Restant = ', 'nexo_woo' );?> ' + WooCategories.length );
			}
			
			if( WooCategories.length > 0 ) {
			
			
			$.ajax( '<?php echo site_url( array( 'rest', 'woocommerce', 'sync_delete_woo_categories' ) );?>/' + WooCategories[0].id, {
				beforeSend: function(xhr) { 
					xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_url_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_url' ];?>'); 
					xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_key_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_cusommer_key' ];?>'); 
					xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_secret_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_secret_key' ];?>'); 
				},
				type		:	'GET',
				success		:	function( data ) {				

					WooCategories.shift();
					
					/**
					 * as long as we have category, we remove it
					**/
					
					if( WooCategories.length > 0 ) {
						RunWooCategoriesDelete();
					} else {
						GetWooCategories();
					}
				},
				error		:	function(){
					$( '.sync_list' ).append( '<li class="list-group-item"><?php echo _s( 'Une erreur s\'est produite... ', 'nexo_woo' );?></li>' );
				},
				dataType	:	"json"
			});
			
			} else {
				// No category to delete
				RunWooItemDelete();
			}
		};
		
		/**
		 * WooItem delete
		**/
		
		var RunWooItemDelete	=	function(){
			
			if( $( '.delete_item_notice' ).length == 0 ){
				$( '.sync_list' ).append( '<li class="list-group-item delete_item_notice"><?php echo _s( 'Suppression des produits. Restant = ', 'nexo_woo' );?> ' + WooItems.length + ' &mdash; <?php echo _s( 'page : ', 'nexo' );?><span class="item_del_page">' + ItemWave + '</span></li>' );
			} else {
				$( '.delete_item_notice' ).html( '<?php echo _s( 'Suppression des produits. Restant = ', 'nexo_woo' );?> ' + WooItems.length + ' &mdash; <?php echo _s( 'page : ', 'nexo' );?><span class="item_del_page">' + ItemWave + '</span>' );
			}
			
			// console.log( WooItems );
			if( ! _.isEmpty( WooItems ) ) {

				$.ajax( '<?php echo site_url( array( 'rest', 'woocommerce', 'sync_delete_woo_item' ) );?>/' + WooItems[0].id, {
					beforeSend: function(xhr) { 
						xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_url_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_url' ];?>'); 
						xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_key_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_cusommer_key' ];?>'); 
						xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_secret_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_secret_key' ];?>'); 
					},
					type		:	'GET',
					success		:	function( data ) {				
						
						WooItems.shift();
						
						/**
						 * as long as we have category, we remove it
						**/
						
						if( WooItems.length > 0 ) {
							RunWooItemDelete();
						} else {
							// Check wheter item still avaialble
							GetWooItems();
						}
					},
					error		:	function(){
						$( '.sync_list' ).append( '<li class="list-group-item"><?php echo _s( 'Une erreur s\'est produite... ', 'nexo_woo' );?></li>' );
					},
					dataType	:	"json"
				});
			} else {
				// No item
				NexoSync.syncCategories();
			}
		};
		
		var deleteContent	=	$( '.delete_on_woocommerce' ).is( ':checked' );
		
		if( deleteContent ) {			
			GetWooCategories();			
		} else {		
			this.syncCategories();
		}				
	}
	
	/**
	 * Sync Categories
	**/
	
	this.syncCategories	=	function(){
		
		$( '.sync_list' ).html( '' );
		
		this.getWooCategories(); 	
	}
	
	/**
	 * Get WooCommece Categories
	**/
	
	this.getWooCategories	=	function(){		
		
		$.ajax( '<?php echo site_url( array( 'rest', 'woocommerce', 'sync_woo_categories' ) );?>', {
			beforeSend: function(xhr) { 
				xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_url_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_url' ];?>'); 
				xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_key_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_cusommer_key' ];?>'); 
				xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_secret_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_secret_key' ];?>'); 
			},
			type		:	'GET',
			success		:	function( data ) {				
				NexoSync.getNexoPOSCategories();
			},
			error		:	function(){
				$( '.sync_list' ).append( '<li class="list-group-item"><?php echo _s( 'Une erreur s\'est produite... ', 'nexo_woo' );?></li>' );
			},
			dataType	:	"json"
		});
	}
	
	/**
	 * Get NexoPOS Categories
	**/
	
	this.getNexoPOSCategories	=	function(){
		
		$( '.sync_list' ).append( '<li class="list-group-item"><?php echo _s( 'Récupération des catégories NexoPOS <span class="sync_fetch_nexoposcategories"></span>', 'nexo_woo' );?></li>' );
		
		$.ajax( '<?php echo site_url( array( 'rest', 'woocommerce', 'sync_nexopos_categories' ) );?>', {
			beforeSend: function(xhr) { 
				xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_url_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_url' ];?>'); 
				xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_key_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_cusommer_key' ];?>'); 
				xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_secret_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_secret_key' ];?>'); 
			},
			type		:	'GET',
			success		:	function( data ) {
				
				// console.log( data );
				
				if( ! _.isEmpty( data ) ) {
					$( '.sync_fetch_nexoposcategories' ).append( ': <?php echo _s( 'Terminé', 'nexo_woo' );?>' );
				} else {
					$( '.sync_fetch_nexoposcategories' ).append( ': <?php echo _s( 'Terminé &mdash; Aucune categorie disponible.', 'nexo_woo' );?>' );
				}
				
				// Merging
				NexoSync.mergeCategories( data );
				
			},
			error		:	function(){
				$( '.sync_list' ).append( '<li class="list-group-item"><?php echo _s( 'Une erreur s\'est produite... ', 'nexo_woo' );?></li>' );
			},
			dataType	:	"json"
		});
	}
	
	/**
	 * Merge Categories
	**/
	
	this.mergeCategories			=	function( NexoPOS ) {
		
		$( '.sync_list' ).append( '<li class="list-group-item"><?php echo _s( 'Création des catégories <span class="sync_categories"></span>', 'nexo_woo' );?></li>' );
				
		var finalCategories			=	NexoPOS;
		
		function run_sync( data ) {
		
			$.ajax( '<?php echo site_url( array( 'rest', 'woocommerce', 'sync_categories', 'nexopos_to_woocommerce' ) );?>', {
				beforeSend: function(xhr) { 
					xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_url_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_url' ];?>'); 
					xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_key_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_cusommer_key' ];?>'); 
					xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_secret_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_secret_key' ];?>');
					
					$( '.sync_categories' ).html( '<?php echo _s( 'Restant(s) = ', 'nexo_woo' );?>' + _.values( data.merged_categories ).length );
				},
				type		:	'POST',
				data		:	_.object( [ 'merged_categories', 'woo_categories' ], [ JSON.stringify( data.merged_categories ), JSON.stringify( data.woo_categories ) ] ),
				success		:	function( data ) {
					
					if( ! _.isEmpty( data.merged_categories ) ) {
						
						setTimeout( function(){
							run_sync({
								merged_categories	:	data.merged_categories,
								woo_categories		:	data.woo_categories
							});
						}, 0 );
						
					} else {
						
						$( '.sync_categories' ).html( ': <?php echo _s( 'Terminé', 'nexo_woo' );?>' );						
						NexoSync.getNexoPOSItems();
						
					}				
				},
				error		:	function(){
					$( '.sync_list' ).append( '<li class="list-group-item"><?php echo _s( 'Une erreur s\'est produite... ', 'nexo_woo' );?></li>' );
				},
				dataType	:	"json"
			});		
		
		}
		
		// Now launch Run
		run_sync({
			merged_categories	:	finalCategories,
			woo_categories		:	[]
		});
	}
	
	/** 
	 * Sync Tags used on NexoPOS as Radius
	**/
	
	this.getWooItems			=	function(){ // deprecated ?
		
		var deleteContent	=	$( '.delete_on_woocommerce' ).is( ':checked' ) ? 'clear' : '';
		
		var getWooItem		=	function(){
			$.ajax( '<?php echo site_url( array( 'rest', 'woocommerce', 'sync_woo_items' ) );?>/' + deleteContent, {
				beforeSend: function(xhr) { 
					xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_url_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_url' ];?>'); 
					xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_key_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_cusommer_key' ];?>'); 
					xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_secret_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_secret_key' ];?>'); 
				},
				type		:	'GET',
				success		:	function( data ) {
					
					if( ! _.isEmpty( data ) ) {						
						deleteWooItem( data );						
					} else {
						NexoSync.getNexoPOSItems();
					}
								
				},
				error		:	function(){
					$( '.sync_list' ).append( '<li class="list-group-item"><?php echo _s( 'Une erreur s\'est produite... ', 'nexo_woo' );?></li>' );
				},
				dataType	:	"json"
			});
		}
		
		var deleteWooItem	=	function( data ){
			if( ! _.isEmpty( data ) ) {
				$.ajax( '<?php echo site_url( array( 'rest', 'woocommerce', 'sync_delete_woo_item' ) );?>/' + data[0].ID, {
					beforeSend: function(xhr) { 
						xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_url_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_url' ];?>'); 
						xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_key_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_cusommer_key' ];?>'); 
						xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_secret_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_secret_key' ];?>'); 
					},
					type		:	'GET',
					success		:	function() {
						
						data.shift();
						
						deleteWooItem( data );
						
					},
					error		:	function(){
						$( '.sync_list' ).append( '<li class="list-group-item"><?php echo _s( 'Une erreur s\'est produite... ', 'nexo_woo' );?></li>' );
					},
					dataType	:	"json"
				});
			} else {
				
				/**
				 * If all returned item has been delete, we try to look 
				**/
				
				getWooItem();
			}	
		} 	
	}
	
	/**
	 * Get NexoPOS Items
	**/
	
	this.getNexoPOSItems			=	function() {
		
		$.ajax( '<?php echo site_url( array( 'rest', 'woocommerce', 'sync_nexopos_items' ) );?>', {
			beforeSend: function(xhr) { 
				xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_url_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_url' ];?>'); 
				xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_key_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_cusommer_key' ];?>'); 
				xhr.setRequestHeader('<?php echo $this->config->item( 'nexo_woo_consumer_secret_prefix' );?>',	'<?php echo @$Options[ 'woocommerce_secret_key' ];?>'); 
			},
			type		:	'GET',
			success		:	function( data ) {
				
				$( '.sync_get_nexopositems' ).html( ': <?php echo _s( 'Terminé', 'nexo_woo' );?>' );
				
				if( NexoSync.Exclude == 'all' ) {
					NexoSync.mergeItems( data );
				} else {
					var ItemsArray	=	new Array;
					_.each( data, function( _item ){
						
						/**
						 * Si la collection de l'article correspond à la collection a supprimer'
						**/
						
						if( value.ID != NexoSync.Exclude ) {
							ItemsArray.push( _item );
						}
					});
					NexoSync.mergeItems( ItemsArray );
				}
			},
			error		:	function(){
				$( '.sync_list' ).append( '<li class="list-group-item"><?php echo _s( 'Une erreur s\'est produite... ', 'nexo_woo' );?></li>' );
			},
			dataType	:	"json"
		});
	}
	
	/**
	 * Merge Item
	**/
	
	this.mergeItems		=	function( NexoPOS ) {
		
		var Items		=	NexoPOS;
		var ttItems		=	_.values( Items ).length;
		var ProcessCount	=	0;
		
		$( '.sync_list' ).append( '<li class="list-group-item"><?php echo _s( 'Création des produits : <span class="sync_item_status"></span>', 'nexo_woo' );?></li>' );
		
		$( '.sync_item_status' ).html( '0/' + ttItems );		
		
		function insert_item( _item ) {
			
			if( ! _.isEmpty( _item ) ) {
				
				$.ajax( '<?php echo site_url( array( 'rest', 'woocommerce', 'sync_items' ) );?>', {
					beforeSend: function(xhr) { 
						
						ProcessCount++;
						
						xhr.setRequestHeader(	
							'<?php echo $this->config->item( 'nexo_woo_url_prefix' );?>',	
							'<?php echo @$Options[ 'woocommerce_url' ];?>'
						); 
						
						xhr.setRequestHeader(	
							'<?php echo $this->config->item( 'nexo_woo_consumer_key_prefix' );?>',	
							'<?php echo @$Options[ 'woocommerce_cusommer_key' ];?>'
						); 
						
						xhr.setRequestHeader(
							'<?php echo $this->config->item( 'nexo_woo_consumer_secret_prefix' );?>',	
							'<?php echo @$Options[ 'woocommerce_secret_key' ];?>'
						); 
						
					},
					type		:	'POST',
					data		:	_item[0],
					success		:	function( data ) {
						
						_item.shift();
						
						$( '.sync_item_status' ).html( ProcessCount + '/' + ttItems );
						
						setTimeout( function(){
						
							insert_item( _item );	
						
						}, 3000 );
						
					},
					error		:	function(){
						$( '.sync_list' ).append( '<li class="list-group-item"><?php echo _s( 'Une erreur s\'est produite... ', 'nexo_woo' );?></li>' );
					},
					dataType	:	"json"
				});
				
			} else {
				$( '.sync_item_status' ).html( '<?php echo _s( 'Terminé', 'nexo_woo' );?>' );
			}
		}		
		
		// Launch
		insert_item( _.values( Items ) );
	}
	
	/**
	 * Boot Syncing
	**/
	
	this.boot	=	function(){
		this.getWooItems();
	}
}
$( document ).ready(function(e) {
	
	$( '.sync_btn' ).bind( 'click', function(){
		NexoSync.proceedDelete();
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
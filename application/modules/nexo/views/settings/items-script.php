<div class="btn-group">
	<button type="button" class="reset_barcode btn btn-primary"><?php _e('Recréer les codes barres', 'nexo');?></button>

</div>
<button type="button" class="refresh_barcode btn btn-primary"><?php _e('Rafraichir les codes barres', 'nexo');?></button>
<p><?php echo tendoo_warning(__('Après avoir lancé le processus, veuillez patienter qu\'il s\'acheve.', 'nexo'));?></p>

<p class="reset_barcode_notice"></p>

<div class="progress xs reset_barcode_progress" style="display:none;">
  <div class="progress-bar progress-bar-aqua barcode_progress_line" style="width: 0%" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
    <span class="sr-only"><span class="progress_id">0</span>% <?php _e('Terminé', 'nexo');?></span>
  </div>
</div>

<script>
$( document ).ready(function(e) {
	<?php
    global $Options;
    if (@$Options[ 'nexo_product_codebar' ] == 'ean13') {
        $divider        =    80;
    } elseif (@$Options[ 'nexo_product_codebar' ] == 'ean8') {
        $divider        =    80;
    } else {
        $divider        =    100;
    }
    ?>
	var	NexoBarcodeSettings		=	new function(){
		this.ResampleIterator	=	0;
		this.ProductLength		=	0;
		this.FetchProduct		=	function( refresh ){
			$.ajax( '<?php echo site_url([ 'rest', 'nexo', 'item' . store_get_param( '?' ) ]);?>', {
				beforeSend	: function() {
					NexoBarcodeSettings.Notice( '<?php echo addslashes(__('Récupération des produits en cours...', 'nexo'));?>' );
				},
				success		: function( data ){
					if( data.length > 0 ) {
						NexoBarcodeSettings.TreatProducts( data, refresh );
					} else {
						tendoo.notify.warning( '<?php echo addslashes(__('Aucun produit n\'a été retrouvé', 'nexo'));?>', '<?php echo addslashes(__('Le rafraichissement des codebarres ne peut se faire, car aucun produit n\'est disponible.', 'nexo'));?>', true );
						NexoBarcodeSettings.Notice( '<?php echo addslashes(__('Aucun produit n\'a été retrouvé.', 'nexo'));?>' );
					}
				},
				dataType	:"json",
				error		: function(){
					NexoBarcodeSettings.Notice( '<?php echo addslashes(__('Une erreur s\'est produite.', 'nexo'));?>' );
				}
			});
		}

		this.Notice				=	function( notice ){
			$( '.reset_barcode_notice' ).html( notice );
		}

		this.TreatProducts		=	function( data, refresh ) {
			this.products		=	data;
			this.ProductLength	=	NexoBarcodeSettings.products.length;

			NexoBarcodeSettings.Notice( '<?php echo addslashes(__('Produits trouvés: ', 'nexo'));?>' + NexoBarcodeSettings.products.length );

			if( typeof refresh != 'undefined' ) {
				NexoBarcodeSettings.RefreshBarcode();
			} else {
				NexoBarcodeSettings.ResetSavedBarcode();
			}
		}

		this.ResetSavedBarcode	=	function() {
			$.ajax( '<?php echo site_url(array( 'dashboard', 'nexo', 'rest', 'trigger', 'model', 'Nexo_Products', 'reset_barcode' ));?>', {
				beforeSend : function(){
					NexoBarcodeSettings.Notice( '<?php echo addslashes(__('Suppression du cache...', 'nexo'));?>' );
				},
				success		: function(){
					NexoBarcodeSettings.Notice( '<?php echo addslashes(__('Rafraichissement en cours...', 'nexo'));?>' );
					NexoBarcodeSettings.Resample();
				}
			});
		}

		this.RefreshBarcode	=	function() {
			this.ResampleIterator++;
			$.ajax( '<?php echo site_url(array( 'dashboard', 'nexo', 'rest', 'trigger', 'model', 'Nexo_Products', 'generate_barcode' ));?>/' + NexoBarcodeSettings.products[0].CODEBAR + '/' + NexoBarcodeSettings.products[0].BARCODE_TYPE,
			{
				success			:	function( data ){
					if( NexoBarcodeSettings.ResampleIterator < NexoBarcodeSettings.ProductLength ) {
						NexoBarcodeSettings.products.shift();
						NexoBarcodeSettings.SetProgress();
						NexoBarcodeSettings.RefreshBarcode();
					} else {
						$( '.reset_barcode_progress' ).fadeOut( 500, function(){
							$( '.barcode_progress_line' ).css({ 'width' : 0 + '%' });
						});
						NexoBarcodeSettings.Notice( '' );
						NexoBarcodeSettings.ResampleIterator	=	0;
						bootbox.alert( '<?php echo addslashes(__('Les codes barres des produits ont été correctement mis à jour', 'nexo'));?>' );
					}
				}
			});
		}

		this.Resample			=	function( ){
			this.ResampleIterator++;
			$.ajax( '<?php echo site_url(array( 'dashboard', 'nexo', 'rest', 'trigger', 'model', 'Nexo_Products', 'resample_codebar' ));?>/' + NexoBarcodeSettings.products[0].ID + '/' + NexoBarcodeSettings.products[0].CODEBAR + '/' +
			NexoBarcodeSettings.products[0].BARCODE_TYPE,
			{
				success			:	function( data ){
					if( NexoBarcodeSettings.ResampleIterator < NexoBarcodeSettings.ProductLength ) {
						NexoBarcodeSettings.products.shift();
						NexoBarcodeSettings.SetProgress();
						NexoBarcodeSettings.Resample();
					} else {
						$( '.reset_barcode_progress' ).fadeOut( 500, function(){
							$( '.barcode_progress_line' ).css({ 'width' : 0 + '%' });
						});
						NexoBarcodeSettings.Notice( '' );
						NexoBarcodeSettings.ResampleIterator	=	0;
						bootbox.alert( '<?php echo addslashes(__('Les codes barres des produits ont été correctement mis à jour', 'nexo'));?>' );
					}
				}
			});
		}

		this.SetProgress		=	function() {
			var Percent			=	this.ResampleIterator * 100 / NexoBarcodeSettings.products.length;

			$( '.reset_barcode_progress' ).show();
			$( '.barcode_progress_line' ).css({ 'width' : Percent + '%' });
		}
	};
	var divider					=	<?php echo $divider;?>;
    $( '.reset_barcode' ).bind( 'click', function(){
		NexoBarcodeSettings.FetchProduct();
	});

	$( '.refresh_barcode' ).bind( 'click',function(){
		NexoBarcodeSettings.FetchProduct( true );
	});
});
</script>

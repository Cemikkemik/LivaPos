<script type="text/javascript">
function isInt(n){
		return Number(n)===n && n%1===0;
	}
	function isFloat(n) {
		return n === +n && n !== (n|0);
	}
	function check_if_product_exists( filter, value )
	{
		<?php
        $segments                =    $this->uri->segment_array();
        $item_id                    =    end($segments) ;
        ?>
		$.ajax( '<?php echo site_url(array( 'nexo', 'compare_item' ));?>/' + filter + '/<?php echo $item_id;?>',{
			success	:	function( a ){
				__check_if_product_exist( a, filter );
			},
			data	:	_.object( [ 'filter' ], [ value ] ),
			type:'POST',
			dataType:"json"
			
		});
	}
	function __check_if_product_exist( passed, filter )
	{
		if( filter == 'DESIGN' ) {
			if( passed.length > 0 )
			{
				bootbox.confirm( '<?php _e('Un produit avec ce nom existe déjà, souhaitez-vous pré-remplir ce formulaire ?', 'nexo');?>' , function( result ){
					$('[name="TAUX_DE_MARGE"]').val( passed[0].TAUX_DE_MARGE );
					$('[name="PRIX_DE_VENTE"]').val( passed[0].PRIX_DE_VENTE );
					$('[name="PRIX_DACHAT"]').val( passed[0].PRIX_DACHAT );
					$('[name="REF_RAYON"]').val( passed[0].REF_RAYON );
					$('[name="FRAIS_ACCESSOIRE"]').val( passed[0].FRAIS_ACCESSOIRE );
				})
			}
		} else if( filter == 'SKU' ) {
			if( passed.length > 0 ) {
				tendoo.notify.warning( '<?php echo _s('Attention', 'nexo');?>', '<?php echo _s('L\'unité de gestion de stock spécifié est déjà en cours d\'utilisation. Veuilez en définir un autre.', 'nexo');?>' );
				$( '[name="SKU"]' ).val( '' );
			}
		}
	}
	
</script>
<div class="input-group">
  <span class="input-group-addon" id="basic-addon1"><?php _e( 'Sélectionner une collection', 'nexo_advanced_reports' );?></span>
  <select ng-model="collection" class="form-control">
  	<option value=""><?php _e( 'Sélectionnez une collection', 'nexo_advanced_reports' );?></option>
  </select>
</div>
<br />
<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <td><?php _e( 'UGS', 'nexo_advanced_reports' );?></td>
                <td><?php _e( 'Catégorie', 'nexo_advanced_reports' );?></td>
                <td><?php _e( 'Désignation', 'nexo_advanced_reports' );?></td>
                <td><?php _e( 'Taille', 'nexo_advanced_reports' );?></td>
                <td><?php _e( 'Couleur', 'nexo_advanced_reports' );?></td>
                <td><?php _e( 'Quantité', 'nexo_advanced_reports' );?></td>
                <td><?php _e( 'Qte en bon état', 'nexo_advanced_reports' );?></td>
                <td><?php _e( 'Qte défectueux', 'nexo_advanced_reports' );?></td>
                <td><?php _e( 'Image', 'nexo_advanced_reports' );?></td>
            </tr>
        </thead>
        <tbody>
        	<tr ng-class="hideLoader">
            	<td colspan="8"><?php _e( 'Chargement en cours...', 'nexo_advanced_reports' );?></td>
            </tr>
            <tr data-template="item" style="display:none">
            	<td data-item="sku"></td>
                <td data-item="cat_id"></td>
                <td data-item="design"></td>
                <td data-item="taille"></td>
                <td data-item="couleur"></td>
                <td data-item="quality"></td>
                <td data-item="left"></td>	
                <td data-item="defectueux"></td>
            </tr>
            <tr data-index="total_line">
            	<td colspan="5" class="text-center"><?php _e( 'Total collection :', 'nexo_advanced_reports' );?></td>
                <td data-item="sum_quantity"></td>
                <td data-item="sum_left"></td>
                <td data-item="sum_defectueux"></td>
            </tr>
        </tbody>
    </table>
</div>
<?php 
global $Options;
$this->load->config('rest');

$header_key        	=    $this->config->item('rest_key_name');
$key           	 	=    @$Options[ 'rest_key' ];
?>
<script type="text/javascript">
"use strict";

var NarCA	=	new function(){
	
	this.getCol		=	function(){
		$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'collection' ) );?>', {
			success	:	function( data ) {
				_.each( data, function( value, key ) {
					$( '[ng-model="collection"]' ).append( '<option  value="' + value.ID + '">' + value.TITRE + '</option>' );
				});
				
				$( '[ng-model="collection"]' ).bind( 'change', function(){
					NarCA.fetchItem( $( this ).val() );
				});
			},
			dataType: 'json',
			type: 'GET'		
		});
	}
	
	this.run		=	function(){
		this.getCol();
	}
	
	/**
	 * Fetch Item
	**/
	
	this.fetchItem		=	function( col_id ){
		$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'item_by_collection' ) );?>/' + col_id , {
			success	:	function( data ){
				$( '[ng-class="hideLoader"]' ).remove();
				$( '[data-template="item"]' ).remove();
				if( data.length > 0 ){
					_.each( data, function( _item, key ) {
						$( '<tr data-template="item">' +
								'<td data-item="sku">' + _item.SKU + '</td>' +
								'<td data-item="cat_name">' + _item.CAT_NAME + '</td>' +
								'<td data-item="design">' + _item.DESIGN + '</td>' +
								'<td data-item="taille">' + _item.TAILLE + '</td>' +
								'<td data-item="couleur">' + _item.COULEUR + '</td>' +
								'<td data-item="quantity">' + _item.QUANTITY + '</td>' +
								'<td data-item="left">' + ( parseInt( _item.QUANTITY ) - parseInt( _item.DEFECTUEUX ) )  + '</td>' +
								'<td data-item="defectueux">' + _item.DEFECTUEUX + '</td>' +
								'<td data-item="apercu"><img style="max-height:150px" src="<?php echo upload_url();?>' + _item.APERCU + '"/></td>' +
							'</tr>' ).insertBefore( '[data-index="total_line"]' )
					})
					
				} else {
					$( '<tr data-template="item" ng-class="hideLoader">' +
							'<td colspan="8"><?php echo _s ( 'Aucun produit pour cette collection', 'nexo' );?></td>' +
						'</tr>' ).insertBefore( '[data-index="total_line"]' )
				}
				
				var TotalQuantity	=	0;
				$( '[data-item="quantity"]' ).each( function(){
					TotalQuantity	+=	parseInt( $( this ).html() );
				});
				
				$( '[data-item="sum_quantity"]' ).html( TotalQuantity );
				
				var TotalLeft	=	0;
				$( '[data-item="left"]' ).each( function(){
					TotalLeft	+=	parseInt( $( this ).html() );
				});
				
				$( '[data-item="sum_left"]' ).html( TotalLeft );
				
				var TotalDefectueux	=	0;
				$( '[data-item="defectueux"]' ).each( function(){
					TotalDefectueux	+=	parseInt( $( this ).html() );
				});
				
				$( '[data-item="sum_defectueux"]' ).html( TotalDefectueux );
			},
			dataType:	'json',
			type	:	'GET'
		});
	};
};

$( document ).ready(function(e) {
    NarCA.run();
});
</script>
<div class="input-group"> <span class="input-group-addon" id="basic-addon1">
    <?php _e ( 'Selectionnez une collection', 'nexo_advanced_reports' );?>
    </span>
    <select class="form-control collection_list">
        <option value="">
        <?php _e( 'Faites un choix', 'nexo_advanced_reports' );?>
        </option>
    </select>
</div>
<br />
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php _e( 'Fiche récapitulative', 'nexo_advanced_reports' );?></h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body no-padding">
    	<div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <td class="text-center" colspan="<?php echo $Categories_Depth;?>"><?php _e( 'Hiérarchie des catégories', 'nexo_advanced_reports' );?></td>
                        <td class="text-center" colspan="4"><?php _e( 'Détails de la collection', 'nexo_advanced_reports' );?></td>
                    </tr>
                    <tr>
                    <?php 
                if( $Categories_Depth > 0 ) {
                    for( $i = 0; $i < $Categories_Depth; $i++ ) {
                        ?>
                    <td class="text-center"><?php echo sprintf( __( 'Niveau : %s', 'nexo_advanced_reports' ), $i );?></td>
                    <?php
                    }
                }
                ?>
                    <td class="text-center"><?php _e( 'Stock', 'nexo_advanced_reports' );?></td>
                    <td class="text-center"><?php _e( 'Coût d\'achat', 'nexo_advanced_reports' );?></td>
                    <td class="text-center"><?php _e( 'Prix de vente', 'nexo_advanced_reports' );?></td>
                    <td class="text-center"><?php _e( 'Chiffre d\'affaire', 'nexo_advanced_reports' );?></td>
                </tr>
                </thead>
                <tbody>
                <?php
                    echo $this->Nexo_Misc->build_table( $Categories_Hierarchy, $Categories_Depth, 1, '', 4);
                ?>
                </tbody>
                <tfoot>
                	<tr>
                    	<td colspan="<?php echo $Categories_Depth;?>" class="text-center"><?php _e( 'Total', 'nexo' );?></td>
                        <td class="text-right" total-stock></td>
                        <td class="text-right" total-ca></td>
                        <td class="text-right" total-pv></td>
                        <td class="text-right" total-income></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <!-- /.box-body --> 
</div>
<script type="text/javascript">
"use strict";

var NexoRecap		=	new function(){
	this.getCols	=	function(){
		$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'collection' ) );?>', {
			success		:	function( data ){
				_.each( data, function( col, key ) {
					$( '.collection_list' ).append( '<option value="' + col.ID + '">' + col.TITRE + '</option>' );
				})
				
				NexoRecap.bindAction( 'collection_change' );
			},
			dataType	:	'json'
		});
	}
	
	/**
	 * Bind Changing actions
	**/
	
	this.bindAction		=	function( action_namespace ) {
		if( action_namespace == 'collection_change' ) {
			$( '.collection_list' ).bind( 'change', function(){
				NexoRecap.runRecapFor( $( this ).val() );
			});
		}
	}
	
	/**
	 * Run Recap for
	**/
	
	this.runRecapFor			=	function( col_id ) {
		
		var categoriesArray		=	new Array();
		
		$( 'tr' ).each( function(){
			if( $(this).find( 'td[data-id]' ).length > 0 ) {
				categoriesArray.push( $(this).find( 'td[data-id]' ).last().attr( 'data-id' ) );
			}
		});	
		
		// console.log( categoriesArray );
		
		$.ajax( '<?php echo site_url( array( 'rest', 'nar', 'categories_recap' ) );?>', {
			error		:	function(){
				bootbox.alert( '<?php echo _s( 'Une erreur s\'est produite durant le chargement', 'nexo_advanced_reports' );?>' );
			},
			dataType	:	'json',
			type		:	'post',
			data		:	_.object( [ 'categories_id' ], [ categoriesArray ] ),
			success		:	function( data ) {
				
				_.each( categoriesArray, function( cat_id, key ) {
					
					var this_category_items		=	new Array();
					var	usedItem				=	new Array();
					
					_.each( data, function( _item, _key ) {
						if( _item.CAT_ID == cat_id ) {
							this_category_items.push( _item );
						}
					});
					
					// console.log( this_category_items );
					
					var Stock			=	0;
					var PurchasePrice	=	0;
					var PrixDeVente		=	0;
					
					_.each( this_category_items, function( _item ) {
						if( _.contains( usedItem, _item.ITEM_ID ) == false) {
							Stock			+=	parseInt( _item.QUANTITY );
							PurchasePrice	+=	( parseFloat( _item.PRIX_DACHAT ) * ( parseInt( _item.QUANTITY ) - parseInt( _item.DEFECTUEUX ) ) );
							PrixDeVente		+=	( parseFloat( _item.PRIX_DE_VENTE ) * ( parseInt( _item.QUANTITY ) - parseInt( _item.DEFECTUEUX ) ) );
							
							usedItem.push( _item.ITEM_ID );
						}
					});
					
					$( '[data-id="' + cat_id + '"]' ).siblings( '[month-id="1"]' ).html( Stock );
					
					$( '[data-id="' + cat_id + '"]' ).siblings( '[month-id="2"]' ).html( NexoAPI.DisplayMoney( PurchasePrice ) );
					$( '[data-id="' + cat_id + '"]' ).siblings( '[month-id="2"]' ).attr( 'price', PurchasePrice );
					
					$( '[data-id="' + cat_id + '"]' ).siblings( '[month-id="3"]' ).html( NexoAPI.DisplayMoney( PrixDeVente ) );
					$( '[data-id="' + cat_id + '"]' ).siblings( '[month-id="3"]' ).attr( 'price', PrixDeVente );
				
				});
				
				// Launch get Prix d'achats
				NexoRecap.getIncome( categoriesArray );
			}
		});
	}
	
	/**
	 * Get Prix d'achat
	**/
	
	this.getIncome		=	function( object ){
		_.each( object, function( cat_id, key ) {
			
			$.ajax( '<?php echo site_url( array( 'rest', 'nar', 'categories_purchase_price' ) );?>', {
				error		:	function(){
					bootbox.alert( '<?php echo _s( 'Une erreur s\'est produite durant le chargement', 'nexo_advanced_reports' );?>' );
				},
				dataType	:	'json',
				type		:	'post',
				data		:	_.object( [ 'cat_id' ], [ cat_id ] ),
				success		:	function( data ) {
					var income		=	0;
					_.each( data, function( sale, key ) {
						income		+=	( NexoAPI.ParseFloat( sale.PRIX_TOTAL ) );
					});
					
					$( '[data-id="' + cat_id + '"]' ).siblings( '[col-total]' ).html( NexoAPI.DisplayMoney( income ) );
					$( '[data-id="' + cat_id + '"]' ).siblings( '[col-total]' ).attr( 'price', income );
					
					// Where are at the end
					if( key + 1 == object.length ) {
						var total	=	0;
						$( '[month-id="1"]' ).each( function(){
							total	+=	parseInt( $( this ).html() );
						});
						
						$( '[total-stock]' ).html( total );
						
						var totalCA	=	0;
						
						$( '[month-id="2"]' ).each( function(){
							totalCA	+=	parseInt( $( this ).attr( 'price' ) );
						});
						
						$( '[total-ca]' ).html( NexoAPI.DisplayMoney( totalCA ) );
						
						var totalPV	=	0;
						
						$( '[month-id="3"]' ).each( function(){
							totalPV	+=	parseInt( $( this ).attr( 'price' ) );
						});
						
						$( '[total-pv]' ).html( NexoAPI.DisplayMoney( totalPV ) );
						
						var TotalIncome	=	0;
												
						$( '[col-total]' ).each( function(){
							TotalIncome	+=	parseInt( $( this ).attr( 'price' ) );
						});
						
						$( '[total-income]' ).html( NexoAPI.DisplayMoney( TotalIncome ) );
						
					}
				}
			});			
		});
		
		
	}
	
	this.run		=	function(){
		this.getCols();
	}
};

$( document ).ready(function(e) {
    NexoRecap.run();	
});
</script>
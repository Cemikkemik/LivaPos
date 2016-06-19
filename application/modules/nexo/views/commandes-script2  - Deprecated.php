<?php global $Options,$NexoEditScreen;?>
<script type="text/javascript">

"use strict";

var NexoScreen			=	'edit';
var NexoCurrentClient	=	0;
var NexoCommandesEdit	=	new function(){
		this.Nexo_Order_Avance	=	'<?php echo 'nexo_order_advance';?>';
		this.Nexo_Order_Cash	=	'<?php echo 'nexo_order_comptant';?>';
		this.Nexo_Order_Devis	=	'<?php echo 'nexo_order_devis';?>';
		this.AllowAddItem		=	<?php echo in_array(@$Options[ 'nexo_enable_additem' ], array( null, 'no' ), true) ? 'false' : 'true';?>;
		this.GetOrder			=	function( orderID ){
			
			$.ajax( tendoo.site_url  + 'nexo_checkout/orders/' + orderID,{
				success	:	function( a ){
					// Disable edit for advance and cash
					if( _.contains( [ NexoCommandesEdit.Nexo_Order_Cash, NexoCommandesEdit.Nexo_Order_Avance ], a[0].TYPE ) ) {
						$( '#REMISE_field_box' ).hide( 500 );
						$( '.Nexo-cart-table thead tr td' ).last().hide();
						$( '#codebar-wrapper' ).closest( '.box' ).hide( 500 );
						
						NexoCommandes.ReduceEffectEnabled	=	false;
						NexoCommandes.DisplayReduceButtons	=	false;
						
						// For Cash order
						/**
						 * Now Way to change received price
						**/
												
						if( NexoCommandesEdit.Nexo_Order_Cash == a[0].TYPE ) {
							// Disable editing for cash order
							NexoCommandes.RequireConfirm		=	false;
							// Remove Cancel discount button
							NexoCommandes.DisplayCancelDiscountButton	=	false;
							
							$( '#SOMME_PERCU_field_box' ).hide( 500 );
							$( '#checkout.btn.btn-primary' ).html( '<?php echo addslashes(__('Modifier', 'nexo'));?>' );
						} else {
							// $( '#checkout.btn.btn-primary' ).html( '<?php echo addslashes(__('Payer', 'nexo'));?>' );
						}
					} else { // For Quote Order
						if( ! NexoCommandesEdit.AllowAddItem ) { // if adding item to quote order is disabled
							$( '#nexo-cart' ).append( '<div class="overlay"><i class="fa fa-warning"></i></div>' );
						}
					}
					
					NexoCommandes.AutomatePercentDiscount	=	false;
					NexoCommandes.SetOtherCharge( parseInt( a[0].RISTOURNE ) );
					// Si la ristourne est supérieure à 0
					if( parseInt( a[0].RISTOURNE ) > 0 ) {
						// console.log( a[0].RISTOURNE );
						NexoCommandes.DisplayDiscountText( a[0].DISCOUNT_TYPE );
					}
					NexoCommandesEdit.GetOrderProducts( a[0].CODE );										
					// console.log( a );
					$( '#crudForm' ).append( '<input type="hidden" name="command_code" value="' + a[0].CODE + '">' );
				},
				dataType:"json"
			});
		}
		this.GetOrderProducts	=	function( orderCode ){

			$.ajax( tendoo.site_url  + '/nexo_checkout/orders_products/' + orderCode,{
				success	:	function( a ){
					NexoCommandesEdit.FillCart( a )
				},
				dataType:"json"
			});
		}
		this.FillCart			=	function( a ){
			var products		=	new Array();
			_.each( a, function( value, key ) {
				$.ajax( tendoo.site_url  + 'nexo/item/' + value.REF_PRODUCT_CODEBAR + '/CODEBAR',{
					success	:	function( product_details ){
						/**
						 * On considere que comme le produit est en cours de modification, les produits sont remis en stock
						**/
						value					=	_.extend( value, product_details[0] );
						value.ADDED_TO_CART		=	parseInt( value.QUANTITE );
						value.QUANTITE_RESTANTE	=	parseInt( product_details[0].QUANTITE_RESTANTE ) + value.ADDED_TO_CART;
						value.QUANTITE_VENDU	= 	parseInt( product_details[0].QUANTITE_VENDU ) - value.ADDED_TO_CART;
						var i;
						for( i = 1; i <= value.QUANTITE; i++ ){
							NexoCommandes.TreatProduct( new Array( value ) );	
						}						
						
						// Show content
						$( '.content-wrapper .content' ).eq(0).css({
							'visibility' : 'visible'
						});
						$( '#meta-produits' ).css({
							'visibility' : 'visible'
						});
					},
					dataType:"json"
				});
			});
			
			// Display cart content
			$( '.content-wrapper .content' ).eq(0).css({
				'display' : 'inherit'
			});
			$( '#meta-produits' ).css({
				'display' : 'inherit'
			});
			$( '.content-header' ).css({
				'display' : 'inherit'
			});
		}
	}
	var url			=	document.location.href,
		urlToRemove	=	'<?php echo site_url(array( 'dashboard', 'nexo', 'commandes', 'lists', 'edit' ));?>/',
		NexoOrderId	=	url.substr( urlToRemove.length );
$(document).ready(function(e) {		
	// Modification de l'écran	
	NexoCurrentClient	=	$( '[name="REF_CLIENT"]' ).val();
	NexoCommandesEdit.GetOrder( NexoOrderId );
});
</script>
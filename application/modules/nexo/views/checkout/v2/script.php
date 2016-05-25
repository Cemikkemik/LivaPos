<script type="text/javascript">
"use strict";

var v2Checkout					=	new function(){
	
	this.ProductListWrapper		=	'#product-list-wrapper';
	this.CartTableBody			=	'#cart-table-body';
	this.ItemsListSplash		=	'#product-list-splash';
	this.CartTableWrapper		=	'#cart-details-wrapper';
	this.CartTableBody			=	'#cart-table-body';
	this.ItemsCategories		=	new Object;
	this.CartItems				=	new Array;
	
	/**
	 * Reset Cart
	**/
	
	this.resetCart				=	function(){
		this.CartValue			=	0;
		this.CartVAT			=	0;
		this.CartDiscount		=	0;
		this.CartToPay			=	0;
		
		this.refreshCartValues();
	}
	
	/**
	 * Show Product List Splash
	**/
	
	this.showSplash				=	function( position ){
		if( position == 'right' ) {
			// Simulate Show Splash
			$( this.ItemsListSplash ).show();
			$( this.ProductListWrapper ).find( '.box-body' ).css({'visibility' :'hidden'});
		}
	};
	
	/**
	 * Hid Splash
	**/
	
	this.hideSplash				=	function( position ){
		if( position == 'right' ) {
			// Simulate Show Splash
			$( this.ItemsListSplash ).hide();
			$( this.ProductListWrapper ).find( '.box-body' ).css({'visibility' :'visible'});
		}
	};
	
	/**
	 * Fix Product Height
	**/
	
	this.fixHeight				=	function(){
		// Height and Width
		var headerHeight		=	$( '.main-header' ).outerHeight();
		var contentHeader		=	$( '.content-header' ).outerHeight();
		var contentPadding		=	20;
		// Col 1
		var cartDetailsHeight	=	$( '#cart-details' ).outerHeight();
		var cartPanelHeight		=	$( '#cart-panel' ).outerHeight();
		var cartSearchHeight	=	$( '#cart-search-wrapper' ).outerHeight();
		var cartHeader			=	$( '#cart-header' ).outerHeight();
		var cartTableHeader		=	-9; // $( '#cart-item-table-header' ).outerHeight();
		var col1Height			=	window.innerHeight - ( ( cartDetailsHeight + cartPanelHeight + cartSearchHeight + cartHeader + cartTableHeader ) + ( ( headerHeight + contentHeader + contentPadding ) * 2 ) );
		$( this.CartTableBody ).height( col1Height );
		// Col 2				
		var col2Height			=	window.innerHeight	- ( -5 + ( headerHeight + contentHeader + contentPadding ) * 2 );
		$( this.ProductListWrapper	).find( '.direct-chat-messages' ).height( col2Height );
	};
	
	/**
	 * Filter Item
	 *
	 * @params string
	 * @return void
	**/
	
	this.filterItems			=	function( content ) {
		content					=	_.toArray( content );
		if( content.length > 0 ) {
			$( '#product-list-wrapper' ).find( '[data-category]' ).hide();
			_.each( content, function( value, key ){
				$( '#product-list-wrapper' ).find( '[data-category="' + value + '"]' ).show();
			});
		} else {
			$( '#product-list-wrapper' ).find( '[data-category]' ).show();
		}
	}
	
	/**
	 * Get Items
	**/
	
	this.getItems				=	function( beforeCallback, afterCallback){
		$.ajax('<?php echo site_url( array( 'nexo', 'item' ) );?>', {
			beforeSend	:	function(){
				if( typeof beforeCallback == 'function' ) {
					beforeCallback();
				}
			},
			error	:	function(){
				bootbox.alert( '<?php echo addslashes( __( 'Une erreur s\'est produite durant la récupération des produits', 'nexo' ) );?>' );
			}, 
			success: function( content ){
				$( this.ItemsListSplash ).hide();
				$( this.ProductListWrapper ).find( '.box-body' ).css({'visibility' :'visible' });				
				v2Checkout.displayItems( content );
				
				if( typeof afterCallback == 'function' ) {
					afterCallback();
				}
			},
			dataType:"json"
		});
	};
	
	/**
	 * Display Items on the grid
	 * @params Array
	 * @return void
	**/
	
	this.displayItems			=	function( json ) {
		if( json.length > 0 ) {
			_.each( json, function( value, key ) {
				if( parseInt( value.QUANTITE_RESTANTE ) > 0 ) {
					
					var MainPrice	= parseInt( value.PRIX_DE_VENTE )
					
					$( '#filter-list' ).append( 
					'<div class="col-sm-3 col-md-2 col-xs-3" style="padding:5px; border-right: solid 1px #DEDEDE;border-bottom: solid 1px #DEDEDE;" data-category="' + value.REF_CATEGORIE + '">' +
						'<img src="<?php echo img_url() . '../';?>/' + value.APERCU + '" style="max-height:120px;width:100%">' + 
						'<div class="caption" style="padding:2px;"><strong>' + value.DESIGN + '</strong><br>' + 
							'<span>' + NexoAPI.DisplayMoney( MainPrice ) + '</span><br>' + 
							'<br>' + 
							'<div class="btn-group btn-group-justified" role="group" aria-label="...">' +
								'<div class="btn-group">' +
									'<button class="btn btn-primary btn-sm filter-add-product" data-codebar="' + value.CODEBAR + '"><?php echo addslashes( __ ( 'Ajouter', 'nexo' ) );?></button>' + 
								'</div>' +
								'<div class="btn-group"><a href="" class="btn btn-default btn-sm filter-product-details" data-codebar="147852"><?php echo addslashes( __ ( 'Details', 'nexo' ) );?></a> </div>' +
							'</div>' +
						'</div>' +
					'</div>' );	
								
					v2Checkout.ItemsCategories	=	_.extend( v2Checkout.ItemsCategories, _.object( [ value.REF_CATEGORIE ], [ value.NOM ] ) );
				}
			});
			
			// Build Category for the filter
			this.buildItemsCategories();
			
			// Bind Add to Items
			this.bindAddToItems();
		} else {
			bootbox.alert( '<?php echo addslashes( __( 'Vous ne pouvez pas procéder à une vente, car aucun article n\'est disponible pour la vente.' ) );?>' );
		}
	};
	
	/**
	 * Build Items Categories
	 * @return void
	**/
	
	this.buildItemsCategories	=	function() {
		_.each( this.ItemsCategories, function( value, id ) {
			$( '.filter-by-categories' ).append( '<option value="' + id + '">' + value + '</option>' );
		});
		
		$( '.filter-by-categories' ).trigger( 'chosen:updated' );
	}
	
	/**
	 * Bind Add To Item
	 *
	 * @return void
	**/
	
	this.bindAddToItems			=	function(){
		$( '#filter-list' ).find( '[data-category]' ).each( function(){
			$( this ).find( '.filter-add-product' ).bind( 'click', function(){
				var codebar	=	$( this ).attr( 'data-codebar' );
				v2Checkout.fetchItem( codebar );
			});
		});
	};
	
	/**
	 * Fetch Items
	 * Check whether an item is available and add it to the cart items table
	 * @return void
	**/
	
	this.fetchItem				=	function( codebar, qte_to_add, allow_increase ) {
		
		allow_increase			=	typeof allow_increase	==	'undefined' ? true : allow_increase
		qte_to_add				=	typeof qte_to_add == 'undefined' ? 1 : qte_to_add;
		
		$.ajax( '<?php echo site_url( array( 'nexo', 'item' ) );?>/' + codebar + '/CODEBAR', {
			success				:	function( _item ){
				if( _item.length > 0 ) {					
					var InCart			=	false;
					var InCartIndex		=	null;
					// Let's check whether an item is already added to cart
					_.each( v2Checkout.CartItems, function( value, _index ) {
						if( value.CODEBAR == _item[0].CODEBAR ) {
							InCartIndex	=	_index;
							InCart		=	true;
						} 
					});
										
					if( InCart ) {	
						// if increase is disabled, we set value
						var comparison_qte	=	allow_increase == true ? parseInt( v2Checkout.CartItems[ InCartIndex ].QTE_ADDED ) + parseInt( qte_to_add ) : qte_to_add;
						console.log( comparison_qte );
						if( parseInt( _item[0].QUANTITE_RESTANTE ) - ( comparison_qte ) < 0 ) {
							tendoo.notify.error( 
								'<?php echo addslashes( __( 'Stock épuisé' ) );?>', 
								'<?php echo addslashes( __( 'Impossible d\'ajouter ce produit. La quantité restante du produit n\'est pas suffisante.', 'nexo' ) );?>' 
							);							
						} else {
							if( allow_increase ) {
								v2Checkout.CartItems[ InCartIndex ].QTE_ADDED	+=	parseInt( qte_to_add );
							} else {
								if( qte_to_add > 0 ){
									v2Checkout.CartItems[ InCartIndex ].QTE_ADDED	=	parseInt( qte_to_add );
								} else {
									bootbox.confirm( '<?php echo addslashes( __( 'Défininr "0" comme quantité, retirera le produit du panier. Voulez-vous continuer ?' ) );?>', function( response ) {
										// Delete item from cart when confirmed
										if( response ) {
											v2Checkout.CartItems.splice( InCartIndex, 1 );
											v2Checkout.buildCartItemTable();
										}
										
									});									
								}
							}
						} 
					} else {
						if( parseInt( _item[0].QUANTITE_RESTANTE ) - qte_to_add < 0 ) {
							tendoo.notify.error( 
								'<?php echo addslashes( __( 'Stock épuisé' ) );?>', 
								'<?php echo addslashes( __( 'Impossible d\'ajouter ce produit, car son stock est épuisé.', 'nexo' ) );?>' 
							);
						} else {
							v2Checkout.CartItems.unshift( _.extend( _item[0], _.object( [ 'QTE_ADDED' ], [ qte_to_add ] ) ) );
						}
					}
					
					// Build Cart Table Items
					v2Checkout.refreshCart();
					v2Checkout.buildCartItemTable();
					
				} else {
					tendoo.notify.error( '<?php echo addslashes( __( 'Erreur sur le code/article' ) );?>', '<?php echo addslashes( __( 'Impossible de récupérer l\'article, ce dernier est introuvable ou le code envoyé est incorrecte.' ) );?>' );
				}
			},
			dataType			:	'json',
			error				:	function(){
				tendoo.notify.error( '<?php echo addslashes( __( 'Une erreur s\'est produite' ) );?>', '<?php echo addslashes( __( 'Impossible de récupérer les données. Veuillez vérifier votre connexion internet' ) );?>' );
			}
			
		});
	};
	
	/**
	 * Is Cart empty
	 * @return boolean
	**/
	
	this.isCartEmpty			=	function(){
		if( _.toArray( this.CartItems ).length ) {
			return false;
		} 
		return true;
	}
	
	/**
	 * Refresh Cart
	 *
	**/
	
	this.refreshCart			=	function(){
		if( this.isCartEmpty() ) {
			$( '#cart-table-notice' ).show();
		} else {
			$( '#cart-table-notice' ).hide();
		}
	};
	
	/**
	 * Build Cart Item table
	 * @return void
	**/
	
	this.buildCartItemTable		=	function() {
		// Empty Cart item table first
		this.emptyCartItemTable();
		this.CartValue		=	0;
		var _tempCartValue	=	0;
		
		if( _.toArray( this.CartItems ).length > 0 ){
			_.each( this.CartItems, function( value, key ) {
				$( '#cart-table-body' ).find( 'table' ).append( 
					'<tr cart-item data-line-weight="' + ( value.PRIX_DE_VENTE * value.QTE_ADDED ) + '" data-item-barcode="' + value.CODEBAR + '">' + 
						'<td width="250" class="text-left">' + value.DESIGN + '</td>' +
						'<td width="100" class="text-center">' + NexoAPI.DisplayMoney( value.PRIX_DE_VENTE ) + '</td>' +
						'<td width="150" class="text-center"><span class="btn btn-primary btn-xs item-reduce">-</span> <input type="number" name="shop_item_quantity" value="' + value.QTE_ADDED + '" style="width:40px;border-radius:5px;border:solid 1px #CCC;" maxlength="3"/> <span class="btn btn-primary btn-xs item-add">+</span></td>' +
						'<td width="100" class="text-right">' + NexoAPI.DisplayMoney( value.PRIX_DE_VENTE * value.QTE_ADDED ) + '</td>' +
					'</tr>'
				);
				_tempCartValue	+=	( value.PRIX_DE_VENTE * value.QTE_ADDED );
			});	
			
			this.CartValue	=	_tempCartValue;
			
		} else {
			$( this.CartTableBody ).find( 'tbody' ).html( '<tr id="cart-table-notice"><td colspan="4"><?php _e( 'Veuillez ajouter un produit...', 'nexo' );?></td></tr>' );
		}
		
		this.bindAddReduceActions();
		this.bindAddByInput();
		this.refreshCartValues();
	}
	
	/**
	 * Bind Add Reduce Actions on Cart table items
	**/
	
	this.bindAddReduceActions	=	function(){
		
		$( '#cart-table-body .item-reduce' ).each(function(){
			$( this ).bind( 'click', function(){
				var parent	=	$( this ).closest( 'tr' );
				_.each( v2Checkout.CartItems, function( value, key ) {	
					if( value.CODEBAR == $( parent ).data( 'item-barcode' ) ) {
						value.QTE_ADDED--;
						// If item reach "0";
						if( value.QTE_ADDED == 0 ) {
							v2Checkout.CartItems.splice( key, 1 );
						}
					}
				});
				v2Checkout.buildCartItemTable();
			});
		});
		
		$( '#cart-table-body .item-add' ).each(function(){
			$( this ).bind( 'click', function(){
				var parent	=	$( this ).closest( 'tr' );
				v2Checkout.fetchItem( $( parent ).data( 'item-barcode' ), 1, true );
			});
		});
	};
	
	/**
	 * Bind Add by input
	**/
	
	this.bindAddByInput			=	function(){
		var currentInputValue	=	0;
		$( '[name="shop_item_quantity"]' ).bind( 'focus', function(){
			currentInputValue	=	$( this ).val();
		});
		$( '[name="shop_item_quantity"]' ).bind( 'change', function(){
			var parent 			=	$( this ).closest( 'tr' );
			var value			=	$( this ).val();
			var codebar			=	$( parent ).data( 'item-barcode' );
			
			if( value >= 0 ) {
				v2Checkout.fetchItem( codebar, value, false );
			} else {
				$( this ).val( currentInputValue );
			}
		});
	}
	
	/**
	 * Refresh Cart Values
	 *
	**/
	
	this.refreshCartValues		=	function(){
		$( '#cart-value' ).html( NexoAPI.DisplayMoney( this.CartValue ) );
		$( '#cart-vat' ).html( NexoAPI.DisplayMoney( this.CartVAT ) );
		$( '#cart-discount' ).html( NexoAPI.DisplayMoney( this.CartDiscount ) );
		$( '#cart-topay' ).html( NexoAPI.DisplayMoney( this.CartToPay ) );
	};
	
	/**
	 * Empty cart item table
	 *
	**/
	
	this.emptyCartItemTable		=	function() {
		$( '#cart-table-body' ).find( '[cart-item]' ).remove();
	};
	
	/**
	 * Init Cart Date
	 *
	**/
	
	this.initCartDateTime		=	function(){
		this.CartDateTime			=	moment( '<?php echo date_now();?>' );
		$( '.content-header h1' ).append( '<small class="pull-right" id="cart-date" style="display:none;line-height: 30px;"></small>' );
		
		setInterval( function(){
			v2Checkout.CartDateTime.add( 1, 's' );
			// YYYY-MM-DD 
			$( '#cart-date' ).html( v2Checkout.CartDateTime.format( 'HH:mm:ss' ) );
		},1000 );
		
		setTimeout( function(){
			$( '#cart-date' ).show( 500 );
		}, 1000 );
	};	 
	
	/**
	 * Run Checkout
	 *
	**/
	
	this.run						=	function(){
		this.fixHeight();
		this.resetCart();
		this.initCartDateTime();		
		
		$( this.ProductListWrapper ).css( 'visibility', 'visible');
		$( this.CartTableWrapper ).css( 'visibility', 'visible');
		
		this.getItems(null, function(){
			v2Checkout.hideSplash( 'right' );
		});
	}
	
};

$( document ).ready(function(e) {
	$( '.filter-by-categories' ).chosen();	
	$( '.filter-by-categories' ).bind( 'change', function(){
		v2Checkout.filterItems( $( '.filter-by-categories' ).val() );
	});
	
	v2Checkout.run();
});
</script>
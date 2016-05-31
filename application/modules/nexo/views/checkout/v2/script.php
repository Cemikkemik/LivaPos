<?php
$this->load->config( 'nexo' );
global $Options;
?>
<script type="text/javascript">
"use strict";

var v2Checkout					=	new function(){
	
	this.ProductListWrapper		=	'#product-list-wrapper';
	this.CartTableBody			=	'#cart-table-body';
	this.ItemsListSplash		=	'#product-list-splash';
	this.CartTableWrapper		=	'#cart-details-wrapper';
	this.CartTableBody			=	'#cart-table-body';
	this.CartDiscountButton		=	'#cart-discount-button';
	this.ProductSearchInput		=	'#search-product-code-bar';
	this.ItemsCategories		=	new Object;
	this.CartItems				=	new Array;
	this.CustomersGroups		=	new Array;
	this.CartVATEnabled			=	<?php echo @$Options[ 'nexo_enable_vat' ] == 'oui' ? 'true' : 'false';?>;
	this.CartVATPercent			=	<?php echo in_array( @$Options[ 'nexo_vat_percent' ], array( null, '' ) ) ? 0 : @$Options[ 'nexo_vat_percent' ];?>
	
	if( this.CartVATPercent == 0 ) {
		this.CartVATEnabled		=	false;
	}
	
	/**
	 * Reset Cart
	**/
	
	this.resetCart					=	function(){
		this.CartValue				=	0;
		this.CartVAT				=	0;
		this.CartDiscount			=	0;
		this.CartToPay				=	0;
		
		this.CartRemiseType			=	null;
		this.CartRemise				=	0;
		this.CartRemiseEnabled		=	false;
		this.CartRemisePercent		=	null;
		
		this.CartRistourneType		=	'<?php echo @$Options[ 'discount_type' ];?>';
		this.CartRistourneAmount	=	'<?php echo @$Options[ 'discount_amount' ];?>';
		this.CartRistournePercent	=	'<?php echo @$Options[ 'discount_percent' ];?>';
		this.CartRistourneEnabled	=	false;
		this.CartRistourne			=	0;
		
		this.cartGroupDiscountReset();
		
		this.CartRabais				=	0;
		
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
		var headerHeight		=	$( '.main-header' ).height();
		var contentHeader		=	$( '.content-header' ).outerHeight();
		var contentPadding		=	23;
		var windowHeight		=	window.innerHeight < 700 ? 700 : window.innerHeight;
		// Col 1
		var cartDetailsHeight	=	$( '#cart-details' ).outerHeight();
		var cartPanelHeight		=	$( '#cart-panel' ).outerHeight();
		var cartSearchHeight	=	$( '#cart-search-wrapper' ).outerHeight();
		var cartHeader			=	$( '#cart-header' ).outerHeight();
		var cartTableHeader		=	-9; // $( '#cart-item-table-header' ).outerHeight();
		var col1Height			=	windowHeight - ( ( cartDetailsHeight + cartPanelHeight + cartSearchHeight + cartHeader + cartTableHeader ) + ( ( headerHeight + contentHeader + contentPadding ) * 2 ) );
		$( this.CartTableBody ).height( col1Height );
		// Col 2				
		var searchProductInputHeight	=	$( this.ProductSearchInput ).height();
		var col2Height			=	windowHeight - ( -16 + ( headerHeight + contentHeader + contentPadding + searchProductInputHeight ) * 2 );
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
					
					var promo_start	= moment( value.SPECIAL_PRICE_START_DATE );
					var promo_end	= moment( value.SPECIAL_PRICE_END_DATE );	
					
					var MainPrice	= parseInt( value.PRIX_DE_VENTE )
					var Discounted	= '';
					var CustomBackground	=	'';
					
					if( promo_start.isBefore( v2Checkout.CartDateTime ) ) {
						if( promo_end.isSameOrAfter( v2Checkout.CartDateTime ) ) {
							MainPrice			=	parseInt( value.PRIX_PROMOTIONEL );
							Discounted			=	'<small style="color:#999;"><del>' + NexoAPI.DisplayMoney( parseInt( value.PRIX_DE_VENTE ) ) + '</del></small>';
							CustomBackground	=	'background:<?php echo $this->config->item( 'discounted_item_background' );?>';
						}
					}
					
					$( '#filter-list' ).append( 
					'<div class="col-lg-2 col-md-3 col-xs-3" style="' + CustomBackground + ';padding:5px; border-right: solid 1px #DEDEDE;border-bottom: solid 1px #DEDEDE;" data-category="' + value.REF_CATEGORIE + '">' +
						'<img src="<?php echo upload_url();?>' + value.APERCU + '" style="height:120px;width:100%">' + 
						'<div class="caption text-center" style="padding:2px;"><strong>' + value.DESIGN + '</strong><br>' + 
							'<span class="align-center">' + NexoAPI.DisplayMoney( MainPrice ) + '</span><br>' + Discounted + 
							'<br>' + 
							'<div class="btn-group btn-group-justified" role="group" aria-label="..." style="margin-top:5px;">' +
								'<div class="btn-group">' +
									'<button class="btn btn-primary btn-sm filter-add-product" data-codebar="' + value.CODEBAR + '"><?php echo addslashes( __ ( 'Ajouter', 'nexo' ) );?></button>' + 
								'</div>' +
								'<div class="btn-group"><a href="<?php echo site_url( 'dashboard/nexo/produits/lists/edit' );?>/' + value.ID + ' " class="btn btn-default btn-sm filter-product-details" data-codebar="147852"><?php echo addslashes( __ ( 'Details', 'nexo' ) );?></a> </div>' +
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
		
		// $( '.filter-by-categories' ).trigger( 'chosen:updated' );
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
				
				var promo_start	= moment( value.SPECIAL_PRICE_START_DATE );
				var promo_end	= moment( value.SPECIAL_PRICE_END_DATE );	
				
				var MainPrice	= parseInt( value.PRIX_DE_VENTE )
				var Discounted	= '';
				var CustomBackground	=	'';
				
				if( promo_start.isBefore( v2Checkout.CartDateTime ) ) {
					if( promo_end.isSameOrAfter( v2Checkout.CartDateTime ) ) {
						MainPrice			=	parseInt( value.PRIX_PROMOTIONEL );
						Discounted			=	'<small><del>' + NexoAPI.DisplayMoney( parseInt( value.PRIX_DE_VENTE ) ) + '</del></small>';
						CustomBackground	=	'background:<?php echo $this->config->item( 'discounted_item_background' );?>';
					}
				}
				
				$( '#cart-table-body' ).find( 'table' ).append( 
					'<tr cart-item data-line-weight="' + ( MainPrice * value.QTE_ADDED ) + '" data-item-barcode="' + value.CODEBAR + '">' + 
						'<td width="240" class="text-left">' + value.DESIGN + '</td>' +
						'<td width="140" class="text-center">' + NexoAPI.DisplayMoney( MainPrice ) + ' ' + Discounted + '</td>' +
						'<td width="120" class="text-center"><span class="btn btn-primary btn-xs item-reduce">-</span> <input type="number" name="shop_item_quantity" value="' + value.QTE_ADDED + '" style="width:40px;border-radius:5px;border:solid 1px #CCC;" maxlength="3"/> <span class="btn btn-primary btn-xs item-add">+</span></td>' +
						'<td width="100" class="text-right">' + NexoAPI.DisplayMoney( MainPrice * value.QTE_ADDED ) + '</td>' +
					'</tr>'
				);
				_tempCartValue	+=	( MainPrice * value.QTE_ADDED );
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
		
		<?php if( @$Options[ 'nexo_enable_numpad' ] != 'non' ):?>
		// Bind Num padd
		$( '[name="shop_item_quantity"]' ).bind( 'click', function(){
			v2Checkout.showNumPad( $( this ), '<?php echo addslashes( __( 'Définir la quantité à  ajouter', 'nexo' ) );?>' );
		});
		<?php endif;?>
	}
	
	/**
	 * Bind remove cart group discount
	**/
	
	this.bindRemoveCartGroupDiscount	=	function(){
		$( '.btn.cart-group-discount' ).each( function(){
			if( ! $( this ).hasClass( 'remove-action-bound' ) ) {
				$( this ).addClass( 'remove-action-bound' );
				$( this ).bind( 'click', function(){
					bootbox.confirm( '<?php echo addslashes( __( 'Souhaitez-vous annuler la réduction de groupe ?', 'nexo' ) );?>', function( action ) {
						if( action == true ) {
							v2Checkout.cartGroupDiscountReset();
							v2Checkout.refreshCartValues();
						}
					})
				});
			}
		});
	};
	
	/**
	 * Bind Remove Cart Remise
	 * Let use to cancel a discount directly from the cart table, when it has been added
	**/
	
	this.bindRemoveCartRemise	=	function(){
		$( '.btn.cart-discount' ).each( function(){
			if( ! $( this ).hasClass( 'remove-action-bound' ) ) {
				$( this ).addClass( 'remove-action-bound' );
				$( this ).bind( 'click', function(){
					bootbox.confirm( '<?php echo addslashes( __( 'Souhaitez-vous annuler cette remise ?', 'nexo' ) );?>', function( action ) {
						if( action == true ) {
							v2Checkout.CartRemise			=	0;
							v2Checkout.CartRemiseType		=	null;
							v2Checkout.CartRemiseEnabled	=	false;
							v2Checkout.CartRemisePercent	=	null;
							v2Checkout.refreshCartValues();
						}
					})
				});
			}
		});
	};
	
	/**
	 * Bind Remove Cart Ristourne
	**/
	
	this.bindRemoveCartRistourne=	function(){
		$( '.btn.cart-ristourne' ).each( function(){
			if( ! $( this ).hasClass( 'remove-action-bound' ) ) {
				$( this ).addClass( 'remove-action-bound' );
				$( this ).bind( 'click', function(){
					bootbox.confirm( '<?php echo addslashes( __( 'Souhaitez-vous annuler cette ristourne ?', 'nexo' ) );?>', function( action ) {
						if( action == true ) {
							v2Checkout.CartRistourne		=	0;
							v2Checkout.CartRistourneEnabled	=	false;
							v2Checkout.refreshCartValues();
						}
					})
				});
			}
		});
	};
	
	/**
	 * Bind Add Discount
	**/
	
	this.bindAddDiscount		=	function(){
		var	DiscountDom			=	
		'<div id="discount-box-wrapper">' + 
			'<h4 class="text-center"><?php echo addslashes( __( 'Appliquer une remise', 'nexo' ) );?><span class="discount_type"></h4><br>' + 
			'<div class="input-group input-group-lg">' +
			  '<span class="input-group-btn">' + 
				'<button class="btn btn-default percentage_discount" type="button"><?php echo addslashes( __( 'Pourcentage', 'nexo' ) );?></button>' + 
			  '</span>' + 
			  '<input type="number" name="discount_value" class="form-control" placeholder="<?php echo addslashes( __( 'Définir le montant ou le pourcentage ici...', 'nexo' ) );?>">' +
			  '<span class="input-group-btn">' + 
				'<button class="btn btn-default flat_discount" type="button"><?php echo addslashes( __( 'Espèces', 'nexo' ) );?></button>' + 
			  '</span>' + 
			'</div>' +
			'<br>' +
			'<div class="row">' +
				'<div class="col-lg-8">' +
					'<div class="row">' +
						'<div class="col-lg-3 col-md-3 col-xs-3">' +
							'<input type="button" class="btn btn-default btn-block btn-lg numpad7" value="<?php echo addslashes( __( '7', 'nexo' ) );?>"/>' +
						'</div>' +
						'<div class="col-lg-3 col-md-3 col-xs-3">' +
							'<input type="button" class="btn btn-default btn-block btn-lg numpad8" value="<?php echo addslashes( __( '8', 'nexo' ) );?>"/>' +
						'</div>' +
						'<div class="col-lg-3 col-md-3 col-xs-3">' +
							'<input type="button" class="btn btn-default btn-block btn-lg numpad9" value="<?php echo addslashes( __( '9', 'nexo' ) );?>"/>' +
						'</div>' +
						'<div class="col-lg-3 col-md-3 col-xs-3">' +
							'<input type="button" class="btn btn-warning btn-block btn-lg numpaddel" value="<?php echo addslashes( __( 'Del', 'nexo' ) );?>"/>' +
						'</div>' +
					'</div>' +
					'<br>'+
					'<div class="row">' +
						'<div class="col-lg-3 col-md-3 col-xs-3">' +
							'<input type="button" class="btn btn-default btn-block btn-lg numpad4" value="<?php echo addslashes( __( '4', 'nexo' ) );?>"/>' +
						'</div>' +
						'<div class="col-lg-3 col-md-3 col-xs-3">' +
							'<input type="button" class="btn btn-default btn-block btn-lg numpad5" value="<?php echo addslashes( __( '5', 'nexo' ) );?>"/>' +
						'</div>' +
						'<div class="col-lg-3 col-md-3 col-xs-3">' +
							'<input type="button" class="btn btn-default btn-block btn-lg numpad6" value="<?php echo addslashes( __( '6', 'nexo' ) );?>"/>' +
						'</div>' +
						'<div class="col-lg-3 col-md-3 col-xs-3">' +
							'<input type="button" class="btn btn-danger btn-block btn-lg numpadclear" value="<?php echo addslashes( __( 'Clear', 'nexo' ) );?>"/>' +
						'</div>' +
					'</div>' +
					'<br>'+
					'<div class="row">' +
						'<div class="col-lg-3 col-md-3 col-xs-3">' +
							'<input type="button" class="btn btn-default btn-block btn-lg numpad1" value="<?php echo addslashes( __( '1', 'nexo' ) );?>"/>' +
						'</div>' +
						'<div class="col-lg-3 col-md-3 col-xs-3">' +
							'<input type="button" class="btn btn-default btn-block btn-lg numpad2" value="<?php echo addslashes( __( '2', 'nexo' ) );?>"/>' +
						'</div>' +
						'<div class="col-lg-3 col-md-3 col-xs-3">' +
							'<input type="button" class="btn btn-default btn-block btn-lg numpad3" value="<?php echo addslashes( __( '3', 'nexo' ) );?>"/>' +
						'</div>' +
					'</div>' +
					'<br>' +
					'<div class="row">' +
						'<div class="col-lg-3 col-md-3 col-xs-3">' +
							'<input type="button" class="btn btn-default btn-block btn-lg numpad00" value="<?php echo addslashes( __( '00', 'nexo' ) );?>"/>' +
						'</div>' +
						'<div class="col-lg-6 col-md-6 col-xs-6">' +
							'<input type="button" class="btn btn-default btn-block btn-lg numpad0" value="<?php echo addslashes( __( '0', 'nexo' ) );?>"/>' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</div>' +
		'</div>';
		
		bootbox.confirm( DiscountDom, function( action ) {
			if( action == true ) {	
				var value	=	$( '[name="discount_value"]' ).val();
				console.log( value );
				if( value  == '' || value == '0' ) {
					bootbox.alert( '<?php echo addslashes( __( 'Vous devez définir un pourcentage ou une somme.', 'nexo' ) );?>' );
					return false;
				}
			
				$( '[name="discount_value"]' ).focus();
				$( '[name="discount_value"]' ).blur();
				
				v2Checkout.calculateCartDiscount( value );	
				v2Checkout.refreshCartValues();			
			}
		});
		
		$( '.percentage_discount' ).bind( 'click', function(){
			if( ! $( this ).hasClass( 'active' ) ) {
				if( $( '.flat_discount' ).hasClass( 'active' ) ) {
					$( '.flat_discount' ).removeClass( 'active' );
				}
				
				$( this ).addClass( 'active' );
				
				// Proceed a quick check on the percentage value
				$( '[name="discount_value"]' ).focus();
				
				v2Checkout.CartRemiseType	=	'percentage';
				
				$( '.discount_type' ).html( '<?php echo addslashes( __( ' : <span class="label label-primary">au pourcentage</span>', 'nexo' ) );?>' );
			}
		});
		
		$( '.flat_discount' ).bind( 'click', function(){
			if( ! $( this ).hasClass( 'active' ) ) {
				if( $( '.percentage_discount' ).hasClass( 'active' ) ) {
					$( '.percentage_discount' ).removeClass( 'active' );
				}
				
				$( this ).addClass( 'active' );
				
				$( '[name="discount_value"]' ).focus();
				$( '[name="discount_value"]' ).blur();
				
				v2Checkout.CartRemiseType	=	'flat';
				
				$( '.discount_type' ).html( '<?php echo addslashes( __( ' : <span class="label label-info">à prix fixe</span>', 'nexo' ) );?>' );
			}
		});
		
		// Fillback form
		if( v2Checkout.CartRemiseType != null ) {
			$( '.' + v2Checkout.CartRemiseType + '_discount' ).trigger( 'click' );
			
			if( v2Checkout.CartRemiseType == 'percentage' ) {
				$( '[name="discount_value"]' ).val( v2Checkout.CartRemisePercent );
			} else if( v2Checkout.CartRemiseType == 'flat' ) {
				$( '[name="discount_value"]' ).val( v2Checkout.CartRemise );
			}
			
		} else {
			$( '.flat_discount' ).trigger( 'click' );	
		}
		
		$( '[name="discount_value"]' ).bind( 'blur', function(){

			if( parseInt( $( this ).val() ) < 0 ) {
				$( this ).val( 0 );
			}
			
			// Percentage allowed to 100% only
			if( v2Checkout.CartRemiseType == 'percentage' && parseInt( $( '[name="discount_value"]' ).val() ) > 100 ) {
				$( this ).val( 100 );
			}
		});
		
		for( var i = 0; i <= 9; i++ ) {
			$( '#discount-box-wrapper' ).find( '.numpad' + i ).bind( 'click', function(){
				var current_value	=	$( '[name="discount_value"]' ).val();
					current_value	=	current_value == '0' ? '' : current_value;
				$( '[name="discount_value"]' ).val( current_value + $( this ).val() );
			});
		}
		
		$( '.numpadclear' ).bind( 'click', function(){
			$( '[name="discount_value"]' ).val(0);
		});
		
		$( '.numpad00' ).bind( 'click', function(){
			var current_value	=	$( '[name="discount_value"]' ).val();
				current_value	=	current_value == '0' ? '' : current_value;
			$( '[name="discount_value"]' ).val( current_value + '00' );
		});
		
		$( '.numpaddot' ).bind( 'click', function(){
			var current_value	=	$( '[name="discount_value"]' ).val();
				current_value	=	current_value == '0' ? '' : current_value;
			$( '[name="discount_value"]' ).val( current_value + '...' );
		});
		
		$( '.numpaddel' ).bind( 'click', function(){
			var numpad_value	=	$( '[name="discount_value"]' ).val();
				numpad_value	=	numpad_value.substr( 0, numpad_value.length - 1 );
				numpad_value 	= 	numpad_value == '' ? 0 : numpad_value;
			$( '[name="discount_value"]' ).val( numpad_value );
		});
	};
	
	/**
	 * Show Numpad
	**/
	
	this.showNumPad				=	function( object, text ){
		var NumPad				=	
		'<form id="numpad">' + 
			'<h4 class="text-center">' + ( text ? text : '' ) + '</h4><br>' +
			'<div class="form-group">' +
				'<input type="number" class="form-control input-lg" name="numpad_field"/>' +
			'</div>' +
			'<div class="row">' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad7" value="<?php echo addslashes( __( '7', 'nexo' ) );?>"/>' +
				'</div>' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad8" value="<?php echo addslashes( __( '8', 'nexo' ) );?>"/>' +
				'</div>' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad9" value="<?php echo addslashes( __( '9', 'nexo' ) );?>"/>' +
				'</div>' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad0" value="<?php echo addslashes( __( '0', 'nexo' ) );?>"/>' +
				'</div>' +
				'<div class="col-lg-4 col-md-4 col-xs-4">' +
					'<input type="button" class="btn btn-warning btn-block btn-lg numpaddel" value="<?php echo addslashes( __( 'Del', 'nexo' ) );?>"/>' +
				'</div>' +
			'</div>' +
			'<br>'+
			'<div class="row">' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad4" value="<?php echo addslashes( __( '4', 'nexo' ) );?>"/>' +
				'</div>' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad5" value="<?php echo addslashes( __( '5', 'nexo' ) );?>"/>' +
				'</div>' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad6" value="<?php echo addslashes( __( '6', 'nexo' ) );?>"/>' +
				'</div>' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpadplus" value="<?php echo addslashes( __( '+', 'nexo' ) );?>"/>' +
				'</div>' +
				'<div class="col-lg-4 col-md-4 col-xs-4">' +
					'<input type="button" class="btn btn-danger btn-block btn-lg numpadclear" value="<?php echo addslashes( __( 'Clear', 'nexo' ) );?>"/>' +
				'</div>' +
			'</div>' +
			'<br>'+
			'<div class="row">' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad1" value="<?php echo addslashes( __( '1', 'nexo' ) );?>"/>' +
				'</div>' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad2" value="<?php echo addslashes( __( '2', 'nexo' ) );?>"/>' +
				'</div>' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad3" value="<?php echo addslashes( __( '3', 'nexo' ) );?>"/>' +
				'</div>' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpadminus" value="<?php echo addslashes( __( '-', 'nexo' ) );?>"/>' +
				'</div>' +
			'</div>' +
		'</form>'	
				
		bootbox.confirm( NumPad, function( action ) {
			if( action == true ) {
				$( object ).val( $( '[name="numpad_field"]' ).val() );
				$( object ).trigger( 'change' );
			}
		});
		
		if( $( '[name="numpad_field"]' ).val() == '' ) {
			$( '[name="numpad_field"]' ).val(0);
		}
		
		$( '[name="numpad_field"]' ).focus();
		
		$( '[name="numpad_field"]' ).val( $( object ).val() );
		
		for( var i = 0; i <= 9; i++ ) {
			$( '#numpad' ).find( '.numpad' + i ).bind( 'click', function(){
				var current_value	=	$( '[name="numpad_field"]' ).val();
					current_value	=	current_value == '0' ? '' : current_value;
				$( '[name="numpad_field"]' ).val( current_value + $( this ).val() );
			});
		}
		
		$( '.numpadclear' ).bind( 'click', function(){
			$( '[name="numpad_field"]' ).val(0);
		});
		
		$( '.numpadplus' ).bind( 'click', function(){
			var numpad_value	=	parseInt( $( '[name="numpad_field"]' ).val() );
			$( '[name="numpad_field"]' ).val( ++numpad_value );
		});
		
		$( '.numpadminus' ).bind( 'click', function(){
			var numpad_value	=	parseInt( $( '[name="numpad_field"]' ).val() );
			$( '[name="numpad_field"]' ).val( --numpad_value );
		});
		
		$( '.numpaddel' ).bind( 'click', function(){
			var numpad_value	=	$( '[name="numpad_field"]' ).val();
				numpad_value	=	numpad_value.substr( 0, numpad_value.length - 1 );
				numpad_value 	= 	numpad_value == '' ? 0 : numpad_value;
			$( '[name="numpad_field"]' ).val( numpad_value );
		});
		
		$( '[name="numpad_field"]' ).blur( function(){
			if( $( this ).val() == '' ) {
				$( this ).val(0);
			}
		});
	};
	
	/**
	 * Calculate Cart discount
	**/
	
	this.calculateCartDiscount		=	function( value ) {
		
		// this.CartRemise			=	0;
		this.CartRemiseEnabled	=	true;
		
		if( value == '' ) {
			this.CartRemiseEnabled	=	false;
		}
		
		// Display Notice
		if( $( '.cart-discount-notice-area' ).find( '.cart-discount' ).length > 0 ) {
			$( '.cart-discount-notice-area' ).find( '.cart-discount' ).remove();
		} 
				
		if( this.CartRemiseType == 'percentage' ) {
			if( typeof value != 'undefined' ) {
				this.CartRemisePercent	=	parseInt( value );
			}	
			
			// Only if the cart is not empty
			if( this.CartValue > 0 ) {	
				this.CartRemise			=	( this.CartRemisePercent * this.CartValue ) / 100;
			} else {
				this.CartRemise			=	0;
			}
						
			if( this.CartRemiseEnabled ) {
				$( '.cart-discount-notice-area' ).append( '<span style="cursor: pointer;margin:0px 2px;" class="btn btn-primary btn-xs cart-discount"><i class="fa fa-remove"></i> <?php echo addslashes( __( 'Remise : ', 'nexo' ) );?>' + this.CartRemisePercent + '%</span>' );
			}
			
		} else if( this.CartRemiseType == 'flat' ) {
			if( typeof value != 'undefined' ) {
				this.CartRemise 			=	parseInt( value );
			}
			
			if( this.CartRemiseEnabled ) {
				$( '.cart-discount-notice-area' ).append( '<span style="cursor: pointer;margin:0px 2px;" class="btn btn-primary btn-xs cart-discount"><i class="fa fa-remove"></i> <?php echo addslashes( __( 'Remise : ', 'nexo' ) );?>' + NexoAPI.DisplayMoney( this.CartRemise ) + '</span>' );
			}
		}
		
		this.bindRemoveCartRemise();
	}
	
	/**
	 * Calculate cart ristourne
	**/
	
	this.calculateCartRistourne		=	function(){
			
		// Will be overwritten by enabled ristourne
		this.CartRistourne			=	0;

		$( '.cart-discount-notice-area' ).find( '.cart-ristourne' ).remove();
		
		if( this.CartRistourneEnabled ) {
			
			if( this.CartRistourneType == 'percent' ) {
				
				if( this.CartRistournePercent != '' ) {
					this.CartRistourne	=	( parseInt( this.CartRistournePercent ) * this.CartValue ) / 100;
				}
				
				$( '.cart-discount-notice-area' ).append( '<span style="cursor: pointer; margin:0px 2px;" class="btn btn-info btn-xs cart-ristourne"><i class="fa fa-remove"></i> <?php echo addslashes( __( 'Ristourne : ', 'nexo' ) );?>' + this.CartRistournePercent + '%</span>' );

			} else if( this.CartRistourneType == 'amount' ) {
				if( this.CartRistourneAmount != '' ) {
					this.CartRistourne	=	parseInt( this.CartRistourneAmount );
				}
				
				$( '.cart-discount-notice-area' ).append( '<span style="cursor: pointer;margin:0px 2px;" class="btn btn-info btn-xs cart-ristourne"><i class="fa fa-remove"></i> <?php echo addslashes( __( 'Ristourne : ', 'nexo' ) );?>' + NexoAPI.DisplayMoney( this.CartRistourneAmount ) + '</span>' );

			}
			
			this.bindRemoveCartRistourne();			
		}
	}
	
	/**
	 * Calculate Group Discount
	**/
	
	this.calculateCartGroupDiscount	=	function(){
		
		$( '.cart-discount-notice-area' ).find( '.cart-group-discount' ).remove();
		
		if( this.CartGroupDiscountEnabled == true ) {
			if( this.CartGroupDiscountType == 'percent' ) {
				if( this.CartGroupDiscountPercent != '' ) {
					this.CartGroupDiscount		=	( parseInt( this.CartGroupDiscountPercent ) * this.CartValue ) / 100;
					
					$( '.cart-discount-notice-area' ).append( '<span style="cursor: pointer; margin:0px 2px;" class="btn btn-warning btn-xs cart-group-discount"><i class="fa fa-remove"></i> <?php echo addslashes( __( 'Remise de groupe : ', 'nexo' ) );?>' + this.CartGroupDiscountPercent + '%</span>' );
				}
			} else if( this.CartGroupDiscountType == 'amount' ) {
				if( this.CartGroupDiscountAmount != '' ) {
				this.CartGroupDiscount		=	parseInt( this.CartGroupDiscountAmount )	;
					
					$( '.cart-discount-notice-area' ).append( '<span style="cursor: pointer; margin:0px 2px;" class="btn btn-warning btn-xs cart-group-discount"><i class="fa fa-remove"></i> <?php echo addslashes( __( 'Remise de groupe : ', 'nexo' ) );?>' + NexoAPI.DisplayMoney( this.CartGroupDiscountAmount ) + '</span>' );
				}
			}
			
			this.bindRemoveCartGroupDiscount();
		}
	};
	
	/**
	 * Refresh Cart Values
	 *
	**/
	
	this.refreshCartValues		=	function(){	
		
		this.calculateCartDiscount();
		this.calculateCartRistourne();
		this.calculateCartGroupDiscount();

		this.CartDiscount		=	( this.CartRemise + this.CartRabais + this.CartRistourne + this.CartGroupDiscount );
		this.CartToPay			=	this.CartValue - this.CartDiscount;
		
		$( '#cart-value' ).html( NexoAPI.DisplayMoney( this.CartValue ) );
		$( '#cart-vat' ).html( NexoAPI.DisplayMoney( this.CartVAT ) );
		$( '#cart-discount' ).html( NexoAPI.DisplayMoney( this.CartDiscount ) );
		$( '#cart-topay' ).html( NexoAPI.DisplayMoney( this.CartToPay ) );
	};
	
	/**
	 * Display specific error
	**/
	
	this.showError				=	function( error_type ) {
		if( error_type == 'ajax_fetch' ) {
			bootbox.alert( '<?php echo addslashes( __( 'Une erreur s\'est produite durant la récupération des données', 'nexo' ) );?>' );
		}
	}
	
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
	 * Customer DropDown Menu
	**/
	
	this.customers			=	new function(){
		
		this.DefaultCustomerID	=	'<?php echo @$Options[ 'default_compte_client' ];?>';
		
		/**
		 * Bind
		**/
		
		this.bind				=	function(){
			$('.dropdown-bootstrap').selectpicker({
			  style: 'btn-default',
			  size: 4
			});
			
			$( '.cart-add-customer' ).bind( 'click', function(){
				v2Checkout.customers.createBox();
			})
			
			$( '.customers-list' ).bind( 'change', function(){
				v2Checkout.customers.bindSelectCustomer( $( this ).val() );
			});
		}
		
		/**
		 * Create Box
		**/
		
		this.createBox			=	function(){
			var userForm		=	
			'<form id="NewClientForm" method="POST">' +
			'<?php echo tendoo_warning(addslashes(__('Toutes les autres informations comme la "date de naissance" pourront être remplis ultérieurement.', 'nexo')));?>' +
				'<div class="form-group">'+
					'<div class="input-group">' +
					  '<span class="input-group-addon" id="basic-addon1"><?php echo addslashes(__('Nom du client', 'nexo'));?></span>'+
					  '<input type="text" class="form-control" placeholder="<?php echo addslashes(__('Name', 'nexo'));?>" name="customer_name" aria-describedby="basic-addon1">' +
					'</div>'+
				'</div>' +
				'<div class="form-group">'+
					'<div class="input-group">' +
					  '<span class="input-group-addon" id="basic-addon1"><?php echo addslashes(__('Prénom du client', 'nexo'));?></span>'+
					  '<input type="text" class="form-control" placeholder="<?php echo addslashes(__('Prénom', 'nexo'));?>" name="customer_surname" aria-describedby="basic-addon1">' +
					'</div>'+
				'</div>' +
				'<div class="form-group">'+
					'<div class="input-group">' +
					  '<span class="input-group-addon" id="basic-addon1"><?php echo addslashes(__('Email du client', 'nexo'));?></span>'+
					  '<input type="text" class="form-control" placeholder="<?php echo addslashes(__('Email', 'nexo'));?>" name="customer_email" aria-describedby="basic-addon1">' +
					'</div>'+
				'</div>' +
				'<div class="form-group">'+
					'<div class="input-group">' +
					  '<span class="input-group-addon" id="basic-addon1"><?php echo addslashes(__('Téléphone du client', 'nexo'));?></span>'+
					  '<input type="text" class="form-control" placeholder="<?php echo addslashes(__('Téléphone', 'nexo'));?>" name="customer_tel" aria-describedby="basic-addon1">' +
					'</div>'+
				'</div>' +
				'<div class="form-group">'+
					'<div class="input-group">' +
					  '<span class="input-group-addon" id="basic-addon1"><?php echo addslashes(__('Group du client', 'nexo'));?></span>'+
					  '<select type="text" class="form-control customers_groups" name="customer_group" aria-describedby="basic-addon1">' +
					  	'<option value=""><?php echo addslashes( __( 'Veuillez choisir un client', 'nexo' ) );?></option>' +
					  '</select>' +
					'</div>'+
				'</div>' +
			'</form>';
			
			bootbox.confirm( userForm, function( action ) {
				if( action ) {
					return v2Checkout.customers.create( 
						$( '[name="customer_name"]' ).val(),
						$( '[name="customer_surname"]' ).val(),
						$( '[name="customer_email"]' ).val(),
						$( '[name="customer_tel"]' ).val(),
						$( '[name="customer_group"]' ).val()
					);
				}
			});
			
			_.each( v2Checkout.CustomersGroups, function( value, key ) {
				$( '.customers_groups' ).append( '<option value="' + value.ID + '">' + value.NAME + '</option>' );
			});
		};
		
		/**
		 * Create Customer
		 *
		 * @params string user name
		 * @params string user surname
		 * @params string user email
		 * @params string user phone
		 * @params int user group
		 * @return bool
		**/
		 
		this.create				=	function( name, surname, email, phone, ref_group ) {
			// Name is required
			if( name == '' ) {
				bootbox.alert( '<?php echo addslashes( __( 'Vous devez définir le nom du client', 'nexo' ) );?>' );
				return false;
			}
			// Group is required
			if( ref_group == '' ) {
				bootbox.alert( '<?php echo addslashes( __( 'Vous devez choisir un groupe pour le client', 'nexo' ) );?>' );
				return false;
			}
			// Ajax
			$.ajax( '<?php echo site_url( array( 'nexo', 'customer' ) );?>', {
				dataType		:	'json', 
				type			:	'POST',
				data			:	_.object( [ 'nom', 'prenom', 'email', 'tel', 'ref_group' ], [ name, surname, email, phone, ref_group ] ),
				success			:	function(){
					v2Checkout.customers.get();
				}
			});
		}
		 
		
		/**
		 * Bind select customer
		 * Check if a specific customer due to his purchages or group
		 * should have a discount
		**/
		
		this.bindSelectCustomer	=	function( customer_id ){
			// Reset Ristourne if enabled
			v2Checkout.CartRistourneEnabled				=	false;
			
			if( customer_id != this.DefaultCustomerID ) {
				// DISCOUNT_ACTIVE
				$.ajax( '<?php echo site_url( array( 'nexo', 'customer' ) );?>/' + customer_id, {
					error		:	function(){
						v2Checkout.showError( 'ajax_fetch' );
					},
					dataType	:	'json',
					success		:	function( data ) {
						v2Checkout.customers.check_discounts( data );
						v2Checkout.customers.check_groups_discounts( data );
					}
				});
			} else {
				// Refresh Cart Value;
				v2Checkout.refreshCartValues();
			}
		};
		
		/** 
		 * Check discount for the customer
		 * @params object customer data
		 * @return void
		**/
		
		this.check_discounts	=	function( object ) {
			if( typeof object == 'object' ) {
				
				_.each( object, function( value, key ) {
					if( value.DISCOUNT_ACTIVE == '1' ) {
						v2Checkout.CartRistourneEnabled 	=	true;
					}
				});
				
				// Refresh Cart value;				
				v2Checkout.refreshCartValues();
			}
		};
		
		/**
		 * Check discount for user group
		 * @params object customer data
		 * @return void
		**/
		
		this.check_groups_discounts	=	function( object ){
			
			// Reset Groups Discounts
			v2Checkout.cartGroupDiscountReset();
			
			if( typeof object == 'object' ) {
				
				_.each( object, function( Customer, key ) {
					// Looping each groups to check whether this customer belong to one existing group
					_.each( v2Checkout.CustomersGroups, function( Group, Key ) {
						if( Customer.REF_GROUP == Group.ID ) {
							// If current customer belong to this group, let see if this group has active discount
							if( Group.DISCOUNT_TYPE == 'percent' ) {
								v2Checkout.CartGroupDiscountType	=	Group.DISCOUNT_TYPE;
								v2Checkout.CartGroupDiscountPercent	=	Group.DISCOUNT_PERCENT;	
								v2Checkout.CartGroupDiscountEnabled	=	true;
							} else if( Group.DISCOUNT_TYPE == 'amount' ) {
								v2Checkout.CartGroupDiscountType	=	Group.DISCOUNT_TYPE;
								v2Checkout.CartGroupDiscountAmount	=	Group.DISCOUNT_AMOUNT;	
								v2Checkout.CartGroupDiscountEnabled	=	true;
							}
						}
					});
				});
				
				// Refresh Cart value;				
				v2Checkout.refreshCartValues();
			}
		};
		
		/**
		 * Get Customers
		**/
		
		this.get		=	function(){
			$.ajax( '<?php echo site_url( array( 'nexo', 'customer' ) );?>', {
				dataType		:	'json',
				success			:	function( customers ){
					
					$( '.customers-list' ).selectpicker('destroy');
					
					// Empty list first	
					$( '.customers-list' ).html('');
					
					_.each( customers, function( value, key ){
						$( '.customers-list' ).append( '<option value="' + value.ID + '">' + value.NOM + '</option>' );
					});
					
					$( '.customers-list' ).selectpicker( 'refresh' );
					
				},
				error			:	function(){
					bootbox.alert( '<?php echo addslashes( __( 'Une erreur s\'est produite durant la récupération des clients', 'nexo' ) );?>' );
				}
			});
		}
		
		/**
		 * Get Customers Groups
		**/
		
		this.getGroups			=	function(){
			$.ajax( '<?php echo site_url( array( 'nexo', 'customers_groups' ) );?>', {
				dataType		:	'json',
				success			:	function( customers ){
					
					v2Checkout.CustomersGroups	=	customers;
					
				},
				error			:	function(){
					bootbox.alert( '<?php echo addslashes( __( 'Une erreur s\'est produite durant la récupération des groupes des clients', 'nexo' ) );?>' );
				}
			});
		}
		
		/**
		 * Start
		**/
		
		this.run				=	function(){
			this.bind();
			this.get();
			this.getGroups();
		};
	}
	
	/**
	 * Cart Group Reset
	**/
	
	this.cartGroupDiscountReset	=	function(){
		this.CartGroupDiscount				=	0; // final amount
		this.CartGroupDiscountAmount		=	0; // Amount set on each group
		this.CartGroupDiscountPercent		=	0; // percent set on each group
		this.CartGroupDiscountType			=	null; // Discount type
		this.CartGroupDiscountEnabled		=	false;
		
		$( '.cart-discount-notice-area' ).find( '.cart-group-discount' ).remove();
	}
	
	/**
	 * Run Checkout
	 *
	**/
	
	this.run						=	function(){
		
		this.customers.run();
		
		this.fixHeight();
		this.resetCart();
		this.initCartDateTime();		
		
		$( this.ProductListWrapper ).css( 'visibility', 'visible');
		$( this.CartTableWrapper ).css( 'visibility', 'visible');
		
		this.getItems(null, function(){
			v2Checkout.hideSplash( 'right' );
		});
		
		$( this.CartDiscountButton ).bind( 'click', function(){
			v2Checkout.bindAddDiscount();
		});		
	}
	
};

$( document ).ready(function(e) {
	// $( '.filter-by-categories' ).chosen();	
	/*$( '.filter-by-categories' ).bind( 'change', function(){
		v2Checkout.filterItems( $( '.filter-by-categories' ).val() );
	});*/
	
	v2Checkout.run();
});
</script>
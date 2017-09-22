<?php
$this->load->config('nexo');
global $Options, $store_id;
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
	this.ItemSettings			=	'.item-list-settings';
	this.ItemSearchForm			=	'#search-item-form';
	this.CartPayButton			=	'#cart-pay-button';
	this.CartCancelButton		=	'#cart-return-to-order';
	this.From 				=	null;
	// @since 3.x
	this.enableBarcodeSearch	=	false;

	this.CartVATEnabled			=	<?php echo @$Options[ store_prefix() . 'nexo_enable_vat' ] == 'oui' ? 'true' : 'false';?>;
	this.CartVATPercent			=	<?php echo in_array(@$Options[ store_prefix() . 'nexo_vat_percent' ], array( null, '' )) ? 0 : @$Options[ store_prefix() . 'nexo_vat_percent' ];?>;
	this.CartShowItemVAT  		=	<?php echo store_option( 'show_item_taxes' ) == 'yes' ? 'true' : 'false';?>;

	if( this.CartVATPercent == '0' ) {
		this.CartVATEnabled		=	false;
	}

	/**
	 *  Add on cart
	 *  @param object item to fetch
	 *  @return void
	**/

	this.addOnCart 				=	function(_item, codebar, qte_to_add, allow_increase, filter) {

		/**
		* If Item is "On Sale"
		**/

		if( _item.length > 0 && _item[0].STATUS == '1' ) {

			var InCart				=	false;
			var InCartIndex			=	null;

			// Let's check whether an item is already added to cart
			_.each( v2Checkout.CartItems, function( value, _index ) {
				// let check whether the item is an inline item
				// the item must not be inline to be added over an existing item
				if( value.CODEBAR == _item[0].CODEBAR && ! value.INLINE ) {
					InCartIndex		=	_index;
					InCart			=	true;
				}
			});

			if( InCart ) {

				// if increase is disabled, we set value
				var comparison_qte	=	allow_increase == true ? parseInt( v2Checkout.CartItems[ InCartIndex ].QTE_ADDED ) + parseInt( qte_to_add ) : qte_to_add;

				/**
				* 	For "Out of Stock" notice to work, item must be physical
				* 	and Stock management must be enabled
				**/

				if(
					parseInt( _item[0].QUANTITE_RESTANTE ) - ( comparison_qte ) < 0
					&& _item[0].TYPE == '1'
					&& _item[0].STOCK_ENABLED == '1'
				) {
					NexoAPI.Notify().warning(
						'<?php echo _s( 'Une erreur s\'est produite', 'nexo' );?>',
						'<?php echo addslashes(__( 'La quantité restante du produit n\'est pas suffisante.', 'nexo' ) );?>'
					);
				} else {
					if( allow_increase ) {
						// Fix concatenation when order was edited
						v2Checkout.CartItems[ InCartIndex ].QTE_ADDED	=	parseInt( v2Checkout.CartItems[ InCartIndex ].QTE_ADDED );
						v2Checkout.CartItems[ InCartIndex ].QTE_ADDED	+=	parseInt( qte_to_add );
					} else {
						if( qte_to_add > 0 ){
							v2Checkout.CartItems[ InCartIndex ].QTE_ADDED	=	parseInt( qte_to_add );
						} else {
							NexoAPI.Bootbox().confirm( '<?php echo addslashes(__('Défininr "0" comme quantité, retirera le produit du panier. Voulez-vous continuer ?', 'nexo'));?>', function( response ) {
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
				if( 
					parseInt( _item[0].QUANTITE_RESTANTE ) - qte_to_add < 0 
					&& _item[0].TYPE == '1'
					&& _item[0].STOCK_ENABLED == '1'
				) {
					NexoAPI.Notify().warning(
						'<?php echo addslashes(__('Stock épuisé', 'nexo'));?>',
						'<?php echo addslashes(__('Impossible d\'ajouter ce produit, car son stock est épuisé.', 'nexo'));?>'
					);
				} else {
					// improved @since 2.7.3
					// add meta by default
					var ItemMeta	=	NexoAPI.events.applyFilters( 'items_metas', [] );

					var FinalMeta	=	[ [ 'QTE_ADDED' ], [ qte_to_add ] ] ;

					_.each( ItemMeta, function( value, key ) {
						FinalMeta[0].push( _.keys( value )[0] );
						FinalMeta[1].push( _.values( value )[0] );
					});

					// @since 2.9.0
					// add unit item discount
					_item[0].DISCOUNT_TYPE		=	'percentage'; // has two type, "percent" and "flat";
					_item[0].DISCOUNT_AMOUNT	=	0;
					_item[0].DISCOUNT_PERCENT	=	0;

					v2Checkout.CartItems.unshift( _.extend( _item[0], _.object( FinalMeta[0], FinalMeta[1] ) ) );
				}
			}

			// Add Item To Cart
			NexoAPI.events.doAction( 'add_to_cart', v2Checkout );

			// Build Cart Table Items
			v2Checkout.refreshCart();
			v2Checkout.buildCartItemTable();

		} else {
			NexoAPI.Notify().error( '<?php echo addslashes(__('Impossible d\'ajouter l\'article', 'nexo'));?>', '<?php echo addslashes(__('Impossible de récupérer l\'article, ce dernier est introuvable, indisponible ou le code envoyé est incorrecte.', 'nexo'));?>' );
		}
	}

	/**
	* Reloaded Add To Cart
	* @param object
	* @return void
	**/

	this.addToCart 	=	function({ item, barcode, quantity = 1, index = null, increase = true, filter = null}) {

		// If we're just adding new quantity (not increasing). We should restore already added quantity
		// if the item has already been added.
		let currentQty 	=	0;

		if( index != null ) {
			item 		=	this.CartItems[ index ];
			currentQty 	=	parseInt( this.CartItems[ index ].QTE_ADDED );
		}

		/**
		* If Item is "On Sale"
		**/

		if( item.STATUS == '1' ) {
			let gridItem 			=	$( '.item-list-container' )
			.find( '[data-codebar="' + item.CODEBAR + '"]' );
			let remainingQuantity 	=	parseInt( $( gridItem ).attr( 'data-remaining-quantity' ) );
			let testQuantity 		=	quantity;

			// if we're just increasing an existing item.
			// proceed to some check before
			if( increase && index ) {
				testQuantity 		=	parseInt( v2Checkout.CartItems[ index ].QTE_ADDED ) + quantity;
			}

			console.log( testQuantity );

			if( 
				remainingQuantity - testQuantity < 0 
				&& item.TYPE == '1'
				&& item.STOCK_ENABLED == '1'
			) {
				NexoAPI.Notify().warning(
					'<?php echo addslashes(__('Stock épuisé', 'nexo'));?>',
					'<?php echo addslashes(__('Impossible d\'ajouter ce produit, car son stock est épuisé.', 'nexo'));?>'
				);
			} else {

				// update grid item remaining quantity when the stock management of this item is enabled
				if( item.STOCK_ENABLED == '1' && item.TYPE == '1' ) {
					if( increase ) {
						$( gridItem ).attr( 'data-remaining-quantity', remainingQuantity - quantity );					
					} else {
						// restore added quantity
						$( gridItem ).attr( 'data-remaining-quantity', ( remainingQuantity + currentQty ) - quantity );
					}
				}

				if( index != null ) {
					if( this.CartItems[ index ] ) {
						if( increase == true ) {
							this.CartItems[ index ].QTE_ADDED 	+=	quantity
						} else {
							this.CartItems[ index ].QTE_ADDED 	=	quantity
						}
					}
				} else {
					// improved @since 2.7.3
					// add meta by default
					var ItemMeta	=	NexoAPI.events.applyFilters( 'items_metas', [] );
					var FinalMeta	=	[ [ 'QTE_ADDED' ], [ quantity ] ] ;

					_.each( ItemMeta, function( value, key ) {
						FinalMeta[0].push( _.keys( value )[0] );
						FinalMeta[1].push( _.values( value )[0] );
					});

					// @since 2.9.0
					// add unit item discount
					item.DISCOUNT_TYPE		=	'percentage'; // has two type, "percent" and "flat";
					item.DISCOUNT_AMOUNT	=	0;
					item.DISCOUNT_PERCENT	=	0;

					v2Checkout.CartItems.unshift( _.extend( item, _.object( FinalMeta[0], FinalMeta[1] ) ) );
					
					// Add Item To Cart
				}

				NexoAPI.events.doAction( 'add_to_cart', v2Checkout );

				// Build Cart Table Items
				v2Checkout.refreshCart();
				v2Checkout.buildCartItemTable();
			}
		} else {
			NexoAPI.Notify().error( '<?php echo addslashes(__('Impossible d\'ajouter l\'article', 'nexo'));?>', '<?php echo addslashes(__('Impossible de récupérer l\'article, ce dernier est introuvable, indisponible ou le code envoyé est incorrecte.', 'nexo'));?>' );
		}
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
	* Close item options
	**/

	this.bindHideItemOptions		=	function(){
		$( '.close-item-options' ).bind( 'click', function(){
			$( v2Checkout.ItemSettings ).trigger( 'click' );
		});
	}

	/**
	* Bind Add To Item
	*
	* @return void
	**/

	this.bindAddToItems			=	function(){
		$( '#filter-list' ).find( '.filter-add-product[data-category]' ).each( function(){
			$( this ).bind( 'click', function(){
				var codebar	=	$( this ).attr( 'data-codebar' );
				v2Checkout.retreiveItem( codebar );
			});
		});
	};

	/**
	 * Retreive item on db
	 * Each retreive add item as single entry
	 * @param string barcode
	 * @return void
	**/

	this.retreiveItem 			=	function( barcode, callback = null ) {
		$.ajax( '<?php echo site_url(array( 'rest', 'nexo', 'item' ));?>/' + barcode + '/sku-barcode<?php echo store_get_param( '?' );?>', { 
			success 	:	( items ) => {
				this.treatFoundItem({ item : items[0], callback })
			}
		});
	}

	/**
	* Bind Add Reduce Actions on Cart table items
	**/

	this.bindAddReduceActions	=	function(){

		$( '#cart-table-body .item-reduce' ).each(function(){
			$( this ).bind( 'click', function(){
				
				let parent	=	$( this ).closest( 'tr' );
				let index 	=	$( this ).closest( 'tr' ).attr( 'cart-item-id' );
				
				_.each( v2Checkout.CartItems, function( value, loop_index ) {
					if( typeof loop_index != 'undefined' ) {
						if( loop_index == index ) {

							let status		=	NexoAPI.events.applyFilters( 'reduce_from_cart', {
								barcode 	:	value.CODEBAR,
								item 		:	value,
								proceed 	:	true
							});

							if( status.proceed == true ) {
								value.QTE_ADDED--;
								// If item reach "0";
								if( parseInt( value.QTE_ADDED ) == 0 ) {
									v2Checkout.CartItems.splice( loop_index, 1 );
								}
							}	

							// restore removed quantity
							let gridItem 			=	$( '.item-list-container' )
							.find( '[data-codebar="' + value.CODEBAR + '"]' );
							let remainingQuantity 	=	parseInt( $( gridItem ).attr( 'data-remaining-quantity' ) );

							// if item is physical and stock is enabled
							if( value.STOCK_ENABLED == '1' && value.TYPE == '1' ) {
								$( gridItem ).attr( 'data-remaining-quantity', remainingQuantity + 1 );
							}					
						}
					}
				});

				// Add Item To Cart
				NexoAPI.events.doAction( 'reduce_from_cart', v2Checkout );

				v2Checkout.buildCartItemTable();
			});
		});

		$( '#cart-table-body .item-add' ).each( function() {
			$( this ).bind( 'click', function() {
				var parent	=	$( this ).closest( 'tr' );
				let index 	=	$( this ).closest( 'tr' ).attr( 'cart-item-id' );
				let barcode 	=	$( parent ).data( 'item-barcode' );

				// check if item is INLINE.
				let item 		=	v2Checkout.CartItems[ index ];
				if( item.INLINE ) {
					v2Checkout.addToCart({
						item,
						index,
						increase 	:	true
					});
				} else {
					v2Checkout.retreiveItem( barcode, ( item ) => {
						v2Checkout.addToCart({
							item,
							index,
							increase 	:	true
						});
					});
				}
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
			var value				=	parseInt( $( this ).val() );
			var barcode			=	$( parent ).data( 'item-barcode' );
			let index 			=	$( parent ).attr( 'cart-item-id' );

			if( value >= 0 ) {
				v2Checkout.addToCart({
					index,
					barcode,
					quantity  	:	value,
					increase  	:	false
				})
			} else {
				$( this ).val( currentInputValue );
			}
		});

		<?php if (@$Options[ store_prefix() . 'nexo_enable_numpad' ] != 'non'):?>
		// Bind Num padd
		$( '[name="shop_item_quantity"]' ).bind( 'click', function(){
			v2Checkout.showNumPad( $( this ), '<?php echo addslashes(__('Définir la quantité à  ajouter', 'nexo'));?>', null, false );
			setTimeout( () => {
				$( '[name="numpad_field"]' ).select();
			}, 500 );
		});
		<?php endif;?>
	}

	/**
	* Bind Add Note
	* @since 2.7.3
	**/

	this.bindAddNote			=	function(){
		$( '[data-set-note]' ).bind( 'click', function(){

			var	dom		=	'<h4 class="text-center"><?php echo _s( 'Ajouter une note à la commande', 'nexo' );?></h4>' +
			'<div class="form-group">' +
			'<label for="exampleInputFile"><?php echo _s( 'Note de la commande', 'nexo' );?></label>' +
			'<textarea class="form-control" order_note rows="10"></textarea>' +
			'<p class="help-block"><?php echo _s( 'Cette note sera rattachée à la commande en cours.', 'nexo' );?></p>' +
			'</div>';

			NexoAPI.Bootbox().confirm( dom, function( action ) {
				if( action ) {
					v2Checkout.CartNote		=	$( '[order_note]' ).val();
				}
			});

			$( '[order_note]' ).val( v2Checkout.CartNote );
		});
	};

	/**
	* Bind hover product
	* @since 3.0.19
    **/

	this.bindHoverItemName 		=	function(){

		if( ! NexoAPI.events.applyFilters( 'hover_item_name', true ) ) {
			return false;
		}

		$( '[cart-item]' ).each( function() {
			$( this ).on( 'mouseenter', function() {
				// item-name
				let item 	=	v2Checkout.getItem( $( this ).attr( 'data-item-barcode' ) );

				if( item ) {
					let speed;
					let length 	=	item.DESIGN.length;
					
					switch( true ) {
						case ( length >= 20 && length < 25 ) : speed 	=	1;break;
						case ( length >= 25 && length < 40 ) : speed 	=	2;break;
						case ( length >= 40 && length < 50 ) : speed 	=	3;break;
						case ( length >= 50 && length < 60 ) : speed 	=	4;break;
						case ( length >= 60 ) : speed 	=	5;break;
						default : speed 	=	4;break;
					}

					if( length > 23 ) {
						$( this ).find( '.item-name' ).attr( 'previous', htmlEntities( $( this ).find( '.item-name' ).html() ) );
						$( this ).find( '.item-name' ).html( '<marquee class="marquee_me" behavior="alternate" scrollamount="' + speed + '" direction="left" style="width:100%;float:left;">' + item.DESIGN + '</marquee>' );
					}
				}
			});			
		})

		$( '[cart-item]' ).each( function() {
			$( this ).on( 'mouseleave', function() {
				let old_previous 	=	htmlEntities( $( this ).find( '.item-name' ).html() ); 
				
				// to avoid displaying empty string
				if( old_previous != '' && typeof $( this ).find( '.item-name' ).attr( 'previous' ) != 'undefined' ) {
					$( this ).find( '.item-name' ).html( EntitiesHtml( $( this ).find( '.item-name' ).attr( 'previous' ) ) );
					$( this ).find( '.item-name' ).attr( 'previous', old_previous );
				}	
			});		
		});
	}

	/**
	* Bind Category Action
	* @since 2.7.1
	**/

	this.bindCategoryActions	=	function(){
		$( '.slick-wrapper' ).remove(); // empty all
		$( '.add_slick_inside' ).html( '<div class="slick slick-wrapper"></div>' );
		
		// Build New category wrapper @since 2.7.1
		_.each( this.ItemsCategories, function( value, id ) {
			// New Categories List
			$( '.slick-wrapper' ).append( '<div data-cat-id="' + id + '" style="padding:0px 20px;font-size:20px;line-height:40px;border-right:solid 1px #EEE;margin-right:-1px;" class="text-center slick-item">' + value + '</div>' );

			// Add category name to each item
			if( $( '[data-category="' + id + '"]' ).length > 0 ){
				$( '[data-category="' + id + '"]' ).each( function(){
					$( this ).attr( 'data-category-name', value.toLowerCase() );
				});
			}
		});

		$('.slick').slick({
			infinite			: 	false,
			arrows			:	false,
			slidesToShow		: 	4,
			slidesToScroll	: 	4,
			variableWidth : true
		});

		$( '.slick-item' ).bind( 'click', function(){

			var categories	=	new Array;
			var proceed		=	true;

			if( $( this ).hasClass( 'slick-item-active' ) ) {
				proceed		=	false;
			}

			$( '.slick-item.slick-item-active' ).each( function(){
				$( this ).removeClass( 'slick-item-active' );
			});

			if( ! $( this ).hasClass( 'slick-item-active' ) && proceed == true ) {
				$( this ).toggleClass( 'slick-item-active' );
				categories.push( $( this ).data( 'cat-id' ) );
			}



			v2Checkout.ActiveCategories		=	categories;
			v2Checkout.filterItems( categories );
		});

		// Bind Next button
		$( '.cat-next' ).bind( 'click', function(){
			$('.slick').slick( 'slickNext' );
		});
		// Bind Prev button
		$( '.cat-prev' ).bind( 'click', function(){
			$('.slick').slick( 'slickPrev' );
		});
	}

	/**
	* Bind Change Unit Price
	* @since 2.9.0
	**/

	this.bindChangeUnitPrice	=	function(){

		<?php if( @$Options[ store_prefix() . 'unit_price_changing' ] == 'yes' ):?>

		$( '.item-unit-price' ).bind( 'click', function(){

			var itemCodebar		=	$(this).closest( 'tr' ).attr( 'data-item-barcode' );
			var currentItem		=	null;

			if( ! itemCodebar ) {
				console.log( 'Cannot edit this item, since his barcode is not available' );
				return false;
			}

			for( var i = 0; i < v2Checkout.CartItems.length ; i++ ) {
				if( v2Checkout.CartItems[i].CODEBAR == itemCodebar ) {
					currentItem		=	v2Checkout.CartItems[i];
				}
			}

			var promo_start					= 	moment( currentItem.SPECIAL_PRICE_START_DATE );
			var promo_end					= 	moment( currentItem.SPECIAL_PRICE_END_DATE );

			var MainPrice					= 	NexoAPI.ParseFloat( currentItem.PRIX_DE_VENTE_TTC )
			var Discounted					= 	'';
			var CustomBackground			=	'';
			currentItem.PROMO_ENABLED	=	false;

			if( promo_start.isBefore( v2Checkout.CartDateTime ) ) {
				if( promo_end.isSameOrAfter( v2Checkout.CartDateTime ) ) {
					currentItem.PROMO_ENABLED	=	true;
					MainPrice			=	NexoAPI.ParseFloat( currentItem.PRIX_PROMOTIONEL );
					Discounted			=	'<small><del>' + NexoAPI.DisplayMoney( NexoAPI.ParseFloat( currentItem.PRIX_DE_VENTE_TTC ) ) + '</del></small>';
					CustomBackground	=	'background:<?php echo $this->config->item('discounted_item_background');?>';
				}
			}

			// @since 2.7.1
			if( v2Checkout.CartShadowPriceEnabled ) {
				MainPrice			=	NexoAPI.ParseFloat( currentItem.SHADOW_PRICE );
			}

			$( this ).replaceWith( '<td width="110"><div class="input-group input-group-sm"><input type="number" value="' + MainPrice + '" class="unit-price-form form-control" aria-describedby="sizing-addon3"></div></td>' );

			// Select field content
			$( '.unit-price-form' ).select();

			$( '.unit-price-form' ).bind( 'blur', function(){

				if( ! isNaN( parseFloat( $( this ).val() ) ) ) {

					$( this ).closest( 'td' ).replaceWith( '<td width="110" class="text-center item-unit-price"  style="line-height:30px;">' + NexoAPI.DisplayMoney( $( this ).val() ) + '</td>' );
				} else {
					$( this ).closest( 'td' ).replaceWith( '<td width="110" class="text-center item-unit-price"  style="line-height:30px;">' + NexoAPI.DisplayMoney( MainPrice ) + '</td>' );
				}

				// Update the price on Cart

				for( var i = 0; i < v2Checkout.CartItems.length ; i++ ) {
					if( v2Checkout.CartItems[i].CODEBAR == itemCodebar ) {
						if( v2Checkout.CartShadowPriceEnabled ) {
							v2Checkout.CartItems[i].SHADOW_PRICE	=	$( this ).val();
						} else {
							if( promo_start.isBefore( v2Checkout.CartDateTime ) ) {
								if( promo_end.isSameOrAfter( v2Checkout.CartDateTime ) ) {
									v2Checkout.CartItems[i].PRIX_PROMOTIONEL	=	$( this ).val();
								}
							} else {
								v2Checkout.CartItems[i].PRIX_DE_VENTE_TTC	=	$( this ).val();
								v2Checkout.CartItems[i].PRIX_DE_VENTE		=	$( this ).val();
							}
						}
					}
				}

				v2Checkout.buildCartItemTable();
			});
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
					NexoAPI.Bootbox().confirm( '<?php echo addslashes(__('Souhaitez-vous annuler la réduction de groupe ?', 'nexo'));?>', function( action ) {
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
		$( '.btn.cart-discount-button' ).each( function(){
			if( ! $( this ).hasClass( 'remove-action-bound' ) ) {
				$( this ).addClass( 'remove-action-bound' );
				$( this ).bind( 'click', function(){
					NexoAPI.Bootbox().confirm( '<?php echo addslashes(__('Souhaitez-vous annuler cette remise ?', 'nexo'));?>', function( action ) {
						if( action == true ) {
							v2Checkout.CartRemise			=	0;
							v2Checkout.CartRemiseType		=	'';
							v2Checkout.CartRemiseEnabled	=	false;
							v2Checkout.CartRemisePercent	=	0;
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
					NexoAPI.Bootbox().confirm( '<?php echo addslashes(__('Souhaitez-vous annuler cette ristourne ?', 'nexo'));?>', function( action ) {
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

	this.bindAddDiscount		=	function( config ){
		var	DiscountDom			=
		'<div id="discount-box-wrapper">' +
		'<h4 class="text-center"><?php echo addslashes(__('Appliquer une remise', 'nexo'));?><span class="discount_type"></h4><br>' +
		'<div class="input-group input-group-lg">' +
		'<span class="input-group-btn">' +
		'<button class="btn btn-default percentage_discount" type="button"><?php echo addslashes(__('Pourcentage', 'nexo'));?></button>' +
		'</span>' +
		'<input type="number" name="discount_value" class="form-control" placeholder="<?php echo addslashes(__('Définir le montant ou le pourcentage ici...', 'nexo'));?>">' +
		'<span class="input-group-btn">' +
		'<button class="btn btn-default flat_discount" type="button"><?php echo addslashes(__('Espèces', 'nexo'));?></button>' +
		'</span>' +
		'</div>' +
		'<br>' +
		'<div class="row">' +
		'<div class="col-lg-12">' +
		'<div class="row">' +
		'<div class="col-lg-2 col-md-2 col-xs-2">' +
		'<input type="button" class="btn btn-default btn-block btn-lg numpad7" value="<?php echo addslashes(__('7', 'nexo'));?>"/>' +
		'</div>' +
		'<div class="col-lg-2 col-md-2 col-xs-2">' +
		'<input type="button" class="btn btn-default btn-block btn-lg numpad8" value="<?php echo addslashes(__('8', 'nexo'));?>"/>' +
		'</div>' +
		'<div class="col-lg-2 col-md-2 col-xs-2">' +
		'<input type="button" class="btn btn-default btn-block btn-lg numpad9" value="<?php echo addslashes(__('9', 'nexo'));?>"/>' +
		'</div>' +
		'<div class="col-lg-6 col-md-6 col-xs-6">' +
		'<input type="button" class="btn btn-warning btn-block btn-lg numpaddel" value="<?php echo addslashes(__('Retour arrière', 'nexo'));?>"/>' +
		'</div>' +
		'</div>' +
		'<br>'+
		'<div class="row">' +
		'<div class="col-lg-2 col-md-2 col-xs-2">' +
		'<input type="button" class="btn btn-default btn-block btn-lg numpad4" value="<?php echo addslashes(__('4', 'nexo'));?>"/>' +
		'</div>' +
		'<div class="col-lg-2 col-md-2 col-xs-2">' +
		'<input type="button" class="btn btn-default btn-block btn-lg numpad5" value="<?php echo addslashes(__('5', 'nexo'));?>"/>' +
		'</div>' +
		'<div class="col-lg-2 col-md-2 col-xs-2">' +
		'<input type="button" class="btn btn-default btn-block btn-lg numpad6" value="<?php echo addslashes(__('6', 'nexo'));?>"/>' +
		'</div>' +
		'<div class="col-lg-6 col-md-6 col-xs-6">' +
		'<input type="button" class="btn btn-danger btn-block btn-lg numpadclear" value="<?php echo addslashes(__('Vider', 'nexo'));?>"/>' +
		'</div>' +
		'</div>' +
		'<br>'+
		'<div class="row">' +
		'<div class="col-lg-2 col-md-2 col-xs-2">' +
		'<input type="button" class="btn btn-default btn-block btn-lg numpad1" value="<?php echo addslashes(__('1', 'nexo'));?>"/>' +
		'</div>' +
		'<div class="col-lg-2 col-md-2 col-xs-2">' +
		'<input type="button" class="btn btn-default btn-block btn-lg numpad2" value="<?php echo addslashes(__('2', 'nexo'));?>"/>' +
		'</div>' +
		'<div class="col-lg-2 col-md-2 col-xs-2">' +
		'<input type="button" class="btn btn-default btn-block btn-lg numpad3" value="<?php echo addslashes(__('3', 'nexo'));?>"/>' +
		'</div>' +
		'</div>' +
		'<br>' +
		'<div class="row">' +
		'<div class="col-lg-2 col-md-2 col-xs-2">' +
		'<input type="button" class="btn btn-default btn-block btn-lg numpad00" value="<?php echo addslashes(__('00', 'nexo'));?>"/>' +
		'</div>' +
		'<div class="col-lg-4 col-md-6 col-xs-6">' +
		'<input type="button" class="btn btn-default btn-block btn-lg numpad0" value="<?php echo addslashes(__('0', 'nexo'));?>"/>' +
		'</div>' +
		'</div>' +
		'</div>' +
		'</div>' +
		'</div>';

		config					=	_.extend( {}, config );

		NexoAPI.Bootbox().confirm( DiscountDom, function( action ) {
			if( action == true ) {

				var value	=	$( '[name="discount_value"]' ).val();

				if( typeof config.onExit	==	'function' ) {
					config.onExit( value );
				}
			}
		});

		$( '.percentage_discount' ).bind( 'click', function(){
			if( ! $( this ).hasClass( 'active' ) ) {
				if( $( '.flat_discount' ).hasClass( 'active' ) ) {
					$( '.flat_discount' ).removeClass( 'active' );
				}

				$( this ).addClass( 'active' );

				// Proceed a quick check on the percentage value
				$( '[name="discount_value"]' ).select();

				if( typeof config.onPercentDiscount	==	'function' ) {
					config.onPercentDiscount();
				}

				$( '.discount_type' ).html( '<?php echo addslashes(__(' : <span class="label label-primary">au pourcentage</span>', 'nexo'));?>' );
			}
		});

		$( '.flat_discount' ).bind( 'click', function(){
			if( ! $( this ).hasClass( 'active' ) ) {
				if( $( '.percentage_discount' ).hasClass( 'active' ) ) {
					$( '.percentage_discount' ).removeClass( 'active' );
				}

				$( this ).addClass( 'active' );

				$( '[name="discount_value"]' ).select();

				if( typeof config.onFixedDiscount	==	'function' ) {
					config.onFixedDiscount();
				}

				$( '.discount_type' ).html( '<?php echo addslashes(__(' : <span class="label label-info">à prix fixe</span>', 'nexo'));?>' );
			}
		});

		// Fillback form
		if( typeof config.beforeLoad == 'function' ) {
			config.beforeLoad();
		}

		$( '[name="discount_value"]' ).bind( 'blur', function(){

			if( NexoAPI.ParseFloat( $( this ).val() ) < 0 ) {
				$( this ).val( 0 );
			}

			if( typeof config.beforeLoad == 'function' ) {
				config.onFieldBlur();
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

		setTimeout( () => {
			// Select field content
			$( '[name="discount_value"]' ).select();
		}, 500 );
	};

	/**
	* Bind Quick Edit item
	*
	**/

	this.bindQuickEditItem		=	function(){
		$( '.quick_edit_item' ).bind( 'click', function(){

			var CartItem		=	$( this ).closest( '[cart-item]' );
			var Barcode			=	$( CartItem ).data( 'item-barcode' );
			var CurrentItem		=	false;

			_.each( v2Checkout.CartItems, function( value, key ) {
				if( typeof value != 'undefined' ) {
					if( value.CODEBAR == Barcode ) {
						CurrentItem		=	value;
						return;
					}
				}
			});

			if( v2Checkout.CartShadowPriceEnabled == false ) {
				document.location	=	'<?php echo site_url('dashboard/nexo/produits/lists/edit');?>/' + CurrentItem.ID
				return;
			}

			if( CurrentItem != false ) {
				var dom				=	'<h4 class="text-center"><?php echo _s( 'Modifier l\'article :', 'nexo' );?> ' + CurrentItem.DESIGN + '</h4>' +

				'<div class="input-group">' +
				'<span class="input-group-addon" id="basic-addon1"><?php echo _s( 'Prix de vente', 'nexo' );?></span>' +
				'<input type="text" class="current_item_price form-control" placeholder="<?php echo _s( 'Définir un prix de vente', 'nexo' );?>" aria-describedby="basic-addon1">' +
				'<span class="input-group-addon"><?php echo _s( 'Seuil :', 'nexo' );?> <span class="sale_price"></span></span>' +
				'</div>';

			} else {

				NexoAPI.Bootbox().alert( '<?php echo _s( 'Produit introuvable', 'nexo' );?>' );

				var dom				=	'';
			}

			// <?php echo site_url('dashboard/nexo/produits/lists/edit');?>

			NexoAPI.Bootbox().confirm( dom, function( action ) {
				if( action ) {
					if( NexoAPI.ParseFloat( $( '.current_item_price' ).val() ) < NexoAPI.ParseFloat( CurrentItem.PRIX_DE_VENTE_TTC ) ) {
						NexoAPI.Bootbox().alert( '<?php echo _s( 'Le nouveau prix ne peut pas être inférieur au prix minimal (seuil)', 'nexo' );?>' );
						return false;
					} else {
						_.each( v2Checkout.CartItems, function( value, key ) {
							if( typeof value != 'undefined' ) {
								if( value.CODEBAR == CurrentItem.CODEBAR ) {
									value.SHADOW_PRICE	=	NexoAPI.ParseFloat( $( '.current_item_price' ).val() );
									return;
								}
							}
						});
						// Refresh Cart
						v2Checkout.buildCartItemTable();
					}
				}
			});

			$( '.sale_price' ).html( NexoAPI.DisplayMoney( CurrentItem.PRIX_DE_VENTE_TTC ) );
			$( '.current_item_price' ).val( CurrentItem.SHADOW_PRICE );

		});
	};

	/**
	* BindToggle Comptact Mode
	**/

	this.bindToggleComptactMode	=	function(){
		$( '.toggleCompactMode' ).bind( 'click', function(){
			v2Checkout.toggleCompactMode();
		});
	}

	/**
	* Bind Unit Item Discount
	* @return void
	* @since 2.9.0
	**/

	this.bindUnitItemDiscount 	=	function(){
		$( '.item-discount' ).bind( 'click', function(){

			let index 		=	$( this ).closest( 'tr' ).attr( 'cart-item-id' );
			var _item			=	v2Checkout.CartItems[ index ];
			var salePrice		=	v2Checkout.getItemSalePrice( _item );

			v2Checkout.bindAddDiscount({
				beforeLoad		:	function(){
					if( _item.DISCOUNT_TYPE == 'percentage' ) {

						$( '.' + _item.DISCOUNT_TYPE + '_discount' ).trigger( 'click' );

					} else {
						$( '.flat_discount' ).trigger( 'click' );
					}

					if( _item.DISCOUNT_TYPE == 'percentage' ) {
						$( '[name="discount_value"]' ).val( _item.DISCOUNT_PERCENT );
					} else if( _item.DISCOUNT_TYPE == 'flat' ) {
						$( '[name="discount_value"]' ).val( _item.DISCOUNT_AMOUNT );
					}
				},
				onFixedDiscount		:	function(){
					_item.DISCOUNT_TYPE	=	'flat';
				},
				onPercentDiscount	:	function(){
					_item.DISCOUNT_TYPE	=	'percentage';
				},
				onFieldBlur			:	function(){
					// console.log( 'Field blur performed' );
					// Percentage allowed to 100% only
					if( _item.DISCOUNT_TYPE == 'percentage' && NexoAPI.ParseFloat( $( '[name="discount_value"]' ).val() ) > 100 ) {
						$( this ).val( 100 );
					} else if( _item.DISCOUNT_TYPE == 'flat' && NexoAPI.ParseFloat( $( '[name="discount_value"]' ).val() ) > salePrice ) {
						// flat discount cannot exceed cart value
						$( this ).val( salePrice );
						NexoAPI.Notify().info( '<?php echo _s('Attention', 'nexo');?>', '<?php echo _s('La remise fixe ne peut pas excéder la valeur actuelle du panier. Le montant de la remise à été réduite à la valeur du panier.', 'nexo');?>' );
					}
				},
				onExit				:	function( value ){
					// console.log( 'Exit discount box	' );
					// Percentage can't exceed 100%
					if( _item.DISCOUNT_TYPE == 'percentage' ) {
						if( NexoAPI.ParseFloat( value ) > 100 ) {
							// Percentage
							_item.DISCOUNT_PERCENT = 100;
						} else {
							_item.DISCOUNT_PERCENT	=	value;
						}
					}

					if( _item.DISCOUNT_TYPE == 'flat' ) {
						if( NexoAPI.ParseFloat( value ) > salePrice ) {
							// flat discount cannot exceed cart value
							_item.DISCOUNT_AMOUNT	= 	salePrice;
						} else {
							_item.DISCOUNT_AMOUNT	=	value;
						}
					}

					$( '[name="discount_value"]' ).focus();
					$( '[name="discount_value"]' ).blur();

					v2Checkout.buildCartItemTable();
				}
			});
		});
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
		this.CartTotalItems	=	0;

		if( _.toArray( this.CartItems ).length > 0 ){
			// reset item vat
			v2Checkout.CartItemsVAT 	=	0;
			_.each( this.CartItems, function( value, key ) {

				var promo_start		= 	moment( value.SPECIAL_PRICE_START_DATE );
				var promo_end			= 	moment( value.SPECIAL_PRICE_END_DATE );

				var MainPrice			= 	NexoAPI.ParseFloat( value.PRIX_DE_VENTE_TTC );
				var Discounted			= 	'';
				var CustomBackground	=	'';
				value.PROMO_ENABLED	=	false;

				if( promo_start.isBefore( v2Checkout.CartDateTime ) && promo_end.isSameOrAfter( v2Checkout.CartDateTime ) ) {
					value.PROMO_ENABLED	=	true;
					MainPrice			=	NexoAPI.ParseFloat( value.PRIX_PROMOTIONEL );
					Discounted			=	'<small><del>' + NexoAPI.DisplayMoney( NexoAPI.ParseFloat( value.PRIX_DE_VENTE_TTC ) ) + '</del></small>';
					CustomBackground	=	'background:<?php echo $this->config->item('discounted_item_background');?>';
				}

				if( v2Checkout.CartShowItemVAT ) {
					v2Checkout.CartItemsVAT	 	+=	( ( NexoAPI.ParseFloat( value.PRIX_DE_VENTE_TTC ) - NexoAPI.ParseFloat( value.PRIX_DE_VENTE ) ) * parseFloat( value.QTE_ADDED ) );
				}

				// @since 2.7.1
				if( v2Checkout.CartShadowPriceEnabled ) {
					MainPrice			=	NexoAPI.ParseFloat( value.SHADOW_PRICE );
				}

				// <span class="btn btn-primary btn-xs item-reduce hidden-sm hidden-xs">-</span> <input type="number" style="width:40px;border-radius:5px;border:solid 1px #CCC;" maxlength="3"/> <span class="btn btn-primary btn-xs   hidden-sm hidden-xs">+</span>

				// <?php echo site_url('dashboard/nexo/produits/lists/edit');?>
				// /' + value.ID + '

				// :: alert( value.DESIGN.length );
				var item_design		=	NexoAPI.events.applyFilters( 'cart_item_name', {
					original 			:	value.DESIGN,
					displayed 		:	value.DESIGN
				}); // .length > 20 ? '<span style="text-overflow:hidden">' + value.DESIGN.substr( 0, 20 ) + '</span>' : value.DESIGN ;

				var DiscountAmount	=	value.DISCOUNT_TYPE	== 'percentage' ? value.DISCOUNT_PERCENT + '%' : NexoAPI.DisplayMoney( value.DISCOUNT_AMOUNT );

				var itemSubTotal	=	MainPrice * parseInt( value.QTE_ADDED );

				if( value.DISCOUNT_TYPE == 'percentage' && parseFloat( value.DISCOUNT_PERCENT ) > 0 ) {
					var itemPercentOff	=	( itemSubTotal * parseFloat( value.DISCOUNT_PERCENT ) ) / 100;
					itemSubTotal	-=	itemPercentOff;
				} else if( value.DISCOUNT_TYPE == 'flat' && parseFloat( value.DISCOUNT_AMOUNT ) > 0 ) {
					var itemPercentOff	=	 parseFloat( value.DISCOUNT_AMOUNT );
					itemSubTotal	-=	itemPercentOff;
				}

				// <marquee class="marquee_me" behavior="alternate" scrollamount="4" direction="left" style="width:100%;float:left;">Earl Klugh - HandPucked</marquee>

				$( '#cart-table-body' ).find( 'table' ).append(
					'<tr cart-item-id="' + key + '" cart-item data-line-weight="' + ( MainPrice * parseInt( value.QTE_ADDED ) ) + '" data-item-barcode="' + value.CODEBAR + '">' +
						'<td width="200" class="text-left" style="line-height:30px;"><p style="width:45px;margin:0px;float:left">' + NexoAPI.events.applyFilters( 'cart_before_item_name', '' ) + '</p><p style="text-transform: uppercase;float:left;width:76%;margin-bottom:0px;" class="item-name">' + item_design.displayed + '</p></td>' +
						'<td width="110" class="text-center item-unit-price hidden-xs"  style="line-height:30px;">' + NexoAPI.DisplayMoney( MainPrice ) + ' ' + Discounted + '</td>' +
						'<td width="100" class="text-center item-control-btns">' +
						'<div class="input-group input-group-sm">' +
						'<span class="input-group-btn item-control-btns-wrapper">' +
							'<button class="btn btn-default item-reduce">-</button>' +
							'<button name="shop_item_quantity" value="' + value.QTE_ADDED + '" class="btn btn-default" style="width:50px;">' + value.QTE_ADDED + '</button>' +
							'<button class="btn btn-default item-add">+</button>' +
						'</span>' +
						'</td>' +
						<?php if( @$Options[ store_prefix() . 'unit_item_discount_enabled' ] == 'yes' ):?>
						'<td width="90" class="text-center item-discount"  style="line-height:28px;"><span class="btn btn-default btn-sm">' + DiscountAmount + '</span></td>' +
						<?php endif;?>
						'<td width="100" class="text-right item-total-price" style="line-height:30px;">' + NexoAPI.DisplayMoney( itemSubTotal ) + '</td>' +
					'</tr>'
				);

				_tempCartValue	+=	( itemSubTotal ); // MainPrice * parseInt( value.QTE_ADDED )

				// Just to count all products
				v2Checkout.CartTotalItems	+=	parseInt( value.QTE_ADDED );
			});

			this.CartValue	=	_tempCartValue;

		} else {
			$( this.CartTableBody ).find( 'tbody' ).html( '<tr id="cart-table-notice"><td colspan="4"><?php _e('Veuillez ajouter un produit...', 'nexo');?></td></tr>' );
		}

		this.bindAddReduceActions();
		this.bindQuickEditItem();
		this.bindAddByInput();
		this.refreshCartValues();
		this.bindChangeUnitPrice(); // @since 2.9.0
		this.bindUnitItemDiscount();
		this.bindHoverItemName(); // @since 3.0.19

		// @since 2.7.3
		// trigger action when cart is refreshed
		NexoAPI.events.doAction( 'cart_refreshed', v2Checkout );
	}

	/**
	* Calculate Cart discount
	**/

	this.calculateCartDiscount		=	function( value ) {

		if( value == '' || value == '0' ) {
			this.CartRemiseEnabled	=	false;
		}

		// Display Notice
		if( $( '.cart-discount-remove-wrapper' ).find( '.cart-discount-button' ).length > 0 ) {
			$( '.cart-discount-remove-wrapper' ).find( '.cart-discount-button' ).remove();
		}

		if( this.CartRemiseEnabled == true ) {

			if( this.CartRemiseType == 'percentage' ) {
				if( typeof value != 'undefined' ) {
					this.CartRemisePercent	=	NexoAPI.ParseFloat( value );
				}

				// Only if the cart is not empty
				if( this.CartValue > 0 ) {
					this.CartRemise			=	( this.CartRemisePercent * this.CartValue ) / 100;
				} else {
					this.CartRemise			=	0;
				}

				if( this.CartRemiseEnabled ) {
					$( '.cart-discount-remove-wrapper' ).prepend( '<span style="cursor: pointer;margin:0px 2px;margin-top: -4px;" class="animated bounceIn btn btn-danger btn-xs cart-discount-button"><i class="fa fa-times"></i></span>' );
				}

			} else if( this.CartRemiseType == 'flat' ) {
				if( typeof value != 'undefined' ) {
					this.CartRemise 			=	NexoAPI.ParseFloat( value );
				}

				if( this.CartRemiseEnabled ) {
					$( '.cart-discount-remove-wrapper' ).prepend( '<span style="cursor: pointer;margin:0px 2px;margin-top: -4px;" class="animated bounceIn btn btn-danger btn-xs cart-discount-button"><i class="fa fa-times"></i></span>' );
				}
			}

		}

		this.bindRemoveCartRemise();
	}

	/**
	* Calculate cart ristourne
	**/

	this.calculateCartRistourne		=	function(){
		// alert( 'ok' );

		// Will be overwritten by enabled ristourne
		this.CartRistourne			=	0;

		$( '.cart-discount-notice-area' ).find( '.cart-ristourne' ).remove();

		if( this.CartRistourneEnabled ) {

			if( this.CartRistourneType == 'percent' ) {

				if( this.CartRistournePercent != '' ) {
					this.CartRistourne	=	( NexoAPI.ParseFloat( this.CartRistournePercent ) * this.CartValue ) / 100;
				}

				if( this.CartRistourne > 0 ) {
					$( '.cart-discount-notice-area' ).prepend( '<span style="cursor: pointer; margin:0px 2px;margin-top: -4px;" class="animated bounceIn btn expandable btn-info btn-xs cart-ristourne"><i class="fa fa-remove"></i> <?php echo addslashes(__('Ristourne : ', 'nexo'));?>' + this.CartRistournePercent + '%</span>' );
				}

			} else if( this.CartRistourneType == 'amount' ) {
				if( this.CartRistourneAmount != '' ) {
					this.CartRistourne	=	NexoAPI.ParseFloat( this.CartRistourneAmount );
				}

				if( this.CartRistourne > 0 ) {
					$( '.cart-discount-notice-area' ).prepend( '<span style="cursor: pointer;margin:0px 2px;margin-top: -4px;" class="animated bounceIn btn expandable btn-info btn-xs cart-ristourne"><i class="fa fa-remove"></i> <?php echo addslashes(__('Ristourne : ', 'nexo'));?>' + NexoAPI.DisplayMoney( this.CartRistourne ) + '</span>' );
				}
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
					this.CartGroupDiscount		=	( NexoAPI.ParseFloat( this.CartGroupDiscountPercent ) * this.CartValue ) / 100;

					$( '.cart-discount-notice-area' ).append( '<p style="cursor: pointer; margin:0px 2px;margin-top: -4px;" class="animated bounceIn btn btn-warning expandable btn-xs cart-group-discount"><i class="fa fa-remove"></i> <?php echo addslashes(__('Remise de groupe : ', 'nexo'));?>' + this.CartGroupDiscountPercent + '%</p>' );
				}
			} else if( this.CartGroupDiscountType == 'amount' ) {
				if( this.CartGroupDiscountAmount != '' ) {
					this.CartGroupDiscount		=	NexoAPI.ParseFloat( this.CartGroupDiscountAmount )	;

					$( '.cart-discount-notice-area' ).append( '<p style="cursor: pointer; margin:0px 2px;margin-top: -4px;" class="animated bounceIn btn btn-warning expandable btn-xs cart-group-discount"><i class="fa fa-remove"></i> <?php echo addslashes(__('Remise de groupe : ', 'nexo'));?>' + NexoAPI.DisplayMoney( this.CartGroupDiscountAmount ) + '</p>' );
				}
			}

			this.bindRemoveCartGroupDiscount();
		}
	};

	/**
	* Calculate Cart VAT
	**/

	this.calculateCartVAT		=	function(){
		if( this.CartVATEnabled == true ) {
			this.CartVAT		=	NexoAPI.ParseFloat( ( this.CartVATPercent * this.CartValueRRR ) / 100 );
		}
	};

	/**
	* Cancel an order and return to order list
	**/

	this.cartCancel				=	function(){
		NexoAPI.Bootbox().confirm( '<?php echo _s('Souhaitez-vous annuler cette commande ?', 'nexo');?>', function( action ) {
			if( action == true ) {
				v2Checkout.resetCart();
			}
		});
	}

	/**
	* Cart Group Reset
	**/

	this.cartGroupDiscountReset			=	function(){
		this.CartGroupDiscount				=	0; // final amount
		this.CartGroupDiscountAmount		=	0; // Amount set on each group
		this.CartGroupDiscountPercent		=	0; // percent set on each group
		this.CartGroupDiscountType			=	null; // Discount type
		this.CartGroupDiscountEnabled		=	false;

		$( '.cart-discount-notice-area' ).find( '.cart-group-discount' ).remove();
	}

	/**
	* Submit order
	* @param object payment mean
	* @deprecated
	**/

	this.cartSubmitOrder			=	function( payment_means ){
		var order_items				=	new Array;

		_.each( this.CartItems, function( value, key ){

			var ArrayToPush			=	{
				id 					:	value.ID,
				qte_added 			:	value.QTE_ADDED,
				codebar 				:	value.CODEBAR,
				sale_price 			:	value.PROMO_ENABLED ? value.PRIX_PROMOTIONEL : ( v2Checkout.CartShadowPriceEnabled ? value.SHADOW_PRICE : value.PRIX_DE_VENTE_TTC ),
				qte_sold 				:	value.QUANTITE_VENDU,
				qte_remaining 			:	value.QUANTITE_RESTANTE,
				// @since 2.8.2
				stock_enabled 			:	value.STOCK_ENABLED,
				// @since 2.9.0
				discount_type 			:	value.DISCOUNT_TYPE,
				discount_amount		:	value.DISCOUNT_AMOUNT,
				discount_percent 		:	value.DISCOUNT_PERCENT,
				metas 				:	typeof value.metas == 'undefined' ? {} : value.metas,
				// @since 3.1
				name 				:	value.DESIGN,
				inline 				:	typeof value.INLINE ? 1 : 0 // if it's an inline item
			};

			// improved @since 2.7.3
			// add meta by default
			ArrayToPush.metas	=	NexoAPI.events.applyFilters( 'items_metas', ArrayToPush.metas );

			order_items.push( ArrayToPush );
		});

		let order_details					=	new Object;
		order_details.TOTAL					=	NexoAPI.ParseFloat( this.CartToPay );
		order_details.REMISE_TYPE			=	this.CartRemiseType;

		// @since 2.9.6
		if( this.CartRemiseType == 'percentage' ) {
			order_details.REMISE_PERCENT	=	NexoAPI.ParseFloat( this.CartRemisePercent );
			order_details.REMISE			=	0;
		} else if( this.CartRemiseType == 'flat' ) {
			order_details.REMISE_PERCENT	=	0;
			order_details.REMISE			=	NexoAPI.ParseFloat( this.CartRemise );
		} else {
			order_details.REMISE_PERCENT	=	0;
			order_details.REMISE			=	0;
		}
		// @endSince
		order_details.RABAIS			=	NexoAPI.ParseFloat( this.CartRabais );
		order_details.RISTOURNE			=	NexoAPI.ParseFloat( this.CartRistourne );
		order_details.TVA				=	NexoAPI.ParseFloat( this.CartVAT );
		order_details.REF_CLIENT		=	this.CartCustomerID == null ? this.customers.DefaultCustomerID : this.CartCustomerID;
		order_details.PAYMENT_TYPE		=	this.CartPaymentType;
		order_details.GROUP_DISCOUNT	=	NexoAPI.ParseFloat( this.CartGroupDiscount );
		order_details.DATE_CREATION		=	this.CartDateTime.format( 'YYYY-MM-DD HH:mm:ss' )
		order_details.ITEMS				=	order_items;
		order_details.DEFAULT_CUSTOMER	=	this.DefaultCustomerID;
		order_details.DISCOUNT_TYPE		=	'<?php echo @$Options[ store_prefix() . 'discount_type' ];?>';
		order_details.HMB_DISCOUNT		=	'<?php echo @$Options[ store_prefix() . 'how_many_before_discount' ];?>';
		// @since 2.7.5
		order_details.REGISTER_ID		=	'<?php echo $register_id;?>';

		// @since 2.7.1, send editable order to Rest Server
		order_details.EDITABLE_ORDERS	=	<?php echo json_encode( $this->events->apply_filters( 'order_editable', array( 'nexo_order_devis' ) ) );?>;

		// @since 2.7.3 add Order note
		order_details.DESCRIPTION		=	this.CartNote;

		// @since 2.9.0
		order_details.TITRE				=	this.CartTitle;

		// @since 2.8.2 add order meta
		this.CartMetas					=	NexoAPI.events.applyFilters( 'order_metas', this.CartMetas );
		order_details.metas				=	this.CartMetas;

		if( payment_means == 'cash' ) {

			order_details.SOMME_PERCU		=	NexoAPI.ParseFloat( this.CartPerceivedSum );
			order_details.SOMME_PERCU 		=	isNaN( order_details.SOMME_PERCU ) ? 0 : order_details.SOMME_PERCU;

		} else if( payment_means == 'cheque' || payment_means == 'bank' ) {

			order_details.SOMME_PERCU		=	NexoAPI.ParseFloat( this.CartToPay );

		} else if( payment_means == 'stripe' ) {
			if( this.CartAllowStripeSubmitOrder == true ) {

				order_details.SOMME_PERCU		=	NexoAPI.ParseFloat( this.CartToPay );

			} else {
				NexoAPI.Notify().info( '<?php echo _s('Attention', 'nexo');?>', '<?php echo _s('La carte de crédit doit d\'abord être facturée avant de valider la commande.', 'nexo');?>' );
				return false;
			}
		} else {
			// Handle for custom Payment Means
			if( NexoAPI.events.applyFilters( 'check_payment_mean', [ false, payment_means ] )[0] == true ) {

				/**
				* Make sure to return order_details
				**/

				order_details		=	NexoAPI.events.applyFilters( 'payment_mean_checked', [ order_details, payment_means ] )[0];

			} else {

				NexoAPI.Bootbox().alert( '<?php echo _s('Impossible de reconnaitre le moyen de paiement.', 'nexo');?>' );
				return false;

			}
		}

		var ProcessObj	=	NexoAPI.events.applyFilters( 'process_data', {
			url			:	this.ProcessURL,
			type			:	this.ProcessType
		});

		// Filter Submited Details
		order_details	=	NexoAPI.events.applyFilters( 'before_submit_order', order_details );

		$.ajax( ProcessObj.url, {
			dataType		:	'json',
			type			:	ProcessObj.type,
			data			:	order_details,
			beforeSend		: function(){
				v2Checkout.paymentWindow.showSplash();
				NexoAPI.Notify().info( '<?php echo _s('Veuillez patienter', 'nexo');?>', '<?php echo _s('Paiement en cours...', 'nexo');?>' );
			},
			success			:	function( returned ) {
				v2Checkout.paymentWindow.hideSplash();
				v2Checkout.paymentWindow.close();

				if( _.isObject( returned ) ) {
					// Init Message Object
					var MessageObject	=	new Object;

					var data	=	NexoAPI.events.applyFilters( 'test_order_type', [ ( returned.order_type == 'nexo_order_comptant' ), returned ] );
					var test_order	=	data[0];

					if( test_order == true ) {

						<?php if (@$Options[ store_prefix() . 'nexo_enable_autoprint' ] == 'yes'):?>

						if( NexoAPI.events.applyFilters( 'cart_enable_print', true ) ) {

							MessageObject.title	=	'<?php echo _s('Effectué', 'nexo');?>';
							MessageObject.msg	=	'<?php echo _s('La commande est en cours d\'impression.', 'nexo');?>';
							MessageObject.type	=	'success';

							$.ajax({
								url 		:	'<?php echo site_url(array( 'dashboard', store_slug(), 'nexo', 'print', 'order_receipt' ));?>/' + returned.order_id + '?refresh=true',
								success 		:	function( data ){
									var params = [
									'height='+screen.height,
									'width='+screen.width,
									'fullscreen=yes' // only works in IE, but here for completeness
									].join(',');
									var mywindow = window.open('', 'my div', params );
									mywindow.document.write('<html><head><title>Store A &rsaquo; Proceed a sale &mdash; Store A</title>');
									mywindow.document.write('</head><body >');
									mywindow.document.write(data);
									mywindow.document.write('</body></html>');

									mywindow.document.close(); // necessary for IE >= 10
									mywindow.focus(); // necessary for IE >= 10
									setTimeout( function(){
										mywindow.print();
										mywindow.close();
									}, 600 );
								}
							})

							// $( 'body' ).append( '<iframe style="display:none;" id="CurrentReceipt" name="CurrentReceipt" src="<?php echo site_url(array( 'dashboard', store_slug(), 'nexo', 'print', 'order_receipt' ));?>/' + returned.order_id + '?refresh=true"></iframe>' );

							// window.frames["CurrentReceipt"].focus();
							// window.frames["CurrentReceipt"].print();

							// setTimeout( function(){
							// 	$( '#CurrentReceipt' ).remove();
							// }, 5000 );

						}
						// Remove filter after it's done
						NexoAPI.events.removeFilter( 'cart_enable_print' );

						<?php else:?>

						MessageObject.title	=	'<?php echo _s('Effectué', 'nexo');?>';
						MessageObject.msg	=	'<?php echo _s('La commande a été enregistrée.', 'nexo');?>';
						MessageObject.type	=	'success';

						<?php endif;?>

						<?php if (@$Options[ store_prefix() . 'nexo_enable_smsinvoice' ] == 'yes'):?>
						/**
						* Send SMS
						**/
						// Do Action when order is complete and submited
						NexoAPI.events.doAction( 'is_cash_order', [ v2Checkout, returned ] );
						<?php endif;?>
					} else {
						<?php if (@$Options[ store_prefix() . 'nexo_enable_autoprint' ] == 'yes'):?>
						MessageObject.title	=	'<?php echo _s('Effectué', 'nexo');?>';
						MessageObject.msg	=	'<?php echo _s('La commande a été enregistrée, mais ne peut pas être imprimée tant qu\'elle n\'est pas complète.', 'nexo');?>';
						MessageObject.type	=	'info';

						<?php else:?>
						MessageObject.title	=	'<?php echo _s('Effectué', 'nexo');?>';
						MessageObject.msg	=	'<?php echo _s('La commande a été enregistrée', 'nexo');?>';
						MessageObject.type	=	'info';
						<?php endif;?>
					}

					// Filter Message Callback
					var data				=	NexoAPI.events.applyFilters( 'callback_message', [ MessageObject, returned ] );
					MessageObject		=	data[0];

					// For Success
					if( MessageObject.type == 'success' ) {

						NexoAPI.Notify().success( MessageObject.title, MessageObject.msg );

						// For Info
					} else if( MessageObject.type == 'info' ) {

						NexoAPI.Notify().info( MessageObject.title, MessageObject.msg );

					}
				}

				<?php if (! isset($order)):?>
				v2Checkout.resetCart();
				<?php else:?>
				// If order is not more editable
				if( returned.order_type != 'nexo_order_devis' ) {
					v2Checkout.resetCart();
					document.location	=	'<?php echo site_url(array( 'dashboard', store_slug(), 'nexo', 'commandes', 'lists' ));?>';
				}
				<?php endif;?>
			},
			error			:	function(){
				v2Checkout.paymentWindow.hideSplash();
				NexoAPI.Notify().warning( '<?php echo _s('Une erreur s\'est produite', 'nexo');?>', '<?php echo _s('Le paiement n\'a pas pu être effectuée.', 'nexo');?>' );
			}
		});
	};

	/**
	 *  Check Item Stock
	 *  @return void
	**/

	this.checkItemsStock			=	function( items ) {
		var stockToReport			=	new Array;
		var minPercentage			=	<?php echo is_numeric( @$Options[ store_prefix() . 'nexo_stock_percentage_warning' ] ) ? @$Options[ store_prefix() . 'nexo_stock_percentage_warning' ] : 100;?>;
		var isEnabled				=	'<?php echo @$Options[ store_prefix() . 'nexo_enable_stock_warning' ];?>';

		if( isEnabled == 'yes' ) {

			_.each( items, function( value, key ) {
				var leftPercentage	=	( parseInt( value.QUANTITE_RESTANTE ) * 100 ) / parseInt( value.QUANTITY );
				if( leftPercentage <= minPercentage ) {
					stockToReport.push({
						'id'		:	value.ID,
						'design'	:	value.DESIGN
					});
				}
			});

			if( stockToReport.length > 0 ) {
				$.ajax({
					url		:	'<?php echo site_url( array( 'rest', 'nexo', 'stock_report' ) );?>?store_id=<?php echo $store_id == null ? 0 : $store_id;?>',
					method	:	'POST',
					data	:	{
						'reported_items'	:	stockToReport
					}
				});
			}
		}
	}



	/**
	* Customer DropDown Menu
	* @deprecated
	**/

	this.customers			=	new function(){

		this.DefaultCustomerID	=	'<?php echo @$Options[ store_prefix() . 'default_compte_client' ];?>';

		/**
		* Bind
		* @deprecated
		**/

		this.bind				=	function(){
			// $('.dropdown-bootstrap').selectpicker({
			// 	style: 'btn-default',
			// 	size: 4
			// });

			// console.log( $( '.customers-list' ).attr( 'change-bound' ) );

			// if( typeof $( '.customers-list' ).attr( 'change-bound' ) == 'undefined' ) {
			// 	$( '.customers-list' ).bind( 'change', function(){
			// 		v2Checkout.customers.bindSelectCustomer( $( this ).val() );
			// 	});
			// 	$( '.customers-list' ).attr( 'change-bound', 'true' );
			// 	console.log( $( '.customers-list' ).attr( 'change-bound' ) );
			// }
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
				$.ajax( '<?php echo site_url(array( 'rest', 'nexo', 'customer' ));?>/' + customer_id + '?<?php echo store_get_param( null );?>', {
					error		:	function(){
						v2Checkout.showError( 'ajax_fetch' );
					},
					dataType	:	'json',
					success		:	function( data ) {
						if( data.length > 0 ){
							v2Checkout.CartCustomerID	=	data[0].ID;
							v2Checkout.customers.check_discounts( data );
							v2Checkout.customers.check_groups_discounts( data );
							// Exect action on selecting customer
							NexoAPI.events.doAction( 'select_customer', data );
						}
					}
				});
			} else {
				// Refresh Cart Value;
				v2Checkout.refreshCartValues();
			}
		};

		/**
		* Check discount for the customer
		* @param object customer data
		* @return void
		**/

		this.check_discounts			=	function( object ) {
			if( typeof object == 'object' ) {
				_.each( object, function( value, key ) {
					// Restore orginal customer discount
					if( NexoAPI.ParseFloat( v2Checkout.CartRistourneCustomerID ) == NexoAPI.ParseFloat( value.ID ) ) {
						v2Checkout.restoreCustomRistourne();
						v2Checkout.buildCartItemTable();
						v2Checkout.refreshCart();
					} else {
						if( value.DISCOUNT_ACTIVE == '1' ) {
							v2Checkout.restoreDefaultRistourne();
							v2Checkout.CartRistourneEnabled 	=	true;
						}
					}
				});

				// Refresh Cart value;
				v2Checkout.refreshCartValues();
			}
		};

		/**
		* Check discount for user group
		* @param object customer data
		* @return void
		**/

		this.check_groups_discounts		=	function( object ){

			// Reset Groups Discounts
			v2Checkout.cartGroupDiscountReset();

			if( typeof object == 'object' ) {

				_.each( object, function( Customer, key ) {
					// Default customer can't benefit from group discount
					if( Customer.ID != v2Checkout.customers.DefaultCustomerID ) {
						// Looping each groups to check whether this customer belong to one existing group
						_.each( v2Checkout.CustomersGroups, function( Group, Key ) {
							if( Customer.REF_GROUP == Group.ID ) {
								// if group discount is enabled
								if( Group.DISCOUNT_ENABLE_SCHEDULE == 'true' ) {
									if(
										moment( Group.DISCOUNT_START ).isSameOrBefore( v2Checkout.CartDateTime ) == false ||
										moment( Group.DISCOUNT_END ).endOf( 'day' ).isSameOrAfter( v2Checkout.CartDateTime ) == false
									) {
										/**
										* Time Range is incorrect to enable Group discount
										**/

										console.log( 'time is incorrect for group discount' );

										return;
									}
								}

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
					}
				});

				// Refresh Cart value;
				v2Checkout.refreshCartValues();
			}
		};

		/**
		* Get Customers Groups
		**/

		this.getGroups					=	function(){
			$.ajax( '<?php echo site_url(array( 'rest', 'nexo', 'customers_groups' ));?>?store_id=<?php echo $store_id == null ? 0 : $store_id;?>', {
				dataType		:	'json',
				success			:	function( customers ){

					v2Checkout.CustomersGroups	=	customers;

				},
				error			:	function(){
					NexoAPI.Bootbox().alert( '<?php echo addslashes(__('Une erreur s\'est produite durant la récupération des groupes des clients', 'nexo'));?>' );
				}
			});
		}

		/**
		* Start
		**/

		this.run						=	function(){
			this.getGroups();
			// this.bind();
		};
	}

	/**
	* Display Items on the grid
	* @param Array
	* @return void
	**/

	this.displayItems			=	function( json ) {
		if( json.length > 0 ) {
			// Empty List
			$( '#filter-list' ).html( '' );

			_.each( json, function( value, key ) {

				/**
				* We test item quantity of skip that test if item is not countable.
				* value.TYPE = 0 means item is physical, = 1 means item is numerical
				* value.STATUS = 0 means item is on sale, = 1 means item is disabled
				**/

				if( ( ( parseInt( value.QUANTITE_RESTANTE ) > 0 && value.TYPE == '1' ) || ( value.TYPE == '2' ) ) && value.STATUS == '1' ) {

					var promo_start	= moment( value.SPECIAL_PRICE_START_DATE );
					var promo_end	= moment( value.SPECIAL_PRICE_END_DATE );

					var MainPrice	= NexoAPI.ParseFloat( value.PRIX_DE_VENTE_TTC )
					var Discounted	= '';
					var CustomBackground	=	'';
					var ImagePath			=	value.APERCU == '' ? '<?php echo '../modules/nexo/images/default.png';?>'  : value.APERCU;

					if( promo_start.isBefore( v2Checkout.CartDateTime ) ) {
						if( promo_end.isSameOrAfter( v2Checkout.CartDateTime ) ) {
							MainPrice			=	NexoAPI.ParseFloat( value.PRIX_PROMOTIONEL );
							Discounted			=	'<small style="color: #999;border: solid 1px #dadada; border-radius: 5px;padding: 2px;position: absolute;box-shadow: 0px 0px 5px 1px #988f8f;top: 10px;left: 10px;z-index: 800;    background: #EEE;"><del>' + NexoAPI.DisplayMoney( NexoAPI.ParseFloat( value.PRIX_DE_VENTE_TTC ) ) + '</del></small>';
							// CustomBackground	=	'background:<?php echo $this->config->item('discounted_item_background');?>';
						}
					}

					// @since 2.7.1
					if( v2Checkout.CartShadowPriceEnabled ) {
						MainPrice			=	NexoAPI.ParseFloat( value.SHADOW_PRICE );
					}

					// style="max-height:100px;"
					// alert( value.DESIGN.length );
					var design	=	value.DESIGN.length > 15 ? '<span class="marquee_me">' + value.DESIGN + '</span>' : value.DESIGN;

					// Reshresh JSon data
					value.MAINPRICE 		=	MainPrice;

					$( '#filter-list' ).append(
						'<div class="col-lg-2 col-md-3 col-xs-6 shop-items filter-add-product noselect text-center" data-remaining-quantity="' + value.QUANTITE_RESTANTE + '" data-codebar="' + value.CODEBAR + '" style="' + CustomBackground + ';padding:5px; border-right: solid 1px #DEDEDE;border-bottom: solid 1px #DEDEDE;" data-design="' + value.DESIGN.toLowerCase() + '" data-category="' + value.REF_CATEGORIE + '" data-sku="' + value.SKU.toLowerCase() + '">' +
							'<img data-original="<?php echo get_store_upload_url() . '/items-images/';?>' + ImagePath + '" width="100" style="max-height:64px;" class="img-responsive img-rounded lazy">' +
							'<div class="caption text-center" style="padding:2px;overflow:hidden;"><strong class="item-grid-title">' + design + '</strong><br>' +
							'<span class="align-center">' + NexoAPI.DisplayMoney( MainPrice ) + '</span>' + Discounted +
							'</div>' +
						'</div>' );

						v2Checkout.ItemsCategories	=	_.extend( v2Checkout.ItemsCategories, _.object( [ value.REF_CATEGORIE ], [ value.NOM ] ) );
					}
				});

				this.POSItems 		=	json;

				// Bind Categorie @since 2.7.1
				v2Checkout.bindCategoryActions();

				// Add Lazy @since 2.6.1
				$("img.lazy").lazyload({
					failure_limit : 200,
					effect : "fadeIn",
					load : function( e ){
						$( this ).removeAttr( 'width' );
					},
					container : $( '.item-list-container' )
				});

				// Bind Add to Items
				this.bindAddToItems();
				// @since 2.9.9
				this.checkItemsStock( json );
			} else {
				NexoAPI.Bootbox().alert( '<?php echo addslashes(__('Vous ne pouvez pas procéder à une vente, car aucun article n\'est disponible pour la vente.', 'nexo' ));?>' );
			}

			$( '.filter-add-product' ).each( function(){
				$(this).bind( 'mouseenter', function(){
					$( this ).find( '.marquee_me' ).replaceWith( '<marquee class="marquee_me" behavior="alternate" scrollamount="4" direction="left" style="width:100%;float:left;">' + $( this ).find( '.marquee_me' ).html() + '</marquee>' );
				})
			});

			$( '.filter-add-product' ).each( function(){
				$(this).bind( 'mouseleave', function(){
					$( this ).find( '.marquee_me' ).replaceWith( '<span class="marquee_me">' + $( this ).find( '.marquee_me' ).html() + '</span>' );
				})
			});
		};

		/**
		* Empty cart item table
		*
		**/

		this.emptyCartItemTable		=	function() {
			$( '#cart-table-body' ).find( '[cart-item]' ).remove();
		};

		/**
		 * Treat Found item
		 * @param object item
		 * @since 3.1
		 * @return void
		**/

		this.treatFoundItem 		=	function({ item, barcode, quantity, increase, index = null, callback = null }){
			
			/**
			* Filter item when is loaded
			**/

			item 			=	NexoAPI.events.applyFilters( 'item_loaded', item );

			/**
			* Override Add Item default Feature
			**/

			if( NexoAPI.events.applyFilters( 'override_add_item' , { 
				item,
				proceed 			: false, 
				quantity,
				increase,
				index
			}).proceed == true ) {
				return;
			}

			if( typeof callback == 'function' ) {
				callback( item );
			} else {
				this.addToCart({ item : item })
			}

			// this.addToCart({ item, barcode, quantity, increase });
		}

		/**
		* Fix Product Height
		**/

		this.fixHeight				=	function(){
			// Height and Width
			var headerHeight				=	parseInt( $( '.main-header' ).outerHeight() );
			var contentHeader				=	parseInt( $( '.content-header' ).outerHeight() );
			var contentMargin				=	parseInt( $( '.content' ).css( 'padding-top' ) );
			var footerHeight				=	parseInt( $( '.main-footer' ).outerHeight() );
			var tabHeight					=	parseInt( $( '.tab-grid' ).outerHeight(true) );
			var checkoutHeader 				=	parseInt( $( '.checkout-header' ).outerHeight() );
			var contentPaddingTop			=	parseInt( $( '.content' ).css( 'padding-top' ) ) + 2;

			// var windowHeight				=	parseInt( window.innerHeight < 500 ? 500 : window.innerHeight );
			if( $( '.content-wrapper' ).css( 'min-height' ) ) {
				var	wrapperHeight			=	window.innerHeight;
				wrapperHeight			-=	footerHeight;
				wrapperHeight			-= 	headerHeight; // GG
				wrapperHeight			-=	contentHeader;
				wrapperHeight			-=	contentMargin;
			} else {
				var toreduce				=	$( '.new-wrapper' ).innerHeight() - $( '.content' ).innerHeight();
				// alert( $( '.content' ).outerHeight() );
				var	wrapperHeight			=	parseInt( $( '.new-wrapper' ).innerHeight() );
			}

			// Col 1

			var col1_paddingWrapper			=	parseInt( $( '#cart-details-wrapper' ).css( 'margin-bottom' ) );
			var cartHeader					=	parseInt( $( '#cart-header' ).outerHeight(true) );
			var cartTableHeader				=	parseInt( $( '#cart-item-table-header' ).outerHeight(true) );
			var cartTableFooter				=	parseInt( $( '#cart-details' ).outerHeight(true) );
			var cartPanel					=	parseInt( $( '#cart-panel' ).outerHeight(true) );

			//	alert( $( '#cart-item-table-header' ).html() );

			// alert( $( '#cart-header' ).css( 'height' ) );
			// alert( col1_paddingWrapper + ' ' +  cartHeader + ' ' + cartTableHeader + ' ' + cartTableFooter + ' ' + cartPanel + ' ' + tabHeight );

			// return;

			$( '#cart-table-body' ).height( wrapperHeight - (
				contentPaddingTop
				+ cartHeader
				+ cartTableHeader
				+ cartTableFooter
				+ cartPanel
				+ col1_paddingWrapper
				+ tabHeight
				+ checkoutHeader
			) );

			// Col 2
			var categorySliderHeight		=	parseInt( $( '.cattegory-slider' ).height() );
			var searchFieldHeight			=	parseInt( $( '.search-field-wrapper' ).outerHeight() );
			var productListWrapper			=	parseInt( $( '#product-list-wrapper' ).css( 'margin-bottom' ) );

			$( '.item-list-container' ).height( wrapperHeight - (
				contentPaddingTop +
				productListWrapper +
				categorySliderHeight +
				searchFieldHeight +
				tabHeight +
				checkoutHeader 
			) );

			this.paymentWindow.hideSplash();
		};

		/**
		* Filter Item
		*
		* @param string
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
			$.ajax('<?php echo $this->events->apply_filters( 'nexo_checkout_item_url', site_url([ 'rest', 'nexo', 'item' ]) ) . '?store_id=' . $store_id;?>', { // _with_meta
				beforeSend	:	function(){
					if( typeof beforeCallback == 'function' ) {
						beforeCallback();
					}
				},
				error	:	function(){
					NexoAPI.Bootbox().alert( '<?php echo addslashes(__('Une erreur s\'est produite durant la récupération des produits', 'nexo'));?>' );
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
		* Get Item
		* get item from cart
		**/

		this.getItem				=	function( barcode ) {
			for( var i = 0; i < this.CartItems.length ; i++ ) {
				if( this.CartItems[i].CODEBAR == barcode ) {
					return this.CartItems[i];
				}
			}
			return false;
		}

		/**
		* Get Item Sale Price
		* @param object item
		* @return float main item price
		**/

		this.getItemSalePrice			=	function( itemObj ) {
			var promo_start				= 	moment( itemObj.SPECIAL_PRICE_START_DATE );
			var promo_end				= 	moment( itemObj.SPECIAL_PRICE_END_DATE );

			var MainPrice				= 	NexoAPI.ParseFloat( itemObj.PRIX_DE_VENTE_TTC )
			var Discounted				= 	'';
			var CustomBackground		=	'';
			itemObj.PROMO_ENABLED	=	false;

			if( promo_start.isBefore( v2Checkout.CartDateTime ) ) {
				if( promo_end.isSameOrAfter( v2Checkout.CartDateTime ) ) {
					itemObj.PROMO_ENABLED	=	true;
					MainPrice				=	NexoAPI.ParseFloat( itemObj.PRIX_PROMOTIONEL );
				}
			}

			// @since 2.7.1
			if( v2Checkout.CartShadowPriceEnabled ) {
				MainPrice			=	NexoAPI.ParseFloat( itemObj.SHADOW_PRICE );
			}
			return MainPrice;
		}

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
		* Is Cart empty
		* @return boolean
		**/

		this.isCartEmpty			=	function(){
			if( _.toArray( this.CartItems ).length > 0 ) {
				return false;
			}
			return true;
		}

		/**
		* Display item Settings
		* this option let you select categories to displays
		**/

		this.itemsSettings					=	function(){
			this.buildItemsCategories( '.categories_dom_wrapper' );
		};

		/**
		* Show Numpad
		**/

		this.showNumPad				=	function( object, text, object_wrapper, real_time ){
			// Field
			var field				=	real_time == true ? object : '[name="numpad_field"]';

			// If real time editing is enabled
			var input_field			=	! real_time ?
			'<div class="form-group">' +
			'<input type="text" class="form-control input-lg" name="numpad_field"/>' +
			'</div>' : '';

			var NumPad				=
			'<div id="numpad">' +
			'<h4 class="text-center">' + ( text ? text : '' ) + '</h4><br>' +
			input_field	+
			'<div class="row">' +
			'<div class="col-lg-3 col-md-3 col-xs-3">' +
			'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad7" value="<?php echo addslashes(__('7', 'nexo'));?>"/>' +
			'</div>' +
			'<div class="col-lg-3 col-md-3 col-xs-3">' +
			'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad8" value="<?php echo addslashes(__('8', 'nexo'));?>"/>' +
			'</div>' +
			'<div class="col-lg-3 col-md-3 col-xs-3">' +
			'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad9" value="<?php echo addslashes(__('9', 'nexo'));?>"/>' +
			'</div>' +
			'<div class="col-lg-3 col-md-3 col-xs-3">' +
			'<input type="button" class="btn btn-default btn-block btn-lg numpad numpadplus" value="<?php echo addslashes(__('+', 'nexo'));?>"/>' +
			'</div>' +
			'</div>' +
			'<br>'+
			'<div class="row">' +
			'<div class="col-lg-3 col-md-3 col-xs-3">' +
			'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad4" value="<?php echo addslashes(__('4', 'nexo'));?>"/>' +
			'</div>' +
			'<div class="col-lg-3 col-md-3 col-xs-3">' +
			'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad5" value="<?php echo addslashes(__('5', 'nexo'));?>"/>' +
			'</div>' +
			'<div class="col-lg-3 col-md-3 col-xs-3">' +
			'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad6" value="<?php echo addslashes(__('6', 'nexo'));?>"/>' +
			'</div>' +
			'<div class="col-lg-3 col-md-3 col-xs-3">' +
			'<input type="button" class="btn btn-default btn-block btn-lg numpad numpadminus" value="<?php echo addslashes(__('-', 'nexo'));?>"/>' +
			'</div>' +
			'</div>' +
			'<br>'+
			'<div class="row">' +
			'<div class="col-lg-3 col-md-3 col-xs-3">' +
			'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad1" value="<?php echo addslashes(__('1', 'nexo'));?>"/>' +
			'</div>' +
			'<div class="col-lg-3 col-md-3 col-xs-3">' +
			'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad2" value="<?php echo addslashes(__('2', 'nexo'));?>"/>' +
			'</div>' +
			'<div class="col-lg-3 col-md-3 col-xs-3">' +
			'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad3" value="<?php echo addslashes(__('3', 'nexo'));?>"/>' +
			'</div>' +
			'<div class="col-lg-3 col-md-3 col-xs-3">' +
			'<input type="button" class="btn btn-warning btn-block btn-lg numpad numpaddel" value="&larr;"/>' +
			'</div>' +
			'</div>' +
			'<br/>' +
			'<div class="row">' +
			'<div class="col-lg-6 col-md-6 col-xs-6">' +
			'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad0" value="<?php echo addslashes(__('0', 'nexo'));?>"/>' +
			'</div>' +
			'<div class="col-lg-3 col-md-3 col-xs-3">' +
			'<input type="button" class="btn btn-default btn-block btn-lg numpad numpaddot" value="<?php echo addslashes(__('.', 'nexo'));?>"/>' +
			'</div>' +
			'<div class="col-lg-3 col-md-3 col-xs-3">' +
			'<button type="button" class="btn btn-danger btn-block btn-lg numpad numpadclear"><i class="fa fa-eraser"></i></button></div>' +
			'</div>' +
			'</div>'
			'</div>';

			if( $( object_wrapper ).length > 0 ) {
				$( object_wrapper ).html( NumPad );
			} else {
				NexoAPI.Bootbox().confirm( NumPad, function( action ) {
					if( action == true ) {
						$( object ).val( $( field ).val() );
						$( object ).trigger( 'change' );
					}
				});
			}

			if( $( field ).val() == '' ) {
				$( field ).val(0);
			}

			$( field ).focus();

			$( field ).click(function () {
			   $(this).select();
			});

			$( field ).val( $( object ).val() );

			for( var i = 0; i <= 9; i++ ) {
				$( '#numpad' ).find( '.numpad' + i ).bind( 'click', function(){
					var current_value	=	$( field ).val();
					current_value	=	current_value == '0' ? '' : current_value;
					$( field ).val( current_value + $( this ).val() );
				});
			}

			$( '.numpadclear' ).bind( 'click', function(){
				$( field ).val(0);
			});

			$( '.numpadplus' ).bind( 'click', function(){
				var numpad_value	=	NexoAPI.ParseFloat( $( field ).val() );
				$( field ).val( ++numpad_value );
			});

			$( '.numpadminus' ).bind( 'click', function(){
				var numpad_value	=	NexoAPI.ParseFloat( $( field ).val() );
				$( field ).val( --numpad_value );
			});

			$( '.numpaddot' ).bind( 'click', function(){
				var current_value	=	$( field ).val();
				current_value	=	current_value == '' ? 0 : parseFloat( current_value );
				//var numpad_value	=	NexoAPI.ParseFloat( $( field ).val() );
				$( field ).val( current_value + '.' );
			});

			$( '.numpaddel' ).bind( 'click', function(){
				var numpad_value	=	$( field ).val();
				numpad_value	=	numpad_value.substr( 0, numpad_value.length - 1 );
				numpad_value 	= 	numpad_value == '' ? 0 : numpad_value;
				$( field ).val( numpad_value );
			});

			$( field ).blur( function(){
				if( $( this ).val() == '' ) {
					$( this ).val(0);
				}
			});
		};

		/**
		* Display specific error
		**/

		this.showError				=	function( error_type ) {
			if( error_type == 'ajax_fetch' ) {
				NexoAPI.Bootbox().alert( '<?php echo addslashes(__('Une erreur s\'est produite durant la récupération des données', 'nexo'));?>' );
			}
		}

		/**
		* Search Item
		**/

		this.searchItems					=	function( value ){
			console.log( 'FIX THIS' );
		};

		/**
		* Quick Search Items
		* @param
		**/

		this.quickItemSearch			=	function( value ) {
			if( value.length <= 3 ) {
				$( '.filter-add-product' ).each( function(){
					$( this ).show();
					$( this ).addClass( 'item-visible' );
					$( this ).removeClass( 'item-hidden' );
					$( this ).find( '.floatting-shortcut' ).remove();
				});
			} else {
				let i 	=	1;
				$( '.filter-add-product' ).each( function(){
					// Filter Item
					if(
						$( this ).attr( 'data-design' ).search( value.toLowerCase() ) == -1 &&
						$( this ).attr( 'data-category-name' ).search( value.toLowerCase() ) == -1 &&
						$( this ).attr( 'data-codebar' ).search( value.toLowerCase() ) == -1 && // Scan, also item Barcode
						$( this ).attr( 'data-sku' ).search( value.toLowerCase() ) == -1  // Scan, also item SKU
					) {
						$( this ).hide();
						$( this ).addClass( 'item-hidden' );
						$( this ).removeClass( 'item-visible' );
					} else {
						$( this ).show();
						$( this ).addClass( 'item-visible' );
						$( this ).removeClass( 'item-hidden' );
						$( this ).find( '.caption' ).append( '<span class="floatting-shortcut">' + i + '</span>' );
						i++;
					}					
				});
			}
		}

		/**
		* Payment
		**/

		this.paymentWindow					=	new function(){
			/// Display Splash
			this.showSplash			=	function(){
				if( $( '.nexo-overlay' ).length == 0 ) {
					$( 'body' ).append( '<div class="nexo-overlay"></div>');
					$( '.nexo-overlay').css({
						'width' : '100%',
						'height' : '100%',
						'background': 'rgba(0, 0, 0, 0.5)',
						'z-index'	: 5000,
						'position' : 'absolute',
						'top'	:	0,
						'left' : 0,
						'display' : 'none'
					}).fadeIn( 500 );

					$( '.nexo-overlay' ).append( '<i class="fa fa-refresh fa-spin nexo-refresh-icon" style="color:#FFF;font-size:50px;"></i>');

					$( '.nexo-refresh-icon' ).css({
						'position' : 'absolute',
						'top'	:	'50%',
						'left' : '50%',
						'margin-top' : '-25px',
						'margin-left' : '-25px',
						'width' : '44px',
						'height' : '50px'
					})
				}
			}

			// Hide splash
			this.hideSplash			=	function(){
				$( '.nexo-overlay' ).fadeOut( 400, function(){
					$( this ).remove();
				} );
			}

			this.close				=	function(){
				$( '.paxbox-box [data-bb-handler="cancel"]' ).trigger( 'click' );
			};
		};

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
		* Refresh Cart Values
		*
		**/

		this.refreshCartValues		=	function(){

			this.calculateCartDiscount();
			this.calculateCartRistourne();
			this.calculateCartGroupDiscount();

			this.CartDiscount		=	NexoAPI.ParseFloat( this.CartRemise + this.CartRabais + this.CartRistourne + this.CartGroupDiscount );
			this.CartValueRRR		=	NexoAPI.ParseFloat( this.CartValue - this.CartDiscount );
			
			this.calculateCartVAT();

			this.CartToPay			=	( this.CartValueRRR + this.CartVAT + this.CartShipping );

			<?php if( in_array(strtolower(@$Options[ store_prefix() . 'nexo_currency_iso' ]), $this->config->item('nexo_supported_currency')) ) {
				?>
				this.CartToPayLong		=	numeral( this.CartToPay ).multiply(100).value();
				<?php
			} else {
				?>
				this.CartToPayLong		=	NexoAPI.Format( this.CartToPay, '0.00' );
				<?php
			};?>

			$( '.cart-value' ).html( NexoAPI.DisplayMoney( this.CartValue ) );
			$( '.cart-vat' ).html( NexoAPI.DisplayMoney( this.CartVAT ) );
			$( '.cart-discount' ).html( NexoAPI.DisplayMoney( this.CartDiscount ) );
			$( '.cart-topay' ).html( NexoAPI.DisplayMoney( this.CartToPay ) );
			$( '.cart-item-vat' ).html( NexoAPI.DisplayMoney( this.CartItemsVAT ) );
			
			//@since 3.0.19
			let itemsNumber 	=	0;
			_.each( this.CartItems, ( item ) => {
				itemsNumber 	+=	parseInt( item.QTE_ADDED );
			});			
			$( '.items-number' ).html( itemsNumber );

			NexoAPI.events.applyFilters( 'refresh_cart_values', this.CartItems );
		};

		/**
		* use saved discount (automatic discount)
		**/

		this.restoreCustomRistourne			=	function(){
			<?php if (isset($order)):?>
			<?php if (floatval($order[ 'order' ][0][ 'RISTOURNE' ]) > 0):?>
			this.CartRistourneEnabled		=	true;
			this.CartRistourneType			=	'amount';
			this.CartRistourneAmount		=	NexoAPI.ParseFloat( <?php echo floatval($order[ 'order' ][0][ 'RISTOURNE' ]);?> );
			this.CartRistourneCustomerID	=	'<?php echo $order[ 'order' ][0][ 'REF_CLIENT' ];?>';
			<?php endif;?>
			<?php endif;?>
		}

		/**
		* Restore default discount (automatic discount)
		**/

		this.restoreDefaultRistourne		=	function(){
			this.CartRistourneType			=	'<?php echo @$Options[ store_prefix() . 'discount_type' ];?>';
			this.CartRistourneAmount		=	'<?php echo @$Options[ store_prefix() . 'discount_amount' ];?>';
			this.CartRistournePercent		=	'<?php echo @$Options[ store_prefix() . 'discount_percent' ];?>';
			this.CartRistourneEnabled		=	false;
			this.CartRistourne				=	0;
		};

		/**
		* Reset Object
		**/

		this.resetCartObject			=	function(){
			this.ItemsCategories		=	new Object;
			this.CartItems				=	new Array;
			this.CustomersGroups		=	new Array;
			this.ActiveCategories		=	new Array;
			// Restore Cart item table
			this.buildCartItemTable();
			// Load Customer and groups
			this.customers.run();
			// Build Items
			this.getItems(null, function(){
				v2Checkout.hideSplash( 'right' );
			});
		};

		/**
		* Reset Cart
		**/

		this.resetCart					=	function(){

			this.CartValue				=	0;
			this.CartValueRRR			=	0;
			this.CartVAT				=	0;
			this.CartDiscount			=	0;
			this.CartToPay				=	0;
			this.CartToPayLong			=	0;
			this.CartRabais			=	0;
			this.CartTotalItems			=	0;
			this.CartRemise			=	0;
			this.CartPerceivedSum		=	0;
			this.CartCreance			=	0;
			this.CartToPayBack			=	0;
			// @since 2.9.6
			this.CartRabaisPercent		=	0;
			this.CartRistournePercent	=	0;
			this.CartRemisePercent		=	0;
			this.POSItems				=	[];
			// @since 3.1.3
			this.CartShipping   		=	0;
			this.CartItemsVAT 			=	0;
			this.CartType 				=	null;
			this.From 				=	null;


			<?php if (isset($order[ 'order' ])):?>
			this.ProcessURL				=	"<?php echo site_url(array( 'rest', 'nexo', 'order', User::id(), $order[ 'order' ][0][ 'ORDER_ID' ] ));?>?store_id=<?php echo get_store_id();?>";
			this.ProcessType				=	'PUT';
			this.CartType 					=	'<?php echo $order[ 'order' ][0][ 'TYPE' ];?>';
			<?php else :?>
			this.ProcessURL				=	"<?php echo site_url(array( 'rest', 'nexo', 'order', User::id() ));?>?store_id=<?php echo get_store_id();?>";
			this.ProcessType				=	'POST';
			<?php endif;?>

			this.CartRemiseType			=	'';
			this.CartRemiseEnabled		=	false;
			this.CartRemisePercent		=	0;
			this.CartPaymentType		=	null;
			this.CartShadowPriceEnabled	=	<?php echo @$Options[ store_prefix() . 'nexo_enable_shadow_price' ] == 'yes' ? 'true' : 'false';?>;
			this.CartCustomerID			=	<?php echo @$Options[ store_prefix() . 'default_compte_client' ] != null ? $Options[ store_prefix() . 'default_compte_client' ] : 'null';?>;
			this.CartAllowStripeSubmitOrder	=	false;

			this.cartGroupDiscountReset();
			this.resetCartObject();
			this.restoreDefaultRistourne();
			this.refreshCartValues();

			// @since 2.7.3
			this.CartNote				=	'';

			// @since 2.9.0
			this.CartTitle				=	'';

			// @since 2.8.2
			this.CartMetas				=	{};

			// Reset Cart
			NexoAPI.events.doAction( 'reset_cart', this );
		}

		/**
		* Run Checkout
		**/

		this.run							=	function(){

			this.resetCart();
			this.initCartDateTime();
			this.bindHideItemOptions();
			// @since 2.7.3
			this.bindAddNote();

			<?php if (isset($order)):?>
			this.emptyCartItemTable();
			<?php foreach ($order[ 'products' ] as $product):?>
			this.CartItems.push( <?php echo json_encode($product);?> );
			<?php endforeach;?>

			<?php if ( ! empty( $order[ 'order' ][0][ 'REMISE_TYPE' ] ) ):?>
			this.CartRemiseType			=	'<?php echo $order[ 'order' ][0][ 'REMISE_TYPE' ];?>';
			this.CartRemise				=	NexoAPI.ParseFloat( <?php echo $order[ 'order' ][0][ 'REMISE' ];?> );
			this.CartRemisePercent		=	<?php echo $order[ 'order' ][0][ 'REMISE_PERCENT' ];?>;
			this.CartRemiseEnabled		=	true;
			<?php endif;?>

			<?php if (floatval($order[ 'order' ][0][ 'GROUP_DISCOUNT' ]) > 0):?>
			this.CartGroupDiscount				=	<?php echo floatval($order[ 'order' ][0][ 'GROUP_DISCOUNT' ]);?>; // final amount
			this.CartGroupDiscountAmount		=	<?php echo floatval($order[ 'order' ][0][ 'GROUP_DISCOUNT' ]);?>; // Amount set on each group
			this.CartGroupDiscountType			=	'amount'; // Discount type
			this.CartGroupDiscountEnabled		=	true;
			<?php endif;?>

			this.CartCustomerID					=	<?php echo $order[ 'order' ][0][ 'REF_CLIENT' ];?>;

			// @since 2.7.3
			this.CartNote						=	'<?php echo $order[ 'order'][0][ 'DESCRIPTION' ];?>';

			// @since 2.9.1
			this.CartTitle						=	'<?php echo $order[ 'order'][0][ 'TITRE' ];?>';

			// Restore Custom Ristourne
			this.restoreCustomRistourne();

			// Refresh Cart
			// Reset Cart state
			this.buildCartItemTable();
			this.refreshCart();
			this.refreshCartValues();
			<?php endif;?>

			this.CartStartAnimation			=	'<?php echo $this->config->item('nexo_cart_animation');?>';

			$( this.ProductListWrapper ).removeClass( this.CartStartAnimation ).css( 'visibility', 'visible').addClass( this.CartStartAnimation );
			$( this.CartTableWrapper ).removeClass( this.CartStartAnimation ).css( 'visibility', 'visible').addClass( this.CartStartAnimation );

			/*this.getItems(null, function(){ // ALREADY Loaded while resetting cart
			v2Checkout.hideSplash( 'right' );
		});*/

		$( this.CartCancelButton ).bind( 'click', function(){
			v2Checkout.cartCancel();
		});

		$( this.CartDiscountButton ).bind( 'click', function(){
			v2Checkout.bindAddDiscount({
				beforeLoad		:	function(){
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
				},
				onFixedDiscount		:	function(){
					v2Checkout.CartRemiseType	=	'flat';
				},
				onPercentDiscount	:	function(){
					v2Checkout.CartRemiseType	=	'percentage';
				},
				onFieldBlur			:	function(){
					// console.log( 'Field blur performed' );
					// Percentage allowed to 100% only
					if( v2Checkout.CartRemiseType == 'percentage' && NexoAPI.ParseFloat( $( '[name="discount_value"]' ).val() ) > 100 ) {
						$( this ).val( 100 );
					} else if( v2Checkout.CartRemiseType == 'flat' && NexoAPI.ParseFloat( $( '[name="discount_value"]' ).val() ) > v2Checkout.CartValue ) {
						// flat discount cannot exceed cart value
						$( this ).val( v2Checkout.CartValue );
						NexoAPI.Notify().info( '<?php echo _s('Attention', 'nexo');?>', '<?php echo _s('La remise fixe ne peut pas excéder la valeur actuelle du panier. Le montant de la remise à été réduite à la valeur du panier.', 'nexo');?>' );
					}
				},
				onExit				:	function( value ){

					var value	=	$( '[name="discount_value"]' ).val();

					if( value  == '' || value == '0' ) {
						NexoAPI.Bootbox().alert( '<?php echo addslashes(__('Vous devez définir un pourcentage ou une somme.', 'nexo'));?>' );
						return false;
					}

					// console.log( 'Exit discount box	' );
					// Percentage can't exceed 100%
					if( v2Checkout.CartRemiseType == 'percentage' && NexoAPI.ParseFloat( value ) > 100 ) {
						value = 100;
					} else if( v2Checkout.CartRemiseType == 'flat' && NexoAPI.ParseFloat( value ) > v2Checkout.CartValue ) {
						// flat discount cannot exceed cart value
						value	=	v2Checkout.CartValue;
					}

					$( '[name="discount_value"]' ).focus();
					$( '[name="discount_value"]' ).blur();

					v2Checkout.CartRemiseEnabled	=	true;
					v2Checkout.calculateCartDiscount( value );
					v2Checkout.refreshCartValues();
				}
			});
		});

		/**
		* Search Item Feature
		**/

		$( this.ItemSearchForm ).bind( 'submit', function(){
			v2Checkout.retreiveItem( $( '[name="item_sku_barcode"]' ).val() );
			$( '[name="item_sku_barcode"]' ).val('');
			return false;
		});

		$( '.enable_barcode_search' ).bind( 'click', function(){
			if( $( this ).hasClass( 'active' ) ) {
				$( this ).removeClass( 'active' );
				v2Checkout.enableBarcodeSearch 	=	false;
			} else {
				$( this ).addClass( 'active' );
				v2Checkout.enableBarcodeSearch 	=	true;
				$( '[name="item_sku_barcode"]' ).focus();
			}
		});

		// check if the button is clicked
		<?php if( @$Options[ 'enable_quick_search' ] == 'yes' ):?>
		$( '.enable_barcode_search' ).trigger( 'click' );
		<?php endif;?>

		/**
		* Filter Item
		**/

		let addItemTimeout;

		$( this.ItemSearchForm ).bind( 'keyup', function(){
			if( v2Checkout.enableBarcodeSearch == false ) {
				v2Checkout.quickItemSearch( $( '[name="item_sku_barcode"]' ).val() );
			}

			// Add found item on the cart
			// @since 3.0.19
			if( typeof this.addItemTimeout == 'undefined' ) {
				this.addItemTimeout 	=	5;
			}

			window.clearTimeout( addItemTimeout );

			addItemTimeout 	=	window.setTimeout( () => {
				if( $( '.filter-add-product.item-visible' ).length == 1 ) {
					// when i item is found, just blur the field to avoid multiple quantity adding
					$( '.filter-add-product.item-visible' ).click();
					$( '[name="item_sku_barcode"]' ).val('');
					v2Checkout.quickItemSearch( '' );
				}
			}, 500 );
		});

		/**
		* Cart Item Settings
		**/

		$( this.ItemSettings ).bind( 'click', function(){
			v2Checkout.itemsSettings();
		});

		// Bind toggle compact mode
		this.bindToggleComptactMode();

		//
		$(window).on("beforeunload", function() {
			if( ! v2Checkout.isCartEmpty() ) {
				return "<?php echo addslashes(__('Le processus de commande a commencé. Si vous continuez, vous perdrez toutes les informations non enregistrées', 'nexo'));?>";
			}
		})

		setTimeout( function(){
			v2Checkout.toggleCompactMode(true);
		}, 800 );
	}

	/**
	* Toggle Compact Mode
	**/

	this.toggleCompactMode		=	function(){
		$( '.content-header' ).css({
			'padding'	:	0,
			'height'	:	0
		});

		$( '.content-header > h1' ).remove();
		$( '.main-footer' ).hide(0);
		$( '.main-sidebar' ).hide(0);
		$( '.main-footer > *' ).remove();
		$( '.main-header' ).css({
			'min-height' : 0,
			'overflow': 'hidden'
		}).animate({
			'height' : '0'
		}, 0 );

		$( '.content-wrapper' ).addClass( 'new-wrapper' ).removeClass( 'content-wrapper' );

		$( '.new-wrapper' ).find( '.content' ).css( 'padding-bottom', 0 );
		$( '.new-wrapper' ).find( '.content' ).css( 'height', window.innerHeight );
		// $( '.new-wrapper' ).find( '.content' ).css( 'overflow', 'hidden' );
		$( '.new-wrapper' ).find( '.content' ).css( 'background', 'rgb(211, 223, 228)' );
		this.CompactMode	=	false;
		this.fixHeight();
	}

};

$( document ).ready(function(e) {
	v2Checkout.run();
});

/**
* Filters
**/

// Default order printable
NexoAPI.events.addFilter( 'test_order_type', function( data ) {
	data[1].order_type == 'nexo_order_comptant';
	return data;
});

// Return default data values
NexoAPI.events.addFilter( 'callback_message', function( data ) {
	// console.log( data );
	return data;
});

// Filter for edit item
NexoAPI.events.addFilter( 'cart_before_item_name', function( item_name ) {
	return '<a class="btn btn-sm btn-default quick_edit_item" href="javascript:void(0)" style="float:left;vertical-align:inherit;margin-right:10px;"><i class="fa fa-edit"></i></a> ' + item_name;
});

NexoAPI.events.addFilter( 'cart_item_name', ( data ) => {
	data.displayed 		=	data.displayed.length > 23 ? data.displayed.substr( 0, 18 ) + '...' : data.displayed;
	return data;
});

function htmlEntities(str) {
    return $( '<div/>' ).text( str ).html()
}

function EntitiesHtml(str) {
    return $( '<div/>' ).html( str ).text();
}
</script>

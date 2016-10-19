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

	this.CartVATEnabled			=	<?php echo @$Options[ store_prefix() . 'nexo_enable_vat' ] == 'oui' ? 'true' : 'false';?>;
	this.CartVATPercent			=	<?php echo in_array(@$Options[ store_prefix() . 'nexo_vat_percent' ], array( null, '' )) ? 0 : @$Options[ store_prefix() . 'nexo_vat_percent' ];?>

	if( this.CartVATPercent == '0' ) {
		this.CartVATEnabled		=	false;
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
		var headerHeight				=	parseInt( $( '.main-header' ).outerHeight() );
		var contentHeader				=	parseInt( $( '.content-header' ).outerHeight() );
		var contentMargin				=	parseInt( $( '.content' ).css( 'padding-top' ) );
		var footerHeight				=	parseInt( $( '.main-footer' ).outerHeight() );
		
		// var windowHeight				=	parseInt( window.innerHeight < 500 ? 500 : window.innerHeight );
		if( $( '.content-wrapper' ).css( 'min-height' ) ) {
			var	wrapperHeight			=	window.innerHeight;
				wrapperHeight			-=	footerHeight;
				wrapperHeight			-= 	headerHeight; // GG
				wrapperHeight			-=	contentHeader;
				wrapperHeight			-=	contentMargin;
		} else {
			var	wrapperHeight			=	parseInt( $( '.new-wrapper' ).innerHeight() - 20 );
		}
		
		// Col 1
		
		var col1_paddingWrapper			=	parseInt( $( '#cart-details-wrapper' ).css( 'margin-bottom' ) );
		var cartHeader					=	parseInt( $( '#cart-header' ).outerHeight(true) );
		var cartTableHeader				=	parseInt( $( '#cart-item-table-header' ).outerHeight(true) );
		var cartTableFooter				=	parseInt( $( '#cart-details' ).outerHeight(true) );
		var cartPanel					=	parseInt( $( '#cart-panel' ).outerHeight(true) );
				
		$( '#cart-table-body' ).height( wrapperHeight - ( 
			cartHeader  
			+ cartTableHeader
			+ cartTableFooter 
			+ cartPanel	
			+ col1_paddingWrapper
		) );		

		// Col 2
		var categorySliderHeight		=	parseInt( $( '.cattegory-slider' ).height() );
		var searchFieldHeight			=	parseInt( $( '.search-field-wrapper' ).outerHeight() );
		var productListWrapper			=	parseInt( $( '#product-list-wrapper' ).css( 'margin-bottom' ) );
		
		$( '.item-list-container' ).height( wrapperHeight - ( 
			productListWrapper + 
			categorySliderHeight +
			searchFieldHeight
		) );
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
				v2Checkout.fetchItem( codebar );
			});
		});
	};

	/**
	 * Bind Add Reduce Actions on Cart table items
	**/

	this.bindAddReduceActions	=	function(){

		$( '#cart-table-body .item-reduce' ).each(function(){
			$( this ).bind( 'click', function(){
				var parent	=	$( this ).closest( 'tr' );
				_.each( v2Checkout.CartItems, function( value, key ) {
					if( typeof value != 'undefined' ) {
						if( value.CODEBAR == $( parent ).data( 'item-barcode' ) ) {
							value.QTE_ADDED--;
							// If item reach "0";
							if( parseInt( value.QTE_ADDED ) == 0 ) {
								v2Checkout.CartItems.splice( key, 1 );
							}
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

		<?php if (@$Options[ store_prefix() . 'nexo_enable_numpad' ] != 'non'):?>
		// Bind Num padd
		$( '[name="shop_item_quantity"]' ).bind( 'click', function(){
			v2Checkout.showNumPad( $( this ), '<?php echo addslashes(__('Définir la quantité à  ajouter', 'nexo'));?>' );
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

			var MainPrice					= 	NexoAPI.ParseFloat( currentItem.PRIX_DE_VENTE )
			var Discounted					= 	'';
			var CustomBackground			=	'';
				currentItem.PROMO_ENABLED	=	false;

			if( promo_start.isBefore( v2Checkout.CartDateTime ) ) {
				if( promo_end.isSameOrAfter( v2Checkout.CartDateTime ) ) {
					currentItem.PROMO_ENABLED	=	true;
					MainPrice			=	NexoAPI.ParseFloat( currentItem.PRIX_PROMOTIONEL );
					Discounted			=	'<small><del>' + NexoAPI.DisplayMoney( NexoAPI.ParseFloat( currentItem.PRIX_DE_VENTE ) ) + '</del></small>';
					CustomBackground	=	'background:<?php echo $this->config->item('discounted_item_background');?>';
				}
			}
			
			// @since 2.7.1
			if( v2Checkout.CartShadowPriceEnabled ) {
				MainPrice			=	NexoAPI.ParseFloat( currentItem.SHADOW_PRICE );
			}
			
			$( this ).replaceWith( '<td width="130"><div class="input-group input-group-sm"><input type="number" value="' + MainPrice + '" class="unit-price-form form-control" aria-describedby="sizing-addon3"></div></td>' );
			
			$( '.unit-price-form' ).focus();
			
			$( '.unit-price-form' ).bind( 'blur', function(){
				
				if( ! isNaN( parseFloat( $( this ).val() ) ) ) {
				
					$( this ).closest( 'td' ).replaceWith( '<td width="130" class="text-center item-unit-price"  style="line-height:30px;">' + NexoAPI.DisplayMoney( $( this ).val() ) + '</td>' );
				} else {
					$( this ).closest( 'td' ).replaceWith( '<td width="130" class="text-center item-unit-price"  style="line-height:30px;">' + NexoAPI.DisplayMoney( MainPrice ) + '</td>' );	
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
								v2Checkout.CartItems[i].PRIX_DE_VENTE	=	$( this ).val();	
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
		$( '.btn.cart-discount' ).each( function(){
			if( ! $( this ).hasClass( 'remove-action-bound' ) ) {
				$( this ).addClass( 'remove-action-bound' );
				$( this ).bind( 'click', function(){
					NexoAPI.Bootbox().confirm( '<?php echo addslashes(__('Souhaitez-vous annuler cette remise ?', 'nexo'));?>', function( action ) {
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
				$( '[name="discount_value"]' ).focus();

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

				$( '[name="discount_value"]' ).focus();
				$( '[name="discount_value"]' ).blur();
				
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
					if( NexoAPI.ParseFloat( $( '.current_item_price' ).val() ) < NexoAPI.ParseFloat( CurrentItem.PRIX_DE_VENTE ) ) {
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
			
			$( '.sale_price' ).html( NexoAPI.DisplayMoney( CurrentItem.PRIX_DE_VENTE ) );
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
			
			var _item			=	v2Checkout.getItem( $( this ).closest( 'tr' ).attr( 'data-item-barcode' ) );
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
			_.each( this.CartItems, function( value, key ) {

				var promo_start			= 	moment( value.SPECIAL_PRICE_START_DATE );
				var promo_end			= 	moment( value.SPECIAL_PRICE_END_DATE );

				var MainPrice			= 	NexoAPI.ParseFloat( value.PRIX_DE_VENTE )
				var Discounted			= 	'';
				var CustomBackground	=	'';
					value.PROMO_ENABLED	=	false;

				if( promo_start.isBefore( v2Checkout.CartDateTime ) ) {
					if( promo_end.isSameOrAfter( v2Checkout.CartDateTime ) ) {
						value.PROMO_ENABLED	=	true;
						MainPrice			=	NexoAPI.ParseFloat( value.PRIX_PROMOTIONEL );
						Discounted			=	'<small><del>' + NexoAPI.DisplayMoney( NexoAPI.ParseFloat( value.PRIX_DE_VENTE ) ) + '</del></small>';
						CustomBackground	=	'background:<?php echo $this->config->item('discounted_item_background');?>';
					}
				}
				
				// @since 2.7.1
				if( v2Checkout.CartShadowPriceEnabled ) {
					MainPrice			=	NexoAPI.ParseFloat( value.SHADOW_PRICE );
				}

				// <span class="btn btn-primary btn-xs item-reduce hidden-sm hidden-xs">-</span> <input type="number" style="width:40px;border-radius:5px;border:solid 1px #CCC;" maxlength="3"/> <span class="btn btn-primary btn-xs   hidden-sm hidden-xs">+</span>
				
				// <?php echo site_url('dashboard/nexo/produits/lists/edit');?>
				// /' + value.ID + '
				
				var	cartTableBeforeItemName		=	NexoAPI.events.applyFilters( 'cart_before_item_name', '<a class="btn btn-sm btn-default quick_edit_item" href="javascript:void(0)" style="vertical-align:inherit;margin-right:10px;"><i class="fa fa-edit"></i></a>' );
				
				// :: alert( value.DESIGN.length );
				var item_design		=	value.DESIGN.length > 20 ? '<span style="text-overflow:hidden">' + value.DESIGN.substr( 0, 20 ) + '</span>' : value.DESIGN ;
				
				var DiscountAmount	=	value.DISCOUNT_TYPE	== 'percentage' ? value.DISCOUNT_PERCENT + '%' : NexoAPI.DisplayMoney( value.DISCOUNT_AMOUNT );
				
				var itemSubTotal	=	MainPrice * parseInt( value.QTE_ADDED );
				
				if( value.DISCOUNT_TYPE == 'percentage' && parseFloat( value.DISCOUNT_PERCENT ) > 0 ) {
					var itemPercentOff	=	( itemSubTotal * parseFloat( value.DISCOUNT_PERCENT ) ) / 100;
						itemSubTotal	-=	itemPercentOff;
				} else if( value.DISCOUNT_TYPE == 'flat' && parseFloat( value.DISCOUNT_AMOUNT ) > 0 ) {
					var itemPercentOff	=	 parseFloat( value.DISCOUNT_AMOUNT );
						itemSubTotal	-=	itemPercentOff;
				}

				$( '#cart-table-body' ).find( 'table' ).append(
					'<tr cart-item data-line-weight="' + ( MainPrice * parseInt( value.QTE_ADDED ) ) + '" data-item-barcode="' + value.CODEBAR + '">' +
						// ' + cartTableBeforeItemName + '  has been disabled
						'<td width="210" class="text-left" style="line-height:30px;"><span style="text-transform: uppercase;">' + item_design + '</span></td>' +
						'<td width="130" class="text-center item-unit-price"  style="line-height:30px;">' + NexoAPI.DisplayMoney( MainPrice ) + ' ' + Discounted + '</td>' +
						'<td width="145" class="text-center">' +
							'<div class="input-group input-group-sm">' +
								'<span class="input-group-btn">' +
									'<button class="btn btn-default item-reduce">-</button>' +
								'</span>'+
								'<input type="number" name="shop_item_quantity" value="' + value.QTE_ADDED + '" class="form-control" aria-describedby="sizing-addon3">' +
								'<span class="input-group-btn">' +
									'<button class="btn btn-default item-add">+</button>' +
								'</span>'+
							'</div>' +
						'</td>' +
						<?php if( @$Options[ store_prefix() . 'unit_item_discount_enabled' ] == 'yes' ):?>
						'<td width="80" class="text-center item-discount"  style="line-height:28px;"><span class="btn btn-default btn-sm">' + DiscountAmount + '</span></td>' +
						<?php endif;?>
						'<td width="110" class="text-right" style="line-height:30px;">' + NexoAPI.DisplayMoney( itemSubTotal ) + '</td>' +
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
		if( $( '.cart-discount-notice-area' ).find( '.cart-discount' ).length > 0 ) {
			$( '.cart-discount-notice-area' ).find( '.cart-discount' ).remove();
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
					$( '.cart-discount-notice-area' ).append( '<span style="cursor: pointer;margin:0px 2px;" class="animated bounceIn btn expandable btn-primary btn-xs cart-discount"><i class="fa fa-remove"></i> <?php echo addslashes(__('Remise : ', 'nexo'));?>' + this.CartRemisePercent + '%</span>' );
				}

			} else if( this.CartRemiseType == 'flat' ) {
				if( typeof value != 'undefined' ) {
					this.CartRemise 			=	NexoAPI.ParseFloat( value );
				}

				if( this.CartRemiseEnabled ) {
					$( '.cart-discount-notice-area' ).append( '<span style="cursor: pointer;margin:0px 2px;" class="animated bounceIn btn expandable btn-primary btn-xs cart-discount"><i class="fa fa-remove"></i> <?php echo addslashes(__('Remise : ', 'nexo'));?>' + NexoAPI.DisplayMoney( this.CartRemise ) + '</span>' );
				}
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
					this.CartRistourne	=	( NexoAPI.ParseFloat( this.CartRistournePercent ) * this.CartValue ) / 100;
				}

				$( '.cart-discount-notice-area' ).append( '<span style="cursor: pointer; margin:0px 2px;" class="animated bounceIn btn expandable btn-info btn-xs cart-ristourne"><i class="fa fa-remove"></i> <?php echo addslashes(__('Ristourne : ', 'nexo'));?>' + this.CartRistournePercent + '%</span>' );

			} else if( this.CartRistourneType == 'amount' ) {
				if( this.CartRistourneAmount != '' ) {
					this.CartRistourne	=	NexoAPI.ParseFloat( this.CartRistourneAmount );
				}

				$( '.cart-discount-notice-area' ).append( '<span style="cursor: pointer;margin:0px 2px;" class="animated bounceIn btn expandable btn-info btn-xs cart-ristourne"><i class="fa fa-remove"></i> <?php echo addslashes(__('Ristourne : ', 'nexo'));?>' + NexoAPI.DisplayMoney( this.CartRistourneAmount ) + '</span>' );

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

					$( '.cart-discount-notice-area' ).append( '<p style="cursor: pointer; margin:0px 2px;" class="animated bounceIn btn btn-warning expandable btn-xs cart-group-discount"><i class="fa fa-remove"></i> <?php echo addslashes(__('Remise de groupe : ', 'nexo'));?>' + this.CartGroupDiscountPercent + '%</p>' );
				}
			} else if( this.CartGroupDiscountType == 'amount' ) {
				if( this.CartGroupDiscountAmount != '' ) {
					this.CartGroupDiscount		=	NexoAPI.ParseFloat( this.CartGroupDiscountAmount )	;

					$( '.cart-discount-notice-area' ).append( '<p style="cursor: pointer; margin:0px 2px;" class="animated bounceIn btn btn-warning expandable btn-xs cart-group-discount"><i class="fa fa-remove"></i> <?php echo addslashes(__('Remise de groupe : ', 'nexo'));?>' + NexoAPI.DisplayMoney( this.CartGroupDiscountAmount ) + '</p>' );
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
				// document.location	=	'<?php echo site_url(array( 'dashboard', 'nexo', 'commandes', 'lists' ));?>';
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
	 * Customer DropDown Menu
	**/

	this.customers			=	new function(){

		this.DefaultCustomerID	=	'<?php echo @$Options[ store_prefix() . 'default_compte_client' ];?>';

		/**
		 * Bind
		**/

		this.bind				=	function(){
			$('.dropdown-bootstrap').selectpicker({
			  style: 'btn-default',
			  size: 4
			});

			if( typeof $( '.cart-add-customer' ).attr( 'bound' ) == 'undefined' ) {
				$( '.cart-add-customer' ).bind( 'click', function(){
					v2Checkout.customers.createBox();
				})
				$( '.cart-add-customer' ).attr( 'bound', 'true' );
			}

			if( typeof $( '.customers-list' ).attr( 'change-bound' ) == 'undefined' ) {
				$( '.customers-list' ).bind( 'change', function(){
					v2Checkout.customers.bindSelectCustomer( $( this ).val() );
				});
				$( '.customers-list' ).attr( 'change-bound', 'true' );
			}
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
					  '<span class="input-group-addon" id="basic-addon1"><?php echo addslashes(__('Groupe du client', 'nexo'));?></span>'+
					  '<select type="text" class="form-control customers_groups" name="customer_group" aria-describedby="basic-addon1">' +
					  	'<option value=""><?php echo addslashes(__('Veuillez choisir un client', 'nexo'));?></option>' +
					  '</select>' +
					'</div>'+
				'</div>' +
			'</form>';

			NexoAPI.Bootbox().confirm( userForm, function( action ) {
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
					NexoAPI.Bootbox().alert( '<?php echo addslashes(__('Vous devez définir le nom du client', 'nexo'));?>' );
					return false;
				}
				// Group is required
				if( ref_group == '' ) {
					NexoAPI.Bootbox().alert( '<?php echo addslashes(__('Vous devez choisir un groupe pour le client', 'nexo'));?>' );
					return false;
				}
				// Ajax
				$.ajax( '<?php echo site_url(array( 'rest', 'nexo', 'customer' ));?>?store_id=<?php echo $store_id == null ? 0 : $store_id;?>', {
					dataType		:	'json',
					type			:	'POST',
					data			:	_.object(
						// if Store Feature is enabled
						[ 'nom', 'prenom', 'email', 'tel', 'ref_group', 'author', 'date_creation' ],
						[ name, surname, email, phone, ref_group, <?php echo User::id();?>, v2Checkout.CartDateTime.format( 'YYYY-MM-DD HH:mm:ss' ) ]
					),
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
		 * @params object customer data
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
		 * @params object customer data
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
		 * Get Customers
		**/

		this.get						=	function(){
			$.ajax( '<?php echo site_url(array( 'rest', 'nexo', 'customer' ));?>?store_id=<?php echo $store_id == null ? 0 : $store_id;?>', {
				dataType		:	'json',
				success			:	function( customers ){

					$( '.customers-list' ).selectpicker('destroy');
					// Empty list first
					$( '.customers-list' ).children().each(function(index, element) {
                        $( this ).remove();
                    });;

					_.each( customers, function( value, key ){
						if( parseInt( v2Checkout.CartCustomerID ) == parseInt( value.ID ) ) {

							$( '.customers-list' ).append( '<option value="' + value.ID + '" selected="selected">' + value.NOM + '</option>' );
							// Fix customer Selection
							NexoAPI.events.doAction( 'select_customer', [ value ] );

						} else {
							$( '.customers-list' ).append( '<option value="' + value.ID + '">' + value.NOM + '</option>' );
						}
					});

					$( '.customers-list' ).selectpicker( 'refresh' );
				},
				error			:	function(){
					NexoAPI.Bootbox().alert( '<?php echo addslashes(__('Une erreur s\'est produite durant la récupération des clients', 'nexo'));?>' );
				}
			});
		}

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
			this.bind();
			this.get();
			this.getGroups();
		};
	}

	/**
	 * Display Items on the grid
	 * @params Array
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

					var MainPrice	= NexoAPI.ParseFloat( value.PRIX_DE_VENTE )
					var Discounted	= '';
					var CustomBackground	=	'';
					var ImagePath			=	value.APERCU == '' ? '<?php echo '../modules/nexo/images/default.png';?>'  : value.APERCU;

					if( promo_start.isBefore( v2Checkout.CartDateTime ) ) {
						if( promo_end.isSameOrAfter( v2Checkout.CartDateTime ) ) {
							MainPrice			=	NexoAPI.ParseFloat( value.PRIX_PROMOTIONEL );
							Discounted			=	'<small style="color:#999;"><del>' + NexoAPI.DisplayMoney( NexoAPI.ParseFloat( value.PRIX_DE_VENTE ) ) + '</del></small>';
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

					$( '#filter-list' ).append(
					'<div class="col-lg-2 col-md-3 col-xs-6 shop-items filter-add-product noselect text-center" data-codebar="' + value.CODEBAR + '" style="' + CustomBackground + ';padding:5px; border-right: solid 1px #DEDEDE;border-bottom: solid 1px #DEDEDE;" data-design="' + value.DESIGN.toLowerCase() + '" data-category="' + value.REF_CATEGORIE + '" data-sku="' + value.SKU.toLowerCase() + '">' +
						'<img data-original="<?php echo get_store_upload_url();?>' + ImagePath + '" width="100" style="max-height:64px;" class="img-responsive img-rounded lazy">' +
						'<div class="caption text-center" style="padding:2px;overflow:hidden;"><strong class="item-grid-title">' + design + '</strong><br>' +
							'<span class="align-center">' + NexoAPI.DisplayMoney( MainPrice ) + '</span>' + Discounted +
						'</div>' +
					'</div>' );

					v2Checkout.ItemsCategories	=	_.extend( v2Checkout.ItemsCategories, _.object( [ value.REF_CATEGORIE ], [ value.NOM ] ) );
				}
			});
			
			$( '.filter-add-product' ).each( function(){
				$(this).bind( 'mouseenter', function(){
					$( this ).find( '.marquee_me' ).replaceWith( '<marquee class="marquee_me" behavior="alternate" scrollamount="4" direction="left" style="width:100%;float:left;">' + $( this ).find( '.marquee_me' ).html() + '</marquee>' );
				})
			});
			
			$( '.filter-add-product' ).bind( 'mouseover', function(){
				$(this).bind( 'mouseleave', function(){
					$( this ).find( '.marquee_me' ).replaceWith( '<span class="marquee_me">' + $( this ).find( '.marquee_me' ).html() + '</span>' );
				})
			});
			
			// Bind Categorie @since 2.7.1
			v2Checkout.bindCategoryActions();

			// Add Lazy @since 2.6.1
			$("img.lazy").lazyload({
				failure_limit : 10,
				load : function( e ){
					$( this ).removeAttr( 'width' );
				},
				container : $( '.item-list-container' )
			});

			// Bind Add to Items
			this.bindAddToItems();
		} else {
			NexoAPI.Bootbox().alert( '<?php echo addslashes(__('Vous ne pouvez pas procéder à une vente, car aucun article n\'est disponible pour la vente.', 'nexo' ));?>' );
		}
	};
	
	/**
	 * Empty cart item table
	 *
	**/

	this.emptyCartItemTable		=	function() {
		$( '#cart-table-body' ).find( '[cart-item]' ).remove();
	};

	/**
	 * Fetch Items
	 * Check whether an item is available and add it to the cart items table
	 * @return void
	**/

	this.fetchItem				=	function( codebar, qte_to_add, allow_increase, filter ) {

		var allow_increase			=	typeof allow_increase	==	'undefined' ? true : allow_increase
		var qte_to_add				=	typeof qte_to_add == 'undefined' ? 1 : qte_to_add;
		var filter					=	typeof filter == 'undefined' ? 'sku-barcode' : filter;
		// For Store Feature
		var store_id				=	'<?php echo $store_id == null ? 0 : $store_id;?>';
		

		$.ajax( '<?php echo site_url(array( 'rest', 'nexo', 'item' ));?>/' + codebar + '/' + filter + '?store_id=' + store_id, {
			success				:	function( _item ){
				
				/**
				 * If Item is "On Sale"
				**/
				
				if( _item.length > 0 && _item[0].STATUS == '1' ) {
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
						
						/**
						 * For "Out of Stock" notice to work, item must be physical
						 * and Stock management must be enabled
						**/
						
						if( 
							parseInt( _item[0].QUANTITE_RESTANTE ) - ( comparison_qte ) < 0 
							&& _item[0].TYPE == '1' 
							&& _item[0].STOCK_ENABLED == '1' 
						) {
							NexoAPI.Notify().error(
								'<?php echo addslashes(__('Stock épuisé', 'nexo'));?>',
								'<?php echo addslashes(__('Impossible d\'ajouter ce produit. La quantité restante du produit n\'est pas suffisante.', 'nexo'));?>'
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
						if( parseInt( _item[0].QUANTITE_RESTANTE ) - qte_to_add < 0 ) {
							NexoAPI.Notify().error(
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

					// Build Cart Table Items
					v2Checkout.refreshCart();
					v2Checkout.buildCartItemTable();

				} else {
					NexoAPI.Notify().error( '<?php echo addslashes(__('Impossible d\'ajouter l\'article', 'nexo'));?>', '<?php echo addslashes(__('Impossible de récupérer l\'article, ce dernier est introuvable, indisponible ou le code envoyé est incorrecte.', 'nexo'));?>' );
				}
			},
			dataType			:	'json',
			error				:	function(){
				NexoAPI.Notify().error( '<?php echo addslashes(__('Une erreur s\'est produite', 'nexo'));?>', '<?php echo addslashes(__('Impossible de récupérer les données. L\'article recherché est introuvable.', 'nexo'));?>' );
			}

		});
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
		$.ajax('<?php echo site_url(array( 'rest', 'nexo', 'item' )) . '?store_id=' . $store_id;?>', {
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

		var MainPrice				= 	NexoAPI.ParseFloat( itemObj.PRIX_DE_VENTE )
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
		'<form id="numpad">' +
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
		'</form>';

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
		this.fetchItem( value, 1, true, 'sku-barcode' ); // 'sku-barcode'
	};
	
	/**
	 * Quick Search Items
	 * @params
	**/
	
	this.quickItemSearch			=	function( value ) {
		if( value.length <= 3 ) {
			$( '.filter-add-product' ).each( function(){
				$( this ).show();
			});
		} else {
			$( '.filter-add-product' ).show();
			$( '.filter-add-product' ).each( function(){
				// Filter Item
				if( 
					$( this ).attr( 'data-design' ).search( value.toLowerCase() ) == -1 && 
					$( this ).attr( 'data-category-name' ).search( value.toLowerCase() ) == -1 &&
					$( this ).attr( 'data-codebar' ).search( value.toLowerCase() ) == -1 && // Scan, also item Barcode
					$( this ).attr( 'data-sku' ).search( value.toLowerCase() ) == -1  // Scan, also item SKU
				) {
					$( this ).hide();
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
			$( '.nexo-overlay' ).fadeOut( 300, function(){
				$( this ).remove();
			} );
		}

		this.close				=	function(){
			$( '[data-bb-handler="cancel"]' ).trigger( 'click' );
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

		this.CartToPay			=	( this.CartValueRRR + this.CartVAT );
		<?php if( in_array(strtolower(@$Options[ store_prefix() . 'nexo_currency_iso' ]), $this->config->item('nexo_supported_currency')) ) {
			?>
			this.CartToPayLong		=	numeral( this.CartToPay ).multiply(100).value();
			<?php
		} else {
			?>
			this.CartToPayLong		=	NexoAPI.Format( this.CartToPay, '0.00' );
			<?php
		};?>
		
		$( '#cart-value' ).html( NexoAPI.DisplayMoney( this.CartValue ) );
		$( '#cart-vat' ).html( NexoAPI.DisplayMoney( this.CartVAT ) );
		$( '#cart-discount' ).html( NexoAPI.DisplayMoney( this.CartDiscount ) );
		$( '#cart-topay' ).html( NexoAPI.DisplayMoney( this.CartToPay ) );
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
		this.CartRabais				=	0;
		this.CartTotalItems			=	0;
		this.CartRemise				=	0;
		this.CartPerceivedSum		=	0;
		this.CartCreance			=	0;
		this.CartToPayBack			=	0;

		this.CartRemiseType			=	null;
		this.CartRemiseEnabled		=	false;
		this.CartRemisePercent		=	null;
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

		<?php if( @$Options[ store_prefix() . 'nexo_compact_enabled' ] == 'yes' ):?>

		this.toggleCompactMode();
		
		<?php endif;?>

		// Remove Slash First
		this.paymentWindow.hideSplash();

		this.fixHeight();
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


			<?php if (floatval($order[ 'order' ][0][ 'REMISE' ]) > 0):?>
			this.CartRemiseType			=	'flat';
			this.CartRemise				=	NexoAPI.ParseFloat( <?php echo $order[ 'order' ][0][ 'REMISE' ];?> );
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
			v2Checkout.searchItems( $( '[name="item_sku_barcode"]' ).val() );
			$( '[name="item_sku_barcode"]' ).val('');
			return false;
		});
		
		/**
		 * Filter Item
		**/
		
		$( this.ItemSearchForm ).bind( 'keyup', function(){
			v2Checkout.quickItemSearch( $( '[name="item_sku_barcode"]' ).val() );
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

		<?php if (in_array('stripe', array_keys($this->config->item('nexo_payments_types')))):?>
		this.stripe.run();
		<?php endif;?>
	}
	
	/**
	 * Toggle Compact Mode
	**/
	
	this.CompactMode		=	true;
	
	this.DefaultModeOption	=	false
	
	this.toggleCompactMode		=	function(){
		
		if( this.DefaultModeOption == false ) {
			this.DefaultModeOption	=	{};
			// Default Class
			this.DefaultModeOption.ContentHeader	=	{
				padding				:	$( '.content-header' ).css( 'padding' ),
				height				:	$( '.content-header' ).css( 'height' ),
				h1					:	$( '.content-header > h1' ).html()
			}
			
			this.DefaultModeOption.MainFooter		=	{
				html				:	 $( '.main-footer' ).html()
			}
			
			this.DefaultModeOption.MainHeader		=	{
				min_height			:	$( '.main-header' ).css( 'min-height' ),
				overflow			:	'visible'
			}
			
			this.DefaultModeOption.background			=	$( '.new-wrapper' ).find( '.content' ).css( 'background' );
			this.DefaultModeOption.contentHeight		=	$( '.new-wrapper' ).find( '.content' ).css( 'height' );
		}
		
		if( this.CompactMode ) {
			
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
			
		} else {
			$( '.content-header' ).css({
				'padding'	:	this.DefaultModeOption.ContentHeader.padding,
				'height'	:	this.DefaultModeOption.ContentHeader.height
			});
			
			$( '.content-header' ).append( '<h1>' + this.DefaultModeOption.ContentHeader.h1 + '</h1>' );
			$( '.main-footer' ).show();
			$( '.main-sidebar' ).show();
			$( '.main-footer' ).html( this.DefaultModeOption.MainFooter.html );
			$( '.main-header' ).css({
				'min-height' : this.DefaultModeOption.MainHeader.min_header,
				'overflow': this.DefaultModeOption.MainHeader.overflow
			});		
			
			$( '.new-wrapper' ).addClass( 'content-wrapper' ).removeClass( 'new-wrapper' );
			$( '.content-wrapper').find( '.content' ).css( 'background', 'inherit' );
			$( '.content-wrapper' ).find( '.content' ).css( 'height', 'auto' );
			
			this.CompactMode	=	true;
		}	
		
		this.fixHeight();			
	}

	/**
	 * Tools
	**/

	this.Tools				=	new function(){
		// this.isFloat
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

</script>
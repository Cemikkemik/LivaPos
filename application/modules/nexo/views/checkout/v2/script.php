<?php
$this->load->config('nexo');
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
	this.ItemSettings			=	'.item-list-settings';
	this.ItemSearchForm			=	'#search-item-form';
	this.CartPayButton			=	'#cart-pay-button';
	this.CartCancelButton		=	'#cart-return-to-order';

	this.CartVATEnabled			=	<?php echo @$Options[ 'nexo_enable_vat' ] == 'oui' ? 'true' : 'false';?>;
	this.CartVATPercent			=	<?php echo in_array(@$Options[ 'nexo_vat_percent' ], array( null, '' )) ? 0 : @$Options[ 'nexo_vat_percent' ];?>

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
		var headerHeight		=	$( '.main-header' ).height();
		var contentHeader		=	$( '.content-header' ).outerHeight();
		var contentPadding		=	23;
		var windowHeight		=	window.innerHeight < 500 ? 500 : window.innerHeight;
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
	 * Build Items Categories
	 * @return void
	**/

	this.buildItemsCategories	=	function( wrapper ) {
		if( $( '.cart-options' ).hasClass( 'in' ) ) {
			$( '.categories_dom_wrapper' ).html('');
		} else {
			var categories_dom	=
			'<div class="btn-group btn-group-lg categories_dom" data-toggle="buttons">';
			_.each( this.ItemsCategories, function( value, id ) {

				var	index	=	_.indexOf( v2Checkout.ActiveCategories, id ) != -1 ? 'active' : '';

				categories_dom +=
				'<label class="btn btn-primary ' + index + '">' +
					'<input type="checkbox" class="categories_id" autocomplete="off" value="' + id + '"> ' + value +
				'</label>' ;
			});

			$( wrapper ).append( categories_dom );
			this.bindFilterCategories();
		}
	}

	/**
	 * Bind Filter Categories
	 *
	**/

	this.bindFilterCategories		=	function(){
		$( '.categories_dom.btn-group' ).find( 'input' ).bind( 'change', function(){
			setTimeout( function(){
				var categories		=	new Array;

				$( '.categories_dom.btn-group > .btn.active' ).each( function(){
					categories.push( $( this ).find( 'input' ).val() );
				});

				v2Checkout.ActiveCategories	=	categories;
				v2Checkout.filterItems( categories );
			}, 100 );
		});
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

		<?php if (@$Options[ 'nexo_enable_numpad' ] != 'non'):?>
		// Bind Num padd
		$( '[name="shop_item_quantity"]' ).bind( 'click', function(){
			v2Checkout.showNumPad( $( this ), '<?php echo addslashes(__('Définir la quantité à  ajouter', 'nexo'));?>' );
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

	this.bindAddDiscount		=	function(){
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

		NexoAPI.Bootbox().confirm( DiscountDom, function( action ) {
			if( action == true ) {
				var value	=	$( '[name="discount_value"]' ).val();

				if( value  == '' || value == '0' ) {
					NexoAPI.Bootbox().alert( '<?php echo addslashes(__('Vous devez définir un pourcentage ou une somme.', 'nexo'));?>' );
					return false;
				}

				// Percentage can't exceed 100%
				if( v2Checkout.CartRemiseType == 'percentage' && parseInt( value ) > 100 ) {
					value = 100;
				} else if( v2Checkout.CartRemiseType == 'flat' && parseInt( value ) > v2Checkout.CartValue ) {
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

		$( '.percentage_discount' ).bind( 'click', function(){
			if( ! $( this ).hasClass( 'active' ) ) {
				if( $( '.flat_discount' ).hasClass( 'active' ) ) {
					$( '.flat_discount' ).removeClass( 'active' );
				}

				$( this ).addClass( 'active' );

				// Proceed a quick check on the percentage value
				$( '[name="discount_value"]' ).focus();

				v2Checkout.CartRemiseType	=	'percentage';

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

				v2Checkout.CartRemiseType	=	'flat';

				$( '.discount_type' ).html( '<?php echo addslashes(__(' : <span class="label label-info">à prix fixe</span>', 'nexo'));?>' );
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
			} else if( v2Checkout.CartRemiseType == 'flat' && parseInt( $( '[name="discount_value"]' ).val() ) > v2Checkout.CartValue ) {
				// flat discount cannot exceed cart value
				$( this ).val( v2Checkout.CartValue );
				NexoAPI.Notify().info( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'La remise fixe ne peut pas excéder la valeur actuelle du panier. Le montant de la remise à été réduite à la valeur du panier.', 'nexo' );?>' );
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

				var MainPrice			= 	parseInt( value.PRIX_DE_VENTE )
				var Discounted			= 	'';
				var CustomBackground	=	'';
					value.PROMO_ENABLED	=	false;

				if( promo_start.isBefore( v2Checkout.CartDateTime ) ) {
					if( promo_end.isSameOrAfter( v2Checkout.CartDateTime ) ) {
						value.PROMO_ENABLED	=	true;
						MainPrice			=	parseInt( value.PRIX_PROMOTIONEL );
						Discounted			=	'<small><del>' + NexoAPI.DisplayMoney( parseInt( value.PRIX_DE_VENTE ) ) + '</del></small>';
						CustomBackground	=	'background:<?php echo $this->config->item('discounted_item_background');?>';
					}
				}

				// <span class="btn btn-primary btn-xs item-reduce hidden-sm hidden-xs">-</span> <input type="number" style="width:40px;border-radius:5px;border:solid 1px #CCC;" maxlength="3"/> <span class="btn btn-primary btn-xs   hidden-sm hidden-xs">+</span>

				$( '#cart-table-body' ).find( 'table' ).append(
					'<tr cart-item data-line-weight="' + ( MainPrice * parseInt( value.QTE_ADDED ) ) + '" data-item-barcode="' + value.CODEBAR + '">' +
						'<td width="210" class="text-left" style="line-height:35px;"><a href="<?php echo site_url('dashboard/nexo/produits/lists/edit');?>/' + value.ID + '">' + value.DESIGN + '</a></td>' +
						'<td width="130" class="text-center"  style="line-height:35px;">' + NexoAPI.DisplayMoney( MainPrice ) + ' ' + Discounted + '</td>' +
						'<td width="145" class="text-center">' +
							'<div class="input-group">' +
								'<span class="input-group-btn">' +
									'<button class="btn btn-default item-reduce">-</button>' +
								'</span>'+
								'<input type="number" name="shop_item_quantity" value="' + value.QTE_ADDED + '" class="form-control" aria-describedby="sizing-addon3">' +
								'<span class="input-group-btn">' +
									'<button class="btn btn-default item-add">+</button>' +
								'</span>'+
							'</div>' +
						'</td>' +
						'<td width="115" class="text-right" style="line-height:35px;">' + NexoAPI.DisplayMoney( MainPrice * parseInt( value.QTE_ADDED ) ) + '</td>' +
					'</tr>'
				);
				_tempCartValue	+=	( MainPrice * parseInt( value.QTE_ADDED ) );

				// Just to count all products
				v2Checkout.CartTotalItems	+=	parseInt( value.QTE_ADDED );
			});

			this.CartValue	=	_tempCartValue;

		} else {
			$( this.CartTableBody ).find( 'tbody' ).html( '<tr id="cart-table-notice"><td colspan="4"><?php _e('Veuillez ajouter un produit...', 'nexo');?></td></tr>' );
		}

		this.bindAddReduceActions();
		this.bindAddByInput();
		this.refreshCartValues();
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
					this.CartRemisePercent	=	parseInt( value );
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
					this.CartRemise 			=	parseInt( value );
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
					this.CartRistourne	=	( parseInt( this.CartRistournePercent ) * this.CartValue ) / 100;
				}

				$( '.cart-discount-notice-area' ).append( '<span style="cursor: pointer; margin:0px 2px;" class="animated bounceIn btn expandable btn-info btn-xs cart-ristourne"><i class="fa fa-remove"></i> <?php echo addslashes(__('Ristourne : ', 'nexo'));?>' + this.CartRistournePercent + '%</span>' );

			} else if( this.CartRistourneType == 'amount' ) {
				if( this.CartRistourneAmount != '' ) {
					this.CartRistourne	=	parseInt( this.CartRistourneAmount );
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
					this.CartGroupDiscount		=	( parseInt( this.CartGroupDiscountPercent ) * this.CartValue ) / 100;

					$( '.cart-discount-notice-area' ).append( '<p style="cursor: pointer; margin:0px 2px;" class="animated bounceIn btn btn-warning expandable btn-xs cart-group-discount"><i class="fa fa-remove"></i> <?php echo addslashes(__('Remise de groupe : ', 'nexo'));?>' + this.CartGroupDiscountPercent + '%</p>' );
				}
			} else if( this.CartGroupDiscountType == 'amount' ) {
				if( this.CartGroupDiscountAmount != '' ) {
					this.CartGroupDiscount		=	parseInt( this.CartGroupDiscountAmount )	;

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
			this.CartVAT		=	( parseInt( this.CartVATPercent ) * this.CartValueRRR ) / 100;
		}
	};

	/**
	 * Cancel an order and return to order list
	**/

	this.cartCancel				=	function(){
		NexoAPI.Bootbox().confirm( '<?php echo _s('Souhaitez-vous annuler la récente modification et revenir à la liste des commandes ?', 'nexo');?>', function( action ) {
			if( action == true ) {
				v2Checkout.resetCart();
				document.location	=	'<?php echo site_url(array( 'dashboard', 'nexo', 'commandes', 'lists' ));?>';
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
	 * @params payment mean
	**/

	this.cartSubmitOrder			=	function( payment_means ){
		var order_items					=	new Array;

		_.each( this.CartItems, function( value, key ){
			order_items.push([
				value.ID,
				value.QTE_ADDED,
				value.CODEBAR,
				value.PROMO_ENABLED ? value.PRIX_PROMOTIONEL : value.PRIX_DE_VENTE ,
				value.QUANTITE_VENDU,
				value.QUANTITE_RESTANTE
			]);
		});

		var order_details					=	new Object;
			order_details.TOTAL				=	this.CartToPay;
			order_details.REMISE			=	this.CartRemise;
			order_details.RABAIS			=	this.CartRabais;
			order_details.RISTOURNE			=	this.CartRistourne;
			order_details.TVA				=	this.CartVAT;
			order_details.REF_CLIENT		=	this.CartCustomerID == null ? this.customers.DefaultCustomerID : this.CartCustomerID;
			order_details.PAYMENT_TYPE		=	this.CartPaymentType;
			order_details.GROUP_DISCOUNT	=	this.CartGroupDiscount;
			order_details.DATE_CREATION		=	this.CartDateTime.format( 'YYYY-MM-DD HH:mm:ss' )
			order_details.ITEMS				=	order_items;
			order_details.DEFAULT_CUSTOMER	=	this.DefaultCustomerID;
			order_details.DISCOUNT_TYPE		=	'<?php echo @$Options[ 'discount_type' ];?>';
			order_details.HMB_DISCOUNT		=	'<?php echo @$Options[ 'how_many_before_discount' ];?>';

		if( payment_means == 'cash' ) {

			order_details.SOMME_PERCU		=	this.CartPerceivedSum;

		} else if( payment_means == 'cheque' || payment_means == 'bank' ) {

			order_details.SOMME_PERCU		=	this.CartToPay;

		} else if( payment_means == 'stripe' ) {
			if( this.CartAllowStripeSubmitOrder == true ) {

				order_details.SOMME_PERCU		=	this.CartToPay;

			} else {
				NexoAPI.Notify().info( '<?php echo _s('Attention', 'nexo');?>', '<?php echo _s('La carte de crédit doit d\'abord être facturée avant de valider la commande.', 'nexo');?>' );
				return false;
			}
		} else {
			NexoAPI.Bootbox().alert( '<?php echo _s('Une erreur s\'est produite', 'nexo');?>', '<?php echo _s('Impossible de reconnaitre le moyen de paiement.', 'nexo');?>' );
			return false;
		}

		<?php if (isset($order[ 'order' ])):?>
		var ProcessURL	=	"<?php echo site_url(array( 'rest', 'nexo', 'order', User::id(), $order[ 'order' ][0][ 'ID' ] ));?>";
		var ProcessType	=	'PUT';
		<?php else :?>
		var ProcessURL	=	"<?php echo site_url(array( 'rest', 'nexo', 'order', User::id() ));?>";
		var ProcessType	=	'POST';
		<?php endif;?>

		$.ajax( ProcessURL, {
			dataType		:	'json',
			type			:	ProcessType,
			data			:	order_details,
			beforeSend		: function(){
				v2Checkout.paymentWindow.showSplash();
				NexoAPI.Notify().info( '<?php echo _s('Veuillez patienter', 'nexo');?>', '<?php echo _s('Paiement en cours...', 'nexo');?>' );
			},
			success			:	function( returned ) {
				v2Checkout.paymentWindow.hideSplash();
				v2Checkout.paymentWindow.close();

				if( _.isObject( returned ) ) {
					if( returned.order_type == 'nexo_order_comptant' ) {
						<?php if (@$Options[ 'nexo_enable_autoprint' ] == 'yes'):?>

						NexoAPI.Notify().success( '<?php echo _s('Effectué', 'nexo');?>', '<?php echo _s('La commande est en cours d\'impression.', 'nexo');?>' );
						$( 'body' ).append( '<iframe style="display:none;" id="CurrentReceipt" name="CurrentReceipt" src="<?php echo site_url(array( 'dashboard', 'nexo', 'print', 'order_receipt' ));?>/' + returned.order_id + '"></iframe>' );

						window.frames["CurrentReceipt"].focus();
						window.frames["CurrentReceipt"].print();

						setTimeout( function(){
							$( '#CurrentReceipt' ).remove();
						}, 5000 );

						<?php else:?>
						NexoAPI.Notify().success( '<?php echo _s('Effectué', 'nexo');?>', '<?php echo _s('La commande a été enregistrée.', 'nexo');?>' );
						<?php endif;?>

						<?php if( @$Options[ 'nexo_enable_smsinvoice' ] == 'yes' ):?>
 						/**
						 * Send SMS
						**/
						// Do Action when order is complete and submited
						NexoAPI.events.doAction( 'is_cash_order', [ v2Checkout, returned ] );
						<?php endif;?>
					} else {
						<?php if (@$Options[ 'nexo_enable_autoprint' ] == 'yes'):?>
						NexoAPI.Notify().info( '<?php echo _s('Effectué', 'nexo');?>', '<?php echo _s('La commande a été enregistrée, mais ne peut pas être imprimée tant qu\'elle n\'est pas complète.', 'nexo');?>' );
						<?php else:?>
						NexoAPI.Notify().success( '<?php echo _s('Effectué', 'nexo');?>', '<?php echo _s('La commande a été enregistrée', 'nexo');?>' );
						<?php endif;?>
					}
				}

				<?php if (! isset($order)):?>
				v2Checkout.resetCart();
				<?php else:?>
				// If order is not more editable
				if( returned.order_type != 'nexo_order_devis' ) {
					v2Checkout.resetCart();
					document.location	=	'<?php echo site_url( array( 'dashboard', 'nexo', 'commandes', 'lists' ) );?>';
				}
				<?php endif;?>
			}
		});
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
			$.ajax( '<?php echo site_url(array( 'nexo', 'customer' ));?>', {
				dataType		:	'json',
				type			:	'POST',
				data			:	_.object( 
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
				$.ajax( '<?php echo site_url(array( 'nexo', 'customer' ));?>/' + customer_id, {
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
					if( parseInt( v2Checkout.CartRistourneCustomerID ) == parseInt( value.ID ) ) {
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
			$.ajax( '<?php echo site_url(array( 'nexo', 'customer' ));?>', {
				dataType		:	'json',
				success			:	function( customers ){

					$( '.customers-list' ).selectpicker('destroy');

					// Empty list first
					$( '.customers-list' ).html('');

					_.each( customers, function( value, key ){
						if( parseInt( v2Checkout.CartCustomerID ) == parseInt( value.ID ) ) {
							$( '.customers-list' ).append( '<option value="' + value.ID + '" selected="selected">' + value.NOM + '</option>' );
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
			$.ajax( '<?php echo site_url(array( 'nexo', 'customers_groups' ));?>', {
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
				if( parseInt( value.QUANTITE_RESTANTE ) > 0 ) {

					var promo_start	= moment( value.SPECIAL_PRICE_START_DATE );
					var promo_end	= moment( value.SPECIAL_PRICE_END_DATE );

					var MainPrice	= parseInt( value.PRIX_DE_VENTE )
					var Discounted	= '';
					var CustomBackground	=	'';
					var ImagePath			=	value.APERCU == '' ? '<?php echo '../modules/nexo/images/default.png';?>'  : value.APERCU;

					if( promo_start.isBefore( v2Checkout.CartDateTime ) ) {
						if( promo_end.isSameOrAfter( v2Checkout.CartDateTime ) ) {
							MainPrice			=	parseInt( value.PRIX_PROMOTIONEL );
							Discounted			=	'<small style="color:#999;"><del>' + NexoAPI.DisplayMoney( parseInt( value.PRIX_DE_VENTE ) ) + '</del></small>';
							// CustomBackground	=	'background:<?php echo $this->config->item('discounted_item_background');?>';
						}
					}
					
					// style="max-height:100px;"
					
					$( '#filter-list' ).append(
					'<div class="col-lg-3 col-md-3 col-xs-6 shop-items filter-add-product noselect text-center" data-codebar="' + value.CODEBAR + '" style="' + CustomBackground + ';padding:5px; border-right: solid 1px #DEDEDE;border-bottom: solid 1px #DEDEDE;" data-category="' + value.REF_CATEGORIE + '">' +
						'<img data-original="<?php echo upload_url();?>' + ImagePath + '" width="126" style="max-height:100px;" class="img-responsive img-rounded lazy">' +
						'<div class="caption text-center" style="padding:2px;"><strong class="item-grid-title">' + value.DESIGN + '</strong><br>' +
							'<span class="align-center">' + NexoAPI.DisplayMoney( MainPrice ) + '</span>' + Discounted +
						'</div>' +
					'</div>' );

					v2Checkout.ItemsCategories	=	_.extend( v2Checkout.ItemsCategories, _.object( [ value.REF_CATEGORIE ], [ value.NOM ] ) );
				}
			});
			
			// Add Lazy @since 2.6.1
			$("img.lazy").lazyload({
				failure_limit : 10,
				load : function( e ){
					$( this ).removeAttr( 'width' );
				}
			});

			// Build Category for the filter
			// this.buildItemsCategories();

			// Bind Add to Items
			this.bindAddToItems();
		} else {
			NexoAPI.Bootbox().alert( '<?php echo addslashes(__('Vous ne pouvez pas procéder à une vente, car aucun article n\'est disponible pour la vente.'));?>' );
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

		allow_increase			=	typeof allow_increase	==	'undefined' ? true : allow_increase
		qte_to_add				=	typeof qte_to_add == 'undefined' ? 1 : qte_to_add;
		filter					=	typeof filter == 'undefined' ? 'CODEBAR' : filter;

		$.ajax( '<?php echo site_url(array( 'nexo', 'item' ));?>/' + codebar + '/' + filter, {
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
									NexoAPI.Bootbox().confirm( '<?php echo addslashes(__('Défininr "0" comme quantité, retirera le produit du panier. Voulez-vous continuer ?'));?>', function( response ) {
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
								'<?php echo addslashes(__('Stock épuisé'));?>',
								'<?php echo addslashes(__('Impossible d\'ajouter ce produit, car son stock est épuisé.', 'nexo'));?>'
							);
						} else {
							v2Checkout.CartItems.unshift( _.extend( _item[0], _.object( [ 'QTE_ADDED' ], [ qte_to_add ] ) ) );
						}
					}

					// Build Cart Table Items
					v2Checkout.refreshCart();
					v2Checkout.buildCartItemTable();

				} else {
					NexoAPI.Notify().error( '<?php echo addslashes(__('Erreur sur le code/article'));?>', '<?php echo addslashes(__('Impossible de récupérer l\'article, ce dernier est introuvable ou le code envoyé est incorrecte.'));?>' );
				}
			},
			dataType			:	'json',
			error				:	function(){
				NexoAPI.Notify().error( '<?php echo addslashes(__('Une erreur s\'est produite'));?>', '<?php echo addslashes(__('Impossible de récupérer les données. L\'article recherché est introuvable.'));?>' );
			}

		});
	};
	
	/**
	 * Get Items
	**/

	this.getItems				=	function( beforeCallback, afterCallback){
		$.ajax('<?php echo site_url(array( 'nexo', 'item' ));?>', {
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
			'<input type="number" class="form-control input-lg" name="numpad_field"/>' +
		'</div>' : '';

		var NumPad				=
		'<form id="numpad">' +
			'<h4 class="text-center">' + ( text ? text : '' ) + '</h4><br>' +
			input_field	+
			'<div class="row">' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad7" value="<?php echo addslashes(__('7', 'nexo'));?>"/>' +
				'</div>' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad8" value="<?php echo addslashes(__('8', 'nexo'));?>"/>' +
				'</div>' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad9" value="<?php echo addslashes(__('9', 'nexo'));?>"/>' +
				'</div>' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad0" value="<?php echo addslashes(__('0', 'nexo'));?>"/>' +
				'</div>' +
				'<div class="col-lg-4 col-md-4 col-xs-4">' +
					'<input type="button" class="btn btn-warning btn-block btn-lg numpad numpaddel" value="<?php echo addslashes(__('Retour arrière', 'nexo'));?>"/>' +
				'</div>' +
			'</div>' +
			'<br>'+
			'<div class="row">' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad4" value="<?php echo addslashes(__('4', 'nexo'));?>"/>' +
				'</div>' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad5" value="<?php echo addslashes(__('5', 'nexo'));?>"/>' +
				'</div>' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad6" value="<?php echo addslashes(__('6', 'nexo'));?>"/>' +
				'</div>' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad numpadplus" value="<?php echo addslashes(__('+', 'nexo'));?>"/>' +
				'</div>' +
				'<div class="col-lg-4 col-md-4 col-xs-4">' +
					'<input type="button" class="btn btn-danger btn-block btn-lg numpad numpadclear" value="<?php echo addslashes(__('Vider', 'nexo'));?>"/>' +
				'</div>' +
			'</div>' +
			'<br>'+
			'<div class="row">' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad1" value="<?php echo addslashes(__('1', 'nexo'));?>"/>' +
				'</div>' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad2" value="<?php echo addslashes(__('2', 'nexo'));?>"/>' +
				'</div>' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad numpad3" value="<?php echo addslashes(__('3', 'nexo'));?>"/>' +
				'</div>' +
				'<div class="col-lg-2 col-md-2 col-xs-2">' +
					'<input type="button" class="btn btn-default btn-block btn-lg numpad numpadminus" value="<?php echo addslashes(__('-', 'nexo'));?>"/>' +
				'</div>' +
			'</div>' +
		'</form>'

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
			var numpad_value	=	parseInt( $( field ).val() );
			$( field ).val( ++numpad_value );
		});

		$( '.numpadminus' ).bind( 'click', function(){
			var numpad_value	=	parseInt( $( field ).val() );
			$( field ).val( --numpad_value );
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
		this.fetchItem( value, 1, true, 'sku-barcode' );
	};

	/**
	 * Pay,
	 * Proceed payment
	**/

	this.pay							=	function(){
		if( this.isCartEmpty() ) {
			NexoAPI.Notify().warning( '<?php echo    _s('Impossible de continuer', 'nexo');?>', '<?php echo _s('Vous ne pouvez pas valider une commande sans article. Veuillez ajouter au moins un article.', 'nexo');?>' );
			return false;
		}

		NexoAPI.Bootbox().dialog({
			message	:	'<div id="pay-wrapper"></div>',
			title	:	'<?php echo _s('Paiement de la commande', 'nexo');?>',
			buttons :	{
				success: {
					label			: '<?php echo _s('Valider & Payer', 'nexo');?>',
					className		: "btn-success",
					callback		: function() {
						// Submiting order
						NexoAPI.events.doAction( 'submit_order' );
						return v2Checkout.cartSubmitOrder( v2Checkout.CartPaymentType );
					}
				},
				cancel: {
					label			: '<?php echo _s('Annuler', 'nexo');?>',
					className		: "btn-default",
					callback		: function() {
						return true;
					}
				}
			}
		});

		// Old Code Here
		var dom		=	'<div class="row pay-box-container">' +
	 '<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 bootstrap-tab-container">' +
		 '<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 bootstrap-tab-menu">' +
			 '<div class="list-group">' +
			 	<?php foreach ($this->config->item('nexo_payment_types') as $payment_namespace => $payment_name):?>
					<?php if ($payment_namespace != 'stripe' || $payment_namespace == 'stripe' && @$Options[ 'nexo_enable_stripe' ] != 'no'):?>
 				'<a ' +
					'data-tab="<?php echo $payment_namespace;?>" ' +
					'data-payment-namespace="<?php echo $payment_namespace;?>" ' +
					'href="#" ' +
					'class="list-group-item text-center"' +
					'style="border-right:0px">' +
 					'<?php echo addslashes($payment_name);?>' +
 				'</a>' +

				//active
					<?php endif;?>
			  	<?php endforeach;?>
				'</div>' +
 			'</div>' +
 			'<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 bootstrap-tab">' +
 				<?php foreach ($this->config->item('nexo_payment_types') as $payment_namespace => $payment_name):?>
					<?php if ($payment_namespace != 'stripe' || $payment_namespace == 'stripe' && @$Options[ 'nexo_enable_stripe' ] != 'no'):?>
				'<!-- flight section -->' +
                '<div class="bootstrap-tab-content" id="<?php echo $payment_namespace;?>">' +
					'<div class="content-for-<?php echo $payment_namespace;?>">' +
					'</div>'+
                '</div>' +
				// active
				<?php endif;?>
			  	<?php endforeach;?>
   			'</div>' +
  		'</div>' +
		'<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">' +
			'<h3 class="text-center"><?php echo _s( 'Détails du panier', 'nexo' );?></h3>' +
			'<div class="checkout-cart-details-wrapper">' +
			'</div>' +
		'</div>' +
  '</div>';

		$( '#pay-wrapper' ).html( dom );

		// Footer Filter
		$( '.modal-footer' ).prepend( '<div class="pay_box_footer pull-left">' + NexoAPI.events.applyFilters( 'pay_box_footer', '' ) + '</div>' );

		// Width Settings
		$( '#pay-wrapper' ).closest( '.modal-dialog' ).css({
			'width'		:	'90%',
		});

		// Height Settings

		var	windowHeight		=	window.innerHeight < 500 ? 500 : window.innerHeight;

		$( '.bootstrap-tab' ).css({
			'height'	:	( windowHeight - ( 90 + Math.abs( $( '.modal-footer' ).height() - 5 ) + Math.abs( $( '.modal-header' ).height() ) ) ) + 'px',
			'overflow-y'	:	'scroll',
			'overflow-x'	:	'hidden'
		});

	  $( '.modal-body' ).css( 'padding', '0px 15px' );

	  $("div.bootstrap-tab-menu>div.list-group>a").click(function(e) {
			// Change tab color according to current Theme
				/** var color		=	$( '.navbar' ).css( 'background-color' );
				$( this ).css( 'background-color', color );**/
			e.preventDefault();
			$(this).siblings('a.active').removeClass("active"); /**.css( 'background-color', 'inherit' ); **/
			$(this).addClass("active");
			var index = $(this).attr( 'data-tab' );
			$("div.bootstrap-tab>div.bootstrap-tab-content").removeClass("active");
			$("div.bootstrap-tab>div#"+ index).addClass("active");
		});

		// Get Cart Details
		$( '.checkout-cart-details-wrapper' ).append( $( '#cart-details' )[0].outerHTML );

		$( '.checkout-cart-details-wrapper table' ).addClass( 'table-striped table-bordered' );

		$( '.checkout-cart-details-wrapper table tr' ).each( function(){

			$( this ).removeClass( 'active danger success' );

			$( this ).find( 'td' ).removeAttr( 'colspan' );
			if( $( this ).find( 'td' ).length > 3 ) {
				$( this ).find( 'td' ).slice( 0, 2 ).remove();
			} else {
				$( this ).find( 'td' ).slice( 0, 1 ).remove();
			}

			$( this ).find( 'td' ).eq(0).removeClass( 'text-right' ).addClass( 'text-left' );
		});

		// end of Cart details

		/**
		 * Cash Payment
		**/

		var cash_dom		=	'<h3 class="text-center" style="margin-top:5px;"><?php echo _s( 'Paiement Comptant : ', 'nexo' );?>' + NexoAPI.DisplayMoney( v2Checkout.CartToPay ) + '</h3>' +
		'<div class="input-group input-group-lg"> <span class="input-group-addon" id="sizing-addon1"><?php echo _s('Somme perçu', 'nexo');?></span> <input type="number" class="form-control" placeholder="<?php echo _s('Veuillez spécifier la somme perçue...', 'nexo');?>" aria-describedby="sizing-addon1" name="perceived_sum"> </div>' +

		'<br><table class="table table-bordered table-striped">' +
			'<tr>'+
				'<td width="220"><?php echo _s('Somme a rembourser', 'nexo');?></td><td class="text-right to_payback"></td>' +
			'</tr>'+
			'<tr>' +
				'<td width="220"><?php echo _s('Créance', 'nexo');?></td><td class="text-right cart_creance"></td>' +
			'</tr>' +
		'</table>' +
		'<div id="cash_payment_numpad_wrapper"></div><br><div id="cash_payment_numpad_wrapper"></div>';

		$( '.content-for-cash' ).append( cash_dom );

		v2Checkout.showNumPad( '[name="perceived_sum"]', '<?php _s('Veuillez définir le montant perçu', 'nexo');?>', '#cash_payment_numpad_wrapper', true );

		$( '.numpad' ).bind( 'click', function(){
			v2Checkout.payCashCalculator();
		});

		$( '[name="perceived_sum"]' ).bind( 'keyup', function(){
			v2Checkout.payCashCalculator();
		});

		$( '[name="perceived_sum"]' ).bind( 'change', function(){
			v2Checkout.payCashCalculator();
		});

		/**
		 * Bank Transfer
		**/

		var bank_dom		=	'<h3 class="text-center" style="margin-top:5px;"><?php echo _s( 'Transfert Bancaire : ', 'nexo' );?>' + NexoAPI.DisplayMoney( v2Checkout.CartToPay ) + '</h3>' +
		'<?php echo addslashes(tendoo_info(__('Un paiement par transfert bancaire paie entièrement la commande. Assurez-vous que transfert banciare est émis pour le compte de votre point de vente à l\'occassion de la présente vente.', 'nexo')));?>';

		$( '.content-for-bank' ).append( bank_dom );

		/**
		 * Stripe
		**/

		var stripe_dom		=	'<h3 class="text-center" style="margin-top:5px;"><?php echo _s( 'Paiement par Stripe : ', 'nexo' );?>' + NexoAPI.DisplayMoney( v2Checkout.CartToPay ) + '</h3>' +
		'<?php echo addslashes(tendoo_info(__('Activer le paiement avec Stripe. Le paiement sera intégrale. La carte de crédit sera facturée. Si l\'opération de paiement réussie, la commande sera validée et enregistrée.', 'nexo')));?>' +

		<?php if ($this->config->item('nexo_test_mode')):?>
		'<?php echo addslashes(tendoo_info(sprintf(__('Pour tester stripe, vous pouvez utiliser des numéros de carte de crédit factices. Par exemple vous pouvez utiliser : <strong>4242 4242 4242 4242</strong>.<br>Retrouvez toutes les listes des cartes utilisables pour tester sur <a href="%s">Stripe</a>.', 'nexo'), 'https://stripe.com/docs/testing')));?>' +
		<?php endif;?>

		'<button class="btn btn-primary" id="pay-with-stripe"><?php echo _s('Facturer la carte de crédit Stripe', 'nexo');?></button>';

		$( '.content-for-stripe' ).append( stripe_dom );

		$('#pay-with-stripe').on('click', function(e) {
			// Open Checkout with further options:
			v2Checkout.stripe.handler.open({
				name: '<?php echo @$Options[ 'site_name' ];?>',
				description: v2Checkout.stripe.getDescription() ,
				amount: v2Checkout.CartToPayLong ,
				currency: '<?php echo @$Options[ 'nexo_currency_iso' ];?>'
			});
			e.preventDefault();
		});

		// trigger Pay Box Loaded Action
		NexoAPI.events.doAction( 'pay_box_loaded' );

		// Event Set Payment Means
		$( '[data-payment-namespace]' ).each( function(){
			$( this ).bind( 'click', function(){
				v2Checkout.CartPaymentType	=	$( this ).data( 'payment-namespace' );
			});
		});

		// Default Payment Mean to
		$( '[data-payment-namespace]' ).eq(0).trigger( 'click' );
		$( '[name="perceived_sum"]' ).trigger( 'change' );
	};

	/**
	 * Pay Calculator
	 * Calculate amount when Cash payment mean is selected
	**/

	this.payCashCalculator				=	function(){

		this.CartPerceivedSum		=	Math.abs( parseInt( $( '[name="perceived_sum"]' ).val() ) );
		this.CartToPayBack 			=	this.CartPerceivedSum - this.CartToPay < 0 ? 0 : this.CartPerceivedSum - this.CartToPay;


		if( this.CartToPayBack > 0 ) {
			$( '.to_payback' ).html( NexoAPI.DisplayMoney( this.CartToPayBack ) );
		} else {
			$( '.to_payback' ).html( NexoAPI.DisplayMoney( 0 ) );
		}

		if( ( this.CartPerceivedSum - this.CartToPay ) < 0 )  {

			this.CartCreance			=	this.CartPerceivedSum - v2Checkout.CartToPay;
			$( '.cart_creance' ).html( NexoAPI.DisplayMoney(  Math.abs( this.CartCreance ) ) );

		} else {

			$( '.cart_creance' ).html( NexoAPI.DisplayMoney( 0 ) );

		}
	}

	/**
	 * Stripe
	**/

	this.stripe							=	new function(){

		this.getDescription	=	function(){
			return	v2Checkout.CartTotalItems + '<?php echo _s(': produit(s) acheté(s)', 'nexo');?>';
		}

		this.run			=	function(){
			<?php if (@$Options[ 'nexo_enable_stripe' ] != 'no'):?>
			if( typeof StripeCheckout != 'undefined' ) {
				<?php if (empty($Options[ 'nexo_stripe_publishable_key' ])):?>
				NexoAPI.Notify().warning( '<?php echo _s('Une erreur s\'est produite', 'nexo');?>', '<?php echo _s('Vous n\'avez pas définit la "publishable key" dans les réglages stripe. Le paiement par ce moyen ne fonctionnera pas.', 'nexo');?>' );
				<?php endif;?>
				this.handler = StripeCheckout.configure({
					key: '<?php echo @$Options[ 'nexo_stripe_publishable_key' ];?>',
					image: '<?php echo img_url('nexo') . '/nexopos-logo.png';?>',
					locale: 'auto',
					token: function(token) {
						v2Checkout.stripe.proceedPayment( token );
					}
					<?php if ($this->config->item('nexo_test_mode') == false):?>
					,zipCode : true,
					billingAddress : true
					<?php endif;?>
				});
			} else {
				NexoAPI.Notify().warning( '<?php echo _s('Une erreur s\'est produite', 'nexo');?>', '<?php echo _s('Stripe ne s\'est pas chargé correctement. Le paiement via ce dernier ne fonctionnera pas. Veuillez rafraichir la page.', 'nexo');?>' );
			}
			<?php endif;?>
		}

		/**
		 * Proceed Payment
		 * @params object
		 * @return void
		**/

		this.proceedPayment		=	function( token ) {
			token				=	_.extend( token, {
				'apiKey' 		: 	'<?php echo @$Options[ 'nexo_stripe_secret_key' ];?>' ,
				'currency'		:	'<?php echo @$Options[ 'nexo_currency_iso' ];?>' ,
				'amount'		:	v2Checkout.CartToPayLong,
				'description'	:	this.getDescription()
			});

			$.ajax( '<?php echo site_url(array( 'rest', 'nexo', 'stripe' ));?>', {
				beforeSend : 	function(){
					v2Checkout.paymentWindow.showSplash();
					NexoAPI.Notify().success( '<?php echo _s('Veuillez patienter', 'nexo');?>', '<?php echo _s('Paiement en cours...', 'nexo');?>' );
				},
				type		:	'POST',
				dataType	:	"json",
				data		:	token,
				success		: 	function( data ) {
					if( data.status == 'payment_success' ) {
						v2Checkout.CartAllowStripeSubmitOrder	=	true;
						$( '[data-bb-handler="success"]' ).trigger( 'click' );
					}
				},
				error		:	function( data ){
					data			=	$.parseJSON( data.responseText );

					if( typeof data.error != 'undefined' ) {
						var message		=	data.error.message;
					} else if( typeof data.httpBody != 'undefined' ) {
						var message		=	data.jsonBody.error.message;
					} else {
						var message		=	'N/A';
					}

					v2Checkout.paymentWindow.hideSplash();
					NexoAPI.Notify().warning( '<?php echo _s('Une erreur s\'est produite', 'nexo');?>', '<?php echo _s('Le paiement n\'a pu être effectuée. Une erreur s\'est produite durant la facturation de la carte de crédit.<br>Le serveur à retourner cette erreur : ', 'nexo');?>' + message );
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

		this.CartDiscount		=	( this.CartRemise + this.CartRabais + this.CartRistourne + this.CartGroupDiscount );
		this.CartValueRRR		=	this.CartValue - this.CartDiscount;

		this.calculateCartVAT();

		this.CartToPay			=	( this.CartValueRRR + this.CartVAT );
		this.CartToPayLong		=	parseInt( this.CartToPay )	<?php echo in_array(strtolower(@$Options[ 'nexo_currency_iso' ]), $this->config->item('nexo_currency_with_double_zero')) ? "+ '00'" : '';?>;

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
			<?php if (intval($order[ 'order' ][0][ 'RISTOURNE' ]) > 0):?>
		this.CartRistourneEnabled		=	true;
		this.CartRistourneType			=	'amount';
		this.CartRistourneAmount		=	parseInt( <?php echo intval($order[ 'order' ][0][ 'RISTOURNE' ]);?> );
		this.CartRistourneCustomerID	=	'<?php echo $order[ 'order' ][0][ 'REF_CLIENT' ];?>';
			<?php endif;?>
		<?php endif;?>
	}

	/**
	 * Restore default discount (automatic discount)
	**/

	this.restoreDefaultRistourne		=	function(){
		this.CartRistourneType			=	'<?php echo @$Options[ 'discount_type' ];?>';
		this.CartRistourneAmount		=	'<?php echo @$Options[ 'discount_amount' ];?>';
		this.CartRistournePercent		=	'<?php echo @$Options[ 'discount_percent' ];?>';
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

		this.resetCartObject();

		this.CartValue				=	0;
		this.CartValueRRR			=	0;
		this.CartVAT				=	0;
		this.CartDiscount			=	0;
		this.CartToPay				=	0;
		this.CartToPayLong			=	0;

		this.CartRemiseType			=	null;
		this.CartRemise				=	0;
		this.CartRemiseEnabled		=	false;
		this.CartRemisePercent		=	null;

		this.restoreDefaultRistourne();

		this.CartPaymentType		=	null;
		this.CartPerceivedSum		=	0;
		this.CartCreance			=	0;
		this.CartToPayBack			=	0;

		this.CartCustomerID			=	null;

		this.cartGroupDiscountReset();

		this.CartRabais				=	0;

		this.CartTotalItems			=	0;
		this.CartAllowStripeSubmitOrder	=	false;

		this.refreshCartValues();
		
		// Reset Cart
		NexoAPI.events.doAction( 'reset_cart', this );
	}

	/**
	 * Run Checkout
	**/

	this.run							=	function(){
		
		// Remove Slash First
		this.paymentWindow.hideSplash();

		this.fixHeight();		
		this.resetCart();
		this.initCartDateTime();
		this.bindHideItemOptions();
		
		<?php if (isset($order)):?>
			this.emptyCartItemTable();
			<?php foreach ($order[ 'products' ] as $product):?>
			this.CartItems.push( <?php echo json_encode($product);?> );
			<?php endforeach;?>


			<?php if (intval($order[ 'order' ][0][ 'REMISE' ]) > 0):?>
			this.CartRemiseType			=	'flat';
			this.CartRemise				=	parseInt( <?php echo $order[ 'order' ][0][ 'REMISE' ];?> );
			this.CartRemiseEnabled		=	true;
			<?php endif;?>
	
			<?php if (intval($order[ 'order' ][0][ 'GROUP_DISCOUNT' ]) > 0):?>
			this.CartGroupDiscount				=	<?php echo intval($order[ 'order' ][0][ 'GROUP_DISCOUNT' ]);?>; // final amount
			this.CartGroupDiscountAmount		=	<?php echo intval($order[ 'order' ][0][ 'GROUP_DISCOUNT' ]);?>; // Amount set on each group
			this.CartGroupDiscountType			=	'amount'; // Discount type
			this.CartGroupDiscountEnabled		=	true;
			<?php endif;?>
	
			this.CartCustomerID					=	<?php echo $order[ 'order' ][0][ 'REF_CLIENT' ];?>;
	
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
			v2Checkout.bindAddDiscount();
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
		 * Cart Item Settings
		**/

		$( this.ItemSettings ).bind( 'click', function(){
			v2Checkout.itemsSettings();
		});

		/**
		 * Bind Pay Button
		**/

		$( this.CartPayButton ).bind( 'click', function(){
			v2Checkout.pay();
		});

		//
		$(window).on("beforeunload", function() {
			if( ! v2Checkout.isCartEmpty() ) {
				return "<?php echo addslashes(__('Le processus de commande a commencé. Si vous continuez, vous perdrez toutes les informations non enregistrées', 'nexo'));?>";
			}
		})

		<?php if (in_array('stripe', array_keys($this->config->item('nexo_payment_types')))):?>
		this.stripe.run();
		<?php endif;?>
	}
};

$( document ).ready(function(e) {
	v2Checkout.run();
});
</script>
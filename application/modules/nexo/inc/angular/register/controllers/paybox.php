<?php
global 	$Options,
$PageNow,
$register_id;
?>
<script>

/**
* Load PHP dependency
**/

var dependency						=
<?php echo json_encode(
	$dependencies	=	$this->events->apply_filters(
		'paybox_dependencies',
		array( '$scope', '$compile', '$filter', '$http', 'serviceKeyboardHandler', 'serviceNumber' )
	)
);?>;

/**
* Create closure
**/

var controller						=	function( <?php echo implode( ',', $dependencies );?> ) {

	$scope.addPaymentDisabled		=	false;
	$scope.cashPaidAmount			=	0;
	$scope.currentPaymentIndex		=	null;
	$scope.defaultAddPaymentText	=	'<?php echo _s( 'Ajouter', 'nexo' );?>';
	$scope.defaultAddPaymentClass	=	'success';
	$scope.editModeEnabled			=	false;
	$scope.paymentTypes				=	<?php echo json_encode( $this->events->apply_filters( 'nexo_payments_types', $this->config->item( 'nexo_payments_types' ) ) );?>;
	$scope.paymentTypesObject		= 	new Object;
	$scope.paymentList				=	[];
	$scope.showCancelEditionButton	=	false;
	$scope.windowHeight				=	window.innerHeight;
	$scope.wrapperHeight			=	$scope.windowHeight - ( ( 56 * 2 ) + 30 );



	_.each( $scope.paymentTypes, function( value, key ) {
		$scope.paymentTypesObject	=	_.extend( $scope.paymentTypesObject, _.object( [ key ], [{
			active	:	false,
			text	:	value
		}]));
	});

	// Allow custom entry on the payementTypesObject;
	$scope.paymentTypesObject		=	NexoAPI.events.applyFilters( 'nexo_payments_types_object', $scope.paymentTypesObject );

	// Create an accessible object
	v2Checkout.paymentTypesObject					=	$scope.paymentTypesObject;

	/**
	* Add Payment
	**/

	$scope.addPayment								=	function( payment_namespace, payment_amount ) {

		if( payment_amount <= 0 || ( isNaN( parseFloat( payment_amount ) ) && isNaN( parseInt( payment_amount ) ) ) ) {
			NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Le montant spécifié est incorrecte', 'nexo' );?>' );
			$scope.paidAmount	=	0;
			return false;
		}

		if( $scope.editModeEnabled ) {

			$scope.paymentList[ $scope.currentPaymentIndex ].amount	=	$scope.paidAmount;
			$scope.cancelPaymentEdition();

		} else {

			if( $scope.cart.paidSoFar >  ( Math.abs( $scope.cart.value ) + $scope.cart.VAT ) ) {

				NexoAPI.Notify().warning(
					'<?php echo _s( 'Attention', 'nexo' );?>',
					'<?php echo _s( 'Vous ne pouvez plus ajouter de paiement supplémentaire.', 'nexo' );?>'
				);

				return false;
			}

			$scope.paymentList.push({
				namespace		:	payment_namespace,
				text			:	_.propertyOf( $scope.paymentTypes )( payment_namespace ),
				amount			:	payment_amount
			});

		}

		$scope.paidAmount	=	0; // reset paid amount
		$scope.refreshPaidSoFar();
		// Trigger Action added on cart
		NexoAPI.events.doAction( 'cart_add_payment', [ $scope.cart.paidSoFar, $scope.cart.balance ]);
	};

	/**
	* bind Keyboard Events
	**/

	$scope.bindKeyBoardEvent	=	function( $event ){
		// console.log( $event );
	};

	/**
	* Cancel Payment Edition
	**/

	$scope.cancelPaymentEdition	=	function( paymentNamespace ){
		$scope.editModeEnabled			=	false;
		$scope.showCancelEditionButton	=	false;
		$scope.defaultAddPaymentText	=	'<?php echo _s( 'Ajouter', 'nexo' );?>';
		$scope.defaultAddPaymentClass	=	'success';
		$scope.paidAmount				=	0;

		if( typeof paymentNamespace != 'undefined' ) {
			$scope.selectPayment( paymentNamespace );
		}

		$scope.refreshBox();
	};


	/**
	* Confirm Order
	* @param bool action
	**/

	$scope.confirmOrder			=	function( action ) {
		if( action ) {
			if( $scope.cart.paidSoFar > 0 ) {

				var payment_means			=	$scope.paymentList[ $scope.paymentList.length - 1 ].namespace; // use the payment name as the order payment type
				var order_items				=	new Array;

				_.each( v2Checkout.CartItems, function( value, key ){

					var ArrayToPush			=	[
						value.ID,
						value.QTE_ADDED,
						value.CODEBAR,
						value.PROMO_ENABLED ? value.PRIX_PROMOTIONEL : ( v2Checkout.CartShadowPriceEnabled ? value.SHADOW_PRICE : value.PRIX_DE_VENTE ),
						value.QUANTITE_VENDU,
						value.QUANTITE_RESTANTE,
						// @since 2.8.2
						value.STOCK_ENABLED,
						// @since 2.9.0
						value.DISCOUNT_TYPE,
						value.DISCOUNT_AMOUNT,
						value.DISCOUNT_PERCENT
					];

					// improved @since 2.7.3
					// add meta by default
					var ItemMeta	=	NexoAPI.events.applyFilters( 'items_metas', [] );

					var MetaKeys	=	new Array;

					_.each( ItemMeta, function( _value, key ) {
						var unZiped	=	_.keys( _value );
						MetaKeys.push( unZiped[0] );
					});

					var AllMetas	=	new Object;

					// console.log( value );

					_.each( MetaKeys, function( MetaKey ) {
						AllMetas	=	_.extend( AllMetas, _.object( [ MetaKey ], [ _.propertyOf( value )( MetaKey ) ] ) );
					});

					// console.log( AllMetas );

					//
					ArrayToPush.push( JSON.stringify( AllMetas ) );

					// Add Meta JSON stringified to order_item
					order_items.push( ArrayToPush );
				});

				var order_details					=	new Object;
				order_details.TOTAL				=	NexoAPI.ParseFloat( v2Checkout.CartToPay );
				order_details.REMISE			=	NexoAPI.ParseFloat( v2Checkout.CartRemise );
				// @since 2.9.6
				if( v2Checkout.CartRemiseType == 'percentage' ) {
					order_details.REMISE_PERCENT	=	NexoAPI.ParseFloat( v2Checkout.CartRemisePercent );
					order_details.REMISE			=	0;
				} else if( v2Checkout.CartRemiseType == 'flat' ) {
					order_details.REMISE_PERCENT	=	0;
					order_details.REMISE			=	NexoAPI.ParseFloat( v2Checkout.CartRemise );
				} else {
					order_details.REMISE_PERCENT	=	0;
					order_details.REMISE			=	0;
				}

				order_details.REMISE_TYPE			=	v2Checkout.CartRemiseType;
				// @endSince
				order_details.RABAIS			=	NexoAPI.ParseFloat( v2Checkout.CartRabais );
				order_details.RISTOURNE			=	NexoAPI.ParseFloat( v2Checkout.CartRistourne );
				order_details.TVA				=	NexoAPI.ParseFloat( v2Checkout.CartVAT );
				order_details.REF_CLIENT		=	v2Checkout.CartCustomerID == null ? v2Checkout.customers.DefaultCustomerID : v2Checkout.CartCustomerID;
				order_details.PAYMENT_TYPE		=	$scope.paymentList.length == 1 ? $scope.paymentList[0].namespace : 'multi'; // v2Checkout.CartPaymentType;
				order_details.GROUP_DISCOUNT	=	NexoAPI.ParseFloat( v2Checkout.CartGroupDiscount );
				order_details.DATE_CREATION		=	v2Checkout.CartDateTime.format( 'YYYY-MM-DD HH:mm:ss' )
				order_details.ITEMS				=	order_items;
				order_details.DEFAULT_CUSTOMER	=	v2Checkout.DefaultCustomerID;
				order_details.DISCOUNT_TYPE		=	'<?php echo @$Options[ store_prefix() . 'discount_type' ];?>';
				order_details.HMB_DISCOUNT		=	'<?php echo @$Options[ store_prefix() . 'how_many_before_discount' ];?>';
				// @since 2.7.5
				order_details.REGISTER_ID		=	'<?php echo $register_id;?>';

				// @since 2.7.1, send editable order to Rest Server
				order_details.EDITABLE_ORDERS	=	<?php echo json_encode( $this->events->apply_filters( 'order_editable', array( 'nexo_order_devis' ) ) );?>;

				// @since 2.7.3 add Order note
				order_details.DESCRIPTION		=	v2Checkout.CartNote;

				// @since 2.9.0
				order_details.TITRE				=	v2Checkout.CartTitle;

				// @since 2.8.2 add order meta
				this.CartMetas					=	NexoAPI.events.applyFilters( 'order_metas', v2Checkout.CartMetas );
				order_details.METAS				=	JSON.stringify( v2Checkout.CartMetas );

				if( _.indexOf( _.keys( $scope.paymentTypes ), payment_means ) != -1 ) {

					order_details.SOMME_PERCU		=	NexoAPI.ParseFloat( $scope.cart.paidSoFar );
					order_details.SOMME_PERCU 		=	isNaN( order_details.SOMME_PERCU ) ? 0 : order_details.SOMME_PERCU;

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

				// Queue Payment
				order_details.payments		=	$scope.paymentList;

				var ProcessObj	=	NexoAPI.events.applyFilters( 'process_data', {
					url			:	v2Checkout.ProcessURL,
					type		:	v2Checkout.ProcessType
				});

				// Filter Submited Details
				order_details	=	NexoAPI.events.applyFilters( 'before_submit_order', order_details );

				NexoAPI.events.doAction( 'submit_order' );

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

									$( 'body' ).append( '<iframe style="display:none;" id="CurrentReceipt" name="CurrentReceipt" src="<?php echo site_url(array( 'dashboard', store_slug(), 'nexo', 'print', 'order_receipt' ));?>/' + returned.order_id + '?refresh=true"></iframe>' );

									window.frames["CurrentReceipt"].focus();
									window.frames["CurrentReceipt"].print();

									setTimeout( function(){
										$( '#CurrentReceipt' ).remove();
									}, 5000 );

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
							document.location	=	'<?php echo site_url(array( 'dashboard', 'nexo', store_slug(), 'commandes', 'lists' ));?>';
						}
						<?php endif;?>
					},
					error			:	function(){
						v2Checkout.paymentWindow.hideSplash();
						NexoAPI.Notify().warning( '<?php echo _s('Une erreur s\'est produite', 'nexo');?>', '<?php echo _s('Le paiement n\'a pas pu être effectuée.', 'nexo');?>' );
					}
				});
			} else {
				NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Vous ne pouvez pas valider une commande qui n\'a pas reçu de paiement. Si vous souhaitez enregistrer cette commande, fermer la fenêtre de paiement et cliquez sur le bouton "En attente".', 'nexo' );?>' );
				return false;
			}
		}
	};

	/**
	* Edit Payment
	**/

	$scope.editPayment			=	function( index ){

		// let use controll whether they would allow specific payement done

		if( NexoAPI.events.applyFilters( 'allow_payment_edition', [ true, $scope.paymentList[ index ].namespace ] )[0] == true ) {
			$scope.selectPayment( $scope.paymentList[ index ].namespace );
			$scope.editModeEnabled			=	true;
			$scope.showCancelEditionButton	=	true;
			$scope.defaultAddPaymentText	=	'<?php echo _s( 'Modifier', 'nexo' );?>';
			$scope.defaultAddPaymentClass	=	'info';
			$scope.currentPaymentIndex		=	index;
			$scope.paidAmount				=	$scope.paymentList[ index ].amount;
		}
	};

	/**
	* Keyboard Input
	**/

	$scope.keyboardInput		=	function( char, field ) {

		if( typeof $scope.paidAmount	==	'undefined' ) {
			$scope.paidAmount	=	''; // reset paid amount
		}

		if( $scope.paidAmount 	==	0 ) {
			$scope.paidAmount	=	'';
		}

		if( char == 'clear' ) {
			$scope.paidAmount	=	'';
		} else if( char == '.' ) {
			$scope.paidAmount	+=	'.';
		} else if( char == 'back' ) {
			$scope.paidAmount	=	$scope.paidAmount.substr( 0, $scope.paidAmount.length - 1 );
		} else if( typeof char == 'number' ) {
			$scope.paidAmount	=	$scope.paidAmount + '' + char;
		}
	};

	/**
	 *  Open Coupon Box
	 *  @param
	 *  @return
	**/

	$scope.openCouponBox		=	function(){
		alert( 'ok' );
	}

	/**
	* Open Box Main Function
	*
	**/

	$scope.openPayBox		=	function() {

		$scope.cart			=	{
			value			:		v2Checkout.CartValue,
			discount		:		v2Checkout.CartDiscount,
			netPayable		:		v2Checkout.CartToPay,
			VAT				:		v2Checkout.CartVAT
		};

		$scope.cashPaidAmount			=	0;
		$scope.paymentList				=	[];

		// Refresh Paid so far
		$scope.refreshPaidSoFar();

		if( v2Checkout.isCartEmpty() ) {
			NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Vous ne pouvez pas payer une commande sans article. Veuillez ajouter au moins un article', 'nexo' );?>' );
			return false;
		}

		NexoAPI.Bootbox().confirm({
			message 		:	'<div class="payboxwrapper"><pay-box-content/></div>',
			title			:	'<?php echo _s( 'Paiement de la commande', 'nexo' );?>',
			buttons: {
				confirm: {
					label: '<span class="hidden-xs"><?php echo _s( 'Valider la commande', 'nexo' );?></span><span class="fa fa-shopping-cart"></i></span>',
					className: 'btn-success'
				},
				cancel: {
					label: '<?php echo _s( 'Fermer', 'nexo' );?>',
					className: 'btn-default'
				}
			},
			callback		:	function( action ) {
				return $scope.confirmOrder( action );
			}
		});

		$( '.payboxwrapper' ).html( $compile( $( '.payboxwrapper' ).html() )($scope) );
		$( '.modal-content > .modal-footer' ).html( $compile( $( '.modal-content > .modal-footer' ).html() )($scope) );

		angular.element( '.modal-dialog' ).css( 'width', '90%' );
		angular.element( '.modal-body' ).css( 'padding-top', '0px' );
		angular.element( '.modal-body' ).css( 'padding-bottom', '0px' );
		angular.element( '.modal-body' ).css( 'padding-left', '0px' );
		angular.element( '.modal-body' ).css( 'height', $scope.wrapperHeight );
		angular.element( '.modal-body' ).css( 'overflow-x', 'hidden' );

		// Select first payment available
		var paymentTypesNamespaces	=	_.keys( $scope.paymentTypes );
		$scope.selectPayment( paymentTypesNamespaces[0] );

		setTimeout( function(){
			var cartDetailsTableHeight		=	angular.element( '.cart-details-table' ).outerHeight();
			var h3Height					=	angular.element( 'h3.text-center' ).outerHeight() * 2;

			angular.element( '.cart-details ul.list-group' ).attr( 'style', 'height:' + ( $scope.wrapperHeight - ( cartDetailsTableHeight + h3Height + 70 ) ) + 'px;overflow-y:scroll;overflow-x:hidden' );
		}, 500 );

		// Add Filter
		angular.element( '.modal-footer' ).prepend( '<div class="pay_box_footer pull-left">' + NexoAPI.events.applyFilters( 'pay_box_footer', '' ) + '</div>' );

		NexoAPI.events.doAction( 'pay_box_loaded' );
	};

	/**
	* Refresh Box
	**/

	$scope.refreshBox		=	function(){
		$( '.payboxwrapper' ).html( $compile( $( '.payboxwrapper' ).html() )($scope) );
	};

	/**
	* Refresh Paid So Far
	**/

	$scope.refreshPaidSoFar		=	function(){

		$scope.cart.paidSoFar		=	0;

		_.each( $scope.paymentList, function( value ) {
			$scope.cart.paidSoFar	+=	parseFloat( value.amount );
		});

		$scope.cart.balance			=	$scope.cart.paidSoFar - ( v2Checkout.CartValueRRR + $scope.cart.VAT );

	};

	/**
	* Remove Payment
	**/

	$scope.removePayment	=	function( index ){

		$scope.cancelPaymentEdition();

		$scope.paymentList.splice( index, 1 );

		$scope.refreshPaidSoFar();

		NexoAPI.events.doAction( 'cart_remove_payment', [ $scope.cart.paidSoFar, $scope.cart.balance ]);
	};

	/**
	* Select Payment
	**/

	$scope.selectPayment		=	function( namespace ) {

		// if edit mode is enabled, disable selection
		if( $scope.editModeEnabled ) {
			NexoAPI.Bootbox().confirm( '<?php echo _s( 'Souhaitez-vous annuler la modification ?', 'nexo' );?>', function( action ) {
				if( action ) {
					$scope.cancelPaymentEdition( namespace );
				}
			});
			return false;
		}

		// reset payment options
		_.each( $scope.paymentTypesObject, function( value, key ) {
			_.propertyOf( $scope.paymentTypesObject )( key ).active = false;
		});

		// set payment option active
		if( _.propertyOf( $scope.paymentTypesObject )( namespace ) ) {
			_.propertyOf( $scope.paymentTypesObject )( namespace ).active = true;

			$scope.defaultSelectedPaymentNamespace	=	namespace;
			$scope.defaultSelectedPaymentText		=	_.propertyOf( $scope.paymentTypesObject )( namespace ).text;
		}

		/**
		* Add event when payment is selected
		**/

		NexoAPI.events.doAction( 'pos_select_payment', [ $scope, namespace ] );
	}

	// Inject method within payBox controller
	<?php $this->events->do_action( 'angular_paybox_footer' );?>};

	/**
	* Add closure to dependency
	**/

	dependency.push( controller );

	/**
	* Load PayBox Controller
	**/

	tendooApp.controller( 'payBox', dependency );

	</script>

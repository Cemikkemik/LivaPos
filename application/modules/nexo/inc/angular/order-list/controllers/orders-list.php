<?php global $Options;?>
<script>
/**
 * Create Controller
**/

tendooApp.controller( 'nexo_order_list', [ '$scope', '$compile', '$timeout', '$http', '__orderStatus', '__paymentName', '__windowSplash', '__stripeCheckout', 'hotkeys', function( $scope, $compile, $timeout, $http, __orderStatus, __paymentName, __windowSplash, __stripeCheckout, hotkeys ) {

	$scope.order_status		=	{
		comptant			:	'nexo_order_comptant',
		avance				:	'nexo_order_advance',
		complete			:	'nexo_order_complete',
		devis				:	'nexo_order_devis'
	}

	$scope.ajaxHeader		=	{
		'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
		// 'Content-Type'											: 	'application/x-www-form-urlencoded'
	}

	$scope.window			=	{
		height				:	$( window ).height()
	}

	$scope.parseFloat      	=	function( value ) {
		return value == '' ?  0 : parseFloat( value );
	};

	/**
	 * addTO
	**/

	$scope.addTo				=	function( place, barcode ) {
		_.each( $scope.orderItems, function( value, key ) {
			if( value.CODEBAR == barcode ) {
				if( place == 'defective' ) {
					if( $scope.orderItems[ key ].QUANTITE > 0 ) {

						$scope.orderItems[ key ].QUANTITE 				=	parseInt( $scope.orderItems[ key ].QUANTITE ) - 1;
						$scope.orderItems[ key ].CURRENT_DEFECTIVE_QTE	=	parseInt( $scope.orderItems[ key ].CURRENT_DEFECTIVE_QTE ) + 1;

						if( $scope.orderItems[key].STOCK_ENABLED == '1' ) {
							$scope.orderItems[ key ].QUANTITE_VENDU			=	parseInt( $scope.orderItems[ key ].QUANTITE_VENDU ) - 1;
							$scope.orderItems[ key ].DEFECTUEUX				=	parseInt( $scope.orderItems[ key ].DEFECTUEUX ) + 1;
						}

						var salePrice	=	parseFloat( $scope.orderItems[ key ].PRIX ); // * $scope.orderItems[ key ].CURRENT_DEFECTIVE_QTE

						if( $scope.orderItems[ key ].DISCOUNT_TYPE == 'percentage' ) {
							var percentPrice	=	( ( parseFloat( $scope.orderItems[ key ].DISCOUNT_PERCENT ) * parseFloat( $scope.orderItems[ key ].PRIX ) ) ) / 100;
								salePrice		-=	percentPrice;
						} else if( $scope.orderItems[ key ].DISCOUNT_TYPE == 'fixed' ) {
								salePrice		-=	parseFloat( $scope.orderItems[ key ].DISCOUNT_AMOUNT );
						}

						$scope.toRefund			+=	salePrice;
						$scope.order.TOTAL		-=	salePrice;

					} else {
						NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Vous ne pouvez plus retirer de quantité', 'nexo' );?>' );
					}
				} else if( place == 'def_to_stock' ) {

					if( $scope.orderItems[ key ].CURRENT_DEFECTIVE_QTE > 0 ) {

						var salePrice	=	parseFloat( $scope.orderItems[ key ].PRIX ); // * $scope.orderItems[ key ].CURRENT_DEFECTIVE_QTE

						if( $scope.orderItems[ key ].DISCOUNT_TYPE == 'percentage' ) {
							var percentPrice	=	( ( parseFloat( $scope.orderItems[ key ].DISCOUNT_PERCENT ) * parseFloat( $scope.orderItems[ key ].PRIX ) ) ) / 100;
								salePrice		-=	percentPrice;
						} else if( $scope.orderItems[ key ].DISCOUNT_TYPE == 'fixed' ) {
								salePrice		-=	parseFloat( $scope.orderItems[ key ].DISCOUNT_AMOUNT );
						}

						$scope.toRefund	=
							$scope.toRefund > 0 ? $scope.toRefund -	salePrice : 0;

						$scope.order.TOTAL		+=	salePrice;

						$scope.orderItems[ key ].QUANTITE 				=	parseInt( $scope.orderItems[ key ].QUANTITE ) + 1;
						$scope.orderItems[ key ].CURRENT_DEFECTIVE_QTE	=	parseInt( $scope.orderItems[ key ].CURRENT_DEFECTIVE_QTE ) - 1;

						if( $scope.orderItems[key].STOCK_ENABLED == '1' ) {
							$scope.orderItems[ key ].QUANTITE_VENDU			=	parseInt( $scope.orderItems[ key ].QUANTITE_VENDU ) + 1;
							$scope.orderItems[ key ].DEFECTUEUX				=	parseInt( $scope.orderItems[ key ].DEFECTUEUX ) - 1
						}

					} else {
						NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Vous ne pouvez plus restaurer une quantité', 'nexo' );?>' );
					}
				} else if( place == 'active' ) {
					if( $scope.orderItems[ key ].QUANTITE > 0 ) {

						$scope.orderItems[ key ].QUANTITE 				=	parseInt( $scope.orderItems[ key ].QUANTITE ) - 1;
						$scope.orderItems[ key ].CURRENT_USABLE_QTE		=	parseInt( $scope.orderItems[ key ].CURRENT_USABLE_QTE ) + 1;

						if( $scope.orderItems[key].STOCK_ENABLED == '1' ) {
							$scope.orderItems[ key ].QUANTITE_VENDU			=	parseInt( $scope.orderItems[ key ].QUANTITE_VENDU ) - 1;
							$scope.orderItems[ key ].QUANTITE_RESTANTE		=	parseInt( $scope.orderItems[ key ].QUANTITE_RESTANTE ) + 1;
						}

						var salePrice	=	parseFloat( $scope.orderItems[ key ].PRIX ); // * $scope.orderItems[ key ].CURRENT_DEFECTIVE_QTE

						if( $scope.orderItems[ key ].DISCOUNT_TYPE == 'percentage' ) {
							var percentPrice	=	( ( parseFloat( $scope.orderItems[ key ].DISCOUNT_PERCENT ) * parseFloat( $scope.orderItems[ key ].PRIX ) ) ) / 100;
								salePrice		-=	percentPrice;
						} else if( $scope.orderItems[ key ].DISCOUNT_TYPE == 'fixed' ) {
								salePrice		-=	parseFloat( $scope.orderItems[ key ].DISCOUNT_AMOUNT );
						}

						$scope.toRefund			+=	salePrice;
						$scope.order.TOTAL		-=	salePrice;

					} else {
						NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Vous ne pouvez plus retirer de quantité', 'nexo' );?>' );
					}
				} else if( place == 'act_to_stock' ) {
					if( $scope.orderItems[ key ].CURRENT_USABLE_QTE > 0 ) {

						var salePrice	=	parseFloat( $scope.orderItems[ key ].PRIX ); // * $scope.orderItems[ key ].CURRENT_DEFECTIVE_QTE

						if( $scope.orderItems[ key ].DISCOUNT_TYPE == 'percentage' ) {
							var percentPrice	=	( ( parseFloat( $scope.orderItems[ key ].DISCOUNT_PERCENT ) * parseFloat( $scope.orderItems[ key ].PRIX ) ) ) / 100;
								salePrice		-=	percentPrice;
						} else if( $scope.orderItems[ key ].DISCOUNT_TYPE == 'fixed' ) {
								salePrice		-=	parseFloat( $scope.orderItems[ key ].DISCOUNT_AMOUNT );
						}

						$scope.toRefund			-=	salePrice;
						$scope.order.TOTAL		+=	salePrice;

						$scope.orderItems[ key ].QUANTITE 				=	parseInt( $scope.orderItems[ key ].QUANTITE ) + 1;
						$scope.orderItems[ key ].CURRENT_USABLE_QTE		=	parseInt( $scope.orderItems[ key ].CURRENT_USABLE_QTE ) - 1;

						if( $scope.orderItems[key].STOCK_ENABLED == '1' ) {
							$scope.orderItems[ key ].QUANTITE_VENDU			=	parseInt( $scope.orderItems[ key ].QUANTITE_VENDU ) + 1;
							$scope.orderItems[ key ].QUANTITE_RESTANTE		=	parseInt( $scope.orderItems[ key ].QUANTITE_RESTANTE ) - 1;
						}

					} else {
						NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Vous ne pouvez plus restaurer une quantité', 'nexo' );?>' );
					}
				}
			}
		});
	}

	/**
	 *  Check Refund Availability
	 *  @param object returned item
	 *  @return boolean
	**/

	$scope.checkRefundAvailability		=	function( returnedItems ) {
		var show 	=	true;
		_.each( returnedItems, function( value ) {
			if( parseFloat( value.USABLE_QTE ) > 0 || parseFloat( value.DEFECTIVE_QTE ) > 0 ) {
				show =	false;
			}
		});
		return show;
	}

	/**
	 * Control Cash Payment
	**/

	$scope.controlCashAmount	=	function(){
		if( parseFloat( $scope.cashPaymentAmount ) > 0 && parseFloat( $scope.cashPaymentAmount ) < parseFloat( $scope.orderBalance ) ) {
			$scope.paymentDisabled		=	false;
		} else if( parseFloat( $scope.cashPaymentAmount ) > parseFloat( $scope.orderBalance ) ) {
			$scope.cashPaymentAmount	=	Number( parseFloat( $scope.orderBalance ).toFixed(2) );
			$scope.paymentDisabled		=	false;
			NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Le paiement ne peut pas excéder la somme à payer', 'nexo' );?>' );
		} else {
			$scope.cashPaymentAmount	=	0;
			$scope.paymentDisabled		=	true;
		}
	};

	/**
	 * Create Options
	**/

	$scope.createOptions		=	function(){
		return	{
				details				:	{
				title				:	'<?php echo _s( 'Détails', 'nexo' );?>',
				visible				:	false,
				class				:	'default',
				content				:	'',
				namespace			:	'details',
				icon				:	'fa fa-eye'
			},	payment				:	{
				title				:	'<?php echo _s( 'Paiment', 'nexo' );?>',
				visible				:	false,
				class				:	'default',
				content				:	'',
				namespace			:	'payment',
				icon				:	'fa fa-money'
			}, refund			:	{
				title				:	'<?php echo _s( 'Remboursement', 'nexo' );?>',
				visible				:	false,
				class				:	'default',
				content				:	'',
				namespace			:	'refund',
				icon				:	'fa fa-frown-o'
			}/*, cancel			:	{
				title				:	'<?php echo _s( 'Annulation', 'nexo' );?>',
				visible				:	false,
				class				:	'default',
				content				:	'',
				namespace			:	'cancel',
				icon				:	'fa fa-eye'
			}, print			:	{
				title				:	'<?php echo _s( 'Imprimer', 'nexo' );?>',
				visible				:	false,
				class				:	'default',
				content				:	'',
				namespace			:	'print',
				icon				:	'fa fa-eye'
			}*/
		};
	};

	/**
	 * Disable Payment
	**/

	$scope.disablePayment		=	function( payment ){
		if( payment == 'cash' ) {
			$scope.paymentDisabled	=	true;
		}
	}

	/**
	 * Load Content
	**/

	$scope.loadContent			=	function( option ){
		if( option.namespace 		==	'details' ) {

			HTML.query( '[data-namespace="' + option.namespace + '"]' ).only(0).add( 'my-spinner' ).each( 'namespace', 'spinner' );

			$( '[data-namespace="' + option.namespace + '"]' ).html( $compile( $( '[data-namespace="' + option.namespace + '"]' ).html() )($scope) );

			$scope.openSpinner();

			$http.get( '<?php echo site_url( array( 'rest', 'nexo', 'order_with_item' ) );?>' + '/' + $scope.order_id + '?<?php echo store_get_param( null );?>', {
				headers			:	{
					'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
				}
			}).then(function( returned ){

				$scope.items			=	returned.data.items;
				$scope.order			=	returned.data.order;
				$scope.order.GRANDTOTAL	=	0;
				$scope.orderCode		=	$scope.order.CODE;
				$scope.order.CHARGE		=	NexoAPI.ParseFloat( $scope.order.REMISE ) + NexoAPI.ParseFloat( $scope.order.RABAIS ) + NexoAPI.ParseFloat( $scope.order.RISTOURNE );

				// hide unused options
				if( $scope.order.TYPE == 'nexo_order_comptant' ) {
					// delete $scope.options.payment;
				}

				// hide unused options
				if( $scope.order.TYPE == 'nexo_order_devis' ) {
					delete $scope.options.refund;
				}

				// Sum total
				_.each( $scope.items, function( value ) {
					// if it's INLINE item
					$scope.order.GRANDTOTAL	+=	( value.QUANTITE * value.PRIX );
				});

				// Remaining
				$scope.order.BALANCE		=	Math.abs( NexoAPI.ParseFloat( $scope.order.TOTAL - $scope.order.SOMME_PERCU ) );

				var content		=
				'<div class="row">' +
					'<div class="col-lg-8 col-md-8 col-xs-12" style="height:{{ wrapperHeight }}px;overflow-y: scroll;">' +
						'<h5><?php echo _s( 'Liste des produits', 'nexo' );?></h5>' +
						'<table class="table table-bordered table-striped">' +
							'<thead>' +
								'<tr>' +
									'<td><?php echo _s( 'Nom de l\'article', 'nexo' );?></td>' +
									'<td><?php echo _s( 'UGS', 'nexo' );?></td>' +
									'<td><?php echo _s( 'Prix Unitaire', 'nexo' );?></td>' +
									'<td><?php echo _s( 'Quantité', 'nexo' );?></td>' +
									'<td><?php echo _s( 'Sous-Total', 'nexo' );?></td>' +
								'</tr>' +
							'</thead>' +
							'<tbody>' +
								'<tr ng-repeat="item in items">' +
									'<td>{{ item.DESIGN == null ? item.NAME : item.DESIGN }}</td>' +
									'<td>{{ item.SKU.length == null ? \'N/A\' : item.SKU }}</td>' +
									'<td>{{ item.PRIX | moneyFormat }}</td>' +
									'<td>{{ item.QUANTITE }}</td>' +
									'<td>{{ item.PRIX * item.QUANTITE | moneyFormat }}</td>' +
								'</tr>' +
								'<tr>' +
									'<td colspan="4"><strong><?php echo _s( 'Sous Total', 'nexo' );?></strong> </td>' +
									'<td>{{ order.GRANDTOTAL | moneyFormat }}</td>' +
								'</tr>' +
								'<tr>' +
									'<td colspan="4"><strong><?php echo _s( 'Remise (-)', 'nexo' );?></strong></td>' +
									'<td>{{ order.CHARGE | moneyFormat }}</td>' +
								'</tr>' +
								'<tr>' +
									'<td colspan="4"><strong><?php echo _s( 'TVA (+)', 'nexo' );?></strong> </td>' +
									'<td>{{ order.TVA | moneyFormat }}</td>' +
								'</tr>' +
								'<tr ng-show="order.SHIPPING_AMOUNT > 0">' +
									'<td colspan="4"><strong><?php echo _s( 'Livraison', 'nexo' );?></strong></td>' +
									'<td>{{ order.SHIPPING_AMOUNT | moneyFormat }}</td>' +
								'</tr>' +								
								'<tr>' +
									'<td colspan="4"><strong><?php echo _s( 'Total', 'nexo' );?></strong></td>' +
									'<td>{{ order.TOTAL | moneyFormat }}</td>' +
								'</tr>' +
								'<tr>' +
									'<td colspan="4"><strong><?php echo _s( 'Payé', 'nexo' );?></strong></td>' +
									'<td>{{ order.SOMME_PERCU | moneyFormat }}</td>' +
								'</tr>' +
								'<tr>' +
									'<td colspan="4"><strong><?php echo _s( 'Reste', 'nexo' );?></strong></td>' +
									'<td>{{ order.BALANCE | moneyFormat }}</td>' +
								'</tr>' +
							'</tbody>' +
						'</table>' +
					'</div>' +
					'<div class="col-lg-4 col-md-4 col-xs-12">' +
						'<h5><?php echo _s( 'Détails sur la commande', 'nexo' );?></h5>' +
						'<ul class="list-group">' +
						  '<li class="list-group-item"><strong><?php echo _s( 'Auteur :', 'nexo' );?></strong> {{ order.AUTHOR_NAME }}</li>' +
						  '<li class="list-group-item"><strong><?php echo _s( 'Effectué le :', 'nexo' );?></strong> {{ order.DATE_CREATION | date:short }}</li>' +
						  '<li class="list-group-item"><strong><?php echo _s( 'Client :', 'nexo' );?></strong> {{ order.CLIENT_NAME }}</li>' +
						  '<li class="list-group-item"><strong><?php echo _s( 'Statut :', 'nexo' );?></strong> {{ order.TYPE | orderStatus }}</li>' +
						'</ul>' +
					'</div>' +
				'</div>';

				$scope.closeSpinner();

				$( '[data-namespace="details"]' ).html( $compile(content)($scope) );
			});
		}
		else if( option.namespace == 'payment' ) {

			$scope.cashPaymentAmount	=	0;

			$( '[data-namespace="payment"]' ).html( '' );

			HTML.query( '[data-namespace="payment"]' ).only(0).add( 'div.row>div.col-lg-6*2' );

			var cols	=	HTML.query( '[data-namespace="payment"] div .col-lg-6' );

				cols.only(0)
					.add( 'h4.text-center{<?php echo _s( 'Effectuer un paiement', 'nexo' );?>}');

				cols.only(0)
					.add( 'div>.input-group.payment-selection>span.input-group-addon{<?php echo _s( 'Choisir un moyen de paiement', 'nexo' );?>}' );
				cols.only(0).query( 'div>.input-group' )
					.add( 'select.form-control' )
					.each( 'ng-model', 'paymentSelected' )
					.each( 'ng-options', 'key as value for ( key, value ) in paymentOptions' )
					.each( 'ng-change', 'loadPaymentOption()' )
					.each( 'ng-disabled', 'disablePaymentsOptions' );

				cols.only(0)
					.add( 'h4>strong.text-center{<?php echo _s( 'Reste à payer', 'nexo' );?>}' )
					.each( 'ng-hide', 'disablePaymentsOptions' )
					.add( 'span.amount-to-pay' )
					.textContent	=	' :  {{ order.BALANCE | moneyFormat }}';

				cols.only(0)
					.add( 'div.payment-option-box' );

				cols.only(0)
					.add( 'div.notice-wrapper.alert.alert-info' ).textContent	=	'{{noticeText}}';

				cols.only(1)
					.add( 'h4.text-center{<?php echo _s( 'Historique des paiements', 'nexo' );?>}' );

				cols.only(1)
					.add( 'table.table.table-bordered>thead>tr.payment-history-thead>td*4' );

				cols.only(1)
					.query( 'table' )
					.add( 'tbody.payment-history' );

				cols.only(1)
					.each( 'class', 'col-lg-6 payment-history-col' );

				cols.query( '.notice-wrapper' ).each( 'ng-show', 'showNotice' );

				$( '.payment-history-col' ).attr( 'style', 'height:{{ wrapperHeight }}px;overflow-y: scroll;' );

			var	tableHeadTD						=	HTML.query( '.payment-history-thead td' );
				tableHeadTD.only(0).textContent	=	'<?php echo _s( 'Montant', 'nexo' );?>';
				tableHeadTD.only(1).textContent	=	'<?php echo _s( 'Caissier', 'nexo' );?>';
				tableHeadTD.only(2).textContent	=	'<?php echo _s( 'Mode de Paiement', 'nexo' );?>';
				tableHeadTD.only(3).textContent	=	'<?php echo _s( 'Date', 'nexo' );?>';

			HTML.query( '[data-namespace="payment"]' ).only(0).add( 'my-spinner' ).each( 'namespace', 'spinner' );

			$( '[data-namespace="payment"]' ).html( $compile( $( '[data-namespace="payment"]' ).html() )($scope) );

			$scope.openSpinner();

			$http.get(
				'<?php echo site_url( array( 'rest', 'nexo', 'order' ) );?>' + '/' +
				$scope.order_id + '?<?php echo store_get_param( null );?>',
			{
				headers			:	{
					'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
				}
			}).then(function( response ){

				$scope.showNotice				=	false;
				$scope.disablePaymentsOptions	=	false;
				$scope.noticeText				=	'';
				$scope.paymentOptions			=	<?php echo json_encode( $this->config->item( 'nexo_payments_types' ) );?>;
				$scope.paymentSelected			=	null;
				$scope.orderBalance				=	NexoAPI.ParseFloat( response.data[0].TOTAL ) - NexoAPI.ParseFloat( response.data[0].SOMME_PERCU );

				// check if Stripe Payment is disabled

				<?php
				if( @$Options[ store_prefix() . 'nexo_enable_stripe' ] == 'no' ) {
					?>
					delete $scope.paymentOptions.stripe;
					<?php
				}
				?>

				if( response.data[0].TYPE == $scope.order_status.comptant ) {

					$scope.showNotice				=	true;
					$scope.disablePaymentsOptions	=	true;
					$scope.noticeText				=	'<?php echo _s( 'Cette commande n\'a pas besoin de paiement supplémentaire.', 'nexo' );?>';

				} else if( response.data[0].TYPE == $scope.order_status.devis ) {
					$scope.showNotice	=	true;
					$scope.noticeText	=	'<?php echo _s( 'Cette commande peut recevoir un paiement. Veuillez choisir le moyen de paiement que vous souhaitez appliquer à cette commande', 'nexo' );?>';
				} else if( response.data[0].TYPE == $scope.order_status.avance ) {
					$scope.showNotice	=	true;
					$scope.noticeText	=	'<?php echo _s( 'Veuillez choisir le moyen de paiement que vous souhaitez appliquer à cette commande', 'nexo' );?>';
				}


				$http({
					headers	:	$scope.ajaxHeader,
					url		:	'<?php echo site_url( array( 'rest', 'nexo', 'order_payment' ) );?>/' + $scope.order.CODE + '?<?php echo store_get_param( null );?>',
					method	:	'GET'
				}).then(function( response ) {

					$scope.order.HISTORY	=	response.data

					HTML.query( '.payment-history' )
						.add( 'tr' )
						.each( 'ng-repeat', 'payment in order.HISTORY | orderBy : "DATE_CREATION" : true' )
						.add( 'td' )
						.textContent	=	'{{ payment.MONTANT | moneyFormat }}';

					HTML.query( '.payment-history tr' )
						.add( 'td' )
						.textContent	=	'{{ payment.AUTHOR_NAME }}';

					HTML.query( '.payment-history tr' )
						.add( 'td' )
						.textContent	=	'{{ payment.PAYMENT_TYPE | paymentName }}';

					HTML.query( '.payment-history tr' )
						.add( 'td' )
						.textContent	=	'{{ payment.DATE_CREATION }}';

					$( '[data-namespace="payment"]' ).html( $compile( $( '[data-namespace="payment"]' ).html() )($scope) );

					$scope.closeSpinner();
				});
			});

		}
		else if( option.namespace == 'refund' ) {

			$( '[data-namespace="' + option.namespace + '"]' ).html('');

			$scope.toRefund			=	0;
			$scope.currentValue		=	0;

			HTML.query( '[data-namespace="' + option.namespace + '"]' ).only(0).add( 'div.row>div.col-lg-8.col-md-8.refund-row' ); // .each( 'style', 'height:{{ wrapperHeight | number }}px;overflow-y: scroll;"' );
			HTML.query( '[data-namespace="' + option.namespace + '"] div.row' ).only(0).add( 'div.col-lg-4.col-md-4.cart' );
			// Title
			HTML.query( '.refund-row' ).only(0).add( 'h4.text-center{<?php echo _s( 'Remboursement', 'nexo' );?>}' );
			// Defective Stock Table
			HTML.query( '.refund-row' ).only(0).add( 'table.table.table-bordered.refund-table' ).add( 'thead>tr>td.text-center*8' );
			HTML.query( '.refund-table td' ).only(0).textContent	=	'<?php echo _s( 'Nom du produit', 'nexo' );?>';
			HTML.query( '.refund-table td' ).only(1).textContent	=	'<?php echo _s( 'Qte défectueuse', 'nexo' );?>';
			HTML.query( '.refund-table td' ).only(2).add( 'i.fa.fa-arrow-right.toCurrentStock' );
			HTML.query( '.refund-table td' ).only(3).add( 'i.fa.fa-arrow-left.toDefective' );
			HTML.query( '.refund-table td' ).only(4).textContent	=	'<?php echo _s( 'Qte vendue', 'nexo' );?>';
			HTML.query( '.refund-table td' ).only(5).add('i.fa.fa-arrow-right' );
			HTML.query( '.refund-table td' ).only(6).add('i.fa.fa-arrow-left' );
			HTML.query( '.refund-table td' ).only(7).textContent	=	'<?php echo _s( 'Qte en état', 'nexo' );?>';

			// Cart Table
			HTML.query( '.cart' ).only(0).add( 'h4.text-center{<?php echo _s( 'Etat du remboursement', 'nexo' );?>}' );
			HTML.query( '.cart' ).only(0).add( 'h4{<?php echo _s( 'Valeur:', 'nexo' );?>}>span.current-order-value.pull-right' ).textContent	=	'{{ order.TOTAL | moneyFormat }}';
			HTML.query( '.cart' ).only(0).add( 'h4{<?php echo _s( 'Remboursement:', 'nexo' );?>}>span.current-order-value.pull-right' ).textContent	=	'{{ toRefund | moneyFormat }}';
			HTML.query( '.cart' ).only(0).add( 'button.btn.btn-primary{<?php echo _s( 'Confirmer le remboursement', 'nexo' );?>}' ).each( 'ng-click', 'proceedRefund()' );

			HTML.query( '.cart' ).only(0).add( 'button.pull-right.btn.btn-primary{<?php echo _s( 'Imprimer le reçu', 'nexo' );?>}' ).each( 'ng-click', 'printRefundReceipt()' );

			HTML.query( '[data-namespace="' + option.namespace + '"]' ).only(0).add( 'my-spinner' ).each( 'namespace', 'spinner' );

			$( '.refund-row' ).attr( 'style', 'height:{{ wrapperHeight }}px;overflow-y: scroll;' );

			$( '[data-namespace="' + option.namespace + '"]' ).html( $compile( $( '[data-namespace="' + option.namespace + '"]' ).html() )($scope) );

			$scope.openSpinner();

			$http.get( '<?php echo site_url( array( 'rest', 'nexo', 'order_items_defectives' ) );?>/' + $scope.orderCode + '?<?php echo store_get_param( null );?>', {
				headers		:	$scope.ajaxHeader
			}).then(function( response ) {
				$scope.orderItems		=	response.data;

				_.each( $scope.orderItems, function( value, key ) {

					if( $scope.orderItems[key].CURRENT_DEFECTIVE_QTE == null ) {
						$scope.orderItems[key].CURRENT_DEFECTIVE_QTE = 0;
					}

					$scope.orderItems[key].CURRENT_USABLE_QTE 	=  0;

					if( $scope.orderItems[key].STOCK_ENABLED != '1' ) {
						$scope.orderItems[key].QUANTITY				=	'...';
						$scope.orderItems[key].QUANTITE_RESTANTE	=	'...';
						$scope.orderItems[key].DEFECTUEUX			=	'...';
					}

					// Manage unlimited items
				});

				HTML.query( '.refund-table' ).only(0).add( 'tbody>tr' ).each( 'ng-repeat', 'item in orderItems' );
				HTML.query( '.refund-table tbody tr' ).only(0).add( 'td.text-center*8' );

				HTML.query( '.refund-table tbody tr td' ).only(0).textContent = '{{ item.DESIGN }}';
				HTML.query( '.refund-table tbody tr td' ).only(1).textContent = '{{ item.CURRENT_DEFECTIVE_QTE }}/{{ item.DEFECTUEUX }}';
				HTML.query( '.refund-table tbody tr td' ).only(2).each( 'ng-click', 'addTo( "def_to_stock", item.CODEBAR )' ).add( 'i.fa.fa-arrow-right' );
				HTML.query( '.refund-table tbody tr td' ).only(3).each( 'ng-click', 'addTo( "defective", item.CODEBAR )' ).add( 'i.fa.fa-arrow-left' );
				HTML.query( '.refund-table tbody tr td' ).only(4).textContent = '{{item.QUANTITE}}/{{item.QUANTITE_VENDU}}';
				HTML.query( '.refund-table tbody tr td' ).only(5).each( 'ng-click', 'addTo( "active", item.CODEBAR )' ).add( 'i.fa.fa-arrow-right' );
				HTML.query( '.refund-table tbody tr td' ).only(6).each( 'ng-click', 'addTo( "act_to_stock", item.CODEBAR )' ).add( 'i.fa.fa-arrow-left' );
				HTML.query( '.refund-table tbody tr td' ).only(7).textContent = '{{ item.CURRENT_USABLE_QTE }}/{{ item.QUANTITE_RESTANTE }}';

				$( '[data-namespace="refund"] .refund-table' ).html( $compile( $( '[data-namespace="refund"] .refund-table' ).html() )($scope) );

				$scope.closeSpinner();
			});

		}
	}

	/**
	 * Load Grand Spinner
	**/

	$scope.loadGrandSpinner		=	function(){

		$scope.showGrandSpinner		=	false;

		if( angular.element( '.modal-content .grandSpinnerWrapper' ).length == 0 ) {
			angular.element( '.modal-content' ).append( '<div class="grandSpinnerWrapper"><grand-spinner/></div>' );
			$( '.modal-content .grandSpinnerWrapper' ).html( $compile( $( '.modal-content .grandSpinnerWrapper' ).html() )($scope) );
		}
	}

	/**
	 * Load Payment Option
	**/

	$scope.loadPaymentOption	=	function(){

		if( $scope.paymentSelected == 'cash' ) {

			$scope.paymentDisabled	=	true;

			$( '.payment-option-box' ).html( $compile( '<cash-payment/>' )($scope) );

		} else if( $scope.paymentSelected == 'bank' ) {

			$scope.paymentDisabled	=	true;

			$( '.payment-option-box' ).html( $compile( '<bank-payment/>' )($scope) );

		} else if( $scope.paymentSelected == 'stripe' ) {

			$scope.paymentDisabled	=	true;

			$( '.payment-option-box' ).html( $compile( '<stripe-payment/>' )($scope) );

		} else if( $scope.paymentSelected == 'creditcard' ) {

			$scope.paymentDisabled	=	true;

			$( '.payment-option-box' ).html( $compile( '<creditcard-payment/>' )($scope) );

		} else if( $scope.paymentSelected == 'cheque' ) {

			$scope.paymentDisabled	=	true;

			$( '.payment-option-box' ).html( $compile( '<cheque-payment/>' )($scope) );

		} else {
			$( '.payment-option-box' ).html('');
		}
	}

	/**
	 * Load Stripe Payment
	**/

	$scope.loadStripeCheckout	=	function(){
		// __stripeCheckout
		<?php if( in_array(strtolower(@$Options[ store_prefix() . 'nexo_currency_iso' ]), $this->config->item('nexo_supported_currency')) ) {
			?>
			var	CartToPayLong		=	numeral( $scope.cashPaymentAmount ).multiply(100).value();
			<?php
		} else {
			?>
			var	CartToPayLong		=	NexoAPI.Format( $scope.cashPaymentAmount, '0.00' );
			<?php
		};?>

		__stripeCheckout.run( CartToPayLong, $scope.order.CODE, $scope );

		__stripeCheckout.handler.open({
			name			: 	'<?php echo @$Options[ store_prefix() . 'site_name' ];?>',
			description		: 	'<?php echo _s( 'Compléter le paiement d\'une commande : ', 'nexo' );?>' + $scope.order.CODE,
			amount			: 	CartToPayLong,
			currency		: 	'<?php echo @$Options[ store_prefix() . 'nexo_currency_iso' ];?>'
		});
	};

	/**
	 * Toggle Tab
	**/

	$scope.toggleTab			=	function( option ){

		_.each( $scope.options, function( value, key ) {
			$scope.options[key].visible		=	false;
			$scope.options[key].class		=	'default';
		});

		option.visible			=	true;
		option.class			=	'active'

		$scope.loadContent( option );
	};

	/**
	 * Open Details
	**/

	$scope.openDetails			=	function( order_id, order_code ) {

		$scope.order_id		=	order_id;
		$scope.orderCode	=	order_code;
		$scope.options		=	$scope.createOptions();

		var content			=
		'<h4 class="text-center"><?php echo _s( 'Options de la commande', 'nexo' );?> : {{ orderCode }}</h4>' +
		'<div class="row" style="border-top:solid 1px #EEE;">' +
			'<div class="col-lg-2 col-md-2 col-sm-2" style="padding:0px;margin:0px;">' +
				'<div class="list-group">' +
				  '<a style="border-radius:0;border-left:0px; border-right:0px;" data-menu-namespace="{{ option.namespace }}" href="#" ng-repeat="option in options" ng-click="toggleTab( option )" class="list-group-item {{ option.class }}"><i class="{{ option.icon }}"></i> {{ option.title }}</a>' +
				'</div>' +
			'</div>' +
			'<div class="col-lg-10 col-md-10 col-sm-10 details-wrapper" style="border-left:solid 1px #EEE;">' +
				'<div ng-repeat="option in options" ng-show="option.visible" data-namespace="{{ option.namespace }}" >' +
				'</div>' +
			'</div>' +
		'</div>';

		NexoAPI.Bootbox().alert( {
			message		:	'<dom></dom>',
			onEscape	:	false,
			closeButton	:	false
		});

		$( 'dom' ).append( $compile(content)($scope) );


		$( '.modal-dialog' ).css( 'width', '98%' );
		$( '.modal-body' ).css( 'padding-bottom', 0 );

		$scope.wrapperHeight		=	$scope.window.height - 180;

		// Default Tab is loaded
		$scope.toggleTab( $scope.options.details );

		// Load Grand Spinner
		$scope.loadGrandSpinner();

	}

	/**
	 * Proceed Payment
	**/

	$scope.proceedPayment		=	function( paymentType, askConfirm, callback ) {

		askConfirm		=	typeof askConfirm == 'undefined' ? true : askConfirm;

		if( askConfirm ) {

			bootbox.confirm( '<?php echo _s( 'Souhaitez-vous confirmer ce paiement ?', 'nexo' );?>', function( action ) {
				if( action ) {
					$http({
						url		:	'<?php echo site_url( array( 'rest', 'nexo', 'order_payment' ) );?>/' + $scope.order.ID + '<?php echo store_get_param( '?' );?>',
						method	:	'POST',
						data	:	{
							amount		:	$scope.cashPaymentAmount,
							author		:	'<?php echo User::id();?>',
							date		:	'<?php echo date_now();?>',
							order_code	:	$scope.order.CODE,
							payment_type:	paymentType
						},
						headers			:	$scope.ajaxHeader
					}).then(function( response ){
						$scope.loadContent( $scope.createOptions().payment );
						if( typeof callback == 'function' ) {
							callback( response );
						}
					});
				}
			});

		} else {

			$http({
				url		:	'<?php echo site_url( array( 'rest', 'nexo', 'order_payment' ) );?>/' + $scope.order.ID + '<?php echo store_get_param( '?' );?>',
				method	:	'POST',
				data	:	{
					amount		:	$scope.cashPaymentAmount,
					author		:	'<?php echo User::id();?>',
					date		:	'<?php echo date_now();?>',
					order_code	:	$scope.order.CODE,
					payment_type:	paymentType
				},
				headers			:	$scope.ajaxHeader
			}).then(function( response ){
				$scope.loadContent( $scope.createOptions().payment );
				callback( response );
			});

		}
	}

	/**
	 * PRoceed Refund
	**/

	$scope.proceedRefund		=	function(){
		if( $scope.toRefund > 0 ) {
			NexoAPI.Bootbox().confirm( '<?php echo _s( 'Souhaitez-vous confirmer le remboursement de cette commande ?', 'nexo' );?>', function( action ){
				if( action ) {
					$scope.openSpinner( 'grand' );
					$http({
						headers		:	$scope.ajaxHeader,
						method		:	'POST',
						url			:	'<?php echo site_url( array( 'rest', 'nexo', 'order_refund' ) );?>' + '/' + $scope.order.CODE + '?<?php echo store_get_param( null );?>',
						data		:	{
							items	:	$scope.orderItems,
						  	author	:	<?php echo User::id();?>,
						  	date		:	tendoo.now()
						}

					}).then(function( data ) {
						$scope.closeSpinner( 'grand' );
						NexoAPI.Bootbox().alert( '<?php echo _s( 'Le remboursement a correctement été effectué.', 'nexo' );?>' );
						$scope.loadContent( $scope.createOptions().refund );
					}, function( data ) {
						$scope.closeSpinner( 'grand' );
					  	NexoAPI.Bootbox().alert( '<?php echo _s( 'Le remboursement n\'a pas pu avoir lieu. Une erreur s\'est produite durant l\'opération', 'nexo' );?>' );
					});
				}
			});
		} else {
			NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Veuillez envoyer un produit dans le stock défectueux ou actif avant de valider le remboursement.', 'nexo' );?>' );
		}
	}

	/**
	 *  Total Returned item quantity
	 *  @param object returned items
	 *  @return int/float
	**/

	$scope.totalQuantity			=	function( obj ) {
		var total 	=	0;
		_.each( obj, function( value ){
			total 	+=	parseInt( value.QUANTITE );
		});

		return total;
	}

	/**
	 *  Total Amount for retunred items
	 *  @param object returned item object
	 *  @return amounts
	**/

	$scope.totalAmount 				=	function( obj ) {
		var total 		=	0;
		_.each( obj, function( value ){
			total 		+=	( parseFloat( value.PRIX ) * parseInt( value.QUANTITE ) );
		});

		return total;
	}

	/**
	 *  Print Refund Receipt
	 *  @param
	 *  @return
	**/

	$scope.printRefundReceipt		=	function( $order_id ){
		$scope.openSpinner( 'grand' );
		$http({
			headers		:	$scope.ajaxHeader,
			method		:	'GET',
			url			:	'<?php echo site_url( array( 'rest', 'nexo', 'order_with_stock' ) );?>' + '/' + $scope.order.CODE + '?<?php echo store_get_param( null );?>',
			data		:	{
				items	:	$scope.orderItems,
				author	:	<?php echo User::id();?>,
				date		:	tendoo.now()
			}
		}).then(function( returned ) {
			$scope.order_items 		=	returned.data;
			$.ajax({
				url 	:	'<?php echo site_url([ 'dashboard', store_slug(), 'nexo', 'print', 'order_refund', store_get_param('?') ] );?>',
				success 	:	function( data ) {
					// replace template
					data 	=	data.replace( ':orderCode', $scope.order_items[0].CODE );
					data 	=	data.replace( ':orderDate', $scope.order_items[0].DATE );
					data 	=	data.replace( ':orderUpdated', $scope.order_items[0].DATE_MOD );
					data 	=	data.replace( ':orderId', $scope.order_items[0].ID );
					data 	=	data.replace( ':orderNote', $scope.order_items[0].DESCRIPTION );
					data 	=	data.replace( ':orderStatus', $scope.order_items[0].TYPE );
					data 	=	data.replace( ':orderCashier', $scope.order_items[0].ORDER_CASHIER );
					data 	=	data.replace( ':orderPaymentType', $scope.order_items[0].PAYMENT_TYPE );
					$( 'body' ).append( '<div class="hidden printRefundHidden">' + data + '</div>' );
					
					$timeout( function(){
						$( '.printRefundHidden' ).html( $compile( $( '.printRefundHidden' ).html() )( $scope ) );
						$timeout( function(){
							NexoAPI.Popup( $( '.printRefundHidden' ).html() );
							$( '.printRefundHidden' ).remove();
						}, 100 );
					}, 100 );
				}
			})
			$scope.closeSpinner( 'grand' );
			// $scope.closeSpinner( 'grand' );
			// $scope.returnedItems 	=	returned.data;

			// $( 'body' ).append( '<iframe style="display:none;" id="CurrentReceipt" name="CurrentReceipt" src="></iframe>' );
			// window.frames["CurrentReceipt"].focus();
			// window.frames["CurrentReceipt"].print();

			// setTimeout( function(){
			// 	$( '#CurrentReceipt' ).remove();
			// }, 5000 );


		}, function( data ) {
			$scope.closeSpinner( 'grand' );
			NexoAPI.Bootbox().alert( '<?php echo _s( 'Une erreur s\'est produite durant l\'opération', 'nexo' );?>' );
		});
	}

	$scope.totalQuantity    =   function() {
		var total   =   0;
		angular.forEach( $scope.order_items, function( value ) {
			total   +=  parseInt( value.QUANTITE );
		});
		return total;
	}

	$scope.totalAmount      =   function(){
		var total   =   0;
		angular.forEach( $scope.order_items, function( value ) {
			total   +=  ( parseInt( value.QUANTITE ) * parseFloat( value.PRIX ) );
		});
		return total;
	}


	/**
	 * Show Spinner
	**/

	$scope.showSpinner			=	false;

	$scope.openSpinner			=	function( namespace ){
		if( namespace == 'grand' ) {
			$( '.modal-content .grandSpinnerWrapper' ).html( $compile( $( '.modal-content .grandSpinnerWrapper' ).html() )($scope) );
			$scope.showGrandSpinner		=	true;
		} else {
			$scope.showSpinner			=	true;
		}
	}

	$scope.closeSpinner			=	function( namespace ){
		if( namespace == 'grand' ) {
			$( '.modal-content .grandSpinnerWrapper' ).html( $compile( $( '.modal-content .grandSpinnerWrapper' ).html() )($scope) );
			$scope.showGrandSpinner		=	false;
		} else {
			$scope.showSpinner			=	false;
		}
	}

	$scope.editWithRegister 				=	function( orderid, register_link ){
		// if a register is disabled, then open the order directly
		<?php if( store_option( 'enable_registers' ) != 'yes' ):?>
		document.location 	=	register_link + orderid;
		return;
		<?php endif;?>

		$this	=	$( this );
		$.ajax( '<?php echo site_url( array( 'rest', 'nexo', 'registers?store_id=' . get_store_id() ) );?>', {
			success	:	function( data ){

				var register_lists	=	'';

				var oneOpen 		=	false;

				_.each( data, function( value, key ) {
					if( value.STATUS == 'opened' ) {
						register_lists	+=	
						'<tr>' +
							'<td>' + value.NAME + '</td>' +
							'<td><a class="btn btn-primary btn-sm" href="<?php echo site_url( array( 'dashboard', store_slug(), 'nexo', 'registers', '__use' ) );?>/' + value.ID + '/' + orderid + '"><?php echo _s( 'Utiliser cette caisse', 'nexo' );?></a></td>' +
						'</tr>';
						oneOpen 		=	true;
					}
				});

				if( oneOpen == false ) {
					register_lists	+=	
					'<tr>' +
						'<td colspan="2"><?php echo _s( 'Aucun caisse n\'est ouverte.', 'nexo' );?></td>' +
					'</tr>';
				}


				var dom		=	'<h4><?php echo _s( 'Selectionner une caisse', 'nexo' );?></h4>' +
				'<br>' +
				'<table class="table table-bordered table-striped">' +
					'<thead>' +
						'<tr>' +
							'<td><?php echo _s( 'Caisse', 'nexo' );?></td>' +
							'<td width="200"><?php echo _s( 'Action', 'nexo' );?></td>' +
						'</tr>' +
					'</thead>' +
					'<tbody>' +
						register_lists +
					'</tbody>' +
				'</table>' +
				'<br>' +
				'<?php echo tendoo_info( _s( 'Les caisses affichées sont celles actuellement ouvertes. Assurez-vous de choisir une caisse ayant une de vos sessions', 'nexo' ) );?>';

				NexoAPI.Bootbox().alert( dom, function( action ) {

				});
			},
			dataType:"json",
			error: function(){
				bootbox.alert( '<?php echo _s( 'Une erreur s\'est produite durant le chargement des caisses.', 'nexo' );?>' );
			}
		});
		return false;
	}

	$(document).ready(function(e) {
	   // $( '.modal-content' ).html( $compile( $( '.modal-content' ).html() )( $scope ) );
	});
	$( document ).ajaxComplete(function(){
		$( '#ajax_list .table.table-striped' ).html( $compile( $( '#ajax_list .table.table-striped' ).html() )( $scope ) );
	});


}]);
</script>

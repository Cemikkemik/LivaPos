<script>
/**
 * Create Controller
**/

tendooApp.controller( 'nexo_order_list', [ '$scope', '$compile', '$http', '__orderStatus', '__paymentName', '__windowSplash', '__stripeCheckout', function( $scope, $compile, $http, __orderStatus, __paymentName, __windowSplash, __stripeCheckout ) {	

	$scope.order_status		=	{
		comptant			:	'nexo_order_comptant',
		avance				:	'nexo_order_advance',
		complete			:	'nexo_order_complete',
		devis				:	'nexo_order_devis'
	}
	
	$scope.ajaxHeader		=	{
		'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
	}
	
	$scope.window			=	{
		height				:	window.innerHeight < 600 ? 600 : window.innerHeight
	}
	
	/**
	 * Control Cash Payment
	**/
	
	$scope.controlCashAmount	=	function(){
		if( parseFloat( $scope.cashPaymentAmount ) > 0 && parseFloat( $scope.cashPaymentAmount ) <= parseFloat( $scope.orderBalance ) ) {
			$scope.paymentDisabled		=	false;
		} else if( parseFloat( $scope.cashPaymentAmount ) > parseFloat( $scope.orderBalance ) ) {
			$scope.cashPaymentAmount	=	parseFloat( $scope.orderBalance );
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
				icon				:	'fa fa-eye'
			}, cancel			:	{
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
			}
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
						$scope.order.GRANDTOTAL	+=	( value.QUANTITE * value.PRIX_DE_VENTE );
					});
					
					// Remaining
					$scope.order.BALANCE		=	Math.abs( NexoAPI.ParseFloat( $scope.order.TOTAL - $scope.order.SOMME_PERCU ) );
					
					var content		=	
					'<div class="row">' +
						'<div class="col-lg-8" style="height:{{ window.height / 1.5 }}px;overflow-y: scroll;">' +
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
										'<td>{{ item.DESIGN }}</td>' +
										'<td>{{ item.SKU }}</td>' +
										'<td>{{ item.PRIX_DE_VENTE | moneyFormat }}</td>' +
										'<td>{{ item.QUANTITE }}</td>' +
										'<td>{{ item.PRIX_DE_VENTE * item.QUANTITE | moneyFormat }}</td>' +
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
									'<tr>' +
										'<td colspan="4"><strong><?php echo _s( 'Total', 'nexo' );?></strong></td>' +
										'<td>{{ order.TOTAL | moneyFormat }}</td>' +
									'</tr>' +
									'<tr>' +
										'<td colspan="4"><strong><?php echo _s( 'Payé (+)', 'nexo' );?></strong></td>' +
										'<td>{{ order.SOMME_PERCU | moneyFormat }}</td>' +
									'</tr>' +
									'<tr>' +
										'<td colspan="4"><strong><?php echo _s( 'Reste (=)', 'nexo' );?></strong></td>' +
										'<td>{{ order.BALANCE | moneyFormat }}</td>' +
									'</tr>' +
								'</tbody>' +
							'</table>' +
						'</div>' +
						'<div class="col-lg-4">' +
							'<h5><?php echo _s( 'Détails sur la commande', 'nexo' );?></h5>' +
							'<ul class="list-group">' +
							  '<li class="list-group-item"><strong><?php echo _s( 'Auteur :', 'nexo' );?></strong> {{ order.AUTHOR_NAME }}</li>' +
							  '<li class="list-group-item"><strong><?php echo _s( 'Effectué le :', 'nexo' );?></strong> {{ order.DATE_CREATION | date:short }}</li>' +
							  '<li class="list-group-item"><strong><?php echo _s( 'Client :', 'nexo' );?></strong> {{ order.CLIENT_NAME }}</li>' +
							  '<li class="list-group-item"><strong><?php echo _s( 'Statut :', 'nexo' );?></strong> {{ order.TYPE | orderStatus }}</li>' +
							'</ul>' +
						'</div>' +
					'</div>';
					
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
					.textContent	=	' :  {{ orderBalance | moneyFormat }}';
					
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
				
				$( '.payment-history-col' ).attr( 'style', 'height:{{ window.height / 1.5 }}px;overflow-y: scroll;' );
				
			var	tableHeadTD						=	HTML.query( '.payment-history-thead td' );
				tableHeadTD.only(0).textContent	=	'<?php echo _s( 'Montant', 'nexo' );?>';
				tableHeadTD.only(1).textContent	=	'<?php echo _s( 'Caissier', 'nexo' );?>';
				tableHeadTD.only(2).textContent	=	'<?php echo _s( 'Mode de Paiement', 'nexo' );?>';
				tableHeadTD.only(3).textContent	=	'<?php echo _s( 'Date', 'nexo' );?>';

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
				$scope.paymentOptions			=	<?php echo json_encode( $this->config->item( 'nexo_payment_types' ) );?>;
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
					url		:	'<?php echo site_url( array( 'rest', 'nexo', 'order_payment' ) );?>/' + $scope.order.CODE,
					method	:	'GET'
				}).then(function( response ) {
					
					$scope.order.HISTORY	=	response.data
					
					cols.only(1)
						.query( '.payment-history' )
						.add( 'tr' )
						.each( 'ng-repeat', 'payment in order.HISTORY | orderBy : "DATE_CREATION" : true' )
						.add( 'td' )
						.textContent	=	'{{ payment.MONTANT | moneyFormat }}';
						
					cols.only(1)
						.query( '.payment-history tr' )
						.add( 'td' )
						.textContent	=	'{{ payment.AUTHOR_NAME }}';
						
					cols.only(1)
						.query( '.payment-history tr' )
						.add( 'td' )
						.textContent	=	'{{ payment.PAYMENT_TYPE | paymentName }}';
						
					cols.only(1)
						.query( '.payment-history tr' )
						.add( 'td' )
						.textContent	=	'{{ payment.DATE_CREATION }}';
						
					$( '[data-namespace="payment"]' ).html( $compile( $( '[data-namespace="payment"]' ).html() )($scope) );
				});
			});
			
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
			$scope.options[key].visible	=	false;
			$scope.options[key].class	=	'default';
		});
		
		option.visible			=	true;
		option.class			=	'active'
		
		$scope.loadContent( option );
	};
	
	/**
	 * Open Details
	**/
	
	$scope.openDetails			=	function( order_id ) {
		
		$scope.order_id		=	order_id;
		$scope.orderCode	=	'';	
		$scope.options		=	$scope.createOptions();
		
		var content			=	
		'<h4 class="text-center"><?php echo _s( 'Options de la commande', 'nexo' );?> : {{ orderCode }}</h4>' +
		'<div class="row" style="border-top:solid 1px #EEE;">' +
			'<div class="col-lg-2" style="padding:0px;margin:0px;">' +
				'<div class="list-group">' +
				  '<a style="border-radius:0;border-left:0px; border-right:0px;" data-menu-namespace="{{ option.namespace }}" href="#" ng-repeat="option in options" ng-click="toggleTab( option )" class="list-group-item {{ option.class }}"><i class="{{ option.icon }}"></i> {{ option.title }}</a>' +
				'</div>' +
			'</div>' +
			'<div class="col-lg-10" style="border-left:solid 1px #EEE;padding-top:10px;height:{{ window.height / 1.5 }}px;">' +
				'<div ng-repeat="option in options" ng-show="option.visible" data-namespace="{{ option.namespace }}" >' +					
				'</div>' +
			'</div>' +
		'</div>';
		
		bootbox.alert( '<dom></dom>' );
		
		$( 'dom' ).append( $compile(content)($scope) );
		
		$( '.modal-dialog' ).css( 'width', '80%' );
		$( '.modal-body' ).css( 'padding-bottom', 0 );
		
		// Default Tab is loaded
		$scope.toggleTab( $scope.options.details );
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
}]);
</script>
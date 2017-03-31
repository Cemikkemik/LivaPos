<?php
global $Options;
$this->load->library('parser');

$data[ 'template' ]						=	array();

$data[ 'template' ][ 'shop_name' ]				=	@$Options[ store_prefix() . 'site_name' ];
$data[ 'template' ][ 'shop_pobox' ]				=	@$Options[ store_prefix() . 'nexo_shop_pobox' ];
$data[ 'template' ][ 'shop_fax' ]				=	@$Options[ store_prefix() . 'nexo_shop_fax' ];
$data[ 'template' ][ 'shop_email' ]		        =	@$Options[ store_prefix() . 'nexo_shop_email' ];
$data[ 'template' ][ 'shop_street' ]	        =	@$Options[ store_prefix() . 'nexo_shop_street' ];
$data[ 'template' ][ 'shop_phone' ]		        =	@$Options[ store_prefix() . 'nexo_shop_phone' ];

$left_side 	=	xss_clean( json_encode(
	get_instance()->parser->parse_string(
		@$Options[ store_prefix() . 'receipt_col_1' ],
		$data[ 'template' ],
		true
	)
) );

$right_side 	=	xss_clean( json_encode(
	get_instance()->parser->parse_string(
		@$Options[ store_prefix() . 'receipt_col_2' ],
		$data[ 'template' ],
		true
	)
) );
?>
<script>
/**
 * Create Controller
**/

tendooApp.directive( 'cashPayment', function() {

	HTML.add('div.cache-angular');

	HTML.query( '.cache-angular' )
		.add( 'form>div.input-group.pay-wrapper>span.input-group-addon{<?php echo _s( 'Valeur du paiement', 'nexo' );?>}' );

	HTML.query( '.cache-angular' )
		.add( 'br' );

	HTML.query( '.cache-angular' )
		.add( 'input.btn.btn-primary' )
		.each( 'value', '<?php echo _s( 'Payer', 'nexo' );?>' )
		.each( 'type', 'button' )
		.each( 'ng-disabled', 'paymentDisabled' )
		.each( 'ng-click', 'proceedPayment( "cash" )' );

	HTML.query( '.cache-angular' )
		.add( 'br' );

	HTML.query( '.cache-angular' )
		.add( 'br' );

	HTML.query( '.pay-wrapper' )
		.add( 'input.form-control.pay-field' )
		.each( 'ng-model', 'cashPaymentAmount' )
		.each( 'ng-change', 'controlCashAmount()' );

	var template	=	$( '.cache-angular' ).html();

	$( '.cache-angular' ).remove();

	return {
		template	:	template
	}
});

tendooApp.directive( 'bankPayment', function() {

	HTML.add('div.cache-angular');

	HTML.query( '.cache-angular' )
		.add( 'form>div.input-group.pay-wrapper>span.input-group-addon{<?php echo _s( 'Valeur du virement', 'nexo' );?>}' );

	HTML.query( '.cache-angular' )
		.add( 'br' );

	HTML.query( '.cache-angular' )
		.add( 'input.btn.btn-primary' )
		.each( 'value', '<?php echo _s( 'Valider le virement', 'nexo' );?>' )
		.each( 'type', 'button' )
		.each( 'ng-disabled', 'paymentDisabled' )
		.each( 'ng-click', 'proceedPayment( "bank" )' );

	HTML.query( '.cache-angular' )
		.add( 'br' );

	HTML.query( '.cache-angular' )
		.add( 'br' );

	HTML.query( '.pay-wrapper' )
		.add( 'input.form-control.pay-field' )
		.each( 'ng-model', 'cashPaymentAmount' )
		.each( 'ng-change', 'controlCashAmount()' );

	var template	=	$( '.cache-angular' ).html();

	$( '.cache-angular' ).remove();

	return {
		template	:	template
	}
});

tendooApp.directive( 'creditcardPayment', function() {

	HTML.add('div.cache-angular');

	HTML.query( '.cache-angular' )
		.add( 'form>div.input-group.pay-wrapper>span.input-group-addon{<?php echo _s( 'Montant prélevé', 'nexo' );?>}' );

	HTML.query( '.cache-angular' )
		.add( 'br' );

	HTML.query( '.cache-angular' )
		.add( 'input.btn.btn-primary' )
		.each( 'value', '<?php echo _s( 'Valider', 'nexo' );?>' )
		.each( 'type', 'button' )
		.each( 'ng-disabled', 'paymentDisabled' )
		.each( 'ng-click', 'proceedPayment( "creditcard" )' );

	HTML.query( '.cache-angular' )
		.add( 'br' );

	HTML.query( '.cache-angular' )
		.add( 'br' );

	HTML.query( '.pay-wrapper' )
		.add( 'input.form-control.pay-field' )
		.each( 'ng-model', 'cashPaymentAmount' )
		.each( 'ng-change', 'controlCashAmount()' );

	var template	=	$( '.cache-angular' ).html();

	$( '.cache-angular' ).remove();

	return {
		template	:	template
	}
});

/** Cheque Payment **/

tendooApp.directive( 'chequePayment', function() {

	HTML.add('div.cache-angular');

	HTML.query( '.cache-angular' )
		.add( 'form>div.input-group.pay-wrapper>span.input-group-addon{<?php echo _s( 'Montant du chèque', 'nexo' );?>}' );

	HTML.query( '.cache-angular' )
		.add( 'br' );

	HTML.query( '.cache-angular' )
		.add( 'input.btn.btn-primary' )
		.each( 'value', '<?php echo _s( 'Valider', 'nexo' );?>' )
		.each( 'type', 'button' )
		.each( 'ng-disabled', 'paymentDisabled' )
		.each( 'ng-click', 'proceedPayment( "cheque" )' );

	HTML.query( '.cache-angular' )
		.add( 'br' );

	HTML.query( '.cache-angular' )
		.add( 'br' );

	HTML.query( '.pay-wrapper' )
		.add( 'input.form-control.pay-field' )
		.each( 'ng-model', 'cashPaymentAmount' )
		.each( 'ng-change', 'controlCashAmount()' );

	var template	=	$( '.cache-angular' ).html();

	$( '.cache-angular' ).remove();

	return {
		template	:	template
	}
});

tendooApp.directive( 'stripePayment', function() {

	HTML.add('div.cache-angular');

	HTML.query( '.cache-angular' )
		.add( 'form>div.input-group.pay-wrapper>span.input-group-addon{<?php echo _s( 'Facturer une carte', 'nexo' );?>}' );

	HTML.query( '.cache-angular' )
		.add( 'br' );

	HTML.query( '.cache-angular' )
		.add( 'input.btn.btn-primary' )
		.each( 'value', '<?php echo _s( 'Facturer', 'nexo' );?>' )
		.each( 'type', 'button' )
		.each( 'ng-disabled', 'paymentDisabled' )
		.each( 'ng-click', 'loadStripeCheckout()' );

	HTML.query( '.cache-angular' )
		.add( 'br' );

	HTML.query( '.cache-angular' )
		.add( 'br' );

	HTML.query( '.pay-wrapper' )
		.add( 'input.form-control.pay-field' )
		.each( 'ng-model', 'cashPaymentAmount' )
		.each( 'ng-change', 'controlCashAmount()' );

	var template	=	$( '.cache-angular' ).html();

	$( '.cache-angular' ).remove();

	return {
		template	:	template
	}
});
</script>

<script type="text/javascript">
	tendooApp.directive( 'refundInvoice', function(){
  		HTML.add( 'div.angular-cache>div.container-fluid>div.row>div.col-md-12.receipt-wrapper' );
		HTML.query( '.receipt-wrapper' ).add( 'h3.text-center').textContent 	=	'<?php echo @$Options[ 'site_name' ];?>';
		HTML.query( '.receipt-wrapper' ).add( 'h4.refund-title.text-center' );
		HTML.query( '.refund-title' ).textContent 	=	'<?php echo _s( 'Facture de remboursement : ', 'nexo' );?>' + '{{ order.CODE }}';

		HTML.query( '.receipt-wrapper' ).add( 'div.row.receipt-details' );
		HTML.query( '.receipt-wrapper' ).add( 'br' );
		HTML.query( '.receipt-details' ).add( 'div.col-lg-6.col-md-6.col-xs-6.receipt-left-side' );
		HTML.query( '.receipt-details' ).add( 'div.col-lg-6.col-md-6.col-xs-6.receipt-right-side' );

		HTML.query( '.receipt-wrapper' )
		.add( 'table.table.table-striped.table-bordered.refund-invoice' );
		HTML.query( '.refund-invoice' ).add( 'thead>tr>td*5');
		HTML.query( '.refund-invoice thead>tr>td' )
		.only(0).textContent	=	'<?php echo _s( 'Nom de l\'article', 'nexo' );?>';

		HTML.query( '.refund-invoice thead>tr>td' )
		.only(1).textContent	=	'<?php echo _s( 'Etat', 'nexo' );?>';

		HTML.query( '.refund-invoice thead>tr>td' )
		.only(2).textContent	=	'<?php echo _s( 'Prix unitaire', 'nexo' );?>';

		HTML.query( '.refund-invoice thead>tr>td' )
		.only(3).textContent	=	'<?php echo _s( 'Quantité', 'nexo' );?>';

		HTML.query( '.refund-invoice thead>tr>td' )
		.only(4).textContent	=	'<?php echo _s( 'Total', 'nexo' );?>';
		HTML.query( '.refund-invoice' ).each( 'style', 'font-size:0.9em' );

		// Table content
		HTML.query( '.refund-invoice' ).add( 'tbody>tr.refund-item-list>td*5' );

		HTML.query( '.refund-invoice' ).add( 'tbody>tr.refund-message>td.refund-notice.hidden-print');

		HTML.query( '.refund-notice' ).each( 'colspan', 5 ).each( 'ng-show', 'returnedItems.length == 0').add( 'p.text-center' ).textContent =	'<?php echo _s( 'Aucun article à afficher', 'nexo' );?>';

		HTML.query( '.refund-item-list' )
		.each( 'ng-repeat', 'item in returnedItems' );

		HTML.query( '.refund-invoice tbody>tr>td' )
		.only(0).textContent 	=	'{{ item.DESIGN }}';

		HTML.query( '.refund-invoice tbody>tr>td' )
		.only(1).textContent 	=	'{{ item.TYPE == "defective" ? "<?php echo _s( 'Défectueux', 'nexo' );?>" : "<?php echo _s( 'En état', 'nexo' );?>" }}';

		HTML.query( '.refund-invoice tbody>tr>td' )
		.only(2).textContent 	=	'{{ item.PRIX | moneyFormat }}';

		HTML.query( '.refund-invoice tbody>tr>td' )
		.only(3).textContent 	=	'{{ item.QUANTITE }}';

		HTML.query( '.refund-invoice tbody>tr>td' )
		.only(4).textContent 	=	'{{ item.QUANTITE * item.PRIX | moneyFormat }}';

		HTML.query( '.refund-invoice' ).add( 'tfoot.refund-footer>tr>td*5' );

		HTML.query( '.refund-invoice tfoot>tr>td' )
		.only(0).textContent 	=	'<?php echo _s( 'Total', 'nexo' );?>';

		HTML.query( '.refund-invoice tfoot>tr>td' )
		.only(3).textContent 	=	'{{ totalQuantity( returnedItems ) }}';

		HTML.query( '.refund-invoice tfoot>tr>td' )
		.only(4).textContent 	=	'{{ totalAmount( returnedItems ) | moneyFormat }}';

		HTML.query( '.refund-footer' ).each( 'ng-show', 'returnedItems.length > 0' );

		$( '.receipt-details .receipt-left-side' ).html( <?php echo $left_side;?> );

		$( '.receipt-details .receipt-right-side' ).html( <?php echo $right_side;?> );

		var template	=	$( '.angular-cache' ).html();

		$( '.angular-cache' ).remove();

		return {
			template	:	template
		}

	})
</script>

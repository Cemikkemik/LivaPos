<?php
global $Options;
$this->load->config( 'nexo' );

$currentRow		=	0;

if( @$Options[ store_prefix() . 'nexo_enable_vat' ] == 'oui' ) {
	$rowNbr		=	7;
} else {
	$rowNbr		=	6;
}
?>
<script>
tendooApp.directive( 'payBoxContent', function(){

	var	paymentTypesObject			= 	v2Checkout.paymentTypesObject;

	HTML.body.add( 'angular-cache' );
	HTML.query( 'angular-cache' ).add( 'div.row.paybox-row').each( 'style', 'margin-left:0px;' );

	HTML.query( '.paybox-row' ).add( 'div.col-lg-2.col-md-2.col-sm-2.col-xs-3.payment-options.bootstrap-tab-menu' );
	HTML.query( '.paybox-row' ).add( 'div.col-lg-7.col-md-7.col-sm-5.col-xs-9.payment-options-content' ).each( 'style', 'padding-left:0px' );
	HTML.query( '.paybox-row' ).add( 'div.col-lg-3.col-md-3.col-sm-5.col-xs-5.cart-details.hidden-xs' ).each( 'style', 'padding-left:0px;overflow-y:scroll;' );

	HTML.query( '.payment-options' ).add( 'div.list-group' );

	// Create menu
	_.each( paymentTypesObject, function( value, key ) {
		HTML.query( '.payment-options div.list-group' )
		.add( 'a.text-left.list-group-item.' + key + '{' + value.text + '}' )
		.each( 'href', 'javascript:void(0)' )
		.each( 'ng-click', 'selectPayment("' + key + '")' )
		.each( 'style', 'margin:0px;border-radius:0px;border:solid 1px #DEDEDE;border-top:solid 0px;border-right:solid 0px;line-height:30px;' )
		.each( 'ng-class', '{ "active" : paymentTypesObject.' + key + '.active }' );
	});

	// Create Content
	_.each( paymentTypesObject, function( value, key ) {

		/**
		 * Only default payment type support keyboard
		**/

		if( ! angular.isDefined( value.isCustom ) ) {

			HTML.query( '.payment-options-content' )
			.add( 'div.tab-wrapper.tab-' + key )
			.each( 'ng-show', 'paymentTypesObject.' + key + '.active' )
			.add( 'default-payment' )
			.each( 'payment'								, 'paymentTypesObject.' + key )
			.each( 'paid_amount'							, 'paidAmount' )
			.each( 'add_payment'							, 'addPayment' )
			.each( 'bind_key_board_event'					, 'bindKeyBoardEvent' )
			.each( 'cancel_payment_edition'					, 'cancelPaymentEdition' )
			.each( 'default_add_payment_text'				, 'defaultAddPaymentText' )
			.each( 'default_add_payment_class'				, 'defaultAddPaymentClass' )
			.each( 'default_selected_payment_text'			, 'defaultSelectedPaymentText' )
			.each( 'default_selected_payment_namespace'		, 'defaultSelectedPaymentNamespace' )
			.each( 'show_cancel_edition_button'				, 'showCancelEditionButton' )
			.each( 'data'									, 'data' );

			HTML.query( '.tab-' + key )
			.add( 'keyboard' )
			.each( 'input_name', key + '-field' )
			.each( 'keyinput', 'keyboardInput' ); // *-field is the class of the payment input

		} else {

			HTML.query( '.payment-options-content' )
			.add( 'div.tab-wrapper.tab-' + key )
			.each( 'ng-show', 'paymentTypesObject.' + key + '.active' )
			.add( key + '-payment' )
			.each( 'payment-name', value.text )
			.each( 'payment'								, 'paymentTypesObject.' + key )
			.each( 'paid_amount'							, 'paidAmount' )
			.each( 'add_payment'							, 'addPayment' )
			.each( 'bind_key_board_event'					, 'bindKeyBoardEvent' )
			.each( 'cancel_payment_edition'					, 'cancelPaymentEdition' )
			.each( 'default_add_payment_text'				, 'defaultAddPaymentText' )
			.each( 'default_add_payment_class'				, 'defaultAddPaymentClass' )
			.each( 'default_selected_payment_text'			, 'defaultSelectedPaymentText' )
			.each( 'default_selected_payment_namespace'		, 'defaultSelectedPaymentNamespace' )
			.each( 'show_cancel_edition_button'				, 'showCancelEditionButton' )
			.each( 'data'									, 'data' );

		}

		// Display Cart details on payment option content, when screen is Xtra Small
		HTML.query( 'div.tab-wrapper.tab-' + key )
		.add( 'div.hidden-sm.hidden-md.hidden-lg.hidden-payment-list.payment-list-' + key )
		.add( 'h4.text-center' )
		.each( 'style', 'margin:10px 0;' )
		.textContent	=	'<?php echo _s( 'Liste des paiements', 'nexo' );?>';

		HTML.query( '.payment-list-' + key )
		.add( 'ul.list-group>li.list-group-item.item-one' );

		HTML.query( '.payment-list-' + key + ' .item-one' )
		.textContent	=	'{{ payement.text }}';

		HTML.query( '.payment-list-' + key + ' .item-one' )
		.add( 'span.pull-right' )
		.each( 'style', 'margin-right:80px' )
		.textContent	=	'{{ payement.amount | moneyFormat }}';

		HTML.query( '.payment-list-' + key + ' .item-one' )
		.each( 'ng-repeat', 'payement in paymentList' )
		.add( 'span.btn.btn-warning.btn-xs.pull-right' )
		.each( 'style', 'position: absolute;right: 0;top: -1px;height: 42px;border-radius: 0px;padding: 10px 15px;' )
		.each( 'ng-click', 'removePayment( $index )' )
		.add( 'span.fa.fa-remove' );

		HTML.query( '.payment-list-' + key + ' .item-one' )
		.add( 'span.btn.btn-info.btn-xs.pull-right' )
		.each( 'style', 'position: absolute;right: 40px;top: -1px;height: 42px;border-radius: 0px;padding: 10px 15px;' )
		.each( 'ng-click', 'editPayment( $index )' )
		.add( 'span.fa.fa-edit' );

		// end

		angular.element( '.tab-' + key ).attr( 'style', 'border-left:solid 1px #DEDEDE;height:{{ wrapperHeight }}px;overflow-y:scroll;padding:15px;' );

	});

	// Creating Cart details
	var colWidth	=	150;

	HTML.query( '.cart-details' )
	.add( 'h4.text-center' )
	.each( 'style', 'margin:10px 0;' )
	.textContent	=	'<?php echo _s( 'Détails du panier', 'nexo' );?>';

	HTML.query( '.cart-details' )
	.add( 'table.table.table-bordered.cart-details-table>tr*<?php echo $rowNbr;?>' )
	.each( 'style', 'font-size:13px' );

	HTML.query( '.cart-details-table tr' ).only(<?php echo $currentRow;?>).add( 'td.text-left' )
	.each( 'width', colWidth ).add( 'strong' ).textContent	=	'<?php echo _s( 'Sous Total', 'nexo' );?>';

	HTML.query( '.cart-details-table tr' )
	.only(<?php echo $currentRow++;?>)
	.add( 'td.text-right' )
	.textContent	=	'{{ cart.value | moneyFormat }}';

	HTML.query( '.cart-details-table tr' )
	.only(<?php echo $currentRow;?>)
	.add( 'td.text-left' )
	.each( 'width', colWidth ).add( 'strong' ).textContent	=	'<?php echo _s( 'Remise sur le panier', 'nexo' );?>';

	HTML.query( '.cart-details-table tr' )
	.only(<?php echo $currentRow++;?>).add( 'td.text-right' )
	.textContent	=	'{{ cart.discount | moneyFormat }}';

	<?php if( @$Options[ store_prefix() . 'nexo_enable_vat' ] == 'oui' ) :?>
	HTML.query( '.cart-details-table tr' )
	.only(<?php echo $currentRow;?>)
	.add( 'td.text-left' )
	.each( 'width', colWidth ).add( 'strong' ).textContent	=	'<?php echo _s( 'TVA', 'nexo' );?>';

	HTML.query( '.cart-details-table tr' )
	.only(<?php echo $currentRow++;?>).add( 'td.text-right' )
	.textContent	=	'{{ cart.VAT | moneyFormat }}';
	<?php endif;?>

	//
	
	<?php if( store_option( 'disable_shipping' ) != 'yes' ):?>

	HTML.query( '.cart-details-table tr' )
	.only(<?php echo $currentRow;?>)
	.add( 'td.text-left' )
	.each( 'width', colWidth ).add( 'strong' ).textContent	=	'<?php echo _s( 'Livraison', 'nexo' );?>';

	HTML.query( '.cart-details-table tr' )
	.only(<?php echo $currentRow++;?>)
	.add( 'td.text-right' ).textContent	=	'{{ cart.shipping | moneyFormat }}';

	<?php endif;?>

	// 

	HTML.query( '.cart-details-table tr' )
	.only(<?php echo $currentRow;?>)
	.add( 'td.text-left' )
	.each( 'width', colWidth ).add( 'strong' ).textContent	=	'<?php echo _s( 'Net à payer', 'nexo' );?>';

	HTML.query( '.cart-details-table tr' )
	.only(<?php echo $currentRow++;?>)
	.add( 'td.text-right' ).textContent	=	'{{ cart.netPayable | moneyFormat }}';

	// Paid Amount so far

	HTML.query( '.cart-details-table tr' )
	.only(<?php echo $currentRow;?>)
	.add( 'td.text-left' )
	.each( 'width', colWidth ).add( 'strong' ).textContent	=	'<?php echo _s( 'Payé', 'nexo' );?>';

	HTML.query( '.cart-details-table tr' )
	.only(<?php echo $currentRow++;?>)
	.add( 'td.text-right' ).textContent	=	'{{ cart.paidSoFar | moneyFormat }}';

	HTML.query( '.cart-details-table tr' )
	.only(<?php echo $currentRow;?>)
	.add( 'td.text-left' )
	.each( 'width', colWidth ).add( 'h5' ).textContent	=	'<?php echo _s( 'Reste', 'nexo' );?>';

	HTML.query( '.cart-details-table tr' )
	.only(<?php echo $currentRow++;?>)
	.add( 'td.text-right' ).add( 'h5' ).textContent	=	'{{ cart.balance | moneyFormat }}';

	// Split Payment
	HTML.query( '.cart-details' )
	.add( 'h4.text-center' )
	.each( 'style', 'margin:10px 0;' )
	.textContent	=	'<?php echo _s( 'Liste des paiements', 'nexo' );?>';

	HTML.query( '.cart-details' )
	.add( 'ul.list-group>li.list-group-item.item-one' );

	HTML.query( '.cart-details .item-one' )
	.textContent	=	'{{ payement.text }}';

	HTML.query( '.cart-details .item-one' )
	.add( 'span.pull-right' )
	.each( 'style', 'margin-right:80px' )
	.textContent	=	'{{ payement.amount | moneyFormat }}';

	HTML.query( '.cart-details .item-one' )
	.each( 'ng-repeat', 'payement in paymentList' )
	.add( 'span.btn.btn-warning.btn-xs.pull-right' )
	.each( 'style', 'position: absolute;right: 0;top: -1px;height: 42px;border-radius: 0px;padding: 10px 15px;' )
	.each( 'ng-click', 'removePayment( $index )' )
	.add( 'span.fa.fa-remove' );

	HTML.query( '.cart-details .item-one' )
	.add( 'span.btn.btn-info.btn-xs.pull-right' )
	.each( 'style', 'position: absolute;right: 40px;top: -1px;height: 42px;border-radius: 0px;padding: 10px 15px;' )
	.each( 'ng-click', 'editPayment( $index )' )
	.add( 'span.fa.fa-edit' );

	var payBoxHTML		=	angular.element( 'angular-cache' ).html();

	angular.element( 'angular-cache' ).remove();

	return {
		template 	:	payBoxHTML
	}
});
</script>

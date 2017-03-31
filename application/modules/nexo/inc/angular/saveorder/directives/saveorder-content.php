<script>
/**
 * Save Order Content
 * @type 	:	directive
**/

tendooApp.directive( 'saveOrderContent', function(){

	HTML.body.add( 'angular-cache' );

	HTML.query( 'angular-cache' )
	.add( 'div.row' )
	.add( 'div.col-lg-12' )
	.add( 'div.input-group.group-content' );

	HTML.query( 'angular-cache' )
	.add( 'br' );

	<?php echo $this->events->apply_filters( 'saveorder_title_field', $this->load->module_include( 'nexo', 'angular.saveorder.templates.title', null, true ) );?>

	HTML.query( 'angular-cache' )
	.add( 'div.alert.alert-info>p' ).textContent	=	'<?php echo _s( 'Vous êtes sur le point de sauvegarder cette commande', 'nexo' );?>';

	HTML.query( 'angular-cache' )
	.add( 'table.table.table-bordered.cart-status-for-save>thead>tr>td*2' );

	HTML.query( '.cart-status-for-save td' ).only(0).textContent	=	'<?php echo _s( 'Détails du panier', 'nexo' );?>';
	HTML.query( '.cart-status-for-save td' ).only(1).textContent	=	'<?php echo _s( 'Montant', 'nexo' );?>';

	HTML.query( '.cart-status-for-save' )
	.add( 'tbody.cart-status-fs-tbody>tr>td*2' );

	HTML.query( '.cart-status-fs-tbody td' ).only(0).textContent	=	'<?php echo _s( 'Valeur du panier', 'nexo' );?>';
	HTML.query( '.cart-status-fs-tbody td' ).only(1).add( 'strong' ).textContent	=	'{{ cart.value | moneyFormat }}';


	HTML.query( '.cart-status-fs-tbody' )
	.add( 'tr>td*2' );

	HTML.query( '.cart-status-fs-tbody td' ).only(2).textContent	=	'<?php echo _s( 'Net à payer', 'nexo' );?>';
	HTML.query( '.cart-status-fs-tbody td' ).only(3).add( 'strong' ).textContent	=	'{{ cart.netPayable | moneyFormat }}';

	HTML.query( '.purchase-value' ).add( 'span.purchase-amount.text-right' )
	.textContent		=	'{{ cart.value | moneyFormat }}';


	var domHTML			=	angular.element( 'angular-cache' ).html();

	angular.element( 'angular-cache' ).remove();

	return {
		template		:	domHTML
	}
});
</script>

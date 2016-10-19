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
	
	HTML.query( '.group-content' )
	.add( 'span.input-group-addon' )
	.textContent	=	'<?php echo _s( 'Intitulé de la commande', 'nexo' );?>';
	
	HTML.query( '.group-content' )
	.add( 'input.form-control' )	
	.each( 'ng-model', 'orderName' )
	.each( 'placeholder', '<?php echo _s( 'Désignation de la commande', 'nexo' );?>' )
	
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
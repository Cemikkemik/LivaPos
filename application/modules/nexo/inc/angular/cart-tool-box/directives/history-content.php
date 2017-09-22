<script>
/***
 * Order History Content Wrapper
**/

tendooApp.directive( 'historyContent', function(){

	HTML.body.add( 'angular-cache' );

	HTML.query( 'angular-cache' )
	.add( 'div.row.row-container' );

	HTML.query( '.row-container' )
	.add( 'div.col-lg-2.col-sm-2.col-md-2.col-xs-2.order-status.bootstrap-tab-menu' );

	HTML.query( '.order-status' )
	.add( 'div.list-group>a.list-group-item' )
	.each( 'ng-repeat', '(key, val) in orderStatusObject' )
	.each( 'ng-class', '{ active : val.active }' )
	.each( 'href', 'javascript:void(0)' )
	.each( 'ng-click', 'selectHistoryTab( key )' )
	.each( 'style', 'margin:0px;border-radius:0px;border:solid 1px #DEDEDE;border-top:solid 0px;border-right:solid 0px;padding-left:30px;' )
	.textContent	=	'{{ val.title }}';

	HTML.query( '.row-container' )
	.add( 'div.col-lg-4.col-sm-4.col-md-4.col-xs-4.middle-content' )
	.each( 'style', 'padding-left:0px;padding-right:0px' );

	HTML.query( '.middle-content' )
	.add( 'div.history-content-wrapper' )
	.each( 'ng-repeat', '(key, val) in orderStatusObject' )
	.each( 'ng-show', 'orderStatusObject[ key ].active' )
	.add( 'history-order-list' )
	.each( 'object', 'loadedOrders[ key ]' )
	.each( 'open-order-details', 'openOrderDetails' )
	.each( 'namespace', '{{ key }}' );

	HTML.query( '.middle-content' ).only(0).add( 'the-spinner' ).each( 'namespace', 'mspinner' ).each( 'spinner-obj', 'theSpinner' );

	HTML.query( '.row-container' )
	.add( 'div.col-lg-6.order-details' );

	HTML.query( '.order-details' ).each( 'style', 'border-left:solid 1px #DEDEDE;overflow-y:scroll;' );

	HTML.query( '.order-details' ).add( 'div.order-details-wrapper.row' );
	HTML.query( '.order-details-wrapper' ).add( 'h3.text-center' ).textContent	=	'<?php echo _s( 'Détails de la commande', 'nexo' );?>';
	HTML.query( '.order-details-wrapper' ).each( 'ng-hide', 'theSpinner[ "rspinner" ]' );
	HTML.query( '.order-details-wrapper' ).add( 'div.col-lg-6.col-md-6.text-left*4' );
	HTML.query( '.order-details-wrapper div' )
	.only(0)
	.add( 'p.details{<?php echo _s( 'Auteur :', 'nexo' );?>}>span.details.pull-right' )
	.textContent	=	'{{ orderDetails.order.AUTHOR_NAME }}';

	HTML.query( '.order-details-wrapper div' )
	.only(1)
	.add( 'p.details{<?php echo _s( 'Date :', 'nexo' );?>}>span.details.pull-right' )
	.textContent	=	'{{ orderDetails.order.DATE_CREATION }}';

	HTML.query( '.order-details-wrapper div' )
	.only(2)
	.add( 'p.details{<?php echo _s( 'Code :', 'nexo' );?>}>span.details.pull-right' )
	.textContent	=	'{{ orderDetails.order.CODE }}';

	HTML.query( '.order-details-wrapper div' )
	.only(3)
	.add( 'p.details{<?php echo _s( 'Impayé :', 'nexo' );?>}>span.details.pull-right' )
	.textContent	=	'{{ orderDetails.order.TOTAL - orderDetails.order.SOMME_PERCU | moneyFormat }}';

	HTML.query( '.order-details-wrapper' ).add( 'h3.text-center' ).textContent	=	'<?php echo _s( 'Liste des produits', 'nexo' );?>';

	HTML.query( '.order-details-wrapper' ).add( 'table.table.table-bordered.table-striped.order-details-table' );
	HTML.query( '.order-details-table' ).add( 'thead>tr>td*5' );

  	HTML.query( '.order-details-table td' ).only(0).textContent	=	'<?php echo _s( 'Nom du produit', 'nexo' );?>';
	HTML.query( '.order-details-table td' ).only(1).textContent	=	'<?php echo _s( 'Prix Untaire', 'nexo' );?>';
	HTML.query( '.order-details-table td' ).only(2).textContent	=	'<?php echo _s( 'Quantité', 'nexo' );?>';
	HTML.query( '.order-details-table td' ).only(3).textContent	=	'<?php echo _s( 'Remise', 'nexo' );?>';
	HTML.query( '.order-details-table td' ).only(4).textContent	=	'<?php echo _s( 'Sous Total', 'nexo' );?>';

	HTML.query( '.order-details-table' ).add( 'tbody>tr.item-row' )
	.each( 'ng-repeat', 'item in orderDetails.items' )
	.add( 'td.text-left*5' );

	HTML.query( '.item-row td' ).only(0).textContent		=	'{{ item.DESIGN || item.NAME }}';
	HTML.query( '.item-row td' ).only(1).textContent		=	'{{ item.PRIX | moneyFormat }}';
	HTML.query( '.item-row td' ).only(2).textContent		=	'{{ item.QUANTITE }}';
	HTML.query( '.item-row td' ).only(3).textContent		=	'{{ item.DISCOUNT_TYPE == "percentage" ? item.DISCOUNT_PERCENT : item.DISCOUNT_AMOUNT | moneyFormat }}';
	HTML.query( '.item-row td' ).only(4).textContent		=	'{{ item.PRIX * item.QUANTITE | moneyFormat }}';

	HTML.query( '.order-details' ).only(0).add( 'the-spinner' ).each( 'namespace', 'rspinner' );

	var domHTML		=	angular.element( 'angular-cache' ).html();
	angular.element( 'angular-cache' ).remove();

	return {
		restrict	: 	'E',
		template	:	domHTML
	};

});
</script>

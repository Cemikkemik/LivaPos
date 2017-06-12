<script>
/***
 * Order History Content Wrapper
**/

tendooApp.directive( 'historyOrderList', function(){

	HTML.body.add( 'angular-cache' );

	HTML.query( 'angular-cache' )
	.add( 'div.list-group>a.list-group-item' )
	.each( 'ng-repeat', 'value in details | orderBy : "ID" : true' )
	.each( 'ng-click', 'openOrderDetails( value.ID )' )
	.each( 'style', 'margin: 0px;    border-radius: 0px;    border-width: 0px 0px 1px 1px;   border-style: solid;    border-bottom-color: rgb(222, 222, 222);    border-left-color: rgb(222, 222, 222);    border-image: initial;    border-top-color: initial;    border-right-color: initial;    padding-left: 30px;border-left:solid 0px;' )
	.each( 'href', 'javascript:void(0)' )
	.each( 'order-id', '{{ value.ID }}' )
	.textContent	=	'<?php echo $this->events->apply_filters( 'order_history_title', '{{ value.TITRE | titleFilter }} - {{ value.CODE }}' );?>';

	HTML.query( 'angular-cache div.list-group' )
	.add( 'a.list-group-item' )
	.each( 'style', 'margin: 0px;    border-radius: 0px;    border-width: 0px 0px 1px 1px;   border-style: solid;    border-bottom-color: rgb(222, 222, 222);    border-left-color: rgb(222, 222, 222);    border-image: initial;    border-top-color: initial;    border-right-color: initial;    padding-left: 30px;border-left:solid 0px;' )
	.each( 'ng-show', 'details.length == 0' )
	.textContent	=	'<?php echo _s( 'Aucune commande disponible', 'nexo' );?>';

	var domHTML		=	angular.element( 'angular-cache' ).html();
	angular.element( 'angular-cache' ).remove();

	return {
		restrict	: 'E',
		template	:	domHTML,
		scope		:	{
			details				:	'=object',
			openOrderDetails	:	'=',
			namespace			:	'@'
		}
	};

});
</script>

<script>
/**
 * Create Controller
**/

tendooApp.controller( 'nexo_order_list', function( $scope, $compile, $http ) {
	
	$scope.order_status		=	{
		comptant			:	'nexo_order_comptant'
	}
	
	$scope.window			=	{
		height				:	window.innerHeight < 600 ? 600 : window.innerHeight
	}
	
	$scope.options			=	[{
		title				:	'<?php echo _s( 'Détails', 'nexo' );?>',
		visible				:	false,
		class				:	'default',
		content				:	'',
		namespace			:	'details'
	},{
		title				:	'<?php echo _s( 'Paiment', 'nexo' );?>',
		visible				:	false,
		class				:	'default',
		content				:	'',
		namespace			:	'payment'
	},{
		title				:	'<?php echo _s( 'Remboursement', 'nexo' );?>',
		visible				:	false,
		class				:	'default',
		content				:	'',
		namespace			:	'refund'
	},{
		title				:	'<?php echo _s( 'Annulation', 'nexo' );?>',
		visible				:	false,
		class				:	'default',
		content				:	'',
		namespace			:	'cancel'
	},{
		title				:	'<?php echo _s( 'Imprimer', 'nexo' );?>',
		visible				:	false,
		class				:	'default',
		content				:	'',
		namespace			:	'print'
	}];
	
	$scope.openDetails		=	function( order_id ) {
		
		$scope.order_id		=	order_id;	
		
		var content			=	
		'<h4 class="text-center"><?php echo _s( 'Options de la commande', 'nexo' );?></h4>' +
		'<div class="row" style="border-top:solid 1px #EEE;">' +
			'<div class="col-lg-2" style="padding:0px;margin:0px;">' +
				'<div class="list-group">' +
				  '<a style="border-radius:0;border-left:0px; border-right:0px;" href="#" ng-repeat="option in options" ng-click="toggleTab( option )" class="list-group-item {{ option.class }}">{{ option.title }}</a>' +
				'</div>' +
			'</div>' +
			'<div class="col-lg-10" style="border-left:solid 1px #EEE;padding-top:10px;height:{{ window.height / 1.5 }}px;overflow:scroll-y">' +
				'<div ng-repeat="option in options" ng-show="option.visible" data-namespace="{{ option.namespace }}" >' +					
				'</div>' +
			'</div>' +
		'</div>';
		
		bootbox.alert( '<dom></dom>' );
		
		$( 'dom' ).append( $compile(content)($scope) );
		
		$( '.modal-dialog' ).css( 'width', '80%' );
		$( '.modal-body' ).css( 'padding-bottom', 0 );
		
		// Default Tab is loaded
		$scope.toggleTab( $scope.options[0] );
	}
	
	$scope.toggleTab			=	function( option ){
		
		_.each( $scope.options, function( value, key ) {
			$scope.options[key].visible	=	false;
			$scope.options[key].class	=	'default';
		});
		
		option.visible			=	true;
		option.class			=	'active'
		
		$scope.loadContent( option );
	};
	
	$scope.loadContent			=	function( option ){
		if( option.namespace 		==	'details' ) {
			$http.get( '<?php echo site_url( array( 'rest', 'nexo', 'order_with_item' ) );?>' + '/' + $scope.order_id + '?<?php echo store_get_param( null );?>', {
				headers			:	{
					'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
				}
			}).then(function( returned ){
				
				$scope.items	=	returned.data.items;				
				$scope.order	=	returned.data.order
				
				var content		=	
				'<div class="row">' +
					'<div class="col-lg-7">' +
						'<h5><?php echo _s( 'Liste des produits', 'nexo' );?></h5>' +
						'<table class="table table-bordered table-striped">' +
							'<thead>' +
								'<tr>' +
									'<td><?php echo _s( 'Nom de l\'article', 'nexo' );?></td>' +
									'<td><?php echo _s( 'UGS', 'nexo' );?></td>' +
									'<td><?php echo _s( 'Prix Unitaire', 'nexo' );?></td>' +
									'<td><?php echo _s( 'Quantité', 'nexo' );?></td>' +
									'<td><?php echo _s( 'Total', 'nexo' );?></td>' +
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
							'</tbody>' +
						'</table>' +
					'</div>' +
					'<div class="col-lg-5">' +
						'<h5><?php echo _s( 'Détails sur la commande', 'nexo' );?></h5>' +
						'<ul class="list-group">' +
						  '<li class="list-group-item"><?php echo _s( 'Auteur :', 'nexo' );?> {{ order.AUTHOR_NAME }}</li>' +
						  '<li class="list-group-item"><?php echo _s( 'Effectué le :', 'nexo' );?> {{ order.DATE_CREATION }}</li>' +
						  '<li class="list-group-item"><?php echo _s( 'Client :', 'nexo' );?> {{ order.CLIENT_NAME }}</li>' +
						  '<li class="list-group-item"><?php echo _s( 'Statut :', 'nexo' );?> {{ order.TYPE }}</li>' +
						'</ul>' +
					'</div>' +
				'</div>';
				
				$( '[data-namespace="details"]' ).html( '' );
				
				$( '[data-namespace="details"]' ).append( $compile(content)($scope) );
			});
		} 
		else if( option.namespace == 'payment' ) {
			
			$http.get( '<?php echo site_url( array( 'rest', 'nexo', 'order' ) );?>' + '/' + $scope.order_id + '?<?php echo store_get_param( null );?>', {
				headers			:	{
					'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
				}
			}).then(function( data ){
				if( data.TYPE == $scope.order_status.comptant ) {
					var content		=	'<?php echo tendoo_info( _s( 'Cette commande n\'a pas besoin de paiement supplémentaire.', 'nexo' ) );?>';
					$( '[data-namespace="payment"]' ).html( '' );
				
					$( '[data-namespace="payment"]' ).append( $compile(content)($scope) );
				}
			});
			
		}
	}
});
</script>
<?php 
global $Options;
$this->load->config( 'rest' );
?>

<div ng-controller="nrPackage">
    <div class="row">
        <div class="col-lg-4" style="height:{{ contentHeight }}px;overflow-y:scroll;">
            <div class="input-group"> <span class="input-group-addon" id="basic-addon1"><?php echo _s( 'Search order', 'nexo-playground-manager' );?></span>
                <input type="text" ng-model="searchOrder" class="form-control" placeholder="<?php echo _s( 'Order Code', 'nexo-playground-manager' );?>" aria-describedby="basic-addon1">
                <span class="input-group-btn">
                <button class="btn btn-default" ng-click="getOrders( 'search' )" type="button"><?php echo _s( 'Search', 'nexo-playground-manager' );?></button>
                <button class="btn btn-default" ng-click="reset()" type="button"><i class="fa fa-times"></i></button>
                </span> </div>
            <br />
            <div class="list-group" > 
            	<a ng-repeat="order in orders" href="#" class="list-group-item" ng-click="editOrder( order.CODE )" ng-class="{ active : order.active }">{{ order.CODE }}</a> 
			</div>
        </div>
        <div class="col-lg-5" style="height:{{ contentHeight }}px;overflow-y:scroll;background:#EEE;">
        	<the-spinner spinner-obj="spinner" namespace="order_details"></the-spinner>
        </div>
        <div class="col-lg-3"> </div>
    </div>
</div>
<?php include_once( MODULESPATH . '/nexo/inc/angular/order-list/directives/loading-spinner.php' );?>
<script>
"use strict";

tendooApp.controller( 'nrPackage', [ '$scope', '$http', '$timeout', '$compile', function( $scope, $http, $timeout, $compile ) {
	
	$scope.searchOrder			=	'';
	$scope.selectedOrder		=	null;
	$scope.spinner				=	{
		order_details			:	false
	};
	$scope.contentHeight		=	angular.element( '.content-wrapper' ).height() - 75;
	
	/**
	 * Clear
	**/
	
	$scope.reset				=	function() {
		$scope.getOrders();
	}
	
	/**
	 * Edit Order
	**/
	
	$scope.editOrder			=	function( order_code ){
		// 
		$scope.setActive( order_code );
	}
	
	/**
	 * Search
	**/
	
	$scope.getOrders		=	function( action ) {
		
		if( action == 'search' ) {
			if( $scope.searchOrder == '' ) {
				NexoAPI.Notify().warning( '<?php echo _s( 'Warning', 'nexo-playground-manager' );?>', '<?php echo _s( 'You must input something for search', 'nexo-playground-manager' );?>' );
				return false;
			}
			
			var url		=	$scope.searchOrder + '/search?<?php echo store_get_param( null );?>';
			
		} else {
			var url		=	'?<?php echo store_get_param( null );?>';
		}
		
		$http.get( '<?php echo site_url( array( 'rest', 'np_manager', 'orders' ) );?>/' + url, {
			headers			:	{
				'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
			}
		}).then( function( returned ){
			$scope.orders	=	returned.data;
		});
	}
	
	/**
	 * Get OrderItems
	**/
	
	$scope.getOrderDetails	=	function() {
		if( $scope.selectedOrder == '' ) {
			console.log( 'error occured while trying to load order' );
			return false;
		}
		
		$http.get( '<?php echo site_url( array( 'rest', 'np_manager', 'orders_items' ) );?>/' + url, {
			headers			:	{
				'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
			}
		}).then( function( returned ){
			$scope.orders	=	returned.data;
		});
	}
	
	/**
	 * Set Active
	**/
	
	$scope.setActive		=	function( order_code ){
		_.each( $scope.orders, function( value, key ) {
			if( value.CODE	==	order_code ) {
				value.active	=	true;
				$scope.selectedOrder	=	value.CODE;
			} else {
				value.active	=	false;
			}
		});
	}
	
	$scope.getOrders();	
}]);
</script>
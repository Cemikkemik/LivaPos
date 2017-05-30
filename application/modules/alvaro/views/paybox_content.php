<?php 
    global $Options;
    $this->load->config( 'rest' );
?>
/**
 * Open Order Details
**/

$scope.openOrderDetails			=	function( order_id ) {
    if( order_id != '' ) {
        $http.get( '<?php echo site_url( array( 'rest', 'nexo', 'order_with_item' ) );?>' + '/' + order_id + '?<?php echo store_get_param( null );?>', {
            headers			:	{
                '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
            }
        }).then(function( returned ){
            $scope.orderDetails					=	returned.data;
            $scope.openOrderOnPOS();
        });
    }    
};

$scope.openOrderOnPOS			=	function(){
    if( $scope.orderDetails == null ) {
        NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Vous devez choisir une commande avant de l\'ouvrir.', 'nexo' );?>' );
        return false;
    }

    NexoAPI.events.addFilter( 'process_data', function( data ){
        data.url			=	"<?php echo site_url(array( 'rest', 'nexo', 'order', User::id() ) );?>/" + $scope.orderDetails.order.ID + "?store_id=<?php echo get_store_id();?>";

        data.type			=	'PUT';
        return data;
    });

    v2Checkout.emptyCartItemTable();
    v2Checkout.CartItems			=	$scope.orderDetails.items;

    _.each( v2Checkout.CartItems, function( value, key ) {
        value.QTE_ADDED		=	value.QUANTITE;
    });

    // @added CartRemisePercent
    // @since 2.9.6

    if( $scope.orderDetails.order.REMISE_TYPE != '' ) {
        v2Checkout.CartRemiseType			=	$scope.orderDetails.order.REMISE_TYPE;
        v2Checkout.CartRemise				=	NexoAPI.ParseFloat( $scope.orderDetails.order.REMISE );
        v2Checkout.CartRemisePercent		=	NexoAPI.ParseFloat( $scope.orderDetails.order.REMISE_PERCENT );
        v2Checkout.CartRemiseEnabled		=	true;
    }

    if( parseFloat( $scope.orderDetails.order.GROUP_DISCOUNT ) > 0 ) {
        v2Checkout.CartGroupDiscount				=	parseFloat( $scope.orderDetails.order.GROUP_DISCOUNT ); // final amount
        v2Checkout.CartGroupDiscountAmount			=	parseFloat( $scope.orderDetails.order.GROUP_DISCOUNT ); // Amount set on each group
        v2Checkout.CartGroupDiscountType			=	'amount'; // Discount type
        v2Checkout.CartGroupDiscountEnabled			=	true;
    }

    v2Checkout.CartCustomerID						=	$scope.orderDetails.order.REF_CLIENT;

    // @since 2.7.3
    v2Checkout.CartNote								=	$scope.orderDetails.order.DESCRIPTION;

    v2Checkout.CartTitle							=	$scope.orderDetails.order.TITRE;

    // Restore Custom Ristourne
    v2Checkout.restoreCustomRistourne();

    // Refresh Cart
    // Reset Cart state
    v2Checkout.buildCartItemTable();
    v2Checkout.refreshCart();
    v2Checkout.refreshCartValues();

    $scope.openPayBox();
};

$scope.openOrderDetails( '<?php echo $this->input->get( 'load-order' );?>' );


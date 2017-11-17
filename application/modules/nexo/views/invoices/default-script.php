<?php include_once( MODULESPATH . '/nexo/inc/angular/order-list/filters/money-format.php' );?>
<script>
tendooApp.controller( 'invoiceCTRL', [ '$scope', function( $scope ) {
    $scope.data         =   <?php echo json_encode( $order );?>;
    $scope.shipping     =   <?php echo json_encode( $shipping[0] );?>;
    $scope.billing     =   <?php echo json_encode( $billing[0] );?>;
    
    /**
     * Sub Total
     * @param object
     * @return numeric
     */
     $scope.subTotal        =   function( items ) {
        var subTotal       =   0;

        _.each( items, ( item ) => {
            console.log( item );
            subTotal        +=  ( parseFloat( item.PRIX ) * parseFloat( item.QUANTITE ) );
        });
        return subTotal;
     }

    /**
     * Calculate total for the invoice
     * @return int
     */
    $scope.total        =   function(){
        let totalItems          =   parseFloat( $scope.subTotal( $scope.data.products ) );
        let totalShipping       =   parseFloat( $scope.data.order[0].SHIPPING_AMOUNT );
        return totalItems   +   totalShipping;
    }
}])
</script>
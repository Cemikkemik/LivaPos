<?php include_once( MODULESPATH . '/nexo/inc/angular/order-list/filters/money-format.php' );?>
<script>
tendooApp.controller( 'invoiceCTRL', [ '$scope', function( $scope ) {
    $scope.data         =   <?php echo json_encode( $order );?>;
    $scope.shipping     =   <?php echo json_encode( $shipping );?>;
    console.log( $scope.data, $scope.shipping );
}])
</script>
<script type="text/javascript">
tendooApp.controller( 'posTabs', [ '$scope', function( $scope ){
    $scope.showPart     =   function( tab ) {
        if( tab == 'cart' ) {
            $scope.cartIsActive = 'active';
            $scope.gridIsActive = '';
            $( '#cart-details-wrapper').show();
            $( '#product-list-wrapper').hide();
            $( '.tab-grid' ).hide();
            $( '.tab-cart' ).show();
        } else if( tab == 'grid' ) {
            $scope.cartIsActive = '';
            $scope.gridIsActive = 'active';
            $( '#cart-details-wrapper').hide();
            $( '#product-list-wrapper').show();
            $( '.tab-grid' ).show();
            $( '.tab-cart' ).hide();
        }

		setTimeout(function(){
            v2Checkout.toggleCompactMode(true);
        }, 200 );
    }

    if( window.innerWidth < 900 ) {
        $scope.showPart( 'grid' );
    }

}])
</script>

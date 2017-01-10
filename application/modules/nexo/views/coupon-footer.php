<?php
    global $Options;
    $this->load->config( 'rest' );
    if (@$Options[ store_prefix() . 'disable_coupon' ] != 'yes' ):
?>

/**
 *  Check Coupon
 *  @param
 *  @return
**/

$scope.usedCoupon       =   [];

$scope.checkCoupon      =   function(){
    v2Checkout.paymentWindow.showSplash();
    $http.get( '<?php echo site_url( array( 'rest', 'nexo', 'coupon_code' ) );?>' + '/' + $scope.couponCode, {
        headers			:	{
            '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
        }
    }).then(function( returned ) {
        v2Checkout.paymentWindow.hideSplash();
        if( returned.data.length == 0 ) {
            NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Ce coupon n\'existe pas', 'nexo' );?>' );
        }

        var coupon      =   returned.data;

        if( coupon.amount == '' ) {
            NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Le montant de ce coupon n\'est pas valide.', 'nexo' );?>' );
        }

        
    },function(){
        v2Checkout.paymentWindow.hideSplash();
    });
}

<?php
endif;

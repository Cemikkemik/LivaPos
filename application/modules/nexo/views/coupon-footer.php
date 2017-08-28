<?php
    global $Options;
    $this->load->config( 'rest' );
    if (@$Options[ store_prefix() . 'disable_coupon' ] != 'yes' ):
?>

/**
 *  Apply Coupon
 *  @param
 *  @return
**/

$scope.applyCoupon          =   function(){
    if( $scope.couponDetails[0].DISCOUNT_TYPE == 'fixed' ) {
        $scope.addPayment( 'coupon', $scope.couponDetails[0].AMOUNT, {
            coupon_id       :   $scope.couponDetails[0].ID
        });
    } else {
        var discount    =   $scope.cart.netPayable * parseFloat( $scope.couponDetails[0].AMOUNT ) / 100;
        $scope.addPayment( 'coupon', discount, {
            coupon_id       :   $scope.couponDetails[0].ID
        });
    }
    // Add coupon to used coupon
    $scope.usedCoupon.push( parseInt( $scope.couponDetails[0].ID ) );
    $scope.allCouponDetails.push( $scope.couponDetails[0] );
    $scope.cancelCoupon();
}

/**
 *  Calculate Coupon Type
 *  @param
 *  @return
**/

$scope.caculateCouponAmount =   function( coupon ) {
    if( angular.isDefined( coupon ) ) {
        if( coupon.DISCOUNT_TYPE == 'percentage' ){
            return coupon.AMOUNT + ' %';
        } else {
            return NexoAPI.DisplayMoney( coupon.AMOUNT );
        }
    }
}

/**
 *  Check Coupon
 *  @param
 *  @return
**/

$scope.usedCoupon           =   [];
$scope.allCouponDetails     =   [];

/**
 *  Cancel Coupon
 *  @param
 *  @return
**/

$scope.cancelCoupon     =   function(){
    $scope.couponDetails    =   [];
    delete $scope.couponValidityError;
    delete $scope.couponProductError;
    delete $scope.couponCategoryError;
    $scope.couponCode       =   '';
    angular.element( '.coupon-field' ).focus();
}

$scope.checkCoupon      =   function(){
    v2Checkout.paymentWindow.showSplash();
    $http.get( '<?php echo site_url( array( 'rest', 'nexo', 'coupon_code' ) );?>' + '/' + $scope.couponCode + '<?php echo store_get_param( '?' );?>', {
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
            return false;
        }

        // if coupon is already used on that cart
        if( _.indexOf( $scope.usedCoupon, parseInt( coupon[0].ID ) ) != -1) {
            NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Ce coupon est déjà en cours d\'utilisation sur ce panier.', 'nexo' );?>' );
            return false;
        }

        // @since 3.1
        // if the coupon reached the usage limit
        if( parseInt( coupon[0].USAGE_LIMIT ) <= parseInt( coupon[0].USAGE_COUNT ) ) {
            NexoAPI.Notify().warning( '<?php echo _s( 'Attention', 'nexo' );?>', '<?php echo _s( 'Le coupon a atteint sa limite d\'utilisation.', 'nexo' );?>' );
            return false;
        }

        $scope.couponDetails    =   coupon;

    },function(){
        v2Checkout.paymentWindow.hideSplash();
    });

    /** NexoAPI.events.addFilter( 'before_submit_order', function( data ) {
        data.COUPON             =   [];
        _.each( $scope.allCouponDetails, function( value ) {
            data.COUPON.push({
                id              :   value.ID,
                amount          :   value.AMOUNT,
                discount_type   :   value.DISCOUNT_TYPE
            });
        });
        return data;
    }); **/
}

/**
 *  Coupon Check Categories
 *  @param
 *  @return
**/

$scope.couponCheckCategories        =   function( categories ) {
    if( categories != '' && angular.isDefined( categories ) ) {
        var cats            =   JSON.parse( '[' + categories + ']' );
        var cat_names       =   [];
        var cat_in_cart     =   [];

        _.each( v2Checkout.ItemsCategories, function( value, id ) {
            if( _.indexOf( cats, parseInt( id ) ) != -1 ) {
                cat_names.push( value );
            }
        });

        _.each( v2Checkout.CartItems, function( value, key ) {
            if( _.indexOf( cats, parseInt( value.REF_CATEGORIE ) ) != -1 ) {
                // Avoid adding twice same category
                if( _.indexOf( cat_in_cart, v2Checkout.ItemsCategories[ value.REF_CATEGORIE ] ) == -1 ) {
                    cat_in_cart.push( v2Checkout.ItemsCategories[ value.REF_CATEGORIE ] );
                }
            }
        });

        if( cat_in_cart.length == 0 ) {
            $scope.couponCategoryError = true;
            return '<?php echo _s( 'Ce coupon requiert qu\'un ou plusieurs produits du panier appartiennent à une categorie suivante : ', 'nexo' );?>' + cat_names.toString();
        }

        return cat_in_cart.toString();
    }
    $scope.couponCategoryError = false;
    return '<?php echo _s( 'Aucune restriction', 'nexo' );?>';
}

/**
 *  Coupon Check DateTime
 *  @param
 *  @return
**/

$scope.couponCheckDate      =   function( dateTimeString ) {
    if( angular.isDefined( dateTimeString ) ) {

        var couponDate          =   moment( dateTimeString );

        if( couponDate.isBefore( v2Checkout.CartDateTime ) ) {
            $scope.couponValidityError  =   true;
            return '<?php echo _s( 'Ce coupon a expiré et ne peut pas être utilisé', 'nexo' );?>';
        }

        $scope.couponValidityError = false;
        return dateTimeString;
    }
    $scope.couponValidityError  =   false;
    return '<?php echo _s( 'Aucune restriction', 'nexo' );?>';
}

/**
 *  Coupon Show Products
 *  @param string id separated by a comma
 *  @return string HTML
**/

$scope.couponShowProducts   =   function( string ){
    if( string != '' && angular.isDefined( string ) ) {

        var ids                 =   JSON.parse( '[' + string + ']' );
        var items_on_coupon     =   [];

        _.each( v2Checkout.CartItems, function( value, key ) {
            if( _.indexOf( ids , parseInt( value.ID ) ) != -1 ) {
                items_on_coupon.push( value.DESIGN );
            }
        });

        _.each( items_on_coupon, function( value ){
            value       =   '<span class="label label-default label-md">' + value + ' </span>';
        });

        if( items_on_coupon.length == 0 ) {
            $scope.couponProductError   =   true;
            return '<?php echo _s( 'Ce coupon nécessite l\'utilisation d\'un ou plusieurs produits qui n\'existent pas dans le panier.', 'nexo' );?>';
        }

        $scope.couponProductError   =   false;
        return items_on_coupon.toString();
    }

    $scope.couponProductError   =   false;
    return '<?php echo _s( 'Aucune restriction', 'nexo' );?>';
}

/**
 *  Is Coupon Valid
 *  @param
 *  @return
**/

$scope.isCouponValid            =   function(){

    if( angular.isDefined( $scope.couponCategoryError ) &&
        angular.isDefined( $scope.couponProductError ) &&
        angular.isDefined( $scope.couponValidityError ) &&
        ! _.isEmpty( $scope.couponDetails )
    ) {
        if(
            $scope.couponCategoryError == false &&
            $scope.couponProductError == false &&
            $scope.couponValidityError == false
        ) {
            return true;
        }
    }
    return false;
}


<?php
endif;

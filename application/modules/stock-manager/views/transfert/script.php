<?php include_once( MODULESPATH . 'nexo/inc/angular/order-list/filters/money-format.php' );?>
<script>
var StockTransferCTRL   =   function( $scope, $http ) {

    $scope.order       =   {
        title       : '',
        store       :   {},
        items       :   []
    };

    $scope.ajaxHeader		=	{
		'<?php echo $this->config->item('rest_key_name');?>'	    :	'<?php echo get_option( 'rest_key' );?>'
		// 'Content-Type'											: 	'application/x-www-form-urlencoded'
	}

    $scope.stores       =   <?php echo json_encode( $this->Nexo_Stores->get() );?>;

    <?php if( is_multistore() ):?>
    $scope.stores.unshift({
        ID      :   0,
        NAME    :   '<?php echo _s( 'Main Warehouse', 'stock-manager' );?>'
    });

    var indexToSplice;
    _.each( $scope.stores, function( store, key ) {
        if( parseInt( store.ID ) == <?php echo get_store_id();?> ) {
            indexToSplice    =   key;
        }
    });

    $scope.stores.splice( indexToSplice, 1 );
    <?php endif;?>

    $scope.$watch( 'barcode', function(){
        $scope.fetchItem( $scope.barcode );
    });

    /** 
     * Fetch item
    **/

    $scope.fetchItem    =   function( barcode ) {
        if( barcode == '' || typeof barcode == 'undefined' ) {
            return;
        }
        
        $http.get( '<?php echo site_url( array( 'rest', 'nexo', 'item' ) );?>' + '/' + barcode + '/sku-barcode?<?php echo store_get_param( null );?>', {
			headers			:	{
				'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo get_option( 'rest_key' );?>'
			}
		}).then(function( returned ){
			$scope.addToCart( returned.data[0], 1 );
            $scope.barcode      =   '';
            $( '.barcode-field' ).val( '' );
            $( '.barcode-field ' ).focus();
		}, function(){
            NexoAPI.Toast()( '<?php echo _s( 'Unable to find this product', 'stock-manager' );?>' );
            $( '.barcode-field' ).val( '' );
            $( '.barcode-field ' ).focus();
        });
    }

    /**
     * Add to cart
     * @return void
    **/


    $scope.addToCart    =   function( item, qty = 1 ) {
        let itemExists  =   false;
        _.each( $scope.order.items, function( _item, key ) {
            if( _item.CODEBAR == item.CODEBAR ) {
                itemExists  =   true;
                if( qty == 1 ) {
                    var remaining   =   parseFloat( item.QUANTITE_RESTANTE ) - ( parseFloat( _item.QTE_ADDED ) + parseFloat( qty ) );
                } else {
                    var remaining   =   parseFloat( item.QUANTITE_RESTANTE ) - ( parseFloat( $scope.oldQte ) + parseFloat( qty ) );
                    $scope.order.items[ key ].QTE_ADDED         =   $scope.oldQte;      
                }

                if( remaining >= 0 ) {                    
                    $scope.order.items[ key ].QTE_ADDED     +=  qty;
                }  else {
                    $scope.order.items[ key ].QTE_ADDED      =   $scope.oldQte;
                    return NexoAPI.Toast()( '<?php echo _s( 'Out of stock, unable to add the product.', 'stock-manager' );?>' );
                }              
            }
        });

        if( item.QUANTITE_RESTANTE == '0' ) {
            return NexoAPI.Toast()( '<?php echo _s( 'Out of stock, unable to add the product.', 'stock-manager' );?>' );
        }

        // if item don't exists
        if( itemExists == false ) {
            item.QTE_ADDED      =   1;
            $scope.order.items.push( item );
        }
    }

    /**
     * Remove
     * @param int index
     * @return void
    **/

    $scope.remove           =   function( index ) {
        $scope.order.items.splice( index, 1 );
        // NexoAPI.Bootbox().confirm( '<?php echo _s( 'Would you like to remove this item', 'stock-manager' );?>', function( action ){
        //     if( action ) {
        //         $scope.order.items.splice( index, 1 );
        //     }
        // });
    }

    /**
     * Watch Item
     * save old qte_added
     * @param object
     * @return void
    **/

    $scope.watchItem        =   function( item ) {
        $scope.oldQte       =   item.QTE_ADDED;
    }

    /**
     * Check Change
     * @param object item
     * @return void
    **/

    $scope.checkChange      =   function( item ) {
        let currentValue    =   item.QTE_ADDED;

        // We're adding quantity
        if( parseFloat( $scope.oldQte ) < parseFloat( currentValue ) ) {
            var diff        =   currentValue    -   $scope.oldQte;
            $scope.addToCart( item, diff );
        } else if( item.QTE_ADDED < 1 ) {
            item.QTE_ADDED  =   1;
        }
    }

    /**
     * Increase and decrease
     * @param object
     * @param string operation
    **/

    $scope.quantity         =   function( item, operation ) {
        if( operation == 'increase' ) {
            $scope.fetchItem( item.CODEBAR );
            $scope.oldQte   =   item.QTE_ADDED;
        } else if( operation == 'decrease' && item.QTE_ADDED > 1 ) {
            item.QTE_ADDED--;
        }
    }

    /**
     * Submit Stock
     * @return void
    **/

    $scope.canSubmitTransfert       =   true;

    $scope.submitStock      =   function() {

        if( $scope.canSubmitTransfert == true ) {
            $scope.canSubmitTransfert   =   false;
        } else {
            return false;
        }

        if( $scope.order.items.length == 0 ) {
            $scope.canSubmitTransfert       =   true;
            return false;
        }

        if( $scope.order.title == '' ) {
            $scope.canSubmitTransfert       =   true;
            return NexoAPI.Toast()( '<?php echo _s( 'You must provide a title', 'stock-manager' );?>' );
        }

        if( angular.equals( $scope.order.store, {} ) ) {
            $scope.canSubmitTransfert       =   true;
            return NexoAPI.Toast()( '<?php echo _s( 'You must select a store', 'stock-manager' );?>' );
        }

        NexoAPI.Bootbox().confirm( '<?php echo _s( 'Would you like to confirm this transfert ?', 'stock-manager' );?>', function( action ) {
            if( action ) {
                $http({
                    url		        :	'<?php echo site_url( array( 'rest', 'stock_manager', 'stock_transfert' ) );?><?php echo store_get_param( '?' );?>',
                    method	        :	'POST',
                    data	        :	$scope.order,
                    headers			:	$scope.ajaxHeader
                }).then(function( response ){
                    NexoAPI.Toast()( '<?php echo _s( 'The stock has been successfully send !', 'stock-manager' );?>' );
                    $scope.order       =   {
                        title       : '',
                        store       :   {},
                        items       :   []
                    };

                    $.ajax({
                        url     :   '<?php echo site_url([ 'dashboard', store_slug(), 'stock-transfert', 'history', 'report' ]);?>/' + response.data.transfert_id,
                        success     :   function( data ) {
                            NexoAPI.Popup( data );
                        }
                    })
                    
                    $scope.canSubmitTransfert   =   true;
                }, function(){
                    $scope.canSubmitTransfert   =   true;
                });
            } else {
                $scope.canSubmitTransfert       =   true;
            }
        });
    }

    $scope.npAutocompleteOptions = {
        url: '<?php echo site_url( array( 'rest', 'nexo', 'item' ) );?>' +  '/:searchParam/search?<?php echo store_get_param( null );?>',
        headers		:	{
            '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo get_option( 'rest_key' );?>'
        },
        queryMode           :   false,
        callback            :   function( data, option ) {
            if( data.length == 1 ) {
                $scope.addToCart( data[0], 1 );
                angular.element( '.search-input' ).val('');
                angular.element( '.search-input' ).select();
                option.close();
                return false;
            }
            return true;
        },
        listClass           :   'list-class',
        nameAttr            :   'DESIGN',
        clearOnSelect       :   true,
        onSelect            :   function( item ) {
            $scope.addToCart( item, 1 );
        }, 
        onError             :   function(){
            NexoAPI.Toast()( '<?php echo __( 'Unable to find this item', 'stock-manager' );?>' );
            angular.element( '.search-input' ).val('');
            angular.element( '.search-input' ).select();
        },
        delay               :   500
    };

    // Initial focus
    $( '.barcode-field' ).focus();
}

StockTransferCTRL.$inject       =   [ '$scope', '$http' ];

tendooApp.controller( 'StockTransferCTRL',  StockTransferCTRL );
</script>
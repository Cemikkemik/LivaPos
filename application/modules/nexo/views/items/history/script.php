<?php include_once( MODULESPATH . '/nexo/inc/angular/order-list/filters/money-format.php' );?>
<script>
    tendooApp.controller( 'itemHistoryCTRL', [ '$scope', '$http', function( $scope, $http ){

        $scope.stock_operation      =   <?php echo json_encode( $this->config->item( 'stock-operation' ) );?>;
        $scope.totalEntries         =   false;

        $scope.total    =   {
            unit_price  :   {
                plus    :   0,
                minus   :   0
            },
            total_price  :   {
                plus    :   0,
                minus   :   0
            },
            quantity  :   {
                plus    :   0,
                minus   :   0
            }
        };

        $scope.itemsPerPage  =   20;
        
        /** 
         * Load Entries
         * @return void
        **/

        $scope.loadHistory      =   function( page = 0 ){
            // item barcode
            $scope.itemBarcode      =   '<?php echo $barcode;?>';
            $scope.currentPage      =   page;

            $http.get( '<?php echo site_url([ 'rest', 'nexo', 'history' ]);?>/' + $scope.itemBarcode + '<?php echo store_get_param( '?' );?>' + '&limit=' + $scope.itemsPerPage + '&page=' + page, {
                headers			:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo get_option( 'rest_key' );?>'
                }
            }).then(function( returned ){
                $scope.total    =   {
                    unit_price  :   {
                        plus    :   0,
                        minus   :   0
                    },
                    total_price  :   {
                        plus    :   0,
                        minus   :   0
                    },
                    quantity  :   {
                        plus    :   0,
                        minus   :   0
                    }
                };
                $scope.items            =   returned.data.items
                $scope.totalEntries     =   returned.data.entries;
                $scope.totalPage        =   $scope.totalEntries / $scope.itemsPerPage;
                $scope.sumTotal();
            });
        }

        /**
         * Test Operation
         * @return string
        **/

        $scope.testOperation    =   function( item ) {
            if( _.indexOf( [ 'sale', 'defective', 'adjustment', 'transfert_out' ], item.type ) != -1 ) {
                return '-';
            } else {
                return '+';
            }
        }

        /**
         * Test Operation class Name
         * @return string operation title
        **/

        $scope.operationClassName    =   function( item ) {
            if( _.indexOf( [ 'sale', 'transfert_out' ], item.type ) != -1 ) {
                return 'info';
            } else if( _.indexOf( item.type, [ 'defective', 'adjustment' ] ) != -1 ) {
                return 'danger';
            } else {
                return 'success';
            }
        }

        /**
         * Test Operation name
         * @return string
        **/

        $scope.operationName        =   function( item_type ) {
            return $scope.stock_operation[ item_type ];
        }

        /**
         * Total 
         * @param object
         * @return void
        **/

        $scope.sumTotal                =   function() {
            _.each( $scope.items, function( item ) {
                if( parseInt( item.quantity ) == 0 ) {
                    return;
                }
                // sale, supply, defective, usable, adjustment
                // only for quantity
                if( _.indexOf([ 'sale', 'defective', 'adjustment', 'transfert_out' ], item.type ) != -1 ) {
                    $scope.total.quantity.minus           +=  parseFloat( item.quantity );
                } else {
                    // for usable and supply
                    $scope.total.quantity.plus           +=  parseFloat( item.quantity );
                }

                // only for amount
                if( _.indexOf([ 'defective', 'supply', 'usable', 'adjustment', 'transfert_in' ], item.type ) != -1 ) {
                    $scope.total.unit_price.minus         +=  parseFloat( item.price );
                    $scope.total.total_price.minus        +=  parseFloat( item.total_price );
                } else {
                    $scope.total.unit_price.plus         +=  parseFloat( item.price );
                    $scope.total.total_price.plus        +=  parseFloat( item.total_price );
                }
            });
        }

        $scope.loadHistory();
    }]);

    tendooApp.filter('range', function() {
        return function(input, total) {
            total = parseInt(total);

            for (var i=0; i<total; i++) {
                input.push(i);
            }

            return input;
        };
    });
</script>
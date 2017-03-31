<?php global $Options;?>
<script type="text/javascript">
    tendooApp.filter( 'table_status', function(){
        return function( filter ) {
            var tableStatus     =   <?php echo json_encode( $this->config->item( 'nexo-restaurant-table-status' ) );?>;
            return tableStatus[ filter ];
        }
    })
    tendooApp.directive( 'restaurantRooms', function(){
        return {
            templateUrl        :  '<?php echo site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'table_selection' ] );?>',
            restrict            :   'E'
        }
    });

    var selectTableCTRL     =   function( $compile, $scope, $timeout, $http ) {

        $scope.spinner                  =   {}
        $scope.rooms                    =   [];
        $scope.areas                    =   [];
        $scope.tables                   =   [];
        $scope.openTableSelection       =   true;
        $scope.selectedTable            =   false;
        $scope.roomHeaderHeight         =   0;
        $scope.hideSideKeys             =   true;
        $scope.hideButton               =   {
            dot             :   true
        }
        $scope.wrapperHeight			=	$scope.windowHeight - ( ( 56 * 2 ) + 30 );
        $scope.reservationPattern       =   <?php echo json_encode( ( array ) explode( ',', @$Options[ store_prefix() . 'reservation_pattern' ] ) );?>;

        /**
         *  check selecting table action
         *  @param string action
         *  @return void
        **/

        $scope.checkSelectingTableAction    =   function( action ) {
            if( action == true ) {
                if( $scope.seatToUse == 0  || angular.isUndefined( $scope.seatToUse ) ) {
                    NexoAPI.Bootbox().alert( '<?php echo _s( 'You must set a used seats. You can set used seats only for available tables.', 'nexo-restaurant' );?>' );
                    return false;
                }

                v2Checkout.CartMetas        =   _.extend( v2Checkout.CartMetas, {
                    table_id            :   $scope.selectedTable.TABLE_ID,
                    room_id             :   $scope.selectedRoom.ID,
                    area_id             :   $scope.selectedArea.AREA_ID,
                    seat_used           :   $scope.seatToUse > parseInt( $scope.selectedTable.MAX_SEATS ) ? $scope.selectedTable.MAX_SEATS  : $scope.seatToUse
                });
            }
        }

        /**
         *  Open table Selection
         *  @param void
         *  @return void
        **/

        $scope.openTableSelection       =       function() {
            $scope.seatToUse                =   0;
            $scope.rooms                    =   [];
            $scope.areas                    =   [];
            $scope.tables                   =   [];
            $scope.selectedRoom             =   false;
            $scope.selectedArea             =   false;
            $scope.selectedTable            =   false;

            NexoAPI.Bootbox().confirm({
    			message 		:	'<div class="table-selection"><restaurant-rooms rooms="rooms"/></div>',
    			title			:	'<?php echo _s( 'Select a Table', 'nexo' );?>',
    			buttons: {
    				confirm: {
    					label: '<?php echo _s( 'Confirm', 'nexo' );?>',
    					className: 'btn-success'
    				},
    				cancel: {
    					label: '<?php echo _s( 'Close', 'nexo' );?>',
    					className: 'btn-default'
    				}
    			},
    			callback		:	function( action ) {
                    return $scope.checkSelectingTableAction( action );
    			}
    		});

            $timeout( function(){
    			angular.element( '.modal-dialog' ).css( 'width', '90%' );
    			angular.element( '.modal-body' ).css( 'padding-top', '0px' );
    			angular.element( '.modal-body' ).css( 'padding-bottom', '0px' );
    			angular.element( '.modal-body' ).css( 'padding-left', '0px' );
    			angular.element( '.modal-body' ).css( 'height', $scope.wrapperHeight );
    			angular.element( '.modal-body' ).css( 'overflow-x', 'hidden' );
    			angular.element( '.middle-content' ).css( 'padding', 0 );
    		}, 150 );

            $( '.table-selection' ).html( $compile( $( '.table-selection').html() )( $scope ) );
            $scope.getRooms();
        }

        // Autorun Table
        angular.element( document ).ready( function(){
            $scope.openTableSelection();
        });

        /**
         *  Get Rooms
         *  @param void
         *  @return void
        **/

        $scope.getRooms         =       function() {
            $scope.showSpinner         =   true;
            $http.get( '<?php echo site_url([ 'rest', 'nexo_restaurant', 'rooms' ]) . store_get_param( '?' );?>', {
                headers			:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                }
            }).then(function( returned ) {
                $scope.showSpinner            =   false;
                $scope.rooms                =   returned.data;
            });
        }

        /**
         *  Load Room
         *  @param int room id
         *  @return void
        **/

        $scope.loadRoomAreas                =   function( room ) {
            _.each( $scope.rooms, function( room ) {
                room.active                 =   false;
            });

            $scope.selectedRoom             =   room;
            room.active                     =   true;
            $scope.spinner[ 'areas' ]       =   true;

            $http.get( '<?php echo site_url([ 'rest', 'nexo_restaurant', 'areas_from_room' ]);?>' + '/' + room.ID + '<?php echo store_get_param( '?' );?>', {
                headers			:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                }
            }).then(function( returned ) {
                $scope.spinner[ 'areas' ]     =   false;
                $scope.areas                    =   returned.data;
            });
        }

        /**
         *  Load table
         *  @param object areas
         *  @return void
        **/

        $scope.loadTables               =   function( area ) {
            _.each( $scope.areas, function( area ) {
                area.active                 =   false;
            });

            $scope.selectedArea             =   area;
            area.active                     =   true;
            $scope.spinner[ 'tables' ]      =   true;

            $http.get( '<?php echo site_url([ 'rest', 'nexo_restaurant', 'tables_from_area' ]);?>' + '/' + area.AREA_ID + '<?php echo store_get_param( '?' );?>', {
                headers			:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                }
            }).then(function( returned ) {
                $scope.spinner[ 'tables' ]     =   false;
                $scope.tables                    =   returned.data;
            });
        }

        /**
    	* Keyboard Input
    	**/

    	$scope.keyboardInput		=	function( char, field, add ) {

    		if( typeof $scope.seatToUse	==	'undefined' ) {
    			$scope.seatToUse	=	''; // reset paid amount
    		}

    		if( $scope.seatToUse 	==	0 ) {
    			$scope.seatToUse	=	'';
    		}

    		if( char == 'clear' ) {
    			$scope.seatToUse	=	'';
    		} else if( char == '.' ) {
    			$scope.seatToUse	+=	'.';
    		} else if( char == 'back' ) {
    			$scope.seatToUse	=	$scope.seatToUse.substr( 0, $scope.seatToUse.length - 1 );
    		} else if( typeof char == 'number' ) {
    			if( add ) {
    				$scope.seatToUse	=	$scope.seatToUse == '' ? 0 : $scope.seatToUse;
    				$scope.seatToUse	=	parseFloat( $scope.seatToUse ) + parseFloat( char );
    			} else {
    				$scope.seatToUse	=	$scope.seatToUse + '' + char;
    			}
    		}

            $scope.seatToUse    =   $scope.seatToUse == '' ? 0 : parseInt( $scope.seatToUse );
            // $scope.seatToUse    =   ( $scope.seatToUse > parseInt( $scope.selectedTable.MAX_SEATS ) ) ? $scope.selectedTable.MAX_SEATS  :  $scope.seatToUse;
    	};


        /**
         *  Select Table
         *  @param object table
         *  @return void
        **/

        $scope.selectTable              =   function( table ) {
            $scope.seatToUse            =   0;
            $scope.selectedTable        =   table;

            // Unselect active on all tables
            _.each( $scope.tables, function( table ){
                table.active    =   false;
            });

            table.active    =   true;
        }

        /**
         *  Cancel Table Selection
         *  @param
         *  @return
        **/

        $scope.cancelTableSelection     =   function(){
            $scope.selectedTable        =   false;
            // Unselect active on all tables
            _.each( $scope.tables, function( table ){
                table.active    =   false;
            });
        }

        /**
         *  Get Table Color Status
         *  @param object table
         *  @return string color
        **/

        $scope.getTableColorStatus      =   function( table ) {
            if( table.active && table.STATUS == 'out_of_use' ) {
                return 'table-out-of-use';
            } else if( table.active && table.STATUS == 'available' ) {
                return 'table-selected';
            } else if( table.active && table.STATUS == 'reserved' ) {
                return 'table-reserved';
            } else if( table.active ) {
                return 'table-in-use';
            }
            return '';
        }

        /**
         *  Set A table available
         *  @param object table
         *  @return void
        **/

        $scope.setAvailable         =   function( selectedtable ) {
            NexoAPI.Bootbox().confirm( '<?php echo _s( 'Would you like to set this table as available ? This assume there is nobody at this table.', 'nexo-restaurant' );?>', function( action ) {
                if( action ) {
                    $http.put( '<?php echo site_url([ 'rest', 'nexo_restaurant', 'table_usage' ]);?>/' + $scope.selectedTable.TABLE_ID +  '<?php echo store_get_param( '?' );?>', {
                        CURRENT_SEATS_USED      :   0,
                        STATUS                  :   'available'
                    }, {
                        headers			:	{
                            '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                        }
                    }).then(function(){
                        _.each( $scope.areas, function( area ) {
                            // Refresh Area table
                            if( area.active ) {
                                $scope.loadTables( area );
                            }
                        });
                    });
                }
            });
        }

        /**
         * Register NexoPOS filters
        **/

        NexoAPI.events.removeFilter( 'cart_item_name' );

        NexoAPI.events.addFilter( 'cart_item_name', function( item_name ) {
            return '<a class="btn btn-sm btn-default restaurant_item_note" href="javascript:void(0)" style="vertical-align:inherit;margin-right:10px;"><i class="fa fa-edit"></i></a> ' + item_name;
        });

        NexoAPI.events.addAction( 'cart_refreshed', function(){
            $( '.restaurant_item_note' ).bind( 'click', function() {
                var item_barcode     =   $( this ).closest( '[cart-item]').attr( 'data-item-barcode');
                var dom             =
                '<div class="form-group">' +
                  '<label for=""></label>' +
                  '<textarea type="text" class="form-control item_note_textarea" id="" placeholder=""/>' +
                  '<p class="help-block">Help text here.</p>' +
                '</div>';

                var item    =   v2Checkout.getItem( item_barcode );

                if( typeof item.metas == 'undefined' ) {
                    item.metas    =   new Object;
                }

                NexoAPI.Bootbox().confirm( '<?php echo _s( 'Add a note to this item', 'nexo-restaurant' );?>' + dom, function( action ) {
                    item.metas.restaurant_note      =   $( '.item_note_textarea' ).val();
                });

                if( angular.isDefined( item.metas.restaurant_note ) ) {
                    $( '.item_note_textarea' ).val( item.metas.restaurant_note )
                }
            });
        });

        NexoAPI.events.addFilter( 'test_order_type', function( order ){
            $http.put( '<?php echo site_url([ 'rest', 'nexo_restaurant', 'table_usage' ]);?>/' + $scope.selectedTable.TABLE_ID +  '<?php echo store_get_param( '?' );?>', {
                CURRENT_SEATS_USED      :   $scope.seatToUse,
                STATUS                  :   'in_use'
            }, {
                headers			:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                }
            });

            return order;
        });

    }

    selectTableCTRL.$inject =   [ '$compile', '$scope', '$timeout', '$http' ];
    tendooApp.controller( 'selectTableCTRL', selectTableCTRL );
</script>
<style type="text/css">
    .table-animation {
        padding:5px 0;
    }
    .table-animation:hover {
        background: #FFF;
        box-shadow: inset 5px 5px 100px #EEE;
        cursor: pointer;
    }
    .table-selected, .table-selected:hover {
        box-shadow: inset 5px 5px 100px #bde7f7;
    }
    .table-out-of-use:hover, .table-out-of-use {
        box-shadow: inset 5px 5px 100px #f7bdbd;
    }
    .table-in-use:hover, .table-in-use {
        box-shadow: inset 5px 5px 100px #c1f7bd;
    }
    .table-reserved:hover, .table-reserved {
        box-shadow: inset 5px 5px 100px #ffef9f;
    }
</style>

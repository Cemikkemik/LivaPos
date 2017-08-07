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
            templateUrl        :  '<?php echo site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'templates', 'table-selection' ] );?>',
            restrict            :   'E'
        }
    });

    tendooApp.directive( 'bookingUi', function(){
        return {
            templateUrl        :  '<?php echo site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'templates', 'booking-ui' ] );?>',
            restrict            :   'E',
            controller          :   function( $scope ) {
                $scope.viewDate         =   '<?php echo date_now();?>';
                $scope.calendarView     =   'month';
                $scope.cellIsOpen       =   false;
            }
        }
    });

    tendooApp.directive( 'orderTypes', function(){
        return {
            templateUrl        :  '<?php echo site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'templates', 'order-types' ] );?>',
            restrict            :   'E',
            controller          :   [ '$scope', '$timeout', function( $scope, $timeout ) {
                $timeout( function(){
                    angular.element( '.modal-dialog' ).css( 'width', '30%' );
                    angular.element( '.modal-body' ).css( 'padding-top', '0px' );
                    angular.element( '.modal-body' ).css( 'padding-bottom', '0px' );
                    angular.element( '.modal-body' ).css( 'padding-left', '0px' );
                    angular.element( '.modal-body' ).css( 'padding-right', '0px' );
                    angular.element( '.modal-body' ).css( 'overflow-x', 'hidden' );
                    angular.element( '.middle-content' ).css( 'padding', 0 );

                    $( '.modal-dialog' ).css({
                        'top': function () {
                            return ( window.innerHeight / 2 ) - ( $( '.modal-dialog' ).height() / 2);
                        }
                    });

                    $( '.modal-footer' ).prepend( '<a href="<?php echo site_url([ 'dashboard', store_slug(), 'nexo', 'commandes', 'lists']);?>" class="btn btn-warning"><?php echo _s( 'Exit', 'nexo-restaurant' );?></a>' );

                    $( '[data-dismiss="modal"]' ).remove();
                    $( '[data-bb-handler="cancel"]' ).remove();
                    $( '.modal-dialog' ).fadeIn(200);
                }, 450 );

                $scope.selectType       =   function( type ) {
                    _.each( $scope.types, function( _type ) {
                        _type.active       =   false;
                    });

                    type.active            =   true;
                }
            }]
        }
    })

    var selectTableCTRL     =   function( $compile, $scope, $timeout, $http, $interval ) {

        $scope.isAreaRoomsDisabled      =   <?php echo store_option( 'disable_area_rooms' ) == 'yes' ? 'true' : 'false';?>;
        $scope.spinner                  =   {}
        $scope.rooms                    =   [];
        $scope.areas                    =   [];
        $scope.tables                   =   [];
        $scope.selectedTable            =   false;
        $scope.roomHeaderHeight         =   0;
        $scope.hideSideKeys             =   true;
        $scope.serverDate               =   moment( '<?php echo date_now();?>' );

        $interval( () => {
            $scope.serverDate.add( 1, 's' );
        }, 1000 );

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
                if( $scope.selectedTable.STATUS != 'available' ) {
                    NexoAPI.Bootbox().alert( '<?php echo _s( 'You must select a table available.', 'nexo-restaurant' );?>' );
                    return false;
                }

                if( $scope.seatToUse == 0  || angular.isUndefined( $scope.seatToUse ) ) {
                    NexoAPI.Bootbox().alert( '<?php echo _s( 'You must set a used seats. You can set used seats only for available tables.', 'nexo-restaurant' );?>' );
                    return false;
                }

                if( $scope.isAreaRoomsDisabled ) { 
                    v2Checkout.CartMetas        =   _.extend( v2Checkout.CartMetas, {
                        table_id            :   $scope.selectedTable.ID,
                        room_id             :   0,
                        area_id             :   0,
                        seat_used           :   $scope.seatToUse > parseInt( $scope.selectedTable.MAX_SEATS ) ? $scope.selectedTable.MAX_SEATS  : $scope.seatToUse
                    });
                } else {
                    v2Checkout.CartMetas        =   _.extend( v2Checkout.CartMetas, {
                        table_id            :   $scope.selectedTable.TABLE_ID,
                        room_id             :   $scope.selectedRoom.ID,
                        area_id             :   $scope.selectedArea.AREA_ID,
                        seat_used           :   $scope.seatToUse > parseInt( $scope.selectedTable.MAX_SEATS ) ? $scope.selectedTable.MAX_SEATS  : $scope.seatToUse
                    });
                }
                
            } else {
                $scope.selectedTable    =   false;
            }
        }

        /**
         *  Get a class when a table is selected
         *  @param
         *  @return
        **/

        $scope.tableSelectedClass       =   ( selectedTable ) => {
            return selectedTable == false ? 'btn-default' :
                selectedTable.STATUS == 'in_use' ? 'btn-default' :  'btn-success';
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
                    if( ! action ) {
                        $scope.selectOrderType();
                    }

                    return $scope.checkSelectingTableAction( action );
    			}
    		});

            $scope.windowHeight				=	window.innerHeight;
            $scope.wrapperHeight			=	$scope.windowHeight - ( ( 56 * 2 ) + 30 );

            $timeout( function(){
    			angular.element( '.modal-dialog' ).css( 'width', '98%' );
		        angular.element( '.modal-body' ).css( 'padding-top', '0px' );
                angular.element( '.modal-body' ).css( 'padding-bottom', '0px' );
                angular.element( '.modal-body' ).css( 'padding-left', '0px' );
                angular.element( '.modal-body' ).css( 'height', $scope.wrapperHeight );
                angular.element( '.modal-body' ).css( 'overflow-x', 'hidden' );
    		}, 200 );

            $( '.table-selection' ).html( $compile( $( '.table-selection').html() )( $scope ) );
            $scope.getRooms();

            $( '[data-bb-handler="cancel"]' ).attr( 'ng-click', 'cancelTableSelection()' );
            $( '.modal-footer' ).html( $compile( $( '.modal-footer' ).html() )( $scope ) );

            /**
            * When the Rooms and Area are disabled. Just load the tables quickly
            **/

            if( $scope.isAreaRoomsDisabled ) { 
                $scope.loadTables();
            }
        }

        // Autorun Table
        angular.element( document ).ready( function(){
            $scope.selectOrderType();
        });

        /**
         * Select Order Type
         * @param void
        **/

        $scope.types               =   [{
            namespace       :   'delivery',
            text            :   '<?php echo _s( 'Delivery', 'nexo-restaurant' );?>'
        },{
            namespace       :   'dinein',
            text            :   '<?php echo _s( 'Dine In', 'nexo-restaurant' );?>'
        },{
            namespace       :   'takeaway',
            text            :   '<?php echo _s( 'Take Away', 'nexo-restaurant' );?>'
        }]

        <?php if( store_option( 'gastro_enable_booking' ) ):?>
        $scope.types.push({
            namespace       :   'booking',
            text            :   '<?php echo _s( 'Booking', 'nexo-restauarnt' );?>'
        });
        <?php endif;?>

        $scope.selectOrderType          =   function(){
            NexoAPI.Bootbox().confirm({
    			message 		:	'<div class="order-type-selection"><order-types types="types"></order-types></div>',
    			title			:	'<?php echo _s( 'Which operation would you proceed ?', 'nexo' );?>',
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
                    // check if there is one selected
                    var selected        =   false;
                    _.each( $scope.types, function( type ) {
                        if( type.active ) {
                            selected    =   true;
                            $scope.selectedOrderType    =   type;
                        }
                    });

                    if( ! selected ) {
                        NexoAPI.Bootbox().alert( '<?php echo _s( 'You must select an order type', 'nexo-restaurant' );?>' );
                        return false;
                    }

                    if( $scope.selectedOrderType.namespace == 'dinein' ) {
                        $( '[ng-click="openDelivery()"]' ).hide();
                        $scope.openTableSelection();
                    } else if( $scope.selectedOrderType.namespace == 'delivery' ) {
                        $( '[ng-click="openDelivery()"]' ).show();
                    } else if( $scope.selectedOrderType.namespace == 'takeaway' ) {
                        $( '[ng-click="openDelivery()"]' ).hide();
                    } else if( $scope.selectedOrderType.namespace == 'booking' ) {
                        // 
                        bootbox.confirm({
                            title: "<?php echo _s( 'Booking Management', 'nexo-restaurant' );?>",
                            message: '<div class="booking-wrapper" style="height:300px"><booking-ui></booking-ui></div>',
                            buttons: {
                                cancel: {
                                    label: '<i class="fa fa-times"></i> <?php echo _s( 'Cancel', 'nexo-restaurant' );?>'
                                },
                                confirm: {
                                    label: '<i class="fa fa-check"></i> <?php echo _s( 'Add the booking', 'nexo-restaurant' );?>'
                                }
                            },
                            callback: function (result) {
                                console.log('This was logged in the callback: ' + result);
                            }
                        });

                        $scope.windowHeight				=	window.innerHeight;
                        $scope.wrapperHeight			=	$scope.windowHeight - ( ( 56 * 2 ) + 30 );

                        $timeout( function(){
                            angular.element( '.modal-dialog' ).css( 'width', '98%' );
                            // angular.element( '.modal-body' ).css( 'padding-top', '0px' );
                            // angular.element( '.modal-body' ).css( 'padding-bottom', '0px' );
                            // angular.element( '.modal-body' ).css( 'padding-left', '0px' );
                            angular.element( '.modal-body' ).css( 'height', $scope.wrapperHeight );
                            angular.element( '.modal-body' ).css( 'overflow-x', 'hidden' );
                        }, 200 );

                        $( '.booking-wrapper' ).html( $compile( $( '.booking-wrapper').html() )( $scope ) );
                        $scope.getRooms();
                    }
    			}
    		});

            $( '.modal-dialog' ).hide();
            $( '.order-type-selection' ).html( $compile( $( '.order-type-selection').html() )( $scope ) );
        }

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
            _.each( $scope.areas, function( _area ) {
                _area.active                 =   false;
            });

            if( $scope.isAreaRoomsDisabled ) {
                link    =   '<?php echo site_url([ 'rest', 'nexo_restaurant', 'tables' ]);?>';
            } else {
                link    =   '<?php echo site_url([ 'rest', 'nexo_restaurant', 'tables_from_area' ]);?>' + '/' + area.AREA_ID;
            }

            if( typeof area != 'undefined' ) {
                $scope.selectedArea             =   area;
                area.active                     =   true;
            }
            
            $scope.spinner[ 'tables' ]      =   true;

            $http.get( link + '<?php echo store_get_param( '?' );?>', {
                headers			:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                }
            }).then(function( returned ) {
                $scope.spinner[ 'tables' ]     =   false;
                $scope.tables                    =   returned.data;
            });
        }

        /**
         *  get table timer
         *  @param table
         *  @return string
        **/

        $scope.getTimer         =   ( since )   =>  {
            if( since != '0000-00-00 00:00:00' ) {
                let now     =   $scope.serverDate.format();
                let then    =   since;

                var ms = moment( now ).diff(moment( then ) );
                var d = moment.duration(ms);
                var s = Math.floor(d.asHours()) + moment.utc(ms).format(":mm:ss");

                return s;
            } else {
                return '--:--:--';
            }
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
            $scope.seatToUse    =   ( $scope.seatToUse > parseInt( $scope.selectedTable.MAX_SEATS ) ) ? $scope.selectedTable.MAX_SEATS  :  $scope.seatToUse;
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

                    if( $scope.isAreaRoomsDisabled ) {
                        var link        =   '<?php echo site_url([ 'rest', 'nexo_restaurant', 'table_usage' ]);?>/' +
                        $scope.selectedTable.ID;
                    } else {
                        var link        =   '<?php echo site_url([ 'rest', 'nexo_restaurant', 'table_usage' ]);?>/' +
                        $scope.selectedTable.TABLE_ID;
                    }

                    $http.put(
                        link +  '<?php echo store_get_param( '?' );?>', {
                        CURRENT_SEATS_USED      :   0,
                        STATUS                  :   'available'
                    }, {
                        headers			:	{
                            '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                        }
                    }).then(function(){
                        if( $scope.isAreaRoomsDisabled ) {
                            $scope.loadTables();
                        } else {
                            _.each( $scope.areas, function( area ) {
                                // Refresh Area table
                                if( area.active ) {
                                    $scope.loadTables( area );
                                }
                            });
                        }
                        $scope.cancelTableSelection();
                    });
                }
            });
        }

        /**
         * Send a current order to the kitchen
         * @param void
         * @return void
        **/

        $scope.sendToKitchen    =   function(){
            if( $scope.selectedTable == false ) {
                v2Checkout.CartTitle    =   '<?php echo __( 'Take away', 'nexo-restaurant' );?>';
            } else {
                if( $scope.isAreaRoomsDisabled ) {
                    v2Checkout.CartTitle    =   '<?php echo __( 'Table Name', 'nexo-restaurant' );?> : ' + $scope.selectedTable.TABLE_NAME
                } else {
                    v2Checkout.CartTitle    =   $scope.selectedRoom.NAME + ' > ' + $scope.selectedArea.AREA_NAME + ' > ' + $scope.selectedTable.TABLE_NAME
                }
            }
			v2Checkout.cartSubmitOrder( 'cash' );
        }

        /**
         * Register NexoPOS filters
        **/

        NexoAPI.events.addFilter( 'item_loaded', ( item ) => {
            item[0].metas      =   {
                restaurant_note             :   '',
                restaurant_food_status      :   'not_ready' 
            }

            return item;
        })

        NexoAPI.events.removeFilter( 'cart_before_item_name' );

        NexoAPI.events.addFilter( 'cart_before_item_name', function( item_name ) {
            return '<a class="btn btn-sm btn-default restaurant_item_note" href="javascript:void(0)" style="vertical-align:inherit;margin-right:10px;float:left;"><i class="fa fa-edit"></i></a> ' + item_name;
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
                    item.metas.restaurant_note          =   $( '.item_note_textarea' ).val();
                    item.metas.restaurant_food_status   =   'not_ready';
                });

                if( angular.isDefined( item.metas.restaurant_note ) ) {
                    $( '.item_note_textarea' ).val( item.metas.restaurant_note )
                }
            });
        });

        /**
         * When cart is reset
         * Ask for other type again
        **/

        NexoAPI.events.addAction( 'reset_cart', function(){
            $timeout( function(){
                if( $scope.selectedOrderType.namespace == 'dinein' ) {
                    $scope.openTableSelection();
                }
            }, 500 );            
        });

        /**
         * If the order is delivery, invite the use to input delivery charges
        **/

        NexoAPI.events.addFilter( 'openPayBox', ( filter ) => {
            if( $scope.selectedOrderType.namespace == 'delivery' && $scope.price == null ) {
                NexoAPI.Toast()( '<?php echo _s( 'You must define delivery details.', 'nexo-restaurant' );?>' );

                var bool        =   true;
                var increment   =   0;
                var interval    =   setInterval( function(){
                    if( bool ) {
                        $( '[ng-click="openDelivery()"]' ).removeClass( 'btn-default' );
                        $( '[ng-click="openDelivery()"]' ).addClass( 'btn-warning' );
                    } else {
                        $( '[ng-click="openDelivery()"]' ).removeClass( 'btn-warning' );
                        $( '[ng-click="openDelivery()"]' ).addClass( 'btn-default' );
                    }

                    bool        =   !bool;
                    increment++;

                    if( increment == 6 ) {
                        clearInterval( interval );
                    }
                }, 250 );
                
                return false;
            } 
            return filter;
        });

        // When the order is submited, we just change the selected table status

        NexoAPI.events.addFilter( 'test_order_type', function( order ){

            if( $scope.isAreaRoomsDisabled ) {
                var link        =   '<?php echo site_url([ 'rest', 'nexo_restaurant', 'table_usage' ]);?>/' +
                $scope.selectedTable.ID;
            } else {
                var link        =   '<?php echo site_url([ 'rest', 'nexo_restaurant', 'table_usage' ]);?>/' +
                $scope.selectedTable.TABLE_ID;
            }

            $http.put( link +  '<?php echo store_get_param( '?' );?>', {
                CURRENT_SEATS_USED      :   $scope.seatToUse,
                STATUS                  :   'in_use',
                ORDER_ID                :   order[1].order_id,
            },{
                headers			:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                }
            });

            order[0]    =   true;

            // Print to kitchen
            let isComplete      =   order[0];
            let orderDetails    =   order[1];

            <?php if( get_option( store_prefix() . 'disable_kitchen_print' ) != 'yes' ): ?>
                
                $http.get( '<?php echo site_url([ 'rest', 'nexo_restaurant', 'print_to_kitchen' ]);?>/' + order[1].order_id +  '<?php echo store_get_param( '?' );?>' + '&app_code=<?php echo @$Options[ store_prefix() . 'nexopos_app_code' ];?>' ,{
                    headers			:	{
                        '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                    }
                }).then( ( returned ) => {
                    console.log( returned );
                });

            <?php endif;?>

            return order;
        });

        NexoAPI.events.addFilter( 'before_submit_order', function( order_details ){
            // no table has been selected
            order_details.ITEMS.map( ( item ) => {
                item.metas.restaurant_food_status   =   'not_ready';
                item.metas.restaurant_food_issue    =   '';
                if( angular.isUndefined( item.metas.restaurant_note ) ) {
                    item.metas.restaurant_note         =   '';
                }
                return item;
            });

            if( $scope.selectedOrderType.namespace == 'delivery' ) {
                // We may support take away or delivery
                <?php if( store_option( 'disable_kitchen_screen' ) != 'yes' ):?>
                order_details[ 'TYPE' ]                     =   'nexo_order_delivery_pending';
                order_details[ 'metas' ].order_real_type    =   'delivery';
                <?php else:?>
                order_details[ 'TYPE' ]                     =   'nexo_order_delivery_ready';
                order_details[ 'metas' ].order_real_type    =   'delivery';
                <?php endif;?>
                return order_details;
            } else if( $scope.selectedOrderType.namespace == 'dinein' ) {
                <?php if( store_option( 'disable_kitchen_screen' ) != 'yes' ):?>
                order_details[ 'TYPE' ]                     =   'nexo_order_dinein_pending';
                order_details[ 'metas' ].order_real_type    =   'dinein';
                <?php else:?>
                order_details[ 'TYPE' ]                     =   'nexo_order_dinein_ready';
                order_details[ 'metas' ].order_real_type    =   'dinein';
                <?php endif;?>
            } else if( $scope.selectedOrderType.namespace == 'takeaway' ) {
                <?php if( store_option( 'disable_kitchen_screen' ) != 'yes' ):?>
                order_details[ 'TYPE' ]                     =   'nexo_order_takeaway_pending';
                order_details[ 'metas' ].order_real_type    =   'takeaway';
                <?php else:?>
                order_details[ 'TYPE' ]                     =   'nexo_order_takeaway_ready';
                order_details[ 'metas' ].order_real_type    =   'takeaway';
                <?php endif;?>
            }

            return order_details;
        });

        NexoAPI.events.addAction( 'reset_cart', function(){
            $scope.selectedTable        =   false;
            $scope.selectedArea         =   false;
            $scope.selectedRoom         =   false;
            // $scope.openTableSelection();
        });

        /**
         * This will add a new button on the paybox
         * 
        **/

        NexoAPI.events.addAction( 'pay_box_loaded', function() {
            $( 'div.modal-footer' ).append( $compile( '<a class="btn btn-primary" ng-click="sendToKitchen()"><i class="fa fa-cutlery"></i> <?php echo _s( 'Send to the kitchen', 'nexo-restaurant' );?></a>' )( $scope ) );
        });

        setInterval( () => {
            $http.get( '<?php echo site_url([ 'rest', 'nexo_restaurant', 'google_refresh', store_get_param( '?' ) ]);?>' + '&app_code=<?php echo @$Options[ store_prefix() . 'nexopos_app_code' ];?>' ,{
                headers			:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                }
            }).then( ( returned ) => {
                console.log( returned );
            });
        }, 3400 * 1000 );
    }

    selectTableCTRL.$inject =   [ '$compile', '$scope', '$timeout', '$http', '$interval' ];
    tendooApp.controller( 'selectTableCTRL', selectTableCTRL );

    /**
     * Register New Order type to display on the order history box
    **/

    NexoAPI.events.addFilter( 'history_orderType', ( orderTypes ) => {
        orderTypes[ 'nexo_order_takeaway_pending' ]     =   {
            title           :   '<?php echo _s( 'Take Away Pending', 'nexo-restaurant' );?>',
            active          :   true
        }

        orderTypes[ 'nexo_order_dine_pending' ]     =   {
            title           :   '<?php echo _s( 'Dine In Pending', 'nexo-restaurant' );?>',
            active          :   false
        }

        orderTypes[ 'nexo_order_dine_pending' ]     =   {
            title           :   '<?php echo _s( 'Delivery Pending', 'nexo-restaurant' );?>',
            active          :   false
        }

        delete orderTypes[ 'nexo_order_devis' ];

        return orderTypes;
    })
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
    .timer {
        border-radius: 5px;
        background: #666;
        padding: 5px;
        color : #FFF;
        display: inline-block;
    }
</style>

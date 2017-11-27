<?php global $Options;?>
<script>

NexoAPI.showInfo     =   function({ message, title = '<?php echo _s( 'Warning', 'gastro' );?>', timer = undefined }) {
    swal({ text : message, title, type : 'info', timer });
}

NexoAPI.showError    =  function({ message, title = '<?php echo _s( 'An error occured', 'gastro' );?>', timer = undefined }) {
    swal({ text : message, title, type : 'error', timer })
}

NexoAPI.showConfirm     =   function({ message, title = '<?php echo _s( 'Would you like to proceed ?', 'nexo' );?>' }) {
    return swal({
        title,
        type                :   'warning',
        text                :   message,
        showCancelButton    :   true,
        confirmButtonText   :   '<?php echo _s( 'Yes', 'gastro' );?>',
        cancelButtonText    :   '<?php echo _s( 'No', 'gastro' );?>',
    })
}
</script>
<script type="text/javascript">
    tendooApp.filter('capitalize', function() {
        return function(input) {
        return (!!input) ? input.charAt(0).toUpperCase() + input.substr(1).toLowerCase() : '';
        }
    });

    tendooApp.directive("masonry", function () {
        var NGREPEAT_SOURCE_RE = '<!-- ngRepeat: ((.*) in ((.*?)( track by (.*))?)) -->';
        return {
            compile: function(element, attrs) {
                // auto add animation to brick element
                var animation = attrs.ngAnimate || "'masonry'";
                var $brick = element.find( '[masonry-item]');
                $brick.attr("ng-animate", animation);
                
                // generate item selector (exclude leaving items)
                var type = $brick.prop('tagName');
                var itemSelector = type+"[masonry-item]:not([class$='-leave-active'])";
                
                return function (scope, element, attrs) {
                    var options = angular.extend({
                        itemSelector: itemSelector
                    }, scope.$eval(attrs.masonry));
                    
                    // try to infer model from ngRepeat
                    if (!options.model) { 
                        var ngRepeatMatch = element.html().match(NGREPEAT_SOURCE_RE);
                        if (ngRepeatMatch) {
                            options.model = ngRepeatMatch[4];
                        }
                    }
                    
                    // initial animation
                    element.addClass('masonry');
                    
                    // Wait inside directives to render
                    setTimeout(function () {
                        element.masonry(options);
                        
                        element.on("$destroy", function () {
                            element.masonry('destroy')
                        });
                        
                        if (options.model) {
                            scope.$apply(function() {
                                scope.$watchCollection(options.model, function (_new, _old) {
                                    if(_new == _old) return;
                                    
                                    // Wait inside directives to render
                                    setTimeout(function () {
                                        element.masonry("reloadItems");
                                        element.masonry(options);
                                    });
                                });
                            });
                        }
                    });
                };
            }
        };
    });

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    var selectTableCTRL     =   function( $compile, $scope, $timeout, $http, $interval, $rootScope, $filter ) {
        $scope.isAreaRoomsDisabled      =   <?php echo store_option( 'disable_area_rooms' ) == 'yes' ? 'true' : 'false';?>;
        $scope.spinner                  =   {}
        $scope.areas                    =   [];
        $scope.tables                   =   [];
        $scope.selectedTable            =   false;
        $scope.roomHeaderHeight         =   0;
        $scope.hideSideKeys             =   true;
        $scope.serverDate               =   moment( '<?php echo date_now( 'Y-m-d h:i:s' );?>' );
        $scope.showHistory              =   false;
        $scope.wasOpenFromTableHistory  =   false;
        $scope.canEditOrderType         =   false; // is set only true when we're editing an order

        /**
         * Init time counter for tables timer
         */
        $interval( () => {
            $scope.serverDate.add( 1, 's' );
        }, 1000 );

        $scope.hideButton               =   {
            dot             :   true
        }

        $scope.wrapperHeight			=	$scope.windowHeight - ( ( 56 * 2 ) + 30 );
        $scope.reservationPattern       =   <?php echo json_encode( ( array ) explode( ',', @$Options[ store_prefix() . 'reservation_pattern' ] ) );?>;

        /**
         * Quick shortcut to print orders
         * @param object order details
         */
        $scope.printReceipt 		=	function( order ){
    		$( '#receipt-wrapper' ).remove();
            $( 'body' ).append( '<iframe id="receipt-wrapper" style="visibility:hidden;height:0px;width:0px;position:absolute;top:0;" src="<?php echo dashboard_url([ 'orders', 'receipt' ]);?>/' + order.ORDER_ID + '?refresh=true&autoprint=true"></iframe>' );
        }

        /**
         * Compare AMount
         * @param numeric first amount
         * @param string operation
         * @param numeric last amount
         * @return boolean
        **/

        $scope.compareAmount        =   function( first, operation, last ) {
            if( operation == '<' ) {
                return parseFloat( first ) < parseFloat( last );
            } else {
                return parseFloat( first ) > parseFloat( last );
            }
            return false;
        }

        /**
         * Parse Modifier Json
         * @param string modifier string
         * @return object
        **/

        $scope.jsonParse            =   function( string ) {
            if( typeof string == 'string' ) {
                return JSON.parse( string );
            }
            return string;
        }

        /**
         *  check selecting table action
         *  @param string action
         *  @return void
        **/

        $scope.checkSelectingTableAction    =   function( action ) {
            if( action == true ) {
                if( $scope.selectedTable.STATUS != 'available' && $scope.selectedTable.CURRENT_SEATS_USED == '0' ) {
                    NexoAPI.Bootbox().alert( '<?php echo _s( 'You must select a table available.', 'gastro' );?>' );
                    return false;
                }

                if( $scope.seatToUse == 0  || angular.isUndefined( $scope.seatToUse ) && $scope.showHistory == false ) {
                    NexoAPI.Bootbox().alert( '<?php echo _s( 'You must set a used seats. You can set used seats only for available tables.', 'gastro' );?>' );
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
                
                // restore visible buttons
                angular.element( '[ng-click="openPayBox()"]' ).closest( '.btn-group' ).show();
                angular.element( '[ng-click="openSaveBox()"]' ).closest( '.btn-group' ).show();
                
            } else {
                $scope.selectedTable    =   false;
            }
        }

        /**
        * GetModifier PRice
        * @param string json
        * @return numeric amount
        **/

        $scope.getModifierPrices        =   function( json ) {
            let total           =   0;
            if( json ) {
                let modifiers       =   $scope.jsonParse( json ) || false;
                if( modifiers !== false ) {
                    modifiers.forEach( entry => {
                        total       +=  parseFloat( entry.price );
                    })
                }
            }

            return total;
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
         * checkDate
         * @return valid date or current server date
        **/

        $scope.checkDate                =   function( date ) {
            if( moment( date ).isValid() ) {
                return date
            } else {
                return tendoo.date.format( 'YYYY-MM-DD HH:mm:ss' );
            }
        }

        /**
         *  Open table Selection
         *  @param void
         *  @return void
        **/

        $scope.openTableSelection       =       function() {
            $scope.seatToUse                =   0;
            $scope.areas                    =   [];
            $scope.tables                   =   [];
            $scope.selectedRoom             =   false;
            $scope.selectedArea             =   false;
            $scope.selectedTable            =   false;

            NexoAPI.Bootbox().confirm({
    			message 		:	'<div class="table-selection"><restaurant-rooms rooms="rooms"/></restaurant-rooms></div>',
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
                    if( 
                        ! action &&  
                        typeof v2Checkout.CartMetas.table_id == 'undefined' && 
                        typeof v2Checkout.CartMetas.seat_used == 'undefined' 
                    ) {
                        // if we're canceling table selection the order type is cancel as well
                        $scope.selectedOrderType            =   false;
                    }

                    $scope.showHistory      =   false;
                    return $scope.checkSelectingTableAction( action );

                },
                className       :   'table-selection-box'
    		});

            $scope.windowHeight				=	window.innerHeight;
            $scope.wrapperHeight			=	$scope.windowHeight - ( ( 56 * 2 ) + 30 );

            $timeout( function(){
    			angular.element( '.table-selection-box .modal-dialog' ).css( 'width', '98%' );
		        angular.element( '.table-selection-box .modal-body' ).css( 'padding-top', '0px' );
                angular.element( '.table-selection-box .modal-body' ).css( 'padding-bottom', '0px' );
                angular.element( '.table-selection-box .modal-body' ).css( 'padding-left', '0px' );
                angular.element( '.table-selection-box .modal-body' ).css( 'padding-right', '0px' );
                angular.element( '.table-selection-box .modal-body' ).css( 'height', $scope.wrapperHeight );
                angular.element( '.table-selection-box .modal-body' ).css( 'overflow-x', 'hidden' );
    		}, 200 );

            setTimeout( function(){
                
                /**
                * When the Rooms and Area are disabled. Just load the tables quickly
                **/

                if( $scope.isAreaRoomsDisabled ) { 
                    $scope.loadTables();
                } else {
                    $scope.loadRoomAreas();
                }

                $( '.table-selection' ).html( $compile( $( '.table-selection').html() )( $scope ) );
                $( '.table-selection-box [data-bb-handler="cancel"]' ).attr( 'ng-click', 'cancelTableSelection()' );
                $( '.table-selection-box .modal-footer' ).html( $compile( $( '.table-selection-box .modal-footer' ).html() )( $scope ) );
            }, 500 );
        }

        /**
         * Is a table selected
         * @return bool
        **/

        $scope.isTableSelected          =       function(){
            if ( ( $scope.selectedTable.STATUS == 'in_use' || $scope.selectedTable.STATUS == 'available' )  && $scope.showHistory == false ) {
                return true;
            }
            return false;
        }

        /**
         *  Open checkout to proceed to payment for a specific order
         * @param object order
         * @return void
        **/

        $scope.openCheckout             =   function( order ) {
            
            v2Checkout.emptyCartItemTable();
            v2Checkout.CartItems 			=	order.items;

            _.each( v2Checkout.CartItems, function( value, key ) {
            	value.QTE_ADDED		=	value.QUANTITE;
            });

            // @added CartRemisePercent
            // @since 2.9.6

            if( order.REMISE_TYPE != '' ) {
                v2Checkout.CartRemiseType			    =	order.REMISE_TYPE;
                v2Checkout.CartRemise				    =	NexoAPI.ParseFloat( order.REMISE );
                v2Checkout.CartRemisePercent			=	NexoAPI.ParseFloat( order.REMISE_PERCENT );
                v2Checkout.CartRemiseEnabled			=	true;
            }

            if( parseFloat( order.GROUP_DISCOUNT ) > 0 ) {
                v2Checkout.CartGroupDiscount 			=	parseFloat( order.GROUP_DISCOUNT ); // final amount
                v2Checkout.CartGroupDiscountAmount 	    =	parseFloat( order.GROUP_DISCOUNT ); // Amount set on each group
                v2Checkout.CartGroupDiscountType 		=	'amount'; // Discount type
                v2Checkout.CartGroupDiscountEnabled 	=	true;
            }

            v2Checkout.CartCustomerID 			=	order.REF_CLIENT;
            // @since 2.7.3
            v2Checkout.CartNote 				=	order.DESCRIPTION;
            v2Checkout.CartTitle 				=	order.TITRE;

            // @since 3.1.2
            v2Checkout.CartShipping 				=	parseFloat( order.SHIPPING_AMOUNT );
            $scope.price 						    =	v2Checkout.CartShipping; // for shipping directive
            $( '.cart-shipping-amount' ).html( $filter( 'moneyFormat' )( $scope.price ) );

            // Restore Custom Ristourne
            v2Checkout.restoreCustomRistourne();

            // Refresh Cart
            // Reset Cart state
            v2Checkout.buildCartItemTable();
            v2Checkout.refreshCart();
            v2Checkout.refreshCartValues();
            v2Checkout.ProcessURL				=	"<?php echo site_url(array( 'rest', 'nexo', 'order', User::id() ));?>" + '/' + order.ORDER_ID + "?store_id=<?php echo get_store_id();?>";
            v2Checkout.ProcessType				=	'PUT';
            

            // // Restore Shipping
            // // @since 3.1
            _.each( order.shipping, ( value, key ) => {
            	$scope[ key ] 	=	value;
            });

            $scope.wasOpenFromTableHistory      =   true;
            $rootScope.$emit( 'payBox.openPayBox' );
        }

        /**
         * Select Order Type
         * @param void
        **/

        $scope.types               =   []
        
        <?php if( store_option( 'disable_delivery' ) != 'yes' ):?>
        $scope.types.push({
            namespace       :   'delivery',
            text            :   '<?php echo _s( 'Delivery', 'gastro' );?>'
        });
        <?php endif;?>
        
        <?php if( store_option( 'disable_dinein' ) != 'yes' ):?>
        $scope.types.push({
            namespace       :   'dinein',
            text            :   '<?php echo _s( 'Dine In', 'gastro' );?>'
        });
        <?php endif;?>
        
        <?php if( store_option( 'disable_takeaway' ) != 'yes' ):?>
        $scope.types.push({
            namespace       :   'takeaway',
            text            :   '<?php echo _s( 'Take Away', 'gastro' );?>'
        });
        <?php endif;?>

        <?php if( store_option( 'disable_readyorders' ) != 'yes' ):?>
        $scope.types.push({
            namespace       :   'readyorders',
            text            :   '<?php echo _s( 'Ready Orders', 'gastro' );?>'
        });
        <?php endif;?>

        <?php if( store_option( 'disable_pendingorders' ) != 'yes' ):?>
        $scope.types.push({
            namespace       :   'pendingorders',
            text            :   '<?php echo _s( 'Pending Orders', 'gastro' );?>'
        });
        <?php endif;?>

        <?php if( store_option( 'disable_pos_waiter' ) != 'yes' ):?>
        $scope.types.push({
            namespace       :   'waiter',
            text            :   '<?php echo _s( 'Waiters', 'gastro' );?>'
        });
        <?php endif;?>

        <?php if( store_option( 'disable_saleslist' ) != 'yes' ):?>
        $scope.types.push({
            namespace       :   'return',
            text            :   '<?php echo _s( 'Sales List', 'gastro' );?>'
        });
        <?php endif;?>

        $scope.noOrderTypeOption        =   function(){
            NexoAPI.Bootbox().confirm( '<?php echo _s( 'You can\'t proceed to sales if there is no order type available. Please contact the manager. <br>Would you like to go back ?', 'gastro' );?>', function( action ) {
                if( action ) {
                    document.location   =   '<?php echo site_url([ 'dashboard', store_slug(), 'nexo', 'commandes', 'lists' ]);?>';
                } else {
                    $scope.noOrderTypeOption();
                }
            }); 
        }

        /**
         * Open history
         * @return void
        **/

        $scope.openHistory              =   function(){
            $scope.showHistory          =   true;
        }

        /**
         *  Load Room
         *  @param int room id
         *  @return void
        **/

        $scope.loadRoomAreas                =   function() {
            $http.get( '<?php echo site_url([ 'api', 'gastro', 'areas', store_get_param( '?' ) ]);?>', {
                headers			:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                }
            }).then(function( returned ) {
                $scope.spinner[ 'areas' ]     =   false;
                $scope.areas                    =   returned.data;
                // Load first room tables
                $scope.loadTables( $scope.areas[0] );
                $rootScope.$emit( 'tables-has-loaded' );
                console.log( 'emit:tables-has-loaded' );
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
                link    =   '<?php echo site_url([ 'api', 'gastro', 'tables' ]);?>';
            } else {
                link    =   '<?php echo site_url([ 'api', 'gastro', 'tables', 'area' ]);?>' + '/' + area.ID;
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
                let now     =   $scope.serverDate.format( 'YYYY-MM-DD HH:mm:ss' );
                let then    =   since;

                var ms = moment( tendoo.now() ).diff( moment( then ) );
                var d = moment.duration(ms);
                var s = Math.floor(d.asHours()) + moment.utc(ms).format(":mm:ss");

                return s;
            } else {
                return '--:--:--';
            }
        }

        /** 
         * New Order
         * Just place a new order over a selected table
         * @return void
        **/

        $scope.addNewItem             =   function( order ){

            if( $scope.isAreaRoomsDisabled ) { 
                v2Checkout.CartMetas        =   _.extend( v2Checkout.CartMetas, {
                    table_id            :   $scope.selectedTable.ID,
                    room_id             :   0,
                    area_id             :   0,
                    seat_used           :   $scope.selectedTable.CURRENT_SEATS_USED,
                    add_on              :   order.REF_ORDER
                });
            } else {
                v2Checkout.CartMetas        =   _.extend( v2Checkout.CartMetas, {
                    table_id            :   $scope.selectedTable.TABLE_ID,
                    room_id             :   $scope.selectedRoom.ID,
                    area_id             :   $scope.selectedArea.AREA_ID,
                    seat_used           :   $scope.selectedTable.CURRENT_SEATS_USED,
                    add_on              :   order.REF_ORDER
                });
            }

            $scope.seatToUse        =   $scope.selectedTable.CURRENT_SEATS_USED;
            angular.element( '.table-selection-box [data-bb-handler="confirm"]' ).trigger( 'click' );
            $scope.toggleConflictingButton( 'hide' );
        }

        /**
         * Hide button which might cause bugs
         * @return void
         */
        $scope.toggleConflictingButton      =   function( action ) {
            if( action === 'hide' ) {
                angular.element( '[ng-click="openTables()"]' ).hide();
                angular.element( '[ng-click="switchOrderType( \'takeaway\' )"]' ).hide();
                angular.element( '[ng-click="switchOrderType( \'delivery\' )"]' ).hide();
                angular.element( '[ng-click="openHistoryBox()"]' ).hide();
                angular.element( '[ng-click="openPayBox()"]' ).closest( '.btn-group' ).hide();
                angular.element( '[ng-click="openSaveBox()"]' ).closest( '.btn-group' ).hide();
            } else {
                angular.element( '[ng-click="openTables()"]' ).show();
                angular.element( '[ng-click="switchOrderType( \'takeaway\' )"]' ).show();
                angular.element( '[ng-click="switchOrderType( \'delivery\' )"]' ).show();
                angular.element( '[ng-click="openHistoryBox()"]' ).show();
                angular.element( '[ng-click="openPayBox()"]' ).closest( '.btn-group' ).show();
                angular.element( '[ng-click="openSaveBox()"]' ).closest( '.btn-group' ).show();
            }
        }

        /** 
         * Switch Order Type
         * @param string order type
         * @return void
        **/

        $scope.realOrderType            =   [
            'dinein',
            'delivery',
            'takeaway',
            'booking'
        ];

        $scope.switchOrderType              =   function( order_type = null ){
            // check if there is one selected
            var selected        =   false;
            _.each( $scope.types, function( type ) {
                if( _.indexOf( $scope.realOrderType, type.namespace ) != -1 ) {
                    if( order_type != null ) {
                        if( type.namespace == order_type ) {
                            selected    =   true;
                            $scope.selectedOrderType    =   type;
                        } 
                    } else {
                        console.log( 'has switched ordertype with no "type" provided as argument' );
                        if( type.active ) {
                            selected    =   true;
                            $scope.selectedOrderType    =   type;
                        }
                    }
                // for any custom action that we add on the order type array
                } else {
                    if( type.active ) {
                        if( type.namespace == 'return' ) {
                            document.location   =   '<?php echo dashboard_url([ 'orders' ]);?>';
                        } else if( type.namespace == 'readyorders' ) {
                            bootbox.hideAll();
                            $rootScope.$broadcast( 'open-ready-orders' );
                        } else if( type.namespace == 'pendingorders' ) {
                            $rootScope.$broadcast( 'open-history-box' );
                        } else if( type.namespace == 'waiter' ) {
                            $rootScope.$broadcast( 'open-waiter-screen' );
                        }
                        type.active     =   false;
                    }
                }
            });

            if( ! selected ) {
                // NexoAPI.Toast()( '<?php echo _s( 'You must select an order type', 'gastro' );?>' );
                return false;
            }

            if( $scope.selectedOrderType.namespace == 'dinein' ) {
                bootbox.hideAll();
                $scope.openTableSelection();
                $( '[ng-click="openDelivery()"]' ).hide();
                $( '#cart-details > tfoot.hidden-xs.hidden-sm.hidden-md > tr:nth-child(3)' ).hide();
            } else if( $scope.selectedOrderType.namespace == 'delivery' ) {
                bootbox.hideAll();
                $( '#cart-details > tfoot.hidden-xs.hidden-sm.hidden-md > tr:nth-child(3)' ).show();
                $( '[ng-click="openDelivery()"]' ).show();
            } else if( $scope.selectedOrderType.namespace == 'takeaway' ) {
                bootbox.hideAll();
                $( '[ng-click="openDelivery()"]' ).hide();
                $( '#cart-details > tfoot.hidden-xs.hidden-sm.hidden-md > tr:nth-child(3)' ).hide();
            } else if( $scope.selectedOrderType.namespace == 'booking' ) {
                bootbox.hideAll();
                bootbox.confirm({
                    title: "<?php echo _s( 'Booking Management', 'gastro' );?>",
                    message: '<div class="booking-wrapper" style="height:300px"><booking-ui></booking-ui></div>',
                    buttons: {
                        cancel: {
                            label: '<i class="fa fa-times"></i> <?php echo _s( 'Cancel', 'gastro' );?>'
                        },
                        confirm: {
                            label: '<i class="fa fa-check"></i> <?php echo _s( 'Add the booking', 'gastro' );?>'
                        }
                    },
                    callback: function (result) {
                        // 
                    },
                    className       :   'booking-box'
                });

                $scope.windowHeight				=	window.innerHeight;
                $scope.wrapperHeight			=	$scope.windowHeight - ( ( 56 * 2 ) + 30 );

                $timeout( function(){
                    angular.element( '.booking-box .modal-dialog' ).css( 'width', '98%' );
                    angular.element( '.booking-box .modal-body' ).css( 'height', $scope.wrapperHeight );
                    angular.element( '.booking-box .modal-body' ).css( 'overflow-x', 'hidden' );
                }, 200 );

                $( '.booking-wrapper' ).html( $compile( $( '.booking-wrapper').html() )( $scope ) );
                $scope.loadRoomAreas();
            } 
            v2Checkout.fixHeight();
        }

        /**
         * masonry orders
         * @return object
        **/

        $scope.masonry                      =   function( orders ) {
            let totalColumn;
            if( window.screen.width <= 320 ) {
                totalColumn         =   1;
            } else if( window.screen.width > 320 && window.screen.width <= 720 ) {
                totalColumn         =   2;
            } else {
                totalColumn         =   3;
            }

            for( let i = 0; i < totalColumn; i++ ) {
                
            }
        }

        /**
         * Set as Served
         * @param int order id
         * @return void
        **/

        $scope.setAsServed          =   function( order_id ) {
            NexoAPI.Bootbox().confirm( '<?php echo _s( 'Would you like to set this order has served ?', 'gastro' );?>', function( action ){
                if( action ) {
                    $http.post( '<?php echo site_url([ 'api', 'gastro', 'tables', 'serve', store_get_param( '?' ) ]);?>', {
                        order_id
                    }, {
                        headers			:	{
                            '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                        }
                    }).then(function( returned ) {
                        $rootScope.$broadcast( 'food-served', returned.data );

                        if( $( '.table-selection-box' ).length > 1 ) {
                            $scope.showHistory      =   false;
                            // to force refresh
                            $timeout(function(){
                                $scope.showHistory  =   true;
                            }, 100 );
                        }
                    }, function( returned ){
                        $rootScope.$broadcast( 'food-no-served', returned.data );
                    });
                }
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
            $scope.seatToUse    =   ( $scope.seatToUse > parseInt( $scope.selectedTable.MAX_SEATS ) ) ? $scope.selectedTable.MAX_SEATS  :  $scope.seatToUse;

            $( '.table-selection-box' ).find( '[data-bb-handler="confirm"]' ).trigger( 'click' );
    	};


        /**
         *  Select Table
         *  @param object table
         *  @return void
        **/

        $scope.selectTable              =   function( table ) {
            // if table is in use, just show his history
            if( table.STATUS == 'in_use' ) {
                $scope.showHistory      =   true;
            }
            
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
            $scope.showHistory          =   false;
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
         * Listen on OpenSelectOrderType
         * @return void
        **/

        $rootScope.$on( 'open-table-selection', function(){
            $scope.openTableSelection();
        });

        $rootScope.$on( 'filter-selected-order-type', function( orderType ) {
            $scope.selectedOrderType        =   orderType;    
        });

        $rootScope.$on( 'serve-order', function( event, $order_id ) {
            $scope.setAsServed( $order_id );
        })

        /**
         *  Set A table available
         *  @param object table
         *  @return void
        **/

        $scope.setAvailable         =   function( selectedtable ) {
            if( $scope.isAreaRoomsDisabled ) {
                var link        =   '<?php echo site_url([ 'api', 'gastro', 'tables', 'status' ]);?>/' +
                $scope.selectedTable.ID;
            } else {
                var link        =   '<?php echo site_url([ 'api', 'gastro', 'tables', 'status' ]);?>/' +
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
                $scope.showHistory      =   false;
                $scope.cancelTableSelection();
            });
        }

        /**
         * Send a current order to the kitchen
         * @param void
         * @return void
        **/

        $scope.sendToKitchen    =   function(){
            if( v2Checkout.isCartEmpty() ) {
                return NexoAPI.Toast()( '<?php echo _s( 'The Cart is empty.', 'gastro' );?>' );
            }

            if( typeof $scope.selectedOrderType === 'undefined' ) {
                return NexoAPI.showError({
                    message     :   '<?php echo _s( 'You must select an order type', 'gastro' );?>',
                    timer   :   2000
                });
            }

            if( [ 'dinein', 'takeaway', 'delivery' ].indexOf( $scope.selectedOrderType.namespace ) == -1 ) {
                return NexoAPI.showError({
                    message     :   '<?php echo _s( 'You must select an order type', 'gastro' );?>',
                    timer   :   2000
                });
            }

            NexoAPI.showConfirm({
                message     :   '<?php echo _s( 'Would you like to send that order to the kitchen ?', 'gastro' );?>',
            }).then( ( result ) => {
                if( result.value ) {
                    if( $scope.selectedTable == false ) {
                        v2Checkout.CartTitle    =   '<?php echo __( 'Take away', 'gastro' );?>';
                    } else {
                        if( $scope.isAreaRoomsDisabled ) {
                            v2Checkout.CartTitle    =   '<?php echo __( 'Table Name', 'gastro' );?> : ' + $scope.selectedTable.NAME
                        } else {
                            v2Checkout.CartTitle    =   $scope.selectedArea.NAME + ' > ' + $scope.selectedTable.TABLE_NAME
                        }
                    }
                    v2Checkout.cartSubmitOrder( 'cash' );
                }
            });
        }

        /**
         * Watch Select Order History
        **/

        $scope.$watch( 'showHistory', function( newValue, oldValue  ) {
            if( newValue ) {
                $scope.spinner[ 'tableHistory' ]        =   true;
                $scope.historyHasLoaded                 =   false;
                $http.get( '<?php echo site_url([ 'api', 'gastro', 'tables', 'history' ]);?>/' + ( $scope.selectedTable.TABLE_ID || $scope.selectedTable.ID ) +  '<?php echo store_get_param( '?' );?>' ,{
                    headers			:	{
                        '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                    }
                }).then( ( returned ) => {
                    $scope.spinner[ 'tableHistory' ]        =   false;
                    $scope.historyHasLoaded                 =   true;
                    $scope.treatTableOrderHistory( returned.data );
                    $timeout(function(){
                        $scope.$apply();
                    }, 3000 )
                });
            } else {
                $scope.historyHasLoaded                     =   false;
            }
        });

        /**
         * Parse Table order history
         * @param object order
         * @return object
        **/
        
        $scope.treatTableOrderHistory           =   function( raw ) {
            $scope.sessions                =   {};
            raw.forEach( ( order, index ) => {
                if( index == 0 ) {
                    $scope.sessionOrder         =   order;  
                }
                
                if( typeof $scope.sessions[ 'session-' + order.SESSION_ID ] == 'undefined' ) {
                    $scope.sessions[ 'session-' + order.SESSION_ID ]    =   {
                        starts      :   order.SESSION_STARTS,
                        ends        :   order.SESSION_ENDS,
                        orders      :   []
                    }
                }

                $scope.sessions[ 'session-' + order.SESSION_ID ].orders.push( order );
            });
        }

        /**
         * Checks whether an object is empty
         * @param object
         * @return bool
        **/

        $scope.isEmptyObject        =   function( object ) {
            return _.keys( object ).length == 0;
        }

        /**
         * Open table
         * @return void
        **/

        $scope.openTables           =   function(){
            $scope.switchOrderType( 'dinein' );
        }

        $( '.delivery-button' ).replaceWith( 
            $compile( $( '.delivery-button' )[0].outerHTML )( $scope ) 
        );
        $( '.takeaway-button' ).replaceWith( 
            $compile( $( '.takeaway-button' )[0].outerHTML )( $scope ) 
        );

        /**
         * Register NexoPOS filters
        **/

        NexoAPI.events.addFilter( 'item_loaded', ( item ) => {
            item.metas      =   {
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

                NexoAPI.Bootbox().confirm( '<?php echo _s( 'Add a note to this item', 'gastro' );?>' + dom, function( action ) {
                    item.metas.restaurant_note          =   $( '.item_note_textarea' ).val();
                    item.metas.restaurant_food_status   =   'not_ready';
                });

                if( angular.isDefined( item.metas.restaurant_note ) ) {
                    $( '.item_note_textarea' ).val( item.metas.restaurant_note )
                }
            });
        });

        /**
         * If the order is delivery, invite the use to input delivery charges
        **/

        NexoAPI.events.addFilter( 'openPayBox', ( filter ) => {
            if( $scope.selectedOrderType == undefined || $scope.selectedOrderType == false ) {
                NexoAPI.showError({
                    message     :   '<?php echo _s( 'You should define an order type before processing to payment', 'gastro' );?>'
                });
                return false;
            }
            return filter;
        });

        // When the order is submited, we just change the selected table status

        NexoAPI.events.addFilter( 'test_order_type', function( order ){

            if( $scope.isAreaRoomsDisabled ) {
                var link        =   '<?php echo site_url([ 'api', 'gastro', 'tables', 'status' ]);?>/' +
                $scope.selectedTable.ID;
            } else {
                var link        =   '<?php echo site_url([ 'api', 'gastro', 'tables', 'status' ]);?>/' +
                $scope.selectedTable.TABLE_ID;
            }

            // If the order is a dinein and a complete order, then set the table as available.
            if( order[1].restaurant_type == 'dinein' && order[1].order_type == 'nexo_order_comptant' ) {
                $scope.setAvailable( $scope.selectedTable );
            } else if( order[1].restaurant_type == 'dinein' && order[1].order_type == 'nexo_order_devis' ) {
                // if the order is a dinein and the order is not complete. Then update the table status
                $http.put( link +  '<?php echo store_get_param( '?' );?>', {
                    CURRENT_SEATS_USED      :   $scope.seatToUse,
                    STATUS                  :   'in_use',
                    ORDER_ID                :   order[1].order_id,
                    ORDER_TYPE              :   order[1].order_type
                },{
                    headers			:	{
                        '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                    }
                }).then( ( response ) => {
                    $rootScope.$on( 'tables-has-loaded', function(){
                        // trigger selected table
                        // since the hook "reset_cart" delete everything
                        _.each( $scope.tables, ( table ) => {
                            if( table.ID === response.data.ID ) {
                                $scope.selectTable( table );
                            }
                        });      
                    })              
                });
            }            

            <?php if( get_option( store_prefix() . 'disable_kitchen_print' ) != 'yes' ): ?>
                <?php if( get_option( store_prefix() . 'printing_option' ) == 'kitchen_printers'):?>
                    $http.get( '<?php echo site_url([ 'api', 'gastro', 'kitchens', 'split-print' ]);?>/' + order[1].order_id +  '<?php echo store_get_param( '?' );?>' + '&app_code=<?php echo @$Options[ store_prefix() . 'nexopos_app_code' ];?>' ,{
                        headers			:	{
                            '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                        }
                    }).then( ( returned ) => {
                        // 
                    });
                <?php elseif( store_option( 'printing_option' ) == 'single_printer' ):?>
                    $http.get( '<?php echo site_url([ 'api', 'gastro', 'kitchens', 'print' ]);?>/' + order[1].order_id +  '<?php echo store_get_param( '?' );?>' + '&app_code=<?php echo @$Options[ store_prefix() . 'nexopos_app_code' ];?>' ,{
                        headers			:	{
                            '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                        }
                    }).then( ( returned ) => {
                        // 
                    });
                <?php endif;?>
            <?php endif;?>

            if( order[1].order_type == 'nexo_order_devis' ) {
                order[0] 	=	true;
            }
            return order;
        });

        // All this works only if we're making a new order
        NexoAPI.events.addFilter( 'before_submit_order', function( order_details ){

            // only when pay box is on
            if( order_details.SOMME_PERCU < order_details.TOTAL && $( '.paxbox-box' ).length > 0 ) {
                NexoAPI.Bootbox().alert( '<?php echo _s( 'Incomplete order aren\'t allowed', 'nexo' );?>' );
                return {}
            }

            if( v2Checkout.ProcessType == 'POST' ) {
                // no table has been selected
                order_details.ITEMS.map( ( item ) => {
                    item.metas.restaurant_food_status   =   'not_ready';
                    item.metas.restaurant_food_issue    =   '';
                    if( angular.isUndefined( item.metas.restaurant_note ) ) {
                        item.metas.restaurant_note         =   '';
                    }
                    return item;
                });
            }

            if( $scope.selectedOrderType.namespace == 'delivery' ) {
                <?php if( store_option( 'disable_kitchen_screen' ) != 'yes' ):?>
                order_details[ 'RESTAURANT_ORDER_TYPE' ]    =   'delivery';
                order_details[ 'RESTAURANT_ORDER_STATUS' ]  =   'pending';
                order_details[ 'metas' ].order_real_type    =   'delivery';
                <?php else:?>
                order_details[ 'RESTAURANT_ORDER_TYPE' ]    =   'delivery';
                order_details[ 'RESTAURANT_ORDER_STATUS' ]  =   'ready';
                order_details[ 'metas' ].order_real_type    =   'delivery';
                <?php endif;?>
            } else if( $scope.selectedOrderType.namespace == 'takeaway' ) {
                <?php if( store_option( 'disable_kitchen_screen' ) != 'yes' ):?>
                order_details[ 'RESTAURANT_ORDER_TYPE' ]    =   'takeaway';
                order_details[ 'RESTAURANT_ORDER_STATUS' ]  =   'pending';
                order_details[ 'metas' ].order_real_type    =   'takeaway';
                <?php else:?>
                order_details[ 'RESTAURANT_ORDER_TYPE' ]    =   'takeaway';
                order_details[ 'RESTAURANT_ORDER_STATUS' ]  =   'ready';
                order_details[ 'metas' ].order_real_type    =   'takeaway';
                <?php endif;?>
            } else if( $scope.selectedOrderType.namespace == 'dinein' ) {
                <?php if( store_option( 'disable_kitchen_screen' ) != 'yes' ):?>
                order_details[ 'RESTAURANT_ORDER_TYPE' ]    =   'dinein';
                order_details[ 'RESTAURANT_ORDER_STATUS' ]  =   'pending';
                order_details[ 'metas' ].order_real_type    =   'dinein';
                <?php else:?>
                order_details[ 'RESTAURANT_ORDER_TYPE' ]    =   'dinein';
                order_details[ 'RESTAURANT_ORDER_STATUS' ]  =   'ready';
                order_details[ 'metas' ].order_real_type    =   'dinein';
                <?php endif;?>
            } 

            return order_details;
        });

        /**
         * This will add a new button on the paybox
        **/

        if( $( '.sendToKitchenButton' ).length > 0 ) {
            $( '.sendToKitchenButton' ).replaceWith( $compile( $( '.sendToKitchenButton' )[0].outerHTML )( $scope ) )
        }
        
        /**
         * Implementing To kitchen button
         */

        NexoAPI.events.addAction( 'reset_cart', function() {
            
            if( angular.element( '.table-selection-box' ).length == 0 ) {
                $scope.selectedTable            =   false;
                $scope.selectedArea             =   false;
                $scope.selectedRoom             =   false;
            }

            $scope.wasOpenFromTableHistory  =   false;

            // if table selection is not already open
            if( angular.element( '.table-selection-box' ).length == 0 ) {
                if( typeof $scope.selectedOrderType != 'undefined' ) {
                    $scope.toggleConflictingButton( 'show' );
                    $timeout( function(){
                        if( $scope.selectedOrderType.namespace == 'dinein' ) {
                            $scope.openTableSelection();
                        }
                    }, 500 );        
                }
            }
            $scope.modifiers    =   [];
        });

        setInterval( () => {
            $http.get( '<?php echo site_url([ 'api', 'gastro', 'kitchens', 'google-refresh', store_get_param( '?' ) ]);?>' + '&app_code=<?php echo @$Options[ store_prefix() . 'nexopos_app_code' ];?>' ,{
                headers			:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                }
            }).then( ( returned ) => {
                // 
            });
        }, 3400 * 1000 );
        
        /**
        * Update order type when an order is being open
        **/

        NexoAPI.events.addFilter( 'override_open_order', function({ order_details, proceed }) {
            
            _.each( $scope.types, function( type, index ) {
                if(

                _.indexOf( [ 
                    'takeaway', 
                    'dinein', 
                    'delivery' 
                ], order_details.order.RESTAURANT_ORDER_TYPE ) != -1 && 

                _.indexOf( [ 
                    'takeaway', 
                    'dinein', 
                    'delivery' 
                ], type.namespace ) != -1 ) {
                    if( order_details.order.RESTAURANT_ORDER_TYPE == type.namespace ) {
                        $scope.selectedOrderType    =   type;
                        type.active                 =   true;
                    } else {
                        type.active                 =   false;
                    }
                }
            });

            // 
            // $scope.switchOrderType();

            // an error may occur if the selected order type is not yet supproted by the system

            return {order_details, proceed};
        });

        /**
         * Refresh modfiiers
        **/

        NexoAPI.events.addAction( 'cart_refreshed', () => {
            // refresh item modifiers
            _.each( v2Checkout.CartItems, ( item, index ) => {
                if( typeof item.metas != 'undefined' ) {
                    if( typeof item.metas.modifiers ) {
                        let modifiersLabels     =   '';
                        let modifiers           =   typeof item.metas.modifiers == 'string' ? JSON.parse( item.metas.modifiers ) : item.metas.modifiers;
                        _.each( modifiers, ( modifier ) => {
                            if( parseInt( modifier.default ) == 1 ) { // means if the modifiers is active
                                modifiersLabels     +=  '<span class="label label-default"> + ' + modifier.group_name + ' : ' + modifier.name + '</span> &mdash; ' + NexoAPI.DisplayMoney( modifier.price ) + '<br>';
                            }
                        });

                        $( '[cart-item-id="'+ index + '"] .item-name' ).after( modifiersLabels );
                    }
                }
            }); 
        });

        $scope.runModifierPromise       =   function({ ids, index, increase, quantity, item, modifierIndex, proceed }) {
            let modifierPromise     =   new Promise( ( resolve, reject ) => {
                // it will be used on the modifiers directive
                if( ids.length > index ) {
                    resolve({ ids, index, increase, quantity, item, modifierIndex, proceed });
                } else {
                    reject();
                }
            });

            modifierPromise.then( ({ ids, index, modifierIndex, increase = false, quantity = 1, proceed, item }) => {

                NexoAPI.Bootbox().confirm({
                    message     :    '<modifiers ' 
                    + 'id="' + 'modifiers-' + modifierIndex
                    + '" increase="' + ( increase ) 
                    + '" qte="' + quantity 
                    + '" ' + ( index != null ? 'index="'+ index + '"' : '' ) 
                    + ' barcode="' + item.CODEBAR 
                    + '" item="' + ( ids[ modifierIndex ] ) 
                    + '" modifier-index="' + modifierIndex
                    + '" modifier-groups-length="' + ids.length + '"></modifiers>',
                    title       :   '<?php echo _s( 'Please select a modifier', 'nexo' );?>',
                    buttons: {
                        confirm: {
                            label: '<?php echo _s( 'Add modifiers', 'gastro' );?>',
                            className: 'btn-success btn-lg'
                        },
                        cancel: {
                            label: '<?php echo _s( 'Cancel', 'gastro' );?>',
                            className: 'btn-default btn-lg'
                        }
                    },
                    callback: function (result) {
                        if( result ) {
                            if( ids.length > modifierIndex + 1 ) {
                                $scope.runModifierPromise({ ids, index, modifierIndex : modifierIndex + 1, increase, quantity, proceed, item });
                            }
                        } else {
                            $rootScope.$emit( 'close.modifierBox', { ids, index, modifierIndex : modifierIndex + 1, increase, quantity, proceed, item })
                        }
                    },
                    animate: false
                });

                $( '#modifiers-' + modifierIndex ).replaceWith( $compile( $( '#modifiers-' + modifierIndex )[0].outerHTML )( $scope ) );
                $( '#modifiers-' + modifierIndex ).closest( '.bootbox' ).addClass( 'modifiers-box' );
                $( '.modal-body' ).css({
                    'overflow-y'    :   'scroll',
                    'height'        :   window.innerHeight - 200,
                });
                $( '#modifiers-' + modifierIndex ).closest( '.modal-dialog' ).addClass( 'animated fadeIn' );
                $( '.bootbox-close-button.close' ).remove(); 
            });
        }

        NexoAPI.events.addFilter( 'override_add_item', ({ item, proceed, quantity, increase = true, index = null }) => {
            if( item.REF_MODIFIERS_GROUP != '' && item.REF_MODIFIERS_GROUP != '0' ) { // && ( ( $scope.isCombo && $scope.comboActive ) || ! $scope.isCombo )
                $scope.currentItem      =   item;
                let ids                 =   item.REF_MODIFIERS_GROUP.split( ',' );
                let modifierIndex       =   0;
                $scope.runModifierPromise({ ids, index, modifierIndex, increase, quantity, proceed, item });  
                return { item, proceed : true };
            } //  if _item support modfiiers
            return { item, proceed };
        }, 99 );

        NexoAPI.events.addAction( 'reset_cart', function(){
            $scope.modifiers    =   [];
        });

        NexoAPI.events.addAction( 'pos_load_order', function( data ){
            $scope.switchOrderType( data.order[0].RESTAURANT_ORDER_TYPE );
        })
        /** 
         * Reset SelectedOrderType
        **/

        NexoAPI.events.addAction( 'order_history_cart_busy', function(){
            $scope.selectedOrderType            =   false;
        });

        // if we're editing an order, let set the order type selected
        // if( v2Checkout.ProcessType == 'PUT' ) {
        //     var orderType   =   v2Checkout.CartType.split( '_' );
        //     $scope.switchOrderType( orderType[2] );
        // }

        /**
         * Closing Order Type Selection in specific cases
        **/

        // NexoAPI.events.addAction( 'open_order_on_pos', function(){
        // });

        NexoAPI.events.addAction( 'close_paybox', function(){
            if( $scope.wasOpenFromTableHistory ) {
                $scope.wasOpenFromTableHistory = false;
            }

            // if we close the paybox, then just restore default informations
            if( v2Checkout.ProcessType == 'PUT' && $( '.table-selection-box' ).length > 0 ) {
                v2Checkout.resetCart();
                v2Checkout.ProcessURL 		=	"<?php echo site_url(array( 'rest', 'nexo', 'order', User::id() ));?>?store_id=<?php echo get_store_id();?>";
                v2Checkout.ProcessType 		=	'POST';
            }
        });
    }

    selectTableCTRL.$inject =   [ '$compile', '$scope', '$timeout', '$http', '$interval', '$rootScope', '$filter' ];
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
    .table-selected {
        background:#00a65a;
        color:#FFF;
    }
    .table-selected, .table-selected:hover {
        box-shadow: -5px 5px 4px 1px #bfbfbf;
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
    .products-list {
        list-style: none;
        margin: 0;
        padding: 0;
        padding-right: 15px;
        border-right: 1px #f4f4f4 solid;
        height: 100px;
        border-top: 1px #F4F4F4 solid;
    }
    .modifier-class {
        border: solid 1px #e8e8e8;
        padding: 2px 5px;
        display: block;
        border-radius: 20px;
        line-height: 16px;
    }
    .btn-no-border {
        border: solid 1px #367fa9 !important;
    }
</style>

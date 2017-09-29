<?php global $Options; ?>
<script type="text/javascript">
    var watchRestaurantCTRL         =   function( $scope, $http, $timeout, $compile ) {
        $scope.orders               =   [];
        $scope.products             =   [];
        $scope.freshOrderMin        =   <?php echo  store_option( 'fresh_order_min', 10 );?>;
        $scope.lateOrderMin         =   <?php  echo store_option( 'late_order_min', 20 );?>;
        $scope.tooLateOrderMin        =   <?php  echo store_option( 'too_late_order_min', 30 );?>;

        $scope.freshOrderColor        =   '<?php  echo store_option( 'fresh_order_color', '' );?>';
        $scope.lateOrderColor        =   '<?php  echo store_option( 'late_order_color', 'bg-warning box-warning' );?>';
        $scope.tooLateOrderColor        =   '<?php  echo store_option( 'too_late_order_color', 'bg-danger box-danger' );?>';
        
        $scope.timeInterval         =   <?php echo @$Options[ 'refreshing_seconds' ] == null ? 3000 : intval( @$Options[ 'refreshing_seconds' ] ) * 1000;?>;

        $scope.isAreaRoomsDisabled  =   <?php echo store_option( 'disable_area_rooms' ) == 'yes' ? 'true': 'false';?>;

        if( ! $scope.isAreaRoomsDisabled ) {
            $scope.kitchen              =   <?php echo json_encode( $kitchen );?>;
            $scope.kitchen              =   $scope.kitchen[0];
            $scope.room_id              =   0;
            $scope.kitchen_id           =   $scope.kitchen.ID;
        } else {
            $scope.kitchen_id           =   0;
            $scope.room_id              =   0;
        }
        
        $scope.order_types              =   {
            'ready'       :    '<?php echo __( 'Ready', 'nexo-restaurant' );?>',
            'ongoing'       :    '<?php echo __( 'On Going', 'nexo-restaurant' );?>',
            'pending'       :    '<?php echo __( 'Pending', 'nexo-restaurant' );?>',
            'canceled'       :    '<?php echo __( 'Canceled', 'nexo-restaurant' );?>',
            'rejected'       :    '<?php echo __( 'Rejected', 'nexo-restaurant' );?>',
            'partially'     :   '<?php echo __( 'Partially', 'nexo-restaurant' );?>'
        }

        /**
         *  Testing order waiting time to apply a color
         * @param object order
         * @return string class
        **/

        $scope.testOrderWaitingTime         =   function( order ) {
            if( $scope.freshOrderMin  < $scope.lateOrderMin  < $scope.tooLateOrderMin ) {
                let currentTime         =   moment( tendoo.date.format() ).diff( 
                    moment( order.DATE_CREATION ).format(), 'minutes' 
                );

                if( currentTime > 0 && currentTime <= $scope.freshOrderMin ) {
                    return $scope.freshOrderColor + ' diff-' + currentTime;
                } else if( currentTime > $scope.freshOrderMin && currentTime <= $scope.lateOrderMin ) {
                    return $scope.lateOrderColor + ' diff-' + currentTime;
                } else if( currentTime > $scope.lateOrderMin ) { // check this if you would like to add more times
                    return $scope.tooLateOrderColor + ' diff-' + currentTime;
                }
            } else {
                console.log( 'Invalid Alert Pattern' );
            }
        }

        /**
         *  Change Food State
         *  @param object order
         *  @param string food state
         *  @return void
        **/

        $scope.changeFoodState      =   ( order, state )  =>  {
            var postObject          =   {
                '<?php echo $this->security->get_csrf_token_name();?>'    :   '<?php echo $this->security->get_csrf_hash();
                ?>',
                selected_foods          :   [],
                all_foods               :   [],
                complete_cooking        :   true,
                order_id                :   order.ORDER_ID,
                order_code              :   order.CODE,
                state                   :   state,
                order_real_type         :   order.REAL_TYPE
            };

            _.each( order.meals, ( meal ) => {
                _.each( meal, ( item ) => {
                    if( item.active ) {
                        postObject.selected_foods.push( item.COMMAND_PRODUCT_ID );
                    }
                    postObject.all_foods.push( item.COMMAND_PRODUCT_ID );
                })
            })

            $http.post('<?php echo site_url([ 'rest', 'nexo_restaurant', 'food_state', store_get_param('?') ] );?>', postObject, {
                headers			:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                }
            }).then(function( data ) {
                $scope.fetchOrders(0);
                $scope.unselectAllItems( order );
            });
        }

        $scope.getOrders            =   function( timeInterval = 0 ) {
            $timeout( function(){
                $scope.fetchOrders( () => {
                    $scope.getOrders();
                });
            }, timeInterval == 0 ? $scope.timeInterval : timeInterval );
        }

        /**
         *  fetch orders
         *  @param
         *  @return
        **/

        $scope.rawOrdersCodes   =   [];
        $scope.ordersCodes      =   [];

        $scope.fetchOrders      =   function( callback = null ) {

            $http.get( '<?php echo site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'get_orders' ]);?>?from-kitchen=true&takeaway_kitchen=<?php echo store_option( 'takeaway_kitchen' );?>&current_kitchen=' + $scope.kitchen_id )
            .then( function( returned ){
                // if nothing is returned, then crash here
                if( returned.data.length == 0 ) {
                    $scope.columns          =   [];
                    typeof callback == 'function' ? callback() : null;
                    return;
                }

                // filter orders. only allow supported orders
                let filteredOrders              =   [];
                $scope.rawOrdersCodes           =   []; // reset RawCodes
                _.each( returned.data, function( order, index ) {
                    if( ! $scope.hideOrder( order ) ) {
                        filteredOrders.push( order );
                        $scope.rawOrdersCodes.push( order.CODE );
                    }
                });
                
                if( $scope.orders.length == 0 ) {
                    $scope.orders     =     filteredOrders;

                    <?php if( store_option( 'disable_meal_feature' ) == 'yes' ):?>
                        $scope.orders.forEach( ( order ) => {
                            if( typeof order.meals == 'undefined' ) {
                                order.meals     =   [];
                            }

                            _.each( order.items, ( item ) => {
                                // if meal feature is disabled, we'll group all mean on the same array
                                if( typeof order.meals[0] == 'undefined' ) {
                                    order.meals[0]   =   [];
                                }

                                item.MODIFIERS      =   angular.fromJson( item.MODIFIERS );   
                                order.meals[0].push( item );
                            });  
                            // save order code
                            $scope.ordersCodes.push( order.CODE );                   
                        });
                    <?php else:?>
                        $scope.orders.forEach( ( order ) => {
                            if( typeof order.meals == 'undefined' ) {
                                order.meals     =   {};
                            }

                            _.each( order.items, ( item ) => {

                                if( typeof order.meals[ item.MEAL ] == 'undefined' ) {
                                    order.meals[ item.MEAL ]   =   [];
                                }

                                item.MODIFIERS      =   angular.fromJson( item.MODIFIERS );   
                                order.meals[ item.MEAL ].push( item );
                            });    
                            // save order code
                            $scope.ordersCodes.push( order.CODE );                          
                        });
                    <?php endif;?>

                } else {
                    // first remove all orders which doesnt exists
                    let newFilteredOrders           =   [];
                    let newFilteredOrdersCodes      =   [];
                    
                    // $scope.ordersCodes              =   [];
                    _.each( $scope.orders, function( order ) {
                        // if an existing order exists in the raw
                        if( _.indexOf( $scope.rawOrdersCodes, order.CODE ) != -1 ) {
                            // Let update current orders items status
                            _.each( filteredOrders, function( __updatedOrder ) {
                                if( order.CODE == __updatedOrder.CODE ) {
                                    <?php if( store_option( 'disable_meal_feature' ) == 'yes' ):?>
                                        if( typeof __updatedOrder.meals == 'undefined' ) {
                                            __updatedOrder.meals     =   [];
                                        }

                                        _.each( __updatedOrder.items, ( item ) => {
                                            // if meal feature is disabled, we'll group all mean on the same array
                                            if( typeof __updatedOrder.meals[0] == 'undefined' ) {
                                                __updatedOrder.meals[0]   =   [];
                                            }

                                            let itemsStatuses           =   {};
                                            _.each( order.meals[0], function( currentMeal ) {
                                                if( item.CODEBAR == currentMeal.CODEBAR ) {
                                                    itemsStatuses[ item.CODEBAR ]     =   typeof currentMeal.active == 'undefined' ? false : currentMeal.active;
                                                }
                                            });

                                            item.active         =   itemsStatuses[ item.CODEBAR ];      
                                            item.MODIFIERS      =   angular.fromJson( item.MODIFIERS );   
                                            __updatedOrder.meals[0].push( item );
                                        });  
                                    <?php else:?>
                                        if( typeof __updatedOrder.meals == 'undefined' ) {
                                            __updatedOrder.meals     =   {};
                                        }

                                        _.each( __updatedOrder.items, ( item ) => {
                                            
                                            if( typeof __updatedOrder.meals[ item.MEAL ] == 'undefined' ) {
                                                __updatedOrder.meals[ item.MEAL ]   =   [];
                                            }

                                            let itemsStatuses           =   {};
                                            
                                            _.each( order.meals, function( meal ) {
                                                _.each( meal, function( meal_item ) {
                                                    if( item.CODEBAR == meal_item.CODEBAR ) {
                                                        itemsStatuses[ meal_item.CODEBAR ]     =   typeof meal_item.active == 'undefined' ? false : meal_item.active;
                                                    }
                                                })
                                            });

                                            item.active         =   itemsStatuses[ item.CODEBAR ];      
                                            item.MODIFIERS      =   angular.fromJson( item.MODIFIERS );   
                                            __updatedOrder.meals[ item.MEAL ].push( item );
                                        });  

                                    <?php endif;?>
                                    order.meals     =   __updatedOrder.meals;
                                }
                            });                            
                            
                            // we can make an animation here
                            newFilteredOrders.push( order );
                            newFilteredOrdersCodes.push( order.CODE );
                            // $scope.ordersCodes.push( order.CODE );
                        }
                    });

                    // Filtered orders and codes
                    $scope.orders               =   newFilteredOrders;
                    $scope.ordersCodes          =   newFilteredOrdersCodes;
                    
                    // just build new
                    let newOrders               =   [];
                    let newOrdersCodes          =   [];
                    
                    _.each( filteredOrders, function( order ) {
                        if( _.indexOf( $scope.ordersCodes, order.CODE ) == -1 ) {
                         // the order we're building should not already exists ont he ordersCodes;
                        <?php if( store_option( 'disable_meal_feature' ) == 'yes' ):?>
                            if( typeof order.meals == 'undefined' ) {
                                order.meals     =   [];
                            }

                            _.each( order.items, ( item ) => {
                                // if meal feature is disabled, we'll group all mean on the same array
                                if( typeof order.meals[0] == 'undefined' ) {
                                    order.meals[0]   =   [];
                                }

                                item.MODIFIERS      =   angular.fromJson( item.MODIFIERS );   
                                order.meals[0].push( item );
                            });  
                            // save order code
                            $scope.ordersCodes.push( order.CODE );                   
                        <?php else:?>
                            if( typeof order.meals == 'undefined' ) {
                                order.meals     =   {};
                            }

                            _.each( order.items, ( item ) => {

                                if( typeof order.meals[ item.MEAL ] == 'undefined' ) {
                                    order.meals[ item.MEAL ]   =   [];
                                }

                                item.MODIFIERS      =   angular.fromJson( item.MODIFIERS );   
                                order.meals[ item.MEAL ].push( item );
                            });    
                            // save order code
                            $scope.ordersCodes.push( order.CODE );                          
                        <?php endif;?>
                            newOrders.push( order );
                            newOrdersCodes.push( order.CODE );
                        }
                    });
                    
                    // Only announce 1 order
                    if( newOrders.length > 0 ) {
                        if( newOrders.length == 1 ) {
                            if( returned.data[0].REAL_TYPE == 'takeaway' ) {
                                $scope.synthesizer( '<?php echo _s( 'A new take away order has been placed.', 'nexo-restaurant' );?>' );
                            } else if( returned.data[0].REAL_TYPE == 'delivery' ) {
                                $scope.synthesizer( '<?php echo _s( 'A new delivery order has been placed.', 'nexo-restaurant' );?>' );
                            } else if( returned.data[0].REAL_TYPE == 'dinein' ) {
                                $scope.synthesizer( '<?php echo _s( 'A new dine in order has been placed, at the table %s.', 'nexo-restaurant' );?>'.replace( '%s', returned.data[0].TABLE_NAME ) );
                            } else if( returned.data[0].REAL_TYPE == 'booking' ) {
                                $scope.synthesizer( '<?php echo _s( 'A new booking order has been placed, at the table %s.', 'nexo-restaurant' );?>'.replace( '%s', returned.data[0].TABLE_NAME ) );
                            }                        
                        } 

                        _.each( newOrders, function( order ){
                            $scope.orders.unshift( order );
                        });

                        $scope.ordersCodes.concat( newOrdersCodes );
                    }
                    
                }

                // Order everything so that it can be shown as masonry
                var availableColumns        =   3;
                var currentIndex            =   0;
                var columns                 =   [];
                
                $scope.noValidOrder         =   false; 
                _.each( $scope.orders, function( order ){
                    if( order.STATUS != 'ready' ) {
                        if( typeof columns[ currentIndex ] == 'undefined' ) {
                            columns[ currentIndex ]  =  [];
                        }

                        // we'll skip order ready
                        
                        columns[ currentIndex ].push( order );

                        currentIndex++;
                        
                        if( currentIndex == availableColumns ) {
                            currentIndex    =   0;
                        }  
                    }
                });

                $scope.columns      =   columns; 
                typeof callback == 'function' ? callback() : null;

            },function(){
                typeof callback == 'function' ? callback() : null;
            });
        }

        /**
         *  get existing order
         *  @param string order code
         *  @return object
        **/

        $scope.getExistingOrder         =   ( order_code ) => {
            for( let order of $scope.orders ) {
                if( order.CODE == order_code ) {
                    return order;
                }
            }
            return {};
        }

        /**
         *  Restrict display for each buttons
         *  @param string item status
         *  @return bool
        **/

        $scope.ifAllSelectedItemsIs        =   ( status, order ) => {
            let isNotActive   =   [], isActiveAndValid     =   [], isActiveAndNotValid     =   [];
            let totalFoodNbr    =   0;

            _.each( order.meals, ( meal ) => {
                _.each( meal, ( food ) => {
                    if( food.active ) {
                        if( food.FOOD_STATUS != status ) {
                            isActiveAndNotValid.push( false );
                        } else {
                            isActiveAndValid.push( true );
                        }
                    } else {
                        isNotActive.push( false );
                    }
                    totalFoodNbr++;
                });
            });

            // as much unchecked that available item
            return ( isActiveAndNotValid.length > 0 ) ? false : ( isActiveAndValid.length > 0 ) ? true : false;          
        }

        /**
         *  Get Existing item
         *  @param object order
         *  @param string barcode
         *  @return object
        **/

        $scope.getExistingItem          =   ( order_index, meal_index, item_barcode ) => {
            if( typeof $scope.orders[ order_index ] != 'undefined') {
                if( typeof $scope.orders[ order_index ].meals[ meal_index ] != 'undefined' ) {
                    for( let item of $scope.orders[ order_index ].meals[ meal_index ] ) {
                        if( item.CODEBAR == item_barcode ) {
                            return item;
                        }
                    }
                }   
            }
            return {}
        }

        /** 
         * Parse JSon
         * @param json
         * @return object
        **/

        $scope.parseJSON        =   function( json ) {
            return angular.fromJson( json )
        }

        /**
         *  Get Order Status
         *  @param object order
         *  @return void
        **/

        $scope.getOrderStatus   =   function( order ) {
            return $scope.order_types[ order.STATUS ] == void(0) ? '<?php echo _s( 'Unknow Order', 'nexo-restaurant' );?>' : $scope.order_types[ order.STATUS ];
        }

        /**
         *  Select Items
         *  @param object item
         *  @return void
        **/

        $scope.selectItem       =   function( food ){
            if( typeof food.active === 'undefined' ) {
                food.active        =   true;
            } else {
                food.active        =   !food.active;
            }
        }

        /**
         *  Select ALl Items
         *  @param object
         *  @return void
        **/

        $scope.selectAllItems       =   function( order ) {
            _.each( order.meals, function( meal ) {
                _.each( meal, function( item ){
                    item.active     =   true;
                })
            })
        }

        /**
         *  Unselect All items
         *  @param object order
         *  @return void
        **/

        $scope.unselectAllItems     =   function( order_index ){
            if( typeof order_index != 'object' ) {
                for( let meal_index in $scope.orders[ order_index ].meals ) {
                    for( let item of $scope.orders[ order_index ].meals[ meal_index ] ) {
                        item.active     =   false;
                    }
                }
            } else {
                for( let meal_index in order_index.meals ) {
                    for( let item of order_index.meals[ meal_index ] ) {
                        item.active     =   false;
                    }
                };
            }
        }

        /**
         *  Cook
         *  @param  order
         *  @return void
        **/

        $scope.cook                 =   ( order )   =>  {
            var postObject          =   {
                '<?php echo $this->security->get_csrf_token_name();?>'    :   '<?php echo $this->security->get_csrf_hash();
                ?>',
                during_cooking          :   [],
                not_cooked              :   [],
                complete_cooking        :   true,
                order_id                :   order.ORDER_ID,
                order_code              :   order.CODE,
                order_real_type         :   order.REAL_TYPE
            };

            for( let item of order.items ) {
                if( ! item.active ) {
                    postObject.complete_cooking     =   false;
                    postObject.not_cooked.push( item.COMMAND_PRODUCT_ID );
                } else {
                    postObject.during_cooking.push( item.COMMAND_PRODUCT_ID );
                }
            }

            $http.post('<?php echo site_url([ 'rest', 'nexo_restaurant', 'start_cooking', store_get_param('?') ] );?>', postObject, {
    			headers			:	{
    				'<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
    			}
    		}).then(function( data ) {
                $scope.fetchOrders();
                $scope.unselectAllItems( order );
            })
        }

        /**
         *  Speech Synthesizer
         * @param void
         * @return void
        **/

        $scope.synthesizer          =   function( word ) {
            <?php if( store_option( 'enable_kitchen_synthesizer' ) == 'yes' ):?>
            var msg = new SpeechSynthesisUtterance();
            var voices = window.speechSynthesis.getVoices();
            msg.voice = voices[1]; // Note: some voices don't support altering params
            msg.voiceURI = 'native';
            msg.volume = 1; // 0 to 1
            msg.rate = 1; // 0.1 to 10
            msg.pitch = 2; //0 to 2
            msg.text = word;
            msg.lang = 'en-US';

            msg.onend = function(e) {
                // console.log('Finished in ' + event.elapsedTime + ' seconds.');
            };

            speechSynthesis.speak(msg);
            <?php endif;?>
        }

        /**
         *  Toggle FullScreen
         *  @param void
         *  @return void
        **/


        $scope.toggleFullScreen     =   ()  =>  {
            if (!document.fullscreenElement &&    // alternative standard method
              !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement ) {  // current working methods
            if (document.documentElement.requestFullscreen) {
              document.documentElement.requestFullscreen();
            } else if (document.documentElement.msRequestFullscreen) {
              document.documentElement.msRequestFullscreen();
            } else if (document.documentElement.mozRequestFullScreen) {
              document.documentElement.mozRequestFullScreen();
            } else if (document.documentElement.webkitRequestFullscreen) {
              document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
            }
          } else {
            if (document.exitFullscreen) {
              document.exitFullscreen();
            } else if (document.msExitFullscreen) {
              document.msExitFullscreen();
            } else if (document.mozCancelFullScreen) {
              document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
              document.webkitExitFullscreen();
            }
          }
        }


        $scope.fetchOrders( () => {
            $scope.getOrders();
        });


        /**
         *  Hide order
         * @param object
         * @return boolean
        **/

        $scope.hideOrder        =   function( order ){
            let __return          =   false;
            
            if( _.indexOf([ 'ready', 'served' ], order.STATUS ) != -1 ) {
                __return        =   true;
            }

            return __return;
        }

        // $( '.content-header h1' ).append( $( '.kitchen-buttons' )[0].innerHTML );
        // angular.element( '.kitchen-buttons' ).html( $compile( $( '.kitchen-buttons' ).html() )($scope) );
    }

    tendooApp.controller( 'watchRestaurantCTRL', [ '$scope', '$http', '$timeout', '$compile', watchRestaurantCTRL ]);
</script>

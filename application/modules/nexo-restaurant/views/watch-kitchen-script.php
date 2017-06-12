<?php global $Options; ?>
<script type="text/javascript">
    var watchRestaurantCTRL         =   function( $scope, $http, $timeout, $compile ) {
        $scope.orders               =   [];
        $scope.products             =   [];
        $scope.timeInterval         =   <?php echo @$Options[ 'refreshing_seconds' ] == null ? 3000 : intval( @$Options[ 'refreshing_seconds' ] ) * 1000;?>;
        $scope.categories           =   <?php echo json_encode( $categories );?>;
        $scope.categories_ids       =   [];

        $scope.categories.forEach( ( category ) => {
            $scope.categories_ids.push( category.ID );
        });

        $scope.kitchen              =   <?php echo json_encode( $kitchen );?>;
        $scope.order_types          =   <?php echo json_encode( $this->config->item( 'nexo_order_types' ) );?>;
        $scope.kitchen              =   $scope.kitchen[0];
        $scope.room_id              =   $scope.kitchen.REF_ROOM;

        /**
         *  Categorie check
         *  @param object item
         *  @return boolean
        **/

        /** $scope.categoryCheck        =   ( item ) => {
            if( item.REF_CATEGORIE == '' ) {
                return true;
            }

            if( _.indexOf( $scope.categories_ids, item.REF_CATEGORIE ) == -1 ) {
                return false;
            }
            return true;
        } **/

        /**
         *  Categories check all
         *  @param object items
         *  @return vdoi
        **/

        /** $scope.categoryCheckAll     =   ( items ) => {

            let isVisible           =   false;
            items.meals.forEach( ( meal ) => {


                if( ! isVisible ) {
                    // isVisible           =  $scope.categoryCheck( item );
                }
            });
            return isVisible;
        } **/

        /**
         *  Check category for all orders
         *  @param object orders
         *  @return return boolean
        **/

        /** $scope.categoryCheckAllOrders   =   ()  => {
            let ordersAreVisible        =   false;
            $scope.orders.forEach( order => {
                if( ! ordersAreVisible ) {
                    ordersAreVisible    =   $scope.categoryCheckAll( order );
                }
            });

            return ordersAreVisible;
        }
        **/

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
            })
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

        $scope.fetchOrders      =   function( callback = null ) {

            $http.get( '<?php echo site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'get_orders' ]);?>?from-room=' + $scope.room_id )
            .then( function( returned ){
                if( $scope.orders.length == 0 ) {
                    $scope.orders     =   returned.data;

                    $scope.orders.forEach( ( order ) => {
                        if( typeof order.meals == 'undefined' ) {
                            order.meals     =   {};
                        }

                        _.each( order.items, ( item ) => {

                            if( typeof order.meals[ item.MEAL ] == 'undefined' ) {
                                 order.meals[ item.MEAL ]   =   [];
                            }

                            order.meals[ item.MEAL ].push( item );
                        });
                    })

                } else {

                    returned.data.forEach( ( order ) => {
                        _.each( order.items, ( item ) => {
                            if( typeof order.meals == 'undefined' ) {
                                order.meals     =   {};
                            }

                            if( typeof order.meals[ item.MEAL ] == 'undefined' ) {
                                 order.meals[ item.MEAL ]   =   [];
                            }
                            
                            order.meals[ item.MEAL ].push( item );
                        });
                    });
                    
                    returned.data       =   returned.data.map( ( order, order_index ) => {
                        let currentOrder    =   $scope.getExistingOrder( order.CODE );

                        if( ! angular.equals({}, currentOrder ) ) {

                            let meals   =   order.meals;
                            for( let meal_key in meals ) {
                                meals[ meal_key ]       =   meals[ meal_key ].map( ( item ) => {
                                    let existingItem    =   $scope.getExistingItem( order_index, meal_key, item.CODEBAR );

                                    if( ! angular.equals({}, existingItem ) ) {
                                        item            =   angular.extend( existingItem, item );
                                        item.active     =   existingItem.active;
                                    }
                                    return item;
                                });
                            };
                        }
                        return order;
                    }); 

                    $scope.orders           =   returned.data;             
                }

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
            if( $scope.orders[ order_index ] ) {
                for( let item of $scope.orders[ order_index ].meals[ meal_index ] ) {
                    if( item.CODEBAR == item_barcode ) {
                        return item;
                    }
                }
            }
            return {}
        }

        /**
         *  Get Order Status
         *  @param object order
         *  @return void
        **/

        $scope.getOrderStatus   =   function( order ) {
            return $scope.order_types[ order.TYPE ] == void(0) ? '<?php echo _s( 'Unknow Order', 'nexo-restaurant' );?>' : $scope.order_types[ order.TYPE ];
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

        $scope.selectAllItems       =   function( order_index ) {
            for( let meal_index in $scope.orders[ order_index ].meals ) {
                for( let item of $scope.orders[ order_index ].meals[ meal_index ] ) {
                    item.active     =   true;
                }
            }
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

        // $( '.content-header h1' ).append( $( '.kitchen-buttons' )[0].innerHTML );
        // angular.element( '.kitchen-buttons' ).html( $compile( $( '.kitchen-buttons' ).html() )($scope) );
    }

    tendooApp.controller( 'watchRestaurantCTRL', [ '$scope', '$http', '$timeout', '$compile', watchRestaurantCTRL ]);
</script>

<?php
global $Options;
$this->load->config( 'rest' );
?>
<script>
    tendooApp.directive( 'modifiers', function() {
        return {
            restrict            :   'E',
            template            :   <?php echo json_encode( $this->load->module_view( 'nexo-restaurant', 'modifiers.dom', null, true ) );?>,
            controller          :      [ '$scope', '$attrs', '$http', '$compile', function( $scope, $attrs, $http, $compile ){
                // override confirm button
                $( '[data-bb-handler="confirm"]' ).hide();
                $( '.modal-footer' ).append( $compile( `
                <a type="button" class="btn btn-success btn-lg ng-scope" ng-click="addModifier()"><?php echo __( 'Add Modifier', 'nexo-restaurant' );?></a>
                ` )( $scope ) );    
                $( '.modal' ).css({
                    background  :   'rgba(136, 136, 136, 0.56)'
                });    

                // reset modifiers
                $scope.modifiers            =       [];

                /**
                 * Get Unique ID
                 * @param void
                 * @return void
                **/
                
                $scope.get_unique_id 	= function(){
                    let uniqueId
                    let debug       =   0;
                    
                    do {
                        uniqueId   =   Math.random().toString(36).substring(7);
                        debug++;
                        if( debug == 1000 ) {
                            return;
                        }
                    } while( _.indexOf( $scope.savedIDS, uniqueId ) != -1 );

                    return uniqueId;
                }

                /**
                 * Add Modifiers
                **/

                $scope.addModifier          =   function() {
                    let atLeastOneSelected  =   false;
                    let modifiersPrice       =   0;
                    let modifiersArray      =   [];
                    _.each( $scope.modifiers, ( modifier ) => {
                        if( parseInt( modifier.default ) == 1 ) {
                            atLeastOneSelected  =   true;
                            modifiersPrice  +=   parseFloat( modifier.price );
                            modifiersArray.push( modifier );
                        }
                    });

                    if( parseInt( $scope.modifiers[0].group_forced ) == 1 && atLeastOneSelected == false ) {
                        return NexoAPI.Toast()( '<?php echo _s( 'You must select at least one modifier.', 'nexo-restaurant' );?>' );
                    }

                    // add new item with his modifier
                    let item                =   $scope.currentItem
                    // item.DESIGN         +=  modifiersLabels;
                    item.PRIX_DE_VENTE      =  parseFloat( item.PRIX_DE_VENTE ) + modifiersPrice;
                    item.PRIX_DE_VENTE_TTC  =  parseFloat( item.PRIX_DE_VENTE_TTC ) + modifiersPrice;
                    item.STATUS             =   1;
                    item.INLINE             =   true; // this item become inline since it's should be singular
                    item.CODEBAR            =   $scope.get_unique_id() + '-barcode-' + item.CODEBAR; // it definitely has to be 
                    // item.QTE_ADDED       =   1;
                    
                    // if meta is not set, then we'll set a default value
                    if( typeof item.metas == 'undefined' ) {
                        item.metas      =   new Object;
                    }

                    item.metas.modifiers    =   modifiersArray;

                    // $scope.modifiers
                    // loop modifier price
                    v2Checkout.addToCart({
                        item, 
                        index       :   $attrs.index,
                        increase    :   true
                    });
                    // v2Checkout.addOnCart([item], $attrs.barcode, $attrs.qte, $attrs.increase == 'true' ? true : false );
                    $( '[data-bb-handler="confirm"]' ).trigger( 'click' );
                }

                $scope.modifiers            =   [];
                $scope.get_modifiers        =   function( group_id ) {
                    $http.get(
                        '<?php echo site_url( array( 'rest', 'nexo_restaurant', 'modifiers_by_group' ) );?>' + '/' +
                        group_id + '?<?php echo store_get_param( null );?>',
                    {
                        headers			:	{
                            '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                        }
                    }).then(function( response ){
                        $scope.modifiers    =   response.data;
                    });
                }

                /**
                 * Select Modifier
                **/

                $scope.select       =   ( modifier ) => {

                    if( modifier.default == '0' ) {
                        // check if modifier group allow multiple selection
                        if( modifier.group_multiselect == '0' ) {
                            // if the group doesn't allow multi select, just disable it
                            _.each( $scope.modifiers, ( _modifier ) => {
                                _modifier.default   =   '0';
                            });
                        }

                        modifier.default    =   '1';
                    } else {
                        modifier.default    =   '0';
                    }
                    
                }

                $scope.get_modifiers( $attrs.item );
            }]
        }
    });

    <?php if( true == false ):?>
    tendooApp.controller( 'comboCTRL', [ '$scope', '$compile', '$rootScope', '$http', function( $scope, $compile, $rootScope, $http ) {

        $scope.combos           =   [];
        $scope.isCombo          =   false;
        $scope.isEdit           =   false;
        $scope.selectedCombo    =   false;
        $scope.savedIDS         =   [];
        // $scope.comboActive      =   <?php echo store_option( 'disable_meal_feature' ) == 'yes' ? 'false' : 'true';?>;

        /**
         * Create Combinaison
         * @param void
         * @return void
        **/
        
        $scope.start_combo 	= function(){

            $scope.isCombo                      =   true;
            v2Checkout.CartItems                =   [];
            v2Checkout.emptyCartItemTable();
            v2Checkout.refreshCart();
                       
        }

        

        /**
         * Finish Combo 
         * @param void
         * @return 
        **/
        
        $scope.finish_combo  	=   function(){

            if( v2Checkout.CartItems.length == 0 ) {
                NexoAPI.Toast()( '<?php echo _s( 'Empty meal !!!', 'nexo-restaurant' );?>' );
            } else {

                // v2Checkout.CartItems    =   [];
                // v2Checkout.refreshCart();

                var combo_name          =   '';
                var combo_price         =   0;
                var combo_qte_restante  =   0;

                v2Checkout.CartItems.forEach( ( item ) => {
                    combo_name          +=      '<span class="combo-item">' + item.DESIGN.replace(/^(.{11}[^\s]*).*/, "$1") + ' ( x ' + item.QTE_ADDED + ' )</span>';
                    combo_price         +=      ( item.PRIX_DE_VENTE * item.QTE_ADDED ); 
                    combo_qte_restante  +=      item.QUANTITE_RESTANTE;
                });

                /**
                 * To Allow Combo edit
                **/

                if( $scope.isEdit ) {
                    _.each( $scope.combos, ( combo ) => {
                        if( combo.barcode == $scope.selectedCombo.barcode ) {
                            combo[ 'name' ]                 =   combo_name;
                            combo[ 'price' ]                =   combo_price;
                            combo[ 'qte_restante' ]         =   combo_qte_restante;
                        }
                    });
                } else {
                    $scope.combos.push({
                        name            :   combo_name,
                        price           :   combo_price,
                        items           :   v2Checkout.CartItems,
                        qte_restante    :   combo_qte_restante,
                        barcode         :   $scope.get_unique_id()
                    });
                }
            }

            $scope.isCombo      =   false;   
            $scope.isEdit       =   false;

            v2Checkout.CartItems        =   [];

            _.each( $scope.combos, ( combo, index ) => {
                v2Checkout.addOnCart([{
                    DESIGN              :   combo.name,
                    PRIX_DE_VENTE       :   combo.price,
                    PRIX_DE_VENTE_TTC   :   combo.price,
                    STATUS              :   '1',
                    QUANTITE_RESTANTE   :   Math.abs( combo.qte_restante ),
                    CODEBAR             :   combo.barcode,
                    metas               :   {
                        is_combo        :   true,
                        items           :   combo.items
                    }
                }], index, 1, false, null );
            });          

            v2Checkout.refreshCart();
        } 

        /**
         * Edit a combo
         * @param event
         * @return void
        **/

        $scope.editCombo        =   ( event ) => {
            let barcode         =   $( event.currentTarget ).closest( 'tr' ).attr( 'data-item-barcode' );
            _.each( $scope.combos, ( combo ) => {
                if( combo.barcode == barcode ) {
                    $scope.isEdit                       =   true;
                    $scope.isCombo                      =   true;
                    $scope.selectedCombo                =   combo;
                    v2Checkout.CartItems                =   combo.items;
                    v2Checkout.buildCartItemTable();
                    v2Checkout.refreshCart();
                }
            })
        }

        /**
         * Allow hover item name animation only on Combo
        **/

        NexoAPI.events.addFilter( 'hover_item_name', ( ) => {
            if( $scope.isCombo ) {
                return true;
            }
            return false;
        });

        /**
         * Add a button to allow product edition
        **/

        // NexoAPI.events.addFilter( 'cart_before_item_name', ( content ) => {
        //     if( $scope.comboActive ) {
        //         // console.log( $scope.isCombo );
        //         if( ! $scope.isCombo ) {
        //             return content + '<a class="btn btn-default btn-sm edit-combo" ng-click="editCombo($event)"><i class="fa fa-cogs"></i></a>';
        //         }
        //     }
        //     return content;
        // }); 

        /**
         * Bind edit action to combo
        **/

        NexoAPI.events.addAction( 'cart_refreshed', () => {
            // if( $scope.comboActive ) {
            //     $( '.edit-combo' ).each( function() {
            //         $( this ).replaceWith( $compile( $( this )[0].outerHTML )( $scope ) )
            //     });   
            // }

            // refresh item modifiers
            _.each( v2Checkout.CartItems, ( item ) => {
                console.log( item );
                if( typeof item.metas != 'undefined' ) {
                    if( typeof item.metas.modifiers ) {
                        let modifiersLabels     =   '';
                        _.each( item.metas.modifiers, ( modifier ) => {
                            if( parseInt( modifier.default ) == 1 ) { // means if the modifiers is active
                                modifiersLabels     +=  '<span class="label label-default"> + ' + modifier.name + '</span> &mdash; ' + NexoAPI.DisplayMoney( modifier.price ) + '<br>';
                            }
                        });

                        $( '[data-item-barcode="' + item.CODEBAR + '"] .item-name' ).after( modifiersLabels );
                    }
                }
            }); 
        })  
        /**
         * @deprecated
        if( $scope.comboActive ) {
            NexoAPI.events.addFilter( 'before_submit_order', ( order_details ) => {
                let global_items    =   [];
                _.each( order_details.ITEMS, ( item ) => {
                    if( item.metas.is_combo ) {
                        _.each( item.metas.items, ( _sub_item ) => {
                            let single_item         =    {
                                id 					:	_sub_item.ID,
                                qte_added 			:	_sub_item.QTE_ADDED,
                                codebar 			:	_sub_item.CODEBAR,
                                sale_price 			:	_sub_item.PROMO_ENABLED ? _sub_item.PRIX_PROMOTIONEL : ( v2Checkout.CartShadowPriceEnabled ? _sub_item.SHADOW_PRICE : _sub_item.PRIX_DE_VENTE ),
                                qte_sold 			:	_sub_item.QUANTITE_VENDU,
                                qte_remaining 		:	parseInt( _sub_item.QUANTITE_RESTANTE ),
                                // @since 2.8.2
                                stock_enabled 		:	_sub_item.STOCK_ENABLED,
                                // @since 2.9.0
                                discount_type 		:	_sub_item.DISCOUNT_TYPE,
                                discount_amount		:	_sub_item.DISCOUNT_AMOUNT,
                                discount_percent 	:	_sub_item.DISCOUNT_PERCENT,
                                metas 				:	typeof _sub_item.metas == 'undefined' ? {} : _sub_item.metas,
                                // @since 3.1 for nexopos
                                name 				:	_sub_item.DESIGN,
                                inline 				:	typeof _sub_item.INLINE ? 1 : 0 // if it's an inline item
                            }
                            single_item.metas.meal    =   item.codebar;
                            global_items.push( single_item );
                        });
                    }
                });

                order_details.ITEMS     =   global_items;
                return order_details;
            });

            NexoAPI.events.addFilter( 'reduce_from_cart', ( data ) => {
                if( typeof data.item.metas != 'undefined' ) {
                    if( data.item.metas[ 'is_combo' ] ) {
                    
                        let indexToKill;
                        _.each( $scope.combos, ( combo, key ) => {
                            if( combo.CODEBAR == data.barcode ) {
                                indexToKill     =   key;
                            }
                        });

                        $scope.combos.splice( indexToKill, 1 );
                    }
                }
                return data;
            });

            NexoAPI.events.addFilter( 'openPayBox', ( filter ) => {
                if( ! $scope.isCombo ) {
                    return true;
                } else {
                    NexoAPI.Toast()( '<?php echo _s( 'You must finish the meal', 'nexo-restaurant' );?>' );
                    return false;
                }
            });

            NexoAPI.events.addFilter( 'override_add_item', ( { _item, proceed, qte_to_add, allow_increase } ) => {
                if( ! $scope.isCombo ) {
                    NexoAPI.Toast()( '<?php echo __( 'You need to click on "make a meal" before adding item', 'nexo-restaurant' );?>' );
                    let buttonClass     =   'default';
                    let animTimes       =   5;
                    let animTry         =   0;
                    let animInterval    =   setInterval( () => {
                        if( animTry == animTimes ) {
                            clearInterval( animInterval );
                        }
                        if( buttonClass == 'default' ) {
                            angular.element( '.meal_button' ).addClass( 'btn-warning' );
                            angular.element( '.meal_button' ).removeClass( 'btn-default' );
                            buttonClass     =   'warning';
                        } else {
                            angular.element( '.meal_button' ).removeClass( 'btn-warning' );
                            angular.element( '.meal_button' ).addClass( 'btn-default' );
                            buttonClass     =   'default';
                        }

                        animTry++;
                    }, 125 );
                    return { _item, proceed : true, qte_to_add, allow_increase };
                }
                return { _item, proceed, qte_to_add, allow_increase };
            }, 99 );
        }
        **/

        NexoAPI.events.addFilter( 'override_add_item', ( { _item, proceed, qte_to_add, allow_increase, index } ) => {
            return;
            if( _item.REF_MODIFIERS_GROUP != '0' ) { // && ( ( $scope.isCombo && $scope.comboActive ) || ! $scope.isCombo )

                // it will be used on the modifiers directive
                $scope.currentItem          =   _item;

                NexoAPI.Bootbox().confirm({
                    message     :    '<modifiers increase="' + allow_increase + '" qte="' + qte_to_add + '" barcode="' + _item.CODEBAR + '" item="' + _item.REF_MODIFIERS_GROUP + '"></modifiers>',
                    title       :   '<?php echo _s( 'Please select a modifier', 'nexo' );?>',
                    buttons: {
                        confirm: {
                            label: '<?php echo _s( 'Add modifiers', 'nexo-restaurant' );?>',
                            className: 'btn-success btn-lg'
                        },
                        cancel: {
                            label: '<?php echo _s( 'Cancel', 'nexo-restaurant' );?>',
                            className: 'btn-default btn-lg'
                        }
                    },
                    callback: function (result) {
                        // console.log( result );
                    }
                });

                $( 'modifiers' ).replaceWith( $compile( $( 'modifiers' )[0].outerHTML )( $scope ) );
                $( 'modifiers' ).closest( '.bootbox' ).addClass( 'modifiers-box' );
                $( '.modal-body' ).css({
                    'overflow-y'    :   'scroll',
                    'height'        :   window.innerHeight - 200
                });
                $( '.bootbox-close-button.close' ).remove();

                return { _item, proceed : true };
            } //  if _item support modfiiers
            return { _item, proceed };
        }, 99 );

        NexoAPI.events.addAction( 'reset_cart', function(){
            $scope.combos       =   [];
            $scope.modifiers    =   [];
        });

        /**
         * Overrite the way order are loaded on NexoPOS$
         * 
        **/

        /**
        NexoAPI.events.addFilter( 'override_open_order', function({ order_details, proceed }) {

            // is meal feature is disabled, just return the object
            if( $scope.comboActive ) {
                return { order_details, proceed };
            }

            let meals       =   new Object;

            _.each( order_details.items, ( item, key ) => {
                if( typeof item.metas != 'undefined' ) {
                    if( typeof meals[ item.metas.meal ] == 'undefined' ) {
                        meals[ item.metas.meal ]        =   [];
                    }

                    meals[ item.metas.meal ].push( item );
                } else {
                    console.log( 'Error : unable to load the order.' );
                }
            });

            /**
             * We're looking each meal
            **/

            /**
            $scope.combos               =   [];

            // _.each( meals, ( items, barcode ) => {

            //     let combo_name          =   '';
            //     let combo_price         =   0;
            //     let combo_qte_restante  =   0;

            //     _.each( items, ( item, key ) => {

            //         let icon    =   '';
            //         if( item.metas.restaurant_food_status == 'denied' ) {
            //             icon    =   '<i class="fa fa-exclamation-triangle"></i> ';
            //         } else if( item.metas.restaurant_food_status == 'ready' ) {
            //             icon    =   '<i class="fa fa-check"></i> ';
            //         }

            //         combo_name          +=      '<span class="combo-item"> ' + icon + item.DESIGN.replace(/^(.{11}[^\s]*).*/, "$1") + ' ( x ' + item.QUANTITE + ' )</span>';
            //         combo_price         +=      ( item.PRIX_DE_VENTE * item.QUANTITE ); 
            //         combo_qte_restante  +=      parseInt( item.QUANTITE_RESTANTE );

            //         // Fix bug with QTE_ADDED
            //         item.QTE_ADDED      =   item.QUANTITE;
            //     });

            //     $scope.combos.push({
            //         name            :   combo_name,
            //         price           :   combo_price,
            //         items           :   items,
            //         qte_restante    :   combo_qte_restante,
            //         barcode         :   barcode
            //     });

            // });
            

            // When each meals is ready

        //     let itemToAdd           =   [];

        //     _.each( $scope.combos, ( combo, index ) => {
        //         itemToAdd.push({
        //             DESIGN              :   combo.name,
        //             PRIX_DE_VENTE       :   combo.price,
        //             PRIX_DE_VENTE_TTC   :   combo.price,
        //             STATUS              :   '1',
        //             QUANTITE_RESTANTE   :   Math.abs( parseFloat( combo.qte_restante ) ),
        //             CODEBAR             :   combo.barcode,
        //             DISCOUNT_TYPE       :   '',
        //             DISCOUNT_AMOUNT     :   0,
        //             QTE_ADDED           :   1,
        //             metas               :   {
        //                 is_combo        :   true,
        //                 items           :   combo.items
        //             }
        //         });
        //     });  

        //     v2Checkout.CartItems        =   itemToAdd;                         

        //     v2Checkout.buildCartItemTable();
        //     v2Checkout.refreshCart();

        //     $scope.isCombo      =   false;

        //     return { order_details, proceed };
        // });

        /**
         * Works only if combo is active
        **/

        // NexoAPI.events.addFilter( 'cart_item_name', ( data ) => {
        //     if( ! $scope.isCombo && $scope.comboActive ) {
        //         data.displayed      =   data.original;
        //         return data;
        //     }
        //     return data;
        // });

    }]);
    <?php endif;?>

</script>
<style>
.combo-item {
    display:block;
    font-size: 12px;
    border-bottom:solid 1px #EEE;
}
.modifiers-item {
    border: solid 1px #d2d2d2;
    height: 160px;
    margin-right: -1px;
    margin-bottom: -1px;
}

.modifiers-item:hover {
    box-shadow: inset 0px 0px 60px 0px #EEE;
    cursor: pointer;
}

.modifiers-item.active {
    box-shadow: inset 0px 0px 60px 0px #c1d3fd; 
}

.modifier-name {
    text-align: center;
    font-weight: 600;
    margin: 0;
}
.modifier-price {
    text-align: center;
    margin: 0;
}
.modifier-image {
    max-height: 90px;
    width: 100%;
    margin: 10px 0 5px;
    border-radius: 10px;
}

.modifiers-box {
  text-align: center;
  padding: 0!important;
}

.modifiers-box:before {
  content: '';
  display: inline-block;
  height: 100%;
  vertical-align: middle;
  margin-right: -4px;
}

.modifiers-box .modal-dialog {
  display: inline-block;
  text-align: left;
  vertical-align: middle;
}
</style>
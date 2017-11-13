<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nexo_Restaurant_Filters extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  Admin Menus
     *  @param array menus
     *  @return array current menu
    **/

    public function admin_menus( $menus )
    {
        if( @$menus[ 'caisse' ] != null ) {

            $menus      =   array_insert_after( 'caisse', $menus, 'restaurant', [
                [
                    'title'     =>      __( 'Restaurant', 'gastro' ),
                    'href'      =>      '#',
                    'icon'      =>      'fa fa-cutlery',
                    'disable'   =>      true
                ],
                [
                    'title'     =>      __( 'Tables', 'gastro' ),
                    'href'      =>      site_url([ 'dashboard', store_slug(), 'gastro', 'tables' ]),
                    'disable'   =>      true
                ]                
            ]);

            if( store_option( 'disable_area_rooms' ) != 'yes' ) {
                $menus[ 'restaurant' ][]    =   [
                    'title'     =>      __( 'Areas', 'gastro' ),
                    'href'      =>      site_url([ 'dashboard', store_slug(), 'gastro', 'areas' ]),
                    'disable'   =>      true
                ];

                // if the rooms and area still available, then the multiple kitchen can be used
                if( store_option( 'disable_kitchen_screen', 'no' ) != 'yes' ) {
                    $menus[ 'restaurant' ][]    =   [
                        'title'     =>      __( 'Kitchens', 'gastro' ),
                        'href'      =>      site_url( [ 'dashboard', store_slug(), 'gastro', 'kitchens' ] )
                    ];
                }


            } else {
                // hide kitchen menu 
                // since @1.4.3
                if( store_option( 'disable_kitchen_screen', 'no' ) == 'no' ) {
                    // When area and rooms are disable, then the single kitchen view is enabled
                    $menus[ 'restaurant' ][]    =   [
                        'title'     =>      __( 'Kitchen View', 'gastro' ),
                        'href'      =>      site_url([ 'dashboard', store_slug(), 'gastro', 'kitchens', 'watch', '0?single-view=true' ]),
                        'disable'   =>      true
                    ]; 
                }
            }   

            if( store_option( 'disable_waiter_screen', 'no' ) == 'no' ) {
                // Waiter screen
                $menus[ 'restaurant' ][]    =   [
                    'title'     =>      __( 'Waiter Screen', 'gastro' ),
                    'href'      =>      site_url([ 'dashboard', store_slug(), 'gastro', 'waiters-screen' ]),
                    'disable'   =>      true
                ]; 
            }
            
            
        }

        if( @$menus[ 'nexo_settings' ] ) {
            $menus[ 'nexo_settings' ][]     =   [
                'title'         =>      __( 'Restaurant Settings', 'gastro' ),
                'href'          =>      site_url([ 'dashboard', store_slug(), 'gastro', 'settings' ])
            ];
        }

        if( @$menus[ 'arrivages' ] ) {
            $menus[ 'arrivages' ][]     =   [
                'title'         =>      __( 'Modifiers', 'gastro' ),
                'href'          =>      site_url([ 'dashboard', store_slug(), 'gastro', 'modifiers' ])
            ];

            $menus[ 'arrivages' ][]     =   [
                'title'         =>      __( 'Modifiers Groups', 'gastro' ),
                'href'          =>      site_url([ 'dashboard', store_slug(), 'gastro', 'modifiers-groups' ])
            ];
        }
        return $menus;
    }

    /**
     *  Add Cart Buttons
     *  @param
     *  @return
    **/

    public function cart_buttons( $menus )
    {
        $menus[]            =   [
            'class' =>  'default',
            'text'  =>  __( 'New Operation', 'nexo' ),
            'icon'  =>  'cutlery',
            'attrs' =>  [
                'ng-click'  =>  'selectOrderType()',
                'ng-controller' => 'selectTableCTRL'
            ]
        ];

        $menus[]            =   [
            'class' =>  'default new-orders-button',
            'text'  =>  __( 'Ready Orders', 'gastro' ) . ' <span class="badge" ng-show="newOrders > 0">{{ newOrders }}</span>',
            'icon'  =>  'check',
            'attrs' =>  [
                'ng-click'  =>  'openReadyOrders()',
                'ng-controller' => 'readerOrdersCTRL'
            ]
        ];

        $menus[]            =   [
            'class' =>  'default show-tables-button',
            'text'  =>  __( 'Tables', 'gastro' ),
            'icon'  =>  'table',
            'attrs' =>  [
                'ng-click'  =>  'openTables()'
            ]
        ];

        return $menus;
    }

    /** 
     * Dashbaord Dependencies
     * @param array
     * @return array
    **/

    public function dashboard_dependencies( $deps )
    {
        // waiting for booking to be ready
        // return $deps;
        // if( ! in_array( 'mwl.calendar', $deps ) ) {
        //     $deps[]     =   'mwl.calendar';
        // }

        // if( ! in_array( 'ui.bootstrap', $deps ) ) {
        //     $deps[]     =   'ui.bootstrap';
        // }

        // if( ! in_array( 'ngMasonry', $deps ) ) {
        //     $deps[]     =   'ngMasonry';
        // }

        return $deps;
    }
    
    /**
     * cart_buttons_2
     * @param array buttons
     * @return array new buttons
    **/
    public function cart_buttons_2( $menus ) 
    {
        $menus[]            =   [
            'class' =>  'default show-ready-meal-button',
            'text'  =>  __( 'Waiter Screen', 'gastro' ) . ' <span ng-show="items.length > 0" class="label label-primary">{{ items.length }}</span>',
            'icon'  =>  'user',
            'attrs' =>  [
                'ng-click'  =>  'openReadyMeals()',
                'ng-controller'     =>  'readyMealCTRL'
            ]
        ];
        return $menus;
    }

    /**
     * Add Combo
     * @param string before cart pay button
     * @deprecated
     * @return string
    **/
    
    public function add_combo( $string )
    {
        // $this->load->module_view( 'gastro', 'combo/combo-button' );
    }

    /**
     * Restaurant Demo
     * @param array demo list
     * @return array demo list
    **/
    
    public function restaurant_demo( $demo )
    {
        $demo[ 'gastro' ]     =   __( 'Restaurant Demo', 'gastro' );
        return $demo;
    }

    /**
     * Customize Product Crud
     * @param object crud object
     * @return object
    **/

    public function load_product_crud( $crud ) 
    {
        $raw_modifiers  =   $this->db->get( store_prefix() . 'nexo_restaurant_modifiers_categories' )
        ->result_array();
        
        $crud->display_as( 'REF_MODIFIERS_GROUP', __( 'Modifiers Group', 'gastro' ) );
        $crud->field_type( 'REF_MODIFIERS_GROUP','multiselect', nexo_convert_raw( $raw_modifiers, 'ID', 'NAME' ) );
        // $crud->set_relation('REF_MODIFIERS_GROUP', store_prefix() . 'nexo_restaurant_modifiers_categories', 'NAME' );
        $crud->field_description('REF_MODIFIERS_GROUP', __( 'Set a modifiers which will be used for this item. According to the modifiers group, the modifiers selection can be forced.') );
        return $crud;
    }

    /** 
     * Filter Pay Button
     * If the kitchen screen is disabled, then we'll only display
     * @param string current button dom string
     * @return string
    **/

    public function cart_pay_button( $button )
    {
        if( store_option( 'disable_kitchen_screen' ) != 'yes' ) {
            return $button . $this->load->module_view( 'gastro', 'checkout.pay_button', null, true );
        }
        return $button;
    }

    /**
     * Post order details
     * @param array order details
     * @return array
    **/

    public function post_order_details( $order_details ) 
    {
        $metas       =   $this->input->post( 'metas' );
        if( @$metas[ 'add_on' ] != null ) {
            $order          =   $this->db->where( 'ID', $metas[ 'add_on' ] )
            ->get( store_prefix() . 'nexo_commandes' )
            ->result_array();

            $total_price        =   0;

            foreach( $this->input->post( 'ITEMS' ) as $item ) {

                // Adding to order product
                if( $item[ 'discount_type' ] == 'percentage' && $item[ 'discount_percent' ] != '0' ) {
                    $discount_amount		=	__floatval( ( __floatval($item[ 'qte_added' ]) * __floatval($item['sale_price']) ) * floatval( $item[ 'discount_percent' ] ) / 100 );
                } elseif( $item[ 'discount_type' ] == 'flat' ) {
                    $discount_amount		=	__floatval( $item[ 'discount_amount' ] );
                } else {
                    $discount_amount		=	0;
                }

                $item_data		=	array(
                    'REF_PRODUCT_CODEBAR'  =>    $item[ 'codebar' ],
                    'REF_COMMAND_CODE'     =>    $order[0][ 'CODE' ],
                    'QUANTITE'             =>    $item[ 'qte_added' ],
                    'PRIX'                 =>    floatval( $item[ 'sale_price' ] ) - $discount_amount,
                    'PRIX_TOTAL'           =>    ( __floatval($item[ 'qte_added' ]) * __floatval($item[ 'sale_price' ]) ) - $discount_amount,
                    // @since 2.9.0
                    'DISCOUNT_TYPE'			=>	$item['discount_type'],
                    'DISCOUNT_AMOUNT'		=>	$item['discount_amount'],
                    'DISCOUNT_PERCENT'		=>	$item['discount_percent'],
                    // @since 3.1
                    'NAME'                      =>    $item[ 'name' ],
                    'INLINE'                    =>    $item[ 'inline' ]
                );

                // filter item
                $item_data                  =     $this->events->apply_filters_ref_array( 'put_order_item', [ $item_data, $item ]);
    
                $this->db->insert( store_prefix() . 'nexo_commandes_produits', $item_data );

                // getcommande product id
                $insert_id = $this->db->insert_id();
            
                // Saving item metas
                $meta_array         =   array();
                foreach( ( array ) @$item[ 'metas' ] as $key => $value ) {
                    $meta_array[]     =   [
                        'REF_COMMAND_PRODUCT'   =>  $insert_id,
                        'REF_COMMAND_CODE'      =>  $order[0][ 'CODE' ],
                        'KEY'                   =>  $key,
                        'VALUE'                 =>  is_array( $value ) ? json_encode( $value ) : $value,
                        'DATE_CREATION'         =>  date_now()
                    ];
                }
    
                // If item has metas, we just save it
                if( $meta_array ) {
                    $this->db->insert_batch( store_prefix() . 'nexo_commandes_produits_meta', $meta_array );
                }
    
                // Add history for this item on stock flow
                $this->db->insert( store_prefix() . 'nexo_articles_stock_flow', [
                    'REF_ARTICLE_BARCODE'       =>  $item[ 'codebar' ],
                    'QUANTITE'                  =>  $item[ 'qte_added' ],
                    'UNIT_PRICE'                =>  $item[ 'sale_price' ],
                    'TOTAL_PRICE'               =>  ( __floatval($item[ 'qte_added' ]) * __floatval($item[ 'sale_price' ]) ) - $discount_amount,
                    'REF_COMMAND_CODE'          =>  $order[0][ 'CODE' ],
                    'AUTHOR'                    =>  User::id(),
                    'DATE_CREATION'             =>  date_now(),
                    'TYPE'                      =>  'sale'
                ]);

                $total_price                    +=  floatval( $item[ 'sale_price' ] );
            }

            // update total price
            $this->db
            ->where( 'CODE', $order[0][ 'CODE' ])
            ->update( store_prefix() . 'nexo_commandes', [
                'TOTAL'         =>  floatval( $order[0][ 'TOTAL' ] )    +   $total_price,
                'RESTAURANT_ORDER_STATUS'   =>  'pending' // change the order status
            ]);

            echo json_encode([
                'status'        =>  'success',
                'message'       =>  'item_added',
                'order_code'    =>  $order[0][ 'CODE' ],
                'order_id'      =>  $order[0][ 'ID' ]
            ]);
            die;
        }
        $order_details[ 'RESTAURANT_ORDER_STATUS' ]         =   $this->input->post( 'RESTAURANT_ORDER_STATUS' );
        $order_details[ 'RESTAURANT_ORDER_TYPE' ]           =   $this->input->post( 'RESTAURANT_ORDER_TYPE' );
        return $order_details;
    }

    /**
     * Put order details
     * @param array order details
     * @return array
    **/

    public function put_order_details( $order_details ) 
    {
        // $order_details[ 'RESTAURANT_ORDER_STATUS' ]         =   $this->input->input_stream( 'RESTAURANT_ORDER_STATUS' );
        // $order_details[ 'RESTAURANT_ORDER_TYPE' ]           =   $this->input->input_stream( 'RESTAURANT_ORDER_TYPE' );
        return $order_details;
    }

    /**
     * Fitler Item  Name to inject modifiers
     * @param array data
     * @return array formated data
    **/

    public function receipt_after_item_name( $data ) {
        $raw_meta           =   $this->db->where( 'REF_COMMAND_PRODUCT', $data[ 'item' ][ 'ITEM_ID' ])
        ->where( 'KEY', 'modifiers' )
        ->get( store_prefix() . 'nexo_commandes_produits_meta' )
        ->result_array();

        $restaurant_note    =   $this->db->where( 'REF_COMMAND_PRODUCT', $data[ 'item'][ 'ITEM_ID' ] )
        ->where( 'KEY', 'restaurant_note' )
        ->get( store_prefix() . 'nexo_commandes_produits_meta' )
        ->result_array();

        $string                 =   '';
        if( $restaurant_note ) {
            $string         .=  $restaurant_note[0][ 'VALUE' ] . '<br>';
        }

        if( $raw_meta ) {
            $metas                  =   json_decode( $raw_meta[0][ 'VALUE'], true );
            if( $metas ) {
                foreach( $metas as $meta ) {
                    $string             .=  '&mdash; ' . $meta[ 'name' ] . '<br> + ' . get_instance()->Nexo_Misc->cmoney_format( floatval( $meta[ 'price' ] ) * $data[ 'item' ][ 'QUANTITE' ] ) . '<br>';
                }
            }
        }

        $data[ 'output' ]       =   $string;
        return $data;
    }

    /**
     * Filter item name
     * @param int current price
     * @return int formated price
    **/

    public function receipt_filter_item_price( $item_price ) {
        global $current_item;
        $raw_meta           =   $this->db->where( 'REF_COMMAND_PRODUCT', $current_item[ 'ITEM_ID' ])
        ->where( 'KEY', 'modifiers' )
        ->where( 'REF_COMMAND_CODE', $current_item[ 'CODE' ])
        ->get( store_prefix() . 'nexo_commandes_produits_meta' )
        ->result_array();
        
        //var_dump( $current_item );

        if( $raw_meta ) {
            $metas                  =   json_decode( $raw_meta[0][ 'VALUE'], true );
            if( $metas ) {
                $allPrices              =   0;
                foreach( @$metas as $meta ) {
                    $allPrices          +=  floatval( $meta[ 'price' ] );
                }
                return  $item_price     - $allPrices;
            }
        }
        return $item_price;
    }

    /**
     * Update Real order type
     * @param item
     * @return item
    **/

    public function put_order_item( $data, $item )
    {
        $barcode    =   explode( '-barcode-', $item[ 'codebar' ]);
        // for item which doesn't have a modifier
        $data[ 'RESTAURANT_PRODUCT_REAL_BARCODE' ]      =   $item[ 'codebar' ];
        if( @$barcode[1] ) {
            $data[ 'RESTAURANT_PRODUCT_REAL_BARCODE' ]  =   $barcode[1]; // after "-barcode-";
        }
        return $data;
    }

    /**
     * Save Real order type
     * @param item
     * @return item
    **/

    public function post_order_item( $data, $item )
    {
        $barcode    =   explode( '-barcode-', $item[ 'codebar' ]);
        // for item which doesn't have a modifier
        $data[ 'RESTAURANT_PRODUCT_REAL_BARCODE' ]      =   $item[ 'codebar' ];
        if( @$barcode[1] ) {
            $data[ 'RESTAURANT_PRODUCT_REAL_BARCODE' ]  =   $barcode[1]; // after "-barcode-";
        }
        return $data;
    }
}

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
                    'title'     =>      __( 'Restaurant', 'nexo-restaurant' ),
                    'href'      =>      '#',
                    'icon'      =>      'fa fa-cutlery',
                    'disable'   =>      true
                ],
                [
                    'title'     =>      __( 'Tables', 'nexo-restaurant' ),
                    'href'      =>      site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'tables' ]),
                    'disable'   =>      true
                ],
                [
                    'title'     =>      __( 'Areas', 'nexo-restaurant' ),
                    'href'      =>      site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'areas' ]),
                    'disable'   =>      true
                ],
                [
                    'title'     =>      __( 'Rooms', 'nexo-restaurant' ),
                    'href'      =>      site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'rooms' ]),
                    'disable'   =>      true
                ],
                [
                    'title'     =>      __( 'Kitchens', 'nexo-restaurant' ),
                    'href'      =>      site_url( [ 'dashboard', store_slug(), 'nexo-restaurant', 'kitchens', 'lists' ] )
                ]
            ]);
        }

        if( @$menus[ 'nexo_settings' ] ) {
            $menus[ 'nexo_settings' ][]     =   [
                'title'         =>      __( 'Restaurant Settings', 'nexo-restaurant' ),
                'href'          =>      site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'settings' ])
            ];
        }

        if( @$menus[ 'arrivages' ] ) {
            $menus[ 'arrivages' ][]     =   [
                'title'         =>      __( 'Modifiers', 'nexo-restaurant' ),
                'href'          =>      site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'modifiers' ])
            ];

            $menus[ 'arrivages' ][]     =   [
                'title'         =>      __( 'Modifiers Groups', 'nexo-restaurant' ),
                'href'          =>      site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'modifiers_groups' ])
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
        $menus[ 'restaurant' ]      =
        '<button class="btn {{ tableSelectedClass( selectedTable ) }}" ng-controller="selectTableCTRL" type="button" ng-click="openTableSelection()"><i class="fa fa-cutlery"></i>
            {{ tableSelectedClass( selectedTable ) == "btn-success" ? "' . __( 'Dine In', 'nexopos-restaurant' ) . '" : "' . __( 'Take Away', 'nexopos-restaurant' ) . '" }}
        </button> {{selectedTable}}';

        return $menus;
    }

    /**
     *  Allow Print for new Order type
     *  @param array order types
     *  @return array
    **/

    public function allow_print( $order_types ) 
    {
        $order_types[]      =   'nexo_order_dine_pending';
        $order_types[]      =   'nexo_order_takeaway_pending';
        return $order_types;
    }

    /**
     * Add Combo
     * @param string before cart pay button
     * @return string
    **/
    
    public function add_combo( $string )
    {
        $this->load->module_view( 'nexo-restaurant', 'combo/combo-button' );
    }

    /**
     * Restaurant Demo
     * @param array demo list
     * @return array demo list
    **/
    
    public function restaurant_demo( $demo )
    {
        $demo[ 'nexo-restaurant' ]     =   __( 'Restaurant Demo', 'nexo-restaurant' );
        return $demo;
    }

    /**
     * Editable order
     * @param array order namespace
     * @return array
    **/
    
    public function order_editable( $orders )
    {
        $orders[]   =   'nexo_order_dine_pending';
        $orders[]   =   'nexo_order_takeaway_pending';
        $orders[]   =   'nexo_order_dine_denied';
        $orders[]   =   'nexo_order_takeaway_denied';

        return $orders;
    }

    /**
     * Customize Product Crud
     * @param object crud object
     * @return object
    **/

    public function load_product_crud( $crud ) 
    {
        $crud->display_as( 'REF_MODIFIERS_GROUP', __( 'Modifiers Group', 'nexo-restaurant' ) );
        $crud->set_relation('REF_MODIFIERS_GROUP', store_prefix() . 'nexo_restaurant_modifiers_categories', 'NAME' );
        $crud->field_description('REF_MODIFIERS_GROUP', __( 'Set a modifiers which will be used for this item. According to the modifiers group, the modifiers selection can be forced.') );
        return $crud;
    }
}

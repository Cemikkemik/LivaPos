<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once( dirname( __FILE__ ) . '/install.php' );
include_once( dirname( __FILE__ ) . '/controller.php' );

class Nexo_Restaurant_Actions extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
        $this->install      =   new Nexo_Restaurant_Install;
    }

    /**
     *  Enable module
     *  @param string module namespace
     *  @return void
    **/

    public function enable_module( $module )
    {
        if( $module == 'nexo-restaurant' ) {
            global $Options;

            // if module is not yet installed
            if( @$Options[ 'nexo_restaurant_installed' ] == null ) {

                $this->options->set( 'nexo_restaurant_installed', true, true );

                $this->load->model( 'Nexo_Stores' );
                $stores         =   $this->Nexo_Stores->get();

                array_unshift( $stores, [
                    'ID'        =>  0
                ]);

                foreach( $stores as $store ) {
                    $store_prefix       =   $this->db->dbprefix . ( $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_' );
                    $this->install->create_tables( $store_prefix );
                }
                                
            }
        }
    }

    /**
     *  Load dashboard
     *  @param void
     *  @return void
    **/

    public function load_dashboard()
    {
        $this->Gui->register_page_object( 'nexo-restaurant', new Nexo_Restaurant_Controller );
        $this->events->add_action( 'stores_controller_callback', function( $array ) {
            $array[ 'nexo-restaurant' ]     =   new Nexo_Restaurant_Controller;
            return $array;
        });

        // Add New Order Types
        $order_types        =   $this->config->item( 'nexo_order_types' );
        $order_types[ 'nexo_order_dinein_pending' ]             =   __( 'Dine In Pending', 'nexo-restaurant' );
        $order_types[ 'nexo_order_dinein_ongoing' ]             =   __( 'Dine Ongoing', 'nexo-restaurant' );
        $order_types[ 'nexo_order_dinein_partially' ]           =   __( 'Dine Partially Ready', 'nexo-restaurant' );
        $order_types[ 'nexo_order_dinein_incomplete' ]          =   __( 'Dine Incomplete', 'nexo-restaurant' );
        $order_types[ 'nexo_order_dinein_ready' ]               =   __( 'Dine Ready', 'nexo-restaurant' );
        $order_types[ 'nexo_order_dinein_canceled' ]            =   __( 'Dine Canceled', 'nexo-restaurant' );
        $order_types[ 'nexo_order_dinein_denied' ]              =   __( 'Dine Denied', 'nexo-restaurant' );
        $order_types[ 'nexo_order_dinein_paid' ]              =   __( 'Dine Paid', 'nexo-restaurant' );

        $order_types[ 'nexo_order_takeaway_pending' ]       =   __( 'Take Away Pending', 'nexo-restaurant' );
        $order_types[ 'nexo_order_takeaway_ongoing' ]       =   __( 'Take Away Ongoing', 'nexo-restaurant' );
        $order_types[ 'nexo_order_takeaway_partially' ]     =   __( 'Take Away Partially Ready', 'nexo-restaurant' );
        $order_types[ 'nexo_order_takeaway_incomplete' ]    =   __( 'Take Away Incomplete', 'nexo-restaurant' );
        $order_types[ 'nexo_order_takeaway_ready' ]         =   __( 'Take Away Ready', 'nexo-restaurant' );
        $order_types[ 'nexo_order_takeaway_canceled' ]      =   __( 'Take Away Canceled', 'nexo-restaurant' );
        $order_types[ 'nexo_order_takeaway_denied' ]        =   __( 'Take Away Denied', 'nexo-restaurant' );
        $order_types[ 'nexo_order_takeaway_paid' ]        =   __( 'Take Away Paid', 'nexo-restaurant' );

        $order_types[ 'nexo_order_delivery_pending' ]       =   __( 'Delivery Pending', 'nexo-restaurant' );
        $order_types[ 'nexo_order_delivery_ongoing' ]       =   __( 'Delivery Ongoing', 'nexo-restaurant' );
        $order_types[ 'nexo_order_delivery_partially' ]     =   __( 'Delivery Partially Ready', 'nexo-restaurant' );
        $order_types[ 'nexo_order_delivery_incomplete' ]    =   __( 'Delivery Incomplete', 'nexo-restaurant' );
        $order_types[ 'nexo_order_delivery_ready' ]         =   __( 'Delivery Ready', 'nexo-restaurant' );
        $order_types[ 'nexo_order_delivery_canceled' ]      =   __( 'Delivery Canceled', 'nexo-restaurant' );
        $order_types[ 'nexo_order_delivery_denied' ]        =   __( 'Delivery Denied', 'nexo-restaurant' );
        $order_types[ 'nexo_order_delivery_paid' ]        =   __( 'Delivery Paid', 'nexo-restaurant' );

        $order_types[ 'nexo_order_booking_pending' ]       =   __( 'Booking Pending', 'nexo-restaurant' );
        $order_types[ 'nexo_order_booking_ongoing' ]       =   __( 'Booking Ongoing', 'nexo-restaurant' );
        $order_types[ 'nexo_order_booking_partially' ]     =   __( 'Booking Partially Ready', 'nexo-restaurant' );
        $order_types[ 'nexo_order_booking_incomplete' ]    =   __( 'Booking Incomplete', 'nexo-restaurant' );
        $order_types[ 'nexo_order_booking_ready' ]         =   __( 'Booking Ready', 'nexo-restaurant' );
        $order_types[ 'nexo_order_booking_canceled' ]      =   __( 'Booking Canceled', 'nexo-restaurant' );
        $order_types[ 'nexo_order_booking_denied' ]        =   __( 'Booking Denied', 'nexo-restaurant' );
        $order_types[ 'nexo_order_booking_paid' ]        =   __( 'Booking Paid', 'nexo-restaurant' );

        $nexo_item_tabs         =  $this->config->item( 'nexo_item_stock_group' );
        $nexo_item_tabs[]       = 'REF_MODIFIERS_GROUP';

        $this->config->set_item( 'nexo_item_stock_group', $nexo_item_tabs );
        $this->config->set_item( 'nexo_order_types', $order_types );
        $this->config->set_item( 'nexo_all_payment_types', array_merge( $order_types, $this->config->item( 'nexo_all_payment_types' ) ) );

        // enqueue styles
        $this->enqueue->css_namespace( 'dashboard_header' );
        $this->enqueue->css( 'bower_components/angular-bootstrap-calendar/dist/css/angular-bootstrap-calendar.min', module_url( 'nexo-restaurant' ) );
        
        $this->enqueue->js_namespace( 'dashboard_footer' );
        $this->enqueue->js( 'bower_components/angular-bootstrap-calendar/dist/js/angular-bootstrap-calendar-tpls.min', module_url( 'nexo-restaurant' ) );
        $this->enqueue->js( 'js/masonry.pkg', module_url( 'nexo-restaurant' ) );
        // $this->enqueue->js( 'js/imagesloaded.pkgd.min', module_url( 'nexo-restaurant' ) );
        // $this->enqueue->js( 'js/angular-masonry', module_url( 'nexo-restaurant' ) );
        
    }

    /**
     *  dashboard footer
     *  @param void
     *  @return void
    **/

    public function dashboard_footer()
    {
        global $PageNow;

        if( $PageNow == 'nexo/registers/__use') {
            $this->load->module_view( 'nexo-restaurant', 'directives.order-type' );
            $this->load->module_view( 'nexo-restaurant', 'directives.table-history' );
            $this->load->module_view( 'nexo-restaurant', 'directives.booking-ui' );
            $this->load->module_view( 'nexo-restaurant', 'directives.table-status' );
            $this->load->module_view( 'nexo-restaurant', 'directives.restaurant-rooms' );
            $this->load->module_view( 'nexo-restaurant', 'register-footer' );
            $this->load->module_view( 'nexo-restaurant', 'combo/combo-script' ); // rename it to modifier directive
            $this->load->module_view( 'nexo-restaurant', 'waiters.screen' );
        }
    }

    /**
     *  Store Install Tables
     *  @param void
     *  @return void
    **/

    public function store_install_tables( $table_prefix )
    {
        $this->install->create_tables( $table_prefix );
    }

    /**
     *  Store Delete Table
     *  @param void
     *  @return void
    **/

    public function store_delete_tables( $store_prefix )
    {
        $this->install->delete_tables( $store_prefix );
    }

    /**
     *  Remove Module
     *  @param string module namespace
     *  @return void
    **/

    public function remove_module( $module_namespace )
    {
        if ( $module_namespace === 'nexo-restaurant') {

            $this->load->model( 'Nexo_Stores' );

            $stores         =   $this->Nexo_Stores->get();

            array_unshift( $stores, [
                'ID'        =>  0
            ]);

            foreach( $stores as $store ) {
                $store_prefix       =   $this->db->dbprefix . ( $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_' );
                $this->install->delete_tables( $store_prefix );
            }
        }
    }

    /**
     *  enable demo
     *  @param string demo name
     *  @return void
    **/

    public function enable_demo( $demo )
    {
        if( $demo == 'nexo-restaurant' ) {
            $this->load->module_view( 'nexo-restaurant', 'demo' );
        }        
    }

    /**
     *  Empty Shop
     *  @param void
     *  @return void
    **/

    public function empty_shop()
    {
        $table_prefix   =   $this->db->dbprefix . store_prefix();

        if( $this->db->table_exists( $table_prefix . 'nexo_restaurant_rooms' ) ) {
            $this->db->query('TRUNCATE `' . $table_prefix . 'nexo_restaurant_rooms`;');
        }

        if( $this->db->table_exists( $table_prefix . 'nexo_restaurant_tables' ) ) {
            $this->db->query('TRUNCATE `' . $table_prefix . 'nexo_restaurant_tables`;');
        }

        if( $this->db->table_exists( $table_prefix . 'nexo_restaurant_areas' ) ) {
            $this->db->query('TRUNCATE `' . $table_prefix . 'nexo_restaurant_areas`;');
        }

        if( $this->db->table_exists( $table_prefix . 'nexo_restaurant_kitchens' ) ) {
            $this->db->query('TRUNCATE `' . $table_prefix . 'nexo_restaurant_kitchens`;');
        }

        if( $this->db->table_exists( $table_prefix . 'nexo_restaurant_tables_relation_orders' ) ) {
            $this->db->query('TRUNCATE `' . $table_prefix . 'nexo_restaurant_tables_relation_orders`;');
        }

        if( $this->db->table_exists( $table_prefix . 'nexo_restaurant_tables_sessions' ) ) {
            $this->db->query('TRUNCATE `' . $table_prefix . 'nexo_restaurant_tables_sessions`;');
        }
    }
}

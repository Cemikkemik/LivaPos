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
        if( $module == 'gastro' ) {
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
        $this->load->module_config( 'gastro' );
        // Add New Order Types
        $order_types        =   $this->config->item( 'nexo_order_types' );
        $order_types[ 'nexo_order_dinein_pending' ]             =   __( 'Dine In Pending', 'gastro' );
        $order_types[ 'nexo_order_dinein_ongoing' ]             =   __( 'Dine Ongoing', 'gastro' );
        $order_types[ 'nexo_order_dinein_partially' ]           =   __( 'Dine Partially Ready', 'gastro' );
        $order_types[ 'nexo_order_dinein_incomplete' ]          =   __( 'Dine Incomplete', 'gastro' );
        $order_types[ 'nexo_order_dinein_ready' ]               =   __( 'Dine Ready', 'gastro' );
        $order_types[ 'nexo_order_dinein_canceled' ]            =   __( 'Dine Canceled', 'gastro' );
        $order_types[ 'nexo_order_dinein_denied' ]              =   __( 'Dine Denied', 'gastro' );
        $order_types[ 'nexo_order_dinein_paid' ]              =   __( 'Dine Paid', 'gastro' );

        $order_types[ 'nexo_order_takeaway_pending' ]       =   __( 'Take Away Pending', 'gastro' );
        $order_types[ 'nexo_order_takeaway_ongoing' ]       =   __( 'Take Away Ongoing', 'gastro' );
        $order_types[ 'nexo_order_takeaway_partially' ]     =   __( 'Take Away Partially Ready', 'gastro' );
        $order_types[ 'nexo_order_takeaway_incomplete' ]    =   __( 'Take Away Incomplete', 'gastro' );
        $order_types[ 'nexo_order_takeaway_ready' ]         =   __( 'Take Away Ready', 'gastro' );
        $order_types[ 'nexo_order_takeaway_canceled' ]      =   __( 'Take Away Canceled', 'gastro' );
        $order_types[ 'nexo_order_takeaway_denied' ]        =   __( 'Take Away Denied', 'gastro' );
        $order_types[ 'nexo_order_takeaway_paid' ]        =   __( 'Take Away Paid', 'gastro' );

        $order_types[ 'nexo_order_delivery_pending' ]       =   __( 'Delivery Pending', 'gastro' );
        $order_types[ 'nexo_order_delivery_ongoing' ]       =   __( 'Delivery Ongoing', 'gastro' );
        $order_types[ 'nexo_order_delivery_partially' ]     =   __( 'Delivery Partially Ready', 'gastro' );
        $order_types[ 'nexo_order_delivery_incomplete' ]    =   __( 'Delivery Incomplete', 'gastro' );
        $order_types[ 'nexo_order_delivery_ready' ]         =   __( 'Delivery Ready', 'gastro' );
        $order_types[ 'nexo_order_delivery_canceled' ]      =   __( 'Delivery Canceled', 'gastro' );
        $order_types[ 'nexo_order_delivery_denied' ]        =   __( 'Delivery Denied', 'gastro' );
        $order_types[ 'nexo_order_delivery_paid' ]        =   __( 'Delivery Paid', 'gastro' );

        $order_types[ 'nexo_order_booking_pending' ]       =   __( 'Booking Pending', 'gastro' );
        $order_types[ 'nexo_order_booking_ongoing' ]       =   __( 'Booking Ongoing', 'gastro' );
        $order_types[ 'nexo_order_booking_partially' ]     =   __( 'Booking Partially Ready', 'gastro' );
        $order_types[ 'nexo_order_booking_incomplete' ]    =   __( 'Booking Incomplete', 'gastro' );
        $order_types[ 'nexo_order_booking_ready' ]         =   __( 'Booking Ready', 'gastro' );
        $order_types[ 'nexo_order_booking_canceled' ]      =   __( 'Booking Canceled', 'gastro' );
        $order_types[ 'nexo_order_booking_denied' ]        =   __( 'Booking Denied', 'gastro' );
        $order_types[ 'nexo_order_booking_paid' ]        =   __( 'Booking Paid', 'gastro' );

        $nexo_item_tabs         =  $this->config->item( 'nexo_item_stock_group' );
        $nexo_item_tabs[]       = 'REF_MODIFIERS_GROUP';

        $this->config->set_item( 'nexo_item_stock_group', $nexo_item_tabs );
        $this->config->set_item( 'nexo_order_types', $order_types );
        $this->config->set_item( 'nexo_all_payment_types', array_merge( $order_types, $this->config->item( 'nexo_all_payment_types' ) ) );

        // enqueue styles
        $this->enqueue->css_namespace( 'dashboard_header' );
        $this->enqueue->css( 'bower_components/angular-bootstrap-calendar/dist/css/angular-bootstrap-calendar.min', module_url( 'gastro' ) );
        
        $this->enqueue->js_namespace( 'dashboard_footer' );
        $this->enqueue->js( 'bower_components/angular-bootstrap-calendar/dist/js/angular-bootstrap-calendar-tpls.min', module_url( 'gastro' ) );
        $this->enqueue->js( 'js/masonry.pkg', module_url( 'gastro' ) );
        // $this->enqueue->js( 'js/imagesloaded.pkgd.min', module_url( 'gastro' ) );
        // $this->enqueue->js( 'js/angular-masonry', module_url( 'gastro' ) );
        
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
            $this->load->module_view( 'gastro', 'directives.order-type' );
            $this->load->module_view( 'gastro', 'directives.table-history' );
            $this->load->module_view( 'gastro', 'directives.booking-ui' );
            $this->load->module_view( 'gastro', 'directives.table-status' );
            $this->load->module_view( 'gastro', 'directives.restaurant-rooms' );
            $this->load->module_view( 'gastro', 'register-footer' );
            $this->load->module_view( 'gastro', 'combo/combo-script' ); // rename it to modifier directive
            $this->load->module_view( 'gastro', 'waiters.screen' );
            $this->load->module_view( 'gastro', 'ready-items.script' );
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
        if ( $module_namespace === 'gastro') {

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
        if( $demo == 'gastro' ) {
            $this->load->module_view( 'gastro', 'demo' );
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

    /**
     * Inject Data on v2Checkout
     * @param array order details
     * @return string vue
     */
    public function edit_loaded_order( $order ) 
    {
        $this->load->module_view( 'gastro', 'register.edit-loaded-order', [ 'order' => $order[ 'order' ][0] ]);
    }
}

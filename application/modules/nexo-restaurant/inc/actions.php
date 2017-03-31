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
            if( @$Options[ 'nexo-restaurant-installed' ] == null ) {

                $this->load->model( 'Nexo_Stores' );
                $stores         =   $this->Nexo_Stores->get();

                array_unshift( $stores, [
                    'ID'        =>  0
                ]);

                foreach( $stores as $store ) {
                    $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';
                    $this->install->create_tables( $store_prefix );
                }

                $this->options->set( 'nexo-restaurant-installed', true, true );
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
            $this->load->module_view( 'nexo-restaurant', 'register-footer' );
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
        if ($namespace === 'nexo-restaurant') {

            $this->load->model( 'Nexo_Stores' );

            $stores         =   $this->Nexo_Stores->get();

            array_unshift( $stores, [
                'ID'        =>  0
            ]);

            foreach( $stores as $store ) {
                $store_prefix       =   $store[ 'ID' ] == 0 ? '' : 'store_' . $store[ 'ID' ] . '_';
                $this->install->delete_tables( $store_prefix );
            }
        }
    }
}

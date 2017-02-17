<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once( dirname( __FILE__ ) . '/inc/controller.php' );

class Ocombarcode extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
        $this->events->add_action( 'dashboard_footer', [ $this, 'dashboard_footer' ] );
        $this->events->add_action( 'load_dashboard', [ $this, 'dashboard' ] );
        $this->events->add_filter( 'admin_menus', [ $this, 'menus' ], 99 );
    }

    /**
     *  Load Dashoard
     *  @param
     *  @return
    **/

    public function dashboard()
    {
        $this->Gui->register_page_object( 'ocombarcode', new Ocombarcode_Controller );
    }

    /**
     *  Load Dashboard Footer. Add javascript hooks
     *  @param  void
     *  @return void
    **/

    public function dashboard_footer()
    {
        $this->load->module_view( 'ocombarcode', 'footer' );
    }

    /**
     *  Menu
     *  @param
     *  @return
    **/

    public function menus( $menus )
    {
        if( @$menus[ 'nexo_settings' ] != null ) {
            array_push( $menus[ 'nexo_settings' ], [
                'title'     =>  __( 'Ocombarcode', 'ocombarcode' ),
                'href'      =>  site_url( [ 'dashboard', 'ocombarcode', 'settings' ] )
            ]);
        }
        return $menus;
    }
}
new Ocombarcode;

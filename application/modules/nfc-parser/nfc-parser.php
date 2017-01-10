<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once( dirname( __FILE__ ) . '/inc/controller.php' );
include_once( dirname( __FILE__ ) . '/inc/library.php' );

class NFC_Parser_Module extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
        $this->events->add_action( 'load_dashboard', [ $this, 'load_dashboard' ] );
        $this->events->add_action( 'load_frontend', [ $this, 'front_end' ] );
        $this->events->add_action( 'dashboard_footer', [ $this, 'footer' ] );
        $this->events->add_filter( 'admin_menus', [ $this, 'admin_menus' ], 20 );
    }

    /**
     *  Front Tend
     *  @param
     *  @return
    **/

    public function front_end()
    {
        global $Options;
        if( $this->uri->segment(1) == @$Options[ 'nfc_slug' ] && @$Options[ 'nfc_enable' ] == 'yes' ) {
            if( $this->uri->segment(2) == 'add_item' ) {
                $NFC        =   new NFC_Library;
                $NFC->add_to_cart( $this->uri->segment(3) );
            }
        }
    }

    /**
     *  Dashboard Footer
     *  @param null
     *  @return void
    **/

    public function footer()
    {
        $this->load->module_view( 'nfc-parser', 'pos-script' );
    }

    /**
     *  Load Dashoard
     *  @param  null
     *  @return null
    **/

    public function load_dashboard()
    {
        $this->Gui->register_page_object( 'nfc_settings', new NFC_Parser_Controller );
    }

    /**
     *  Register Admin Menus
     *  @param
     *  @return
    **/

    public function admin_menus( $menus )
    {
        $menus[ 'nexo_settings' ][]     =   array(
            'title'     =>  __( 'NFC Settings', 'nfc-parser' ),
            'href'      =>  site_url( array( 'dashboard', 'nfc-settings' ) )
        );

        return $menus;
    }
}

new NFC_Parser_Module;

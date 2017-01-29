<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HeaderFooterModule extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
        $this->events->add_action( 'load_dashboard', [ $this, 'dashboard' ] );
        $this->events->add_action( 'dashboard_header', [ $this, 'header' ]);
        $this->events->add_action( 'dashboard_footer', [ $this, 'footer' ]);
        $this->events->add_filter( 'admin_menus', [ $this, 'menus' ], 20 );
    }

    /**
     *  Load Dashboard
     *  @param void
     *  @return void
    **/

    public function dashboard()
    {
        include_once( dirname( __FILE__ ) . '/inc/controller.php' );
        $this->Gui->register_page_object( 'header_footer', new HeaderFooterController );
    }

    /**
     *  header
     *  @param void
     *  @return void
    **/

    public function header()
    {
        global $Options;
        echo @$Options[ 'header_code' ];
    }

    /**
     *  Footer
     *  @param  void
     *  @return void
    **/

    public function footer()
    {
        global $Options;
        echo '<script type="text/javascript">';
        echo @$Options[ 'footer_script' ];
        echo '</script>';
    }

    /**
     *  menus
     *  @param  array menus
     *  @return array
    **/

    public function menus( $menus )
    {
        if( User::can( 'manage_core' ) ) {
            if( @$menus[ 'settings' ] != null ) {
                $menus  =   array_insert_before( 'settings', $menus, 'header_footer', [
                    [
                        'title' =>  'Header & Footer',
                        'href'   =>  site_url( array( 'dashboard', 'header_footer', 'settings' ) ),
                        'icon'  =>  'fa fa-file'
                    ]
                ]);
            }            
        }
        return $menus;
    }
}

new HeaderFooterModule;

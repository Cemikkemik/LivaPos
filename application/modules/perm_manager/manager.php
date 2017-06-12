<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This class is designed for the management of tendoo permission

class PermManagerModule extends Tendoo_Module 
{

    public function __construct() 
    {
        parent::__construct();
        $this->events->add_action( 'load_dashboard', [ $this, 'dashboard_loader' ] );
        $this->events->add_filter( 'admin_menus', [ $this, 'menus' ], 20);
        $this->events->add_filter( 'admin_menus', [ $this, 'menus' ], 20);
        $this->events->add_filter( 'dashboard_dependencies', [ $this, 'dependencies' ] );
        
        $this->register();
    }

    /**
     * Load Dashboard
     **/

    public function dashboard_loader()
    {
        if( User::can( 'manage_core' ) ) {
            include_once( dirname( __FILE__ ) . '/inc/controller.php' );
            $this->Gui->register_page_object( 'perm_manager', new PermManagerController );
        }
    }

    /**
     * Setting module Menu
     */
    
    public function menus( $menus )
    {
        if( User::can( 'manage_core' ) ) {
            if( @$menus[ 'users' ] != null ) {
                $menus[ 'users' ][]     =   [
                    'title'  => 'Manage permission',
                    'href'   =>  site_url( array( 'dashboard', 'perm_manager', 'mainboard' ) ),
                ];
            }            
        }
        return $menus;
    }

    public function register()
    {
        $bower_url      =   '../modules/perm_manager/bower_components/';
        $js_url         =   '../modules/perm_manager/js/';
        $css_url        =   '../modules/perm_manager/css/';
        $root_url       =   '../bower_components/';

        $this->enqueue->css_namespace( 'dashboard_header' ); 
        $this->enqueue->css( $bower_url . 'bootstrap-vertical-tabs/bootstrap.vertical-tabs' );
        $this->enqueue->css( $css_url . 'sweetalert' );

        $this->enqueue->js_namespace( 'dashboard_footer' );
        $this->enqueue->js( $root_url . 'angular-resource/angular-resource.min' );
        $this->enqueue->js( $js_url . 'ui-bootstrap-tpls-2.5.0.min' );
        $this->enqueue->js( $js_url . 'angular-sweetalert.min' );
        $this->enqueue->js( $js_url . 'sweetalert-min' );
    }

    public function dependencies( $deps ){
        $deps[]     =   'oitozero.ngSweetAlert';
        $deps[]     =   'ui.bootstrap';
        $deps[]     =   'ngResource';
        return $deps;
    }

}
new PermManagerModule;
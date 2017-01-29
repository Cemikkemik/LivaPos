<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once(dirname(__FILE__) . '/inc/NsamController.php');

class NexoAdvancedStoreManagerApp extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        //Codeigniter : Write Less Do More
        $this->events->add_filter( 'ui_notices', array( $this, 'notices' ) );
        $this->events->add_filter( 'admin_menus', array( $this, 'menus' ), 20 );
        $this->events->add_filter( 'stores_controller_callback', array( $this, 'multistore' ) );
        $this->events->add_action( 'after_main_store_card', array( $this, 'before_rows' ) );
        $this->events->add_action( 'load_dashboard', [ $this, 'load_dashboard' ] );
    }

    /**
     *  Load Dashboard
     *  @param void
     *  @return void
    **/

    public function load_dashboard()
    {
        $this->Gui->register_page_object( 'nsam', new NsamController );
    }

    /**
     *  Admin menu
     *  @param array
     *  @return array
    **/

    public function menus( $menus )
    {
        if( multistore_enabled() && is_multistore() ) {
            $menus[ 'nexo_settings' ][]   =   array(
                'href'      =>  site_url( array( 'dashboard', store_slug(), 'nsam', 'content_management' ) ),
                'title'     =>  __( 'Content Copy', 'nsam' ),
            );
        } else {
            if( multistore_enabled() ) {

                $menus[ 'nexo_store_settings' ][]   =   array(
                    'href'      =>  site_url( array( 'dashboard', 'nsam', 'module_control' ) ),
                    'title'     =>  __( 'Module Manager', 'nsam' )
                );

                $menus[ 'nexo_store_settings' ][]   =   array(
                    'href'      =>  site_url( array( 'dashboard', 'nsam', 'users_control' ) ),
                    'title'     =>  __( 'Access Manager', 'nsam' )
                );

                $menus          =   array_insert_after( 'nexo_shop', $menus, 'nexo_package', [
                    array(
                        'title' =>  __( 'Subscriptions', 'nsam' ),
                        'href'  =>  site_url( array( 'dashboard', 'nsam', 'subscriptions' ) ),
                        'icon'  =>  'fa fa-calendar'
                    ),
                    array(
                        'title' =>  __( 'Add new', 'nsam' ),
                        'href'  =>  site_url( array( 'dashboard', 'nsam', 'subscriptions', 'add_new' ) ),
                    )
                ]);
            }
        }
        return $menus;
    }

    /**
     *  Multistore controller
     *  @param array controllers
     *  @return array
    **/

    public function multistore( $array )
    {
        $array[ 'nsam' ]  =   new NsamController;
        return $array;
    }

    /**
     *  Ui Notices
     *  checks whether multistore is enabled
     *  @return void
    **/

    public function notices( $notices )
    {
        if( ! multistore_enabled() ) {
            $notices[]    =   array(
                'namespace'     =>  'request_multistore',
                'type'          =>  'info',
                'message'       =>  __( '<strong>Nexo Store Advanced Manager</strong>, is not active, since NexoPOS multi Store feature is not active.', 'nsam' ),
                'href'          =>  site_url( array( 'dashboard', 'nexo', 'stores-settings' ) ),
                'icon'          =>  'fa fa-info'
            );
        }
        return $notices;
    }

    /**
     *  Before Row
    *  @return void
    **/

    public function before_rows( $content )
    {
        // $this->load->module_view( 'nsam', 'main-store-widget' );
    }

}
new NexoAdvancedStoreManagerApp;

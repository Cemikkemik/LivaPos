<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once( dirname( __FILE__ ) . '/inc/nexoCustomerDisplayController.php' );
include_once( dirname( __FILE__ ) . '/inc/nexoCustomerDisplayInstaller.php' );

class NexoCustomerDisplay extends CI_Model{

  public function __construct()
  {
     parent::__construct();
     //Codeigniter : Write Less Do More
     $this->controller     = new NexoCustomerDisplayController;
     $this->install        = new NexoCustomerDisplayInstaller;
     
     $this->events->add_action( 'load_dashboard', array( $this, 'load_dashboard' ) );
     $this->events->add_action( 'do_enable_module', array( $this, 'install' ) );
     $this->events->add_action( 'do_remove_module', array( $this, 'uninstall' ) );
  }

    /**
    * Admin Menus
    * add custom menu to the dashboard
    * @param array admin array
    * @return array
    **/

    public function admin_menus( $menus )
    {
        $backup     =   $menus;
        $menus      =   array_insert_before( 'sales', $menus, 'customer-display', array(
            array(
                'title' =>  __( 'Customer Display', 'nexo_customer_display' ),
                'href'  =>  site_url( array( 'dashboard', store_slug(), 'customer-display' ) ),
                'icon'  =>  'fa fa-star',
                'disable'   => true
            ),
            array(
                'title' =>  __( 'All Sliders', 'nexo_customer_display' ),
                'href'  =>  site_url( array( 'dashboard', store_slug(), 'customer-display', 'cd_sliders' ) )
            ),
            /** array(
                'title' =>  __( 'All Slides', 'nexo_customer_display' ),
                'href'  =>  site_url( array( 'dashboard', store_slug(), 'customer-display', 'cd_slides' ) )
            ),**/
            array(
                'title' =>  __( 'Open Display', 'nexo_customer_display' ),
                'href'  =>  site_url( array( 'dashboard', store_slug(), 'customer-display', 'cd_list' ) )
            ),
            array(
                'title' =>  __( 'Display Setup', 'nexo_customer_display' ),
                'href'  =>  site_url( array( 'dashboard', store_slug(), 'customer-display', 'cd_settings' ) )
            )
        ) );

        return $menus ? $menus : $backup;
    }

    /**
     * Dashboard Footer
     * @return void
    **/

    public function dashboard_footer()
    {
        $this->load->module_view( 'nexo_customer_display', 'pos-script' );
    }

    /**
     * install
     * @return void
    **/

    public function install( $namespace )
    {
        if( $namespace == 'nexo_customer_display' ) {

            global $Options;

            if( @$Options[ 'nexo_customer_display_installed' ] == null ) {
                $this->options->set( 'nexo_customer_display_installed', true, true );
                $this->install->tables( '', $this->db->dbprefix );
            }
        }
    }

    /**
     * Install multistore
     * @param string table_prefix
     * @param string scope
     * @return void
    **/

    public function install_multistore( $prefix = '', $scope = '' )
    {
        $this->install->tables( $scope, $prefix );
    }



    /**
     * Load Dashboard
     * @return void
    **/

    public function  load_dashboard()
    {
        if( ! Modules::is_active( 'nexo' ) ) {
            $this->notice->push_notice(tendoo_warning(__('Nexo Module needs to be installed for Nexo Customer Display to work.', 'nexo_customer_display')));
            return false;
        }
        $this->events->add_filter( 'admin_menus', array( $this, 'admin_menus' ), 15 );
        $this->events->add_action( 'dashboard_footer', array( $this, 'dashboard_footer' ) );
        $this->events->add_action( 'nexo_after_install_tables', array( $this, 'install_multistore' ) );
        $this->events->add_filter( 'stores_controller_callback', array( $this, 'multistore' ) );
        $this->events->add_action( 'nexo_after_delete_tables', array( $this, 'uninstall_multistore' ) );

        $this->Gui->register_page_object( 'customer_display', $this->controller );
    }

    /**
     * Multistore
     * @return void
    **/

    public function multistore( $array )
    {
        $array[ 'customer-display' ]    =   array( $this->controller );
        return $array;
    }

    /**
     * Uninstall
     *
    **/

    public function uninstall()
    {
        $this->install->delete_table( '', $this->db->dbprefix );
    }

    /**
     * Uninstall multistore
     * @param string table_prefix
     * @param array/string scope
     * @return void
    **/

    public function uninstall_multistore( $table_prefix= '', $scope = '' )
    {
        $this->install->delete_table( $scope, $table_prefix );
    }
}
new NexoCustomerDisplay;

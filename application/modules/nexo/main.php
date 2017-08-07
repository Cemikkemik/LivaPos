<?php
// Auto Load
require_once(dirname(__FILE__) . '/vendor/autoload.php');
include_once(dirname(__FILE__) . '/inc/actions.php');
include_once(dirname(__FILE__) . '/inc/filters.php');

if (get_instance()->setup->is_installed()) {
    include_once(dirname(__FILE__) . '/inc/controller.php');
    include_once(dirname(__FILE__) . '/inc/tours.php');
}

require dirname(__FILE__) . '/inc/install.php';

class Nexo_Main extends CI_Model
{
    public function __construct()
    {
		global $PageNow;

		// Default PageNow value
		$PageNow	=	'nexo/index';

        parent::__construct();
        $this->actions      =   new Nexo_Actions;
        $this->filters      =   new Nexo_Filters;

        $this->load->helper('nexopos');
        $this->load->module_model( 'nexo', 'Nexo_Notices_Model', 'Nexo_Notices' );

        $this->events->add_action( 'load_dashboard_home', [ $this->actions, 'init' ] );
        $this->events->add_action( 'load_frontend', [ $this->actions, 'load_frontend' ] );
        $this->events->add_action( 'dashboard_footer', [ $this->actions, 'dashboard_footer' ] );
        $this->events->add_action( 'after_app_init', [ $this->actions, 'after_app_init' ] );
        $this->events->add_action( 'load_dashboard', [ $this->actions, 'load_dashboard' ], 5 );
        $this->events->add_action( 'dashboard_header', [ $this->actions, 'dashboard_header' ] );

        // $this->events->add_filter( 'ui_notices', [ $this->filters, 'ui_notices' ] );
        $this->events->add_filter( 'default_js_libraries', [ $this->filters, 'default_js_libraries' ] );        
        $this->events->add_filter( 'nexo_daily_details_link', [ $this->filters, 'remove_link' ] );        
        $this->events->add_filter( 'nexo_cart_buttons', [ $this->filters, 'nexo_cart_buttons' ] );
        $this->events->add_filter( 'login_redirection', [ $this->filters, 'login_redirection' ] );
        $this->events->add_filter( 'dashboard_dependencies', [ $this->filters, 'dashboard_dependencies' ] );
        $this->events->add_filter( 'signin_logo', [ $this->filters, 'signin_logo' ] );
        $this->events->add_filter( 'dashboard_footer_right', [ $this->filters, 'dashboard_footer_right' ] );
        $this->events->add_filter( 'dashboard_logo_long', [ $this->filters, 'dashboard_logo_long' ]);
        $this->events->add_filter( 'dashboard_logo_small', [ $this->filters, 'dashboard_logo_small' ] );
        $this->events->add_filter( 'dashboard_footer_text', [ $this->filters, 'dashboard_footer_text' ] );
        $this->events->add_filter( 'nexo_store_menus', [ $this->filters, 'store_menus' ] );
        $this->events->add_filter( 'ac_filter_get_request', [ $this->filters, 'ac_filter_get_request' ] ); // Awesome CRUD
        $this->events->add_filter( 'ac_delete_entry', [ $this->filters, 'ac_delete_entry' ] );
    }
}
new Nexo_Main;

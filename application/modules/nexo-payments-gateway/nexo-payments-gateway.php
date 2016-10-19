<?php
include_once( dirname( __FILE__ ) . '/inc/controllers/gateway.php' );
include_once( dirname( __FILE__ ) . '/inc/filters.php' );
include_once( dirname( __FILE__ ) . '/inc/actions.php' );

class Nexo_Payment_Gateway extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		
		$this->events->add_action( 'load_dashboard', array( $this, 'dashboard' ) );		
		$this->events->add_action( 'dashboard_footer', array( $this, 'dashboard_footer' ) );
		$this->events->add_action( 'dashboard_header', array( $this, 'dashboard_header' ) );
		$this->events->add_action( 'angular_paybox_footer', array( 'Nexo_Gateway_Actions', 'angular_paybox_footer' ) );
		$this->events->add_action( 'load_register_content', array( $this, 'register_content' ) );
		$this->events->add_filter( 'nexo_payments_types', array( 'Nexo_Gateway_Filters', 'payment_gateway' ) );
		$this->events->add_filter( 'nexo_settings_menu_array', array( 'Nexo_Gateway_Filters', 'admin_menus' ) );
		$this->events->add_filter( 'paybox_dependencies', array( 'Nexo_Gateway_Filters', 'paybox_dependencies' ) );
	}
	
	/**
	 * Load Dashboard
	**/
	
	public function dashboard()
	{
		$this->Gui->register_page( 'nexo_gateway_settings', array( 'Gateway_Controller', 'gateway_settings' ) );
		$this->Gui->register_page( 'nexo_stripe_settings', array( 'Gateway_Controller', 'stripe_settings' ) );
	}
	
	/**
	 * Dashboard Footer
	**/
	
	public function dashboard_footer()
	{
		global $PageNow;
		
		if( $PageNow == 'nexo/registers/__use' ) {
			$this->load->module_view( 'nexo-payments-gateway', 'dashboard-footer' );
		}
	}
	
	/**
	 * Dashboard Headed
	**/
	
	public function dashboard_header()
	{
		$this->load->module_view( 'nexo-payments-gateway', 'dashboard-header' );
	}
	
	/**
	 *  
	**/
	
	public function register_content()
	{
		include_once( MODULESPATH . '/nexo/inc/angular/order-list/services/window-splash.php' );
		include_once( MODULESPATH . '/nexo-payments-gateway/inc/angular/register/services/stripe-checkout.php' );
		include_once( MODULESPATH . '/nexo-payments-gateway/inc/angular/register/directives/stripe-payment.php' );
	}
}

new Nexo_Payment_Gateway;
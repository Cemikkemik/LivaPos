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
	}

	/**
	 * Load Dashboard
	**/

	public function dashboard()
	{
		global $Options;
		// Set default options for Stripe
		if (@$Options[ store_prefix() . 'nexo_enable_stripe' ] != 'no'):
			$this->load->config( 'nexo' );
			$payments	=	$this->config->item( 'nexo_payments_types' );
			$payments[ 'stripe' ]	=	__( 'Stripe' , 'nexo-payments-gateway' );
			$this->config->set_item( 'nexo_payments_types', $payments );

			$payments_all	=	$this->config->item( 'nexo_all_payment_types' );
			$payments_all[ 'stripe' ]	=	__( 'Stripe' , 'nexo-payments-gateway' );
			$this->config->set_item( 'nexo_all_payment_types', $payments_all );
		endif;

		$this->events->add_action( 'dashboard_footer', array( $this, 'dashboard_footer' ) );
		$this->events->add_action( 'dashboard_header', array( $this, 'dashboard_header' ) );
		$this->events->add_action( 'angular_paybox_footer', array( 'Nexo_Gateway_Actions', 'angular_paybox_footer' ) );
		$this->events->add_action( 'load_register_content', array( $this, 'register_content' ) );
		$this->events->add_filter( 'nexo_payments_types', array( 'Nexo_Gateway_Filters', 'payment_gateway' ) );
		$this->events->add_filter( 'nexo_settings_menu_array', array( 'Nexo_Gateway_Filters', 'admin_menus' ) );
		$this->events->add_filter( 'paybox_dependencies', array( 'Nexo_Gateway_Filters', 'paybox_dependencies' ) );
		// Registers for Multistore
		$this->events->add_filter( 'stores_controller_callback', array( $this, 'multistore' ) );

		$this->Gui->register_page_object( 'nexo_gateway', new Gateway_Controller );
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
		global $PageNow;

		if( $PageNow == 'nexo/registers/__use' ) {
			$this->load->module_view( 'nexo-payments-gateway', 'dashboard-header' );
		}
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

	/**
	 * Register for Multistore
	**/

	public function multistore( $array )
	{
		// to match this uri
		// dashboard/stores/nexo_premium/*
		$array[ 'nexo_gateway' ]	=	new Gateway_Controller;

		return $array;
	}
}

new Nexo_Payment_Gateway;

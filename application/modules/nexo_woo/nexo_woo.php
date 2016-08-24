<?php
class Nexo_Woo extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		
		$this->events->add_action( 'load_dashboard', array( $this, 'load_dashboard' ) );
		$this->events->add_filter( 'admin_menus', array( $this, 'menus' ) );
	}
	
	/**
	 * Load Dashboard
	**/
	
	public function load_dashboard()
	{
		$this->Gui->register_page( 'woocommerce', array( $this, 'Controller_Home' ) );			
		$this->Gui->register_page( 'export_to_woocommerce', array( $this, 'Controller_ExportToWooCommerce' ) );	
	}
	
	/**
	 * Controller Home
	**/
	
	public function Controller_Home()
	{
		$this->Gui->set_title( __( 'Réglages WooCommerce pour NexoPOS', 'nexo_woo' ) );
		$this->load->module_view( 'nexo_woo', 'home' );
	}
	
	/**
	 * Export to WooCommerce
	**/
	
	public function Controller_ExportToWooCommerce()
	{
		
	}
	
	/** 
	 * Admin Menu
	**/
	
	public function menus( $menu ) 
	{
		$menu	=	array_insert_before( 'settings', $menu, 'nexo_woo', array(
			array(
				'title'		=>	__( 'WooCommerce', 'nexo_woo' ),
				'href'		=>	'#',
				'disable'	=>	true
			),
			array(
				'title'		=>	__( 'Syncing', 'nexo_woo' ),
				'href'		=>	site_url( array( 'dashboard', 'woocommerce' ) ),
				'disable'	=>	true
			),
			array(
				'title'		=>	__( 'Export To WooCommerce', 'nexo_woo' ),
				'href'		=>	site_url( array( 'dashboard', 'export_to_woocommerce' ) ),
				'disable'	=>	true
			)
		) );
		return $menu;
	}
}
new Nexo_Woo;
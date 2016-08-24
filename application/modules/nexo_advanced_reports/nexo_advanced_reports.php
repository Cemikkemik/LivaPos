<?php
class Nexo_Advanced_Reports extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->events->add_action( 'load_dashboard', array( $this, 'dashboard' ) );
		$this->events->add_action( 'dashboard_header', array( $this, 'header' ) );
		$this->events->add_filter( 'admin_menus', array( $this, 'menus' ), 20 );
		
	}
	
	/**
	 * Dashboard Header
	**/
	
	public function header()
	{
		return;
		?><script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script><?php
	}
	
	/**
	 * Admin menus
	**/
	
	public function menus( $menus ) 
	{
		$menus[ 'rapports' ][]	=	array(
			'title'			=>		__( 'Fiche de contrôle de l\'arrivage', 'nexo_advanced_reports' ),
			'href'			=>		site_url( array( 'dashboard', 'nar_control_darrivage' ) )
		);
		
		$menus[ 'rapports' ][]	=	array(
			'title'			=>		__( 'Fiche récapitulative', 'nexo_advanced_reports' ),
			'href'			=>		site_url( array( 'dashboard', 'nar_fiche_recapitulative' ) )
		);
		
		return $menus;
	}
	
	/** 
	 * Load Dashboard
	**/
	
	public function dashboard()
	{
		$this->Gui->register_page( 'nar_control_darrivage', array( $this, 'controller_control_darrivage' ) );
		$this->Gui->register_page( 'nar_fiche_recapitulative', array( $this, 'controller_fiche_recapitulative' ) );
	}
	
	/** 
	 * Advanced Reports
	**/
	
	public function controller_control_darrivage()
	{
		$this->Gui->set_title( __( 'Fiche de contrôle d\'arrivage', 'nexo_advanced_reports' ) );
		$this->load->module_view( 'nexo_advanced_reports', 'control_darrivage' );
	}
	
	/**
	 * Fiche Recapitulative
	**/
	
	public function controller_fiche_recapitulative()
	{
		$this->load->model('Nexo_Categories');
        $this->load->model('Nexo_Misc');
		
		$data[ 'Categories' ]            =    $this->Nexo_Categories->get();
		$data[ 'Categories_Hierarchy' ]    =    $this->Nexo_Misc->build_category_hierarchy($data[ 'Categories'    ]);
		$data[ 'Categories_Depth' ]        =    $this->Nexo_Misc->array_depth($data[ 'Categories_Hierarchy' ]);
		
		$this->Gui->set_title( __( 'Fiche récapitulative', 'nexo_advanced_reports' ) );
		$this->load->module_view( 'nexo_advanced_reports', 'fiche_recaptitulative', $data );
	}
}
new Nexo_Advanced_Reports;
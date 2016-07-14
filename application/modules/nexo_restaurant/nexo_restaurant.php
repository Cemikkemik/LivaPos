<?php
include_once( dirname( __FILE__ ) . '/inc/install.php' );

class nexo_restaurant extends CI_Model
{
	/**
	 * Nexo For Restaurant
	**/
	
	public function __construct()
	{
		parent::__construct();
		
		// Creaet menus
		$this->events->add_filter( 'admin_menus', array( $this, 'admin_menus' ) );
		
		// Load Dashboard
		$this->events->add_action( 'load_dashboard', array( $this, 'load_dashboard' ) );
		
		// Admin Menu
		$this->events->add_action( 'display_admin_header_menu', array( $this, 'dash_menu' ) );
		
		// Load Assets
		$this->events->add_filter('default_js_libraries', function ($libraries) {
			$bower_path		=    '../modules/nexo_restaurant/bower_components/';
			
			$libraries[]	=	$bower_path . 'chance/chance';
				
			return $libraries;
		});
		
		$this->events->add_action( 'dashboard_header', function(){
			?>
            <script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
            <?php
		});
	}
	
	/**
	 * Creating menus
	**/
	
	public function admin_menus( $menus )
	{
		$menus	=	array_insert_before( 'settings', $menus, 'tables', array(
			array(
				'title'		=>	__( 'Restaurant Tables', 'nexo_restaurant' ),
				'href'		=>	'#',
				'icon'		=>	'fa fa-cutlery',
				'disable'	=>	true
			),
			array(
				'title'		=>	__( 'Table list', 'nexo_restaurant' ),
				'href'		=>	site_url( array( 'dashboard', 'nexo_restaurant', 'tables' ) ),
			),
			array(
				'title'		=>	__( 'Create a new table', 'nexo_restaurant' ),
				'href'		=>	site_url( array( 'dashboard', 'nexo_restaurant', 'tables', 'add_new' ) ),
			)
		) );
		
		return $menus;
	}
	
	/**
	 * Load Dashbaord
	**/
	
	public function load_dashboard()
	{
		$this->Gui->register_page( 'nexo_restaurant', array( $this, 'nexo_restaurant' ) );
	}
	
	/**
	 * POS In
	**/
	
	public function nexo_restaurant( $page = 'home' )
	{
		$this->Gui->set_title( __( 'Restaurant POS', 'nexo_restaurant' ) );
		$this->load->module_view( 'nexo_restaurant', 'dashboard/gui-restaurant' );	
	}
	
	/** 
	 * Dash Menu
	**/
	
	public function dash_menu()
	{
		?>
        <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
              <?php _e( 'Open Bills', 'nexo' );?>
              <span class="label label-success">2</span>
            </a>
          </li>
          <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
              <?php _e( 'Today Sales', 'nexo' );?>
              <span class="label label-warning">30</span>
            </a>
          </li>
        <?php
	}
}

new nexo_restaurant;
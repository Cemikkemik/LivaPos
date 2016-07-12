<?php
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
		return $menus;
	}
	
	/**
	 * Load Dashbaord
	**/
	
	public function load_dashboard()
	{
		
		$this->Gui->register_page( 'restaurant_pos', array( $this, 'pos_in' ) );
	}
	
	/**
	 * POS In
	**/
	
	public function pos_in()
	{
		$this->Gui->set_title( __( 'Restaurant POS', 'nexo_restaurant' ) );
		$this->load->module_view( 'nexo_restaurant', 'dashboard/gui-restaurant' );	
	}
}

new nexo_restaurant;
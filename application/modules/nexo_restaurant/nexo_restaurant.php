<?php
include_once( dirname( __FILE__ ) . '/inc/install.php' );
include_once( dirname( __FILE__ ) . '/inc/actions.php' );
include_once( dirname( __FILE__ ) . '/inc/filters.php' );

class Nexo_Restaurant_Main extends CI_Model
{
	/**
	 * Nexo For Restaurant
	**/
	
	public function __construct()
	{
		parent::__construct();
		
		global $Options;
		
		if( ! Modules::is_active( 'nexo' ) ) {
			return;
		}		
		
		$this->Actions		=	new Nexo_Restaurant_Actions;
		$this->Filters		=	new Nexo_Restaurant_Filters;
	}
}

new Nexo_Restaurant_Main;
<?php
class NexoPlayGroundController extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Package Manager
	 *
	 * @param string slug parameter
	 * @return void
	**/

	public function manager()
	{
		$this->events->add_action( 'dashboard_footer', function(){
			get_instance()->load->module_view( 'nexo-playground-manager', 'package-management-script', array() );
		});

		$this->Gui->set_title( __( 'Package Management', 'nexo-playground-manager' ) );
		$this->load->module_view( 'nexo-playground-manager', 'package-management' );
	}

	/**
	 * Package Settings
	**/

	public function settings()
	{
		$this->Gui->set_title( __( 'Package Settings', 'nexo-playground-manager' ) );
		$this->load->module_view( 'nexo-playground-manager', 'package-management' );
	}
}

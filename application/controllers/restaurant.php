<?php
class Restaurant extends Tendoo_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Restaurant Index
	**/
	
	public function index()
	{
		$this->load->module_view( 'nexo_restaurant', 'public/home' );
	}
}
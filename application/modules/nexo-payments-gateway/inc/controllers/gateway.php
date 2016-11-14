<?php
class Gateway_Controller
{
	public static function stripe_settings()
	{
		$core	=	get_instance();
		$core->Gui->set_title( 'Settings' );
		$core->load->module_view( 'nexo-payments-gateway', 'stripe-settings' );
	}

	/**
	 * Gateway Settings
	**/

	public static function settings()
	{
		$core	=	get_instance();
		$core->Gui->set_title( 'Settings' );
		$core->load->module_view( 'nexo-payments-gateway', 'gateway-settings' );
	}
}

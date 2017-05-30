<?php
class Nexo_Gateway_Actions
{
	public static function angular_paybox_footer()
	{
		get_instance()->load->module_view( 'nexo-payments-gateway', 'angular-paybox-controller' );
	}
}
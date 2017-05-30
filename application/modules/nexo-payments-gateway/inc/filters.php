<?php
class Nexo_Gateway_Filters
{
	public static function payment_gateway( $gateway )
	{
		global $Options;
		if (@$Options[ store_prefix() . 'nexo_enable_stripe' ] != 'no'):
			$gateway[ 'stripe' ]	=	__( 'Stripe', 'nexo-payments-gateway' );
		endif;

		return $gateway;
	}

	/**
	 * Admin Menu
	**/

	public static function admin_menus( $menus )
	{
		$menus[]		=	array(
			'title'		=>		__( 'Payment Gateway', 'nexo-payments-gateway' ),
			'href'		=>		site_url( array( 'dashboard', store_slug(), 'nexo_gateway', 'settings' ) )
		);

		return $menus;
	}

	/**
	 * PayBox dependency
	 * register Stripe Checkout and Windows_Splash
	**/

	public static function paybox_dependencies( $dependencies )
	{
		return array_merge( $dependencies, array( '__windowSplash', '__stripeCheckout' ) );
	}
}

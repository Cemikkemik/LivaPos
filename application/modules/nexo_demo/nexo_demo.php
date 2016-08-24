<?php
! defined( 'APPPATH' ) ? die() : NULL;

class Nexo_Demo extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->events->add_action( 'after_app_init', array( $this, 'init' ) );
		$this->events->add_action( 'load_dashboard', array( $this, 'dashboard' ) );
	}
	
	function init()
	{
		$this->events->add_filter( 'signin_notice_message', function( $notice ){
			$notice	.= '<h3 style="margin-top:0px;"><strong>Login</strong> : admin<br><strong>Password </strong>: 123456</h3>' . tendoo_info( 'Just Press <strong>Sign In</strong>' );
			ob_start();
			?>
            <script type="text/javascript">
			$( document ).ready(function(e) {
                $( '[name="username_or_email"]' ).val( 'admin' );
				$( '[name="password"]' ).val( '123456' );
            });
			</script>
            <?php
			return $notice . ob_get_clean();
		});
	}
	
	function dashboard()
	{
		return;
		$this->Cache		=	new CI_Cache( array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'nexo_') );
		if( ! $this->Cache->get( 'shop_timeout' ) ) {
			if( class_exists( 'Nexo_Misc' ) ) {
				$this->Nexo_Misc->enable_demo();
				$this->notice->push_notice( tendoo_info( __( 'Demo content has expired. The shop has been restored. If you see this, it means that all date you have created has been deleted. Please consider try again. Shop restore after 12 hours.' ) ) );
				$this->Cache->save( 'shop_timeout', true, 43200 );
			}
		}
	}
}
new Nexo_Demo;
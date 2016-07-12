<?php
class Footer extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->events->add_action( 'dashboard_header', array( $this, 'dashboard_header' ) );
		$this->events->add_action( 'load_dashboard', array( $this, 'load_dashboard' ) );
		$this->events->add_filter( 'admin_menus', array( $this, 'admin_menus' ) );
	}
	
	/**
	 * Admin Menus
	 * @params Array menu
	 * @return Array menu
	**/
	
	public function admin_menus( $menus ) 
	{
		if( User::in_group( 'master' ) ) {
			$menus	=	array_insert_before( 'settings', $menus, 'footer', array(
				array(
					'title'		=>	__( 'Footer', 'footer' ),
					'href'		=>	site_url( array( 'dashboard', 'footer' ) ),
				)
			) );
		}		
		return $menus;	
	}
	
	/**
	 * Load Dashboard
	**/
	
	public function load_dashboard()
	{
		if( User::in_group( 'master' ) ) {
			$this->Gui->set_title( __( 'Footer &mdash; Tendoo CMS' ) );
			$this->Gui->register_page( 'footer', array( $this, 'Controller_Home' ) );
		}
	}
	
	/** 
	 * Controller Home
	**/
	
	public function Controller_Home()
	{
		$this->load->module_view( 'footer', 'home' );
	}
	
	/**
	 * Dashboard Footer
	**/
	
	public function dashboard_header()
	{
		global $Options;
		?>
        <script>
			<?php echo html_entity_decode( @$Options[ 'footer_content' ] );?>
		</script>
        <?php
	}
}
new Footer;
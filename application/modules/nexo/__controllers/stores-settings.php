<?php
class Nexo_Stores_Settings_Controller extends CI_Model
{
    public function __construct($args)
    {
        parent::__construct();
        if (is_array($args) && count($args) > 0) {
            if (method_exists($this, $args[0])) {
                return call_user_func_array(array( $this, $args[0] ), array_slice($args, 1));
            } else {
                return $this->settings();
            }
        }
        return $this->settings();
    }
    
    public function settings($page = 'home')
    {
		global $PageNow;
		
		if (
			User::can('create_shop') &&
			User::can('edit_shop') &&
			User::can('delete_shop')
		) {
			
			$PageNow		=	'nexo/registers/list';
			
			$this->Gui->set_title( store_title( __( 'Réglages des boutiques', 'nexo' ) ) );
			$this->load->module_view( 'nexo', 'stores/main', array() );
			
		} else {
			redirect(array( 'dashboard', 'access-denied' ));
		}
    }
}
new Nexo_Stores_Settings_Controller($this->args);

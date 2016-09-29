<?php
class Nexo_Dashboard extends CI_Model
{
    public function __construct($args)
    {
        parent::__construct();
        if (is_array($args) && count($args) > 1) {
            if (method_exists($this, $args[1])) {
                return call_user_func_array(array( $this, $args[1] ), array_slice($args, 2));
            } else {
                return $this->defaults();
            }
        }
        return $this->defaults();
    }
    
    public function defaults()
    {
		// load widget model here only
		$this->load->model('Dashboard_Model', 'dashboard');
		$this->load->model('Dashboard_Widgets_Model', 'dashboard_widgets');
		
		// trigger action while loading home (for registering widgets)
		$this->events->do_action('load_dashboard_home');
		$this->dashboard->load_widgets();
		
		$this->Gui->set_title( store_title( __( 'Tableau de bord', 'nexo' ) ) );
		$this->load->module_view( 'nexo', 'stores/dashboard' );
    }
}
new Nexo_Dashboard($this->args);

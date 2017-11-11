<?php
class NexoDashboardController extends CI_Model
{
	public function index()
	{
		// load widget model here only
		// $this->load->model('Dashboard_Model', 'dashboard');
		// $this->load->model('Dashboard_Widgets_Model', 'dashboard_widgets');
		
		// trigger action while loading home (for registering widgets)
		$this->events->do_action('load_dashboard_home');
		// $this->dashboard->load_widgets();
		
		$this->Gui->set_title( store_title( __( 'Tableau de bord', 'nexo' ) ) );
		$this->load->module_view( 'nexo', 'stores/dashboard' );
	}
}
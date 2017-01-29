<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once( dirname( __FILE__ ) . '/controllers/nexopos-items.php' );
include_once( dirname( __FILE__ ) . '/controllers/nexopos-angular.php' );

class NexoPOS_Actions extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  register Controller
     *  @param  void
     *  @return void
    **/

    public function register_controllers()
    {
        $this->Gui->register_page_object( 'nexopos-items', new NexoPOS_Items_Controller );
        $this->Gui->register_page_object( 'nexopos-angular', new NexoPOS_Angular_Controller );
    }

    public function dashboard_footer()
    {
        $this->load->module_view( 'nexopos_advanced', 'dashboard/footer' );
    }
}

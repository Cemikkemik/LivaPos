<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NexoPOS_Items_Controller extends Tendoo_Module
{
    public function index()
    {
        $this->Gui->set_title( __( 'NexoPOS &mdash; Items' ) );
        $this->load->module_view( 'nexopos_advanced', 'items/gui' );
    }

    /**
     *  New Items
     *  @param
     *  @return
    **/

    public function add()
    {
        echo 'Hello World';
    }
}

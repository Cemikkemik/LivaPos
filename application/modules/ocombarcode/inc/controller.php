<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ocombarcode_Controller extends Tendoo_Module
{
    public function settings()
    {
        $this->Gui->set_title( store_title( __( 'Ocombarcode Settings', 'ocombarcode' ) ) );
        $this->load->module_view( 'ocombarcode', 'settings' );
    }
}

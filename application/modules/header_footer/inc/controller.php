<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HeaderFooterController extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    public function settings()
    {
        $this->Gui->set_title( 'Header And Footer ' );
        $this->load->module_view( 'header_footer', 'settings' );
    }
}

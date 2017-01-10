<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NFC_Parser_Controller extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  Index
     *  @param
     *  @return
    **/

    public function index()
    {
        $this->Gui->set_title( store_title( __( 'NFC Settings' ) ) );
        $this->load->module_view( 'nfc-parser', 'nfc-settings' );
    }
}

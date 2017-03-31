<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NexoSMS_Controller extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Nexo SMS Settings Controller
    **/

    public function settings()
    {
        $this->Gui->set_title(__('Réglages SMS &mdash; NexoPOS', 'nexo_sms'));
        $this->load->module_view('nexo_sms', 'home');
    }
}

<?php
class NexoSMSController extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Index method
     * @return view
     */
    public function index()
    {
        $this->Gui->set_title(__('Réglages SMS &mdash; NexoPOS', 'nexo_sms'));
        $this->load->module_view('nexo_sms', 'home');
    }
}
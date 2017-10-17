<?php
class NexoAboutController extends Tendoo_Module
{
    public function index()
    {
        $this->Gui->set_title( __( 'Bienvenue sur NexoPOS', 'nexo' ) );
        $this->load->module_view( 'nexo', 'welcome.gui' );
    }
}
<?php
class Nexo_About extends Tendoo_Module
{
    public function __construct($args)
    {
        parent::__construct();
        
        if (is_array($args) && count($args) > 1) {
            if (method_exists($this, $args[1])) {
                return call_user_func_array(array( $this, $args[1] ), array_slice($args, 2));
            } else {
                return $this->index();
            }
        }
        return $this->index();
    }

    public function index()
    {
        $this->Gui->set_title( __( 'Bienvenue sur NexoPOS', 'nexo' ) );
        $this->load->module_view( 'nexo', 'welcome.gui' );
    }
}
new Nexo_About($this->args);
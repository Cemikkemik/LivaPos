<?php

include_once( dirname( __FILE__ ) . '/inc/libraries/Awesome_Crud.php' );
include_once( dirname( __FILE__ ) . '/inc/controller.php' );

class Awesome_Crud_Module extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
        $this->events->add_action( 'load_dashboard', [ $this, 'load_dashboard' ]);
    }   

    public function load_dashboard()
    {
        $this->Gui->register_page_object( 'awesome-crud', new AwesomeCrud_Controller );
    }
}

// new Awesome_Crud_Module;
<?php

include_once( dirname( __FILE__ ) . '/inc/libraries/Awesome_Crud.php' );

class Awesome_Crud_Module extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }   
}

new Awesome_Crud_Module;
<?php

include_once( dirname( __FILE__ ) . '/inc/actions.php' );
include_once( dirname( __FILE__ ) . '/inc/filters.php' );
include_once( dirname( __FILE__ ) . '/inc/controller.php' );

class Nexo_Stock_Manager_Module extends Tendoo_Module
{
    public function __construct()
    {
        $this->filters      =   new Nexo_Stock_Manager_Filters;
        $this->actions      =   new Nexo_Stock_Manager_Actions;

        $this->events->add_filter( 'admin_menus', [ $this->filters, 'admin_menus' ], 20 );
        $this->events->add_filter( 'force_show_inventory', '__return_true' );
        $this->events->add_action( 'load_dashboard', [ $this->actions, 'load_dashboard' ] );
        $this->events->add_action( 'do_enable_module', [ $this->actions, 'do_enable_module' ] );
        $this->events->add_action( 'do_remove_module', [ $this->actions, 'do_remove_module' ] );
        $this->events->add_action( 'nexo_after_install_tables', [ $this->actions, 'install_tables' ] );
        $this->events->add_action( 'nexo_after_delete_tables', [ $this->actions, 'remove_tables' ] );
    }
}
new Nexo_Stock_Manager_Module;
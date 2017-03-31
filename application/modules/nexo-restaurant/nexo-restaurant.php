<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once( dirname( __FILE__ ) . '/inc/actions.php' );
include_once( dirname( __FILE__ ) . '/inc/filters.php' );

class Nexo_Restaurant_Main extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
        $this->actions      =   new Nexo_Restaurant_Actions;
        $this->filters      =   new Nexo_Restaurant_Filters;

        $this->events->add_action( 'do_enable_module', [ $this->actions, 'enable_module' ] );
        $this->events->add_action( 'do_remove_module', [ $this->actions, 'remove_module' ]);
        $this->events->add_action( 'load_dashboard', [ $this->actions, 'load_dashboard' ]);
        $this->events->add_action( 'dashboard_footer', [ $this->actions, 'dashboard_footer' ]);
        $this->events->add_filter( 'admin_menus', [ $this->filters, 'admin_menus' ], 20 );
        $this->events->add_filter( 'nexo_cart_buttons', [ $this->filters, 'cart_buttons' ]);
        $this->events->add_action( 'nexo_after_install_tables', [ $this->actions, 'store_install_tables' ]);
        $this->events->add_action( 'nexo_after_delete_tables', [ $this->actions, 'store_delete_tables' ] );
    }
}

new Nexo_Restaurant_Main;

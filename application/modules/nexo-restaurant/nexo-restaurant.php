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
        $this->events->add_filter( 'allowed_order_for_print', [ $this->filters, 'allow_print' ]);
        $this->events->add_filter( 'before_cart_pay_button', [ $this->filters, 'add_combo' ] );
        $this->events->add_action( 'nexo_after_install_tables', [ $this->actions, 'store_install_tables' ]);
        $this->events->add_action( 'nexo_after_delete_tables', [ $this->actions, 'store_delete_tables' ] );
        $this->events->add_action( 'nexo_empty_shop', [ $this->actions, 'empty_shop' ]);
        $this->events->add_action( 'nexo_enable_demo', [ $this->actions, 'enable_demo' ]);
        $this->events->add_filter( 'nexo_demo_list', [ $this->filters, 'restaurant_demo' ] );
        $this->events->add_filter( 'order_editable', [ $this->filters, 'order_editable' ] );
        $this->events->add_filter( 'load_product_crud', [ $this->filters, 'load_product_crud' ] );
    }
}

new Nexo_Restaurant_Main;

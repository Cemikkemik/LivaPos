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
        $this->events->add_action( 'nexo_after_install_tables', [ $this->actions, 'store_install_tables' ], 11 );
        $this->events->add_action( 'nexo_after_delete_tables', [ $this->actions, 'store_delete_tables' ], 8 );
        $this->events->add_action( 'nexo_empty_shop', [ $this->actions, 'empty_shop' ]);
        $this->events->add_action( 'nexo_enable_demo', [ $this->actions, 'enable_demo' ]);
        $this->events->add_action( 'edit_loaded_order', [ $this->actions, 'edit_loaded_order' ]);
        $this->events->add_filter( 'post_order_details', [ $this->filters, 'post_order_details' ], 10, 1 );
        $this->events->add_filter( 'put_order_details', [ $this->filters, 'put_order_details' ], 10, 2 );
        $this->events->add_filter( 'nexo_demo_list', [ $this->filters, 'restaurant_demo' ] );
        $this->events->add_filter( 'load_product_crud', [ $this->filters, 'load_product_crud' ] );
        $this->events->add_filter( 'dashboard_dependencies', [ $this->filters, 'dashboard_dependencies' ] );
        $this->events->add_filter( 'cart_pay_button', [ $this->filters, 'cart_pay_button' ] );
        $this->events->add_filter( 'admin_menus', [ $this->filters, 'admin_menus' ], 20 );
        $this->events->add_filter( 'checkout_header_menus_1', [ $this->filters, 'cart_buttons' ]);
        $this->events->add_filter( 'receipt_after_item_name', [ $this->filters, 'receipt_after_item_name' ] );
        $this->events->add_filter( 'receipt_filter_item_price', [ $this->filters, 'receipt_filter_item_price' ] );
        $this->events->add_filter( 'post_order_item', [ $this->filters, 'post_order_item' ], 10, 2 );
        $this->events->add_filter( 'put_order_item', [ $this->filters, 'put_order_item' ], 10, 2 );
        $this->events->add_filter( 'checkout_header_menus_2', [ $this->filters, 'cart_buttons_2' ]);
        $this->events->add_filter( 'allowed_order_for_print', [ $this->filters, 'allowed_order_for_print' ]);
        $this->events->add_filter( 'pos_edited_items', [ $this->filters, 'pos_edited_items' ]);
        $this->events->add_filter( 'post_order_response', [ $this->filters, 'post_order_response' ], 10, 2 );
        $this->events->add_filter( 'put_order_response', [ $this->filters, 'put_order_response' ], 10, 2 );
        $this->events->add_filter( 'loaded_order', [ $this->filters, 'loaded_order' ]);
    }
}

new Nexo_Restaurant_Main;

<?php
// Include Actions
include_once( dirname( __FILE__ ) . '/inc/actions.php' );
include_once( dirname( __FILE__ ) . '/inc/filters.php' );

// Nexo Updater
class Nexo_Updater_Module extends Tendoo_Module 
{
     public function __construct()
     {
          parent::__construct();
          $this->actions      =    new Nexo_Updater_Actions;
          $this->filters      =    new Nexo_Updater_Filters;

          $this->events->add_action( 'load_dashboard', [ $this->actions, 'load_dashboard' ] );
          $this->events->add_filter( 'admin_menus', [ $this->filters, 'admin_menus' ], 20 );
          $this->events->add_action( 'do_enable_module', [ $this->actions, 'do_enable_module' ] );
     }
}
new Nexo_Updater_Module;
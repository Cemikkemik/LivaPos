<?php
include_once( dirname( __FILE__ ) . '/inc/controller.php' );
class Ilhan_Module extends Tendoo_Module 
{
     public function __construct()
     {
          parent::__construct();
          $this->events->add_filter( 'checkout_header_menus_1', [ $this, 'checkout_header_menus_1'] );
          $this->events->add_action( 'dashboard_footer', [ $this, 'dashboard_footer' ]);
          $this->events->add_action( 'load_dashboard', [ $this, 'load_dashboard' ]);
     }

     /**
      * Dashboard footer
      * @return void
      */
     public function dashboard_footer() 
     {
          $this->load->module_view( 'ilhan', 'script' );
     }

     /**
      * Load Dashboard
      */
     public function load_dashboard()
     {
          $this->enqueue->js( 'bootstrap-datetimepicker.min', module_url( 'ilhan' ) .'bower_components/eonasdan-bootstrap-datetimepicker/build/js/' );
          $this->enqueue->css( 'bootstrap-datetimepicker.min', module_url( 'ilhan' ) . 'bower_components/eonasdan-bootstrap-datetimepicker/build/css/' );
          
          // register regular Controller
          $this->Gui->register_page_object( 'ilhan', new ilhanCTRL );

          // register store callback for store
          $this->events->add_filter( 'stores_controller_callback', function( $action ) {
               $actions[ 'ilhan' ]      =    new ilhanCTRL;
               return $action;
           });
     }

     /**
      * Checkout Columns 1
      * @param array current columns
      * @return array update current column
      */
     public function checkout_header_menus_1( $menus ) 
     {
          $menus[]       =    [
               'class' =>  'default calendar-button',
               'text'  =>  __( 'Date', 'ilhan' ),
               'icon'  =>  'calendar',
               'attrs' =>  [
                    'ng-controller'     =>   'ilhanCTRL',
                    'ng-click'  =>  'openCalendar()',
               ]
          ];

          return $menus;
     }
}

new Ilhan_Module;
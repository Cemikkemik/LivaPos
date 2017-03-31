<?php
class AngularMaterialModule extends Tendoo_Module
{
    /**
     *  Public Construct
     *  @param
     *  @return void
    **/

    public function __construct()
    {
        parent::__construct();
        $this->events->add_action( 'load_dashboard', array( $this, 'load_assets' ), 50 );
        $this->events->add_action( 'load_dashboard', array( $this, 'load_controller' ) );
    }


    /**
     *  Load Dashboard
     *  @return void
    **/

    public function load_assets()
    {
        $module_url      =   module_url( 'angular_material' );

        $this->enqueue->css_namespace( 'dashboard_header' );
        $this->enqueue->css( $module_url . 'css/angular-material.min', '' );
        $this->enqueue->css( $module_url . 'bower_components/angular-material-data-table/dist/md-data-table.min', '' );


        $this->enqueue->js_namespace( 'dashboard_footer' );
        $this->enqueue->js( $module_url . 'bower_components/file-saver/FileSaver.min', '' );
        //$this->enqueue->js( $module_url . 'bower_components/angular/angular.min', '' );
        $this->enqueue->js( js_url() . '../bower_components/angular-resource/angular-resource.min', '' );
        $this->enqueue->js( js_url() . '../bower_components/angular-route/angular-route.min', '' );
        $this->enqueue->js( $module_url . 'bower_components/angular-animate/angular-animate.min', '' );
        $this->enqueue->js( $module_url . 'bower_components/angular-aria/angular-aria.min', '' );
        $this->enqueue->js( $module_url . 'bower_components/angular-messages/angular-messages.min', '' );
        $this->enqueue->js( $module_url . 'bower_components/angular-material/angular-material.min', '' );
        $this->enqueue->js( $module_url . 'bower_components/angular-material-data-table/dist/md-data-table.min', '' );
        // $this->enqueue->js( $module_url . 'bower_components/js-xlsx/dist/xlsx.full.min', '' );
        $this->enqueue->js( $module_url . 'bower_components/json-export-excel/dest/json-export-excel.min', '' );

        /**
         * Spinner script
        **/

        $this->events->add_action( 'dashboard_footer', function(){
            get_instance()->load->module_view( 'angular_material', 'spinner-script' );
        });
    }

    /**
     *  Load Controller
     *  @param
     *  @return
    **/

    public function load_controller()
    {
        // Load Angular Dependencies
        $this->events->add_filter( 'dashboard_dependencies', function( $array ) {
            ! in_array( 'ngRoute', $array ) ? $array[]          =   'ngRoute'   :   null;
            ! in_array( 'ngResource', $array ) ? $array[]       =   'ngResource' : null;
            ! in_array( 'ngMaterial', $array ) ? $array[]       =   'ngMaterial' : null;
            ! in_array( 'md.data.table', $array ) ? $array[]    =   'md.data.table' : null;
            ! in_array( 'ngJsonExportExcel', $array ) ? $array[]    =   'ngJsonExportExcel' : null;
            return $array;
        });

        $this->Gui->register_page_object( 'angular', new AngularMaterialController );
    }

}

// include_once( dirname( __FILE__ ) . '/inc/library.php' );
// include_once( dirname( __FILE__ ) . '/inc/controller.php' );
// include_once( dirname( __FILE__ ) . '/inc/filters.php' );

// new AngularMaterialModule;

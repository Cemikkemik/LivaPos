<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Alvaro_Assets extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  load Assets
     *  @param void
     *  @return void
    **/

    public function load()
    {
        $this->enqueue->css_namespace( 'dashboard_header' );
        $this->enqueue->css( 'bower_components/angular-bootstrap-calendar/dist/css/angular-bootstrap-calendar.min', module_url( 'alvaro' ) );
        $this->enqueue->js_namespace( 'dashboard_footer' );
        $this->enqueue->js( 'bower_components/angular-touch/angular-touch.min', js_url() . '../' );
        $this->enqueue->js( 'bower_components/angular-bootstrap-calendar/dist/js/angular-bootstrap-calendar.min', module_url( 'alvaro' ) );
        $this->enqueue->js( 'bower_components/angular-bootstrap-calendar/dist/js/angular-bootstrap-calendar-tpls', module_url( 'alvaro' ) );
        $this->enqueue->js( 'bower_components/angular-bootstrap/ui-bootstrap.min', module_url( 'alvaro' ) );
        $this->enqueue->js( 'bower_components/angular-bootstrap/ui-bootstrap-tpls.min', module_url( 'alvaro' ) );
        $this->enqueue->js( 'bower_components/interactjs/dist/interact.min', module_url( 'alvaro' ) );
        $this->enqueue->js( 'bower_components/bootstrap-ui-datetime-picker/dist/datetime-picker.min', module_url( 'alvaro' ) );
        $this->enqueue->js( 'bower_components/moment/locale/es', module_url( 'alvaro' ) );
    }
}

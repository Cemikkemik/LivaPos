<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NexoPOS_Assets extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
        $bower_url      =   '../modules/nexopos_advanced/bower_components/';
        $js_url         =   '../modules/nexopos_advanced/js/';
        $css_url        =   '../modules/nexopos_advanced/css/';

        $this->enqueue->css_namespace( 'dashboard_header' );
        $this->enqueue->css( $bower_url . 'angular-ui-notification/dist/angular-ui-notification.min' );

        $this->enqueue->js_namespace( 'dashboard_footer' );
        // $this->enqueue->js( $bower_url . 'angular-ui-notification/dist/angular-ui-notification.min' );
    }
}

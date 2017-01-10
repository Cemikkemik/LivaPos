<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AngularMaterialFilters extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
        $this->events->add_filter( 'tendoo_spinner',  [ $this, 'spinner' ] );
    }

    /**
     *  Change Default Tendoo Spinner
     *  @param string
     *  @return string
    **/

    public function spinner( $string )
    {
        return '<div style="float:left;margin:10px;display:none;" ng-controller="tendooSpinner"><md-progress-circular md-mode="indeterminate" md-diameter="30" class="md-warn md-hue-3"></md-progress-circular></div>';
    }
}
new AngularMaterialFilters;

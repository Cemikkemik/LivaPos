<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NexoPOS_Angular_Controller extends Tendoo_Module
{
    public function controller( $namespace )
    {
        $this->load->module_view( 'nexopos_advanced', 'angular/' . $namespace . '/controllers/' . $namespace );
    }

    /**
     *  Require
     *  @param
     *  @return
    **/

    public function __require( $namespace )
    {
        $file_name  =   str_replace( '.js', '', $namespace );
        $file_name  =   str_replace( '.', '/', $file_name );
        $this->load->module_view( 'nexopos_advanced', 'angular/' . $file_name );
    }

}

<?php

use Pecee\SimpleRouter\SimpleRouter as Route;
use Pecee\Handlers\IExceptionHandler;
use Pecee\Http\Request;
use Pecee\SimpleRouter\Exceptions\NotFoundHttpException;

class Api_Controller extends Tendoo_Controller 
{
    /**
     * Get Parameter
     * @param string
     * @param string/null default value
     * @return string/null/object
     */
    public function get( $param, $default = null) 
    {
        return input( $index, $default, 'get' );
    }

    /**
     * Post Value
     * @param string
     * @param string default value
     * @return POST value
     */
    public function post( $param, $default = null ) 
    {
        return input( $index, $default, 'post' );
    }
}
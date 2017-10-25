<?php
use Pecee\Http\Request;
class PermissionsController extends Tendoo_Module
{
     public function post()
     {
          var_dump( request() );
     }
}
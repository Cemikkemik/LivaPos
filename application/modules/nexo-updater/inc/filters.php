<?php
class Nexo_Updater_Filters extends Tendoo_Module
{
     public function __construct()
     {
          parent::__construct();
     }
     public function admin_menus( $menus )
     {
          if( @$menus[ 'nexo_settings' ] ) {
               $menus[ 'nexo_settings' ][]   =    [
                    'title'        =>   __( 'Activate Your Licence', 'nexo' ),
                    'href'         =>   site_url([ 'dashboard', 'nexo-updater', 'activate' ])
               ];
          }
          return $menus;
     }
}
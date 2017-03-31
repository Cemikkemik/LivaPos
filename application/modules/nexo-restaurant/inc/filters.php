<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nexo_Restaurant_Filters extends Tendoo_Module
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *  Admin Menus
     *  @param array menus
     *  @return array current menu
    **/

    public function admin_menus( $menus )
    {
        if( @$menus[ 'caisse' ] != null ) {
            $menus      =   array_insert_after( 'caisse', $menus, 'restaurant', [
                [
                    'title'     =>      __( 'Restaurant', 'nexo-restaurant' ),
                    'href'      =>      '#',
                    'icon'      =>      'fa fa-cutlery',
                    'disable'   =>      true
                ],
                [
                    'title'     =>      __( 'Tables', 'nexo-restaurant' ),
                    'href'      =>      site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'tables' ]),
                    'disable'   =>      true
                ],
                [
                    'title'     =>      __( 'Areas', 'nexo-restaurant' ),
                    'href'      =>      site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'areas' ]),
                    'disable'   =>      true
                ],
                [
                    'title'     =>      __( 'Rooms', 'nexo-restaurant' ),
                    'href'      =>      site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'rooms' ]),
                    'disable'   =>      true
                ],
                [
                    'title'     =>      __( 'Kitchens', 'nexo-restaurant' ),
                    'href'      =>      site_url( [ 'dashboard', store_slug(), 'nexo-restaurant', 'kitchens', 'lists' ] )
                ]
            ]);
        }

        if( @$menus[ 'nexo_settings' ] ) {
            $menus[ 'nexo_settings' ][]     =   [
                'title'         =>      __( 'Restaurant Settings', 'nexo-restaurant' ),
                'href'          =>      site_url([ 'dashboard', store_slug(), 'nexo-restaurant', 'settings' ])
            ];
        }
        return $menus;
    }

    /**
     *  Add Cart Buttons
     *  @param
     *  @return
    **/

    public function cart_buttons( $menus )
    {
        $menus[ 'restaurant' ]      =   '<button class="btn btn-default" ng-controller="selectTableCTRL" type="button" ng-click="openTableSelection()"><i class="fa fa-cutlery"></i>
            {{ orderType }}
        </button>';

        return $menus;
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config[ 'nexo-restaurant-table-status' ]       =   [
    'in_use'        =>      __( 'In Use', 'nexo-restaurant' ),
    'out_of_use'    =>      __( 'Out of use', 'nexo-restaurant' ),
    'available'     =>      __( 'Available', 'nexo-restaurant' ),
    'reserved'      =>      __( 'Reserved', 'nexo-restaurant' )
];

$config[ 'nexo-restaurant-table-status-for-crud' ]  =   [
    'out_of_use'    =>      __( 'Out of use', 'nexo-restaurant' ),
    'available'     =>      __( 'Available', 'nexo-restaurant' )
];

// To be removed
$config[ 'nexo-restaurant-table-status-for-crud' ]       =   [
    'in_use'        =>      __( 'In Use', 'nexo-restaurant' ),
    'out_of_use'    =>      __( 'Out of use', 'nexo-restaurant' ),
    'available'     =>      __( 'Available', 'nexo-restaurant' ),
    'reserved'      =>      __( 'Reserved', 'nexo-restaurant' )
];

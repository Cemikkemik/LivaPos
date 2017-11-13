<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config[ 'gastro-table-status' ]       =   [
    'in_use'        =>      __( 'In Use', 'gastro' ),
    'out_of_use'    =>      __( 'Out of use', 'gastro' ),
    'available'     =>      __( 'Available', 'gastro' ),
    'reserved'      =>      __( 'Reserved', 'gastro' )
];

$config[ 'gastro-table-status-for-crud' ]  =   [
    'out_of_use'    =>      __( 'Out of use', 'gastro' ),
    'available'     =>      __( 'Available', 'gastro' )
];

// To be removed
$config[ 'gastro-table-status-for-crud' ]       =   [
    'in_use'        =>      __( 'In Use', 'gastro' ),
    'out_of_use'    =>      __( 'Out of use', 'gastro' ),
    'available'     =>      __( 'Available', 'gastro' ),
    'reserved'      =>      __( 'Reserved', 'gastro' )
];

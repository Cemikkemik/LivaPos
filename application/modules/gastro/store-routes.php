<?php
include( dirname( __FILE__ ) . '/route-config.php' );

global $StoreRoutes;

foreach( $crudRoutes as $crudRoute ) {
    $StoreRoutes->match([ 'get', 'post' ], $crudRoute[0], $crudRoute[1] );
}

foreach( $getRoutes as $getRoute ) {
    $StoreRoutes->get( $getRoute[0], $getRoute[1] );
}
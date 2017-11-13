<?php
global $Route;

include( dirname( __FILE__ ) . '/route-config.php' );

foreach( $crudRoutes as $crudRoute ) {
    $Route->match([ 'get', 'post' ], $crudRoute[0], $crudRoute[1] );
}

foreach( $getRoutes as $getRoute ) {
    $Route->get( $getRoute[0], $getRoute[1] );
}
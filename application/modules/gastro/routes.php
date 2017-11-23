<?php
global $Routes;

include( dirname( __FILE__ ) . '/route-config.php' );

foreach( $crudRoutes as $crudRoute ) {
    $Routes->match([ 'get', 'post' ], $crudRoute[0], $crudRoute[1] );
}

foreach( $getRoutes as $getRoute ) {
    $Routes->get( $getRoute[0], $getRoute[1] );
}
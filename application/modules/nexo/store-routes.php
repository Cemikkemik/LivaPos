<?php
include( dirname( __FILE__ ) . '/routes-array.php' );

global $StoreRoutes;
// Dashboard Index
$StoreRoutes->get( '', 'NexoDashboardController@index' );

foreach( $getRoutes as $getRoute ) {
    // remove "nexo/" prefix
    $StoreRoutes->get( $getRoute[0], $getRoute[1] );
}

// Looping get post
foreach( $getPost as $_getPost ) {
    // var_dump( $_getPost[0], substr( $_getPost[1], 5 ) );
    $StoreRoutes->match( $_getPost[0], $_getPost[1], $_getPost[2] );
}

// Post Routes
foreach( $postRoutes as $postRoute ) {
    // var_dump( substr( $postRoute[0], 5 ) );
    $StoreRoutes->post( $postRoute[0], $postRoute[1] );
}
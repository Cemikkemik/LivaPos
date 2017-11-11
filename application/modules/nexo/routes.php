<?php
global $Route;

include( dirname( __FILE__ ) . '/routes-array.php' );

$Route->get( 'nexo/templates/customers-main', 'NexoTemplateController@customers_main' );
$Route->get( 'nexo/templates/customers-form', 'NexoTemplateController@customers_form' );
$Route->get( 'nexo/templates/shippings', 'NexoTemplateController@shippings' );
$Route->match([ 'get', 'post' ], 'stores/{id}/{any}', 'NexoStoreController@stores' )->where([ 
    'id'      => '[0-9]+', 
    'any'     =>   '.*' 
]);

foreach( $getRoutes as $r ) {
    $Route->get( $r[0], $r[1] );
}

foreach( $getPost as $r ) {
    $Route->match( $r[0], $r[1], $r[2] );
}

foreach( $postRoutes as $r ) {
    $Route->post( $r[0], $r[1] );
}
<?php
global $Routes;

include( dirname( __FILE__ ) . '/routes-array.php' );

<<<<<<< HEAD
$Route->get( 'nexo/templates/customers-main', 'NexoTemplateController@customers_main' );
$Route->get( 'nexo/templates/customers-form', 'NexoTemplateController@customers_form' );
$Route->get( 'nexo/templates/shippings', 'NexoTemplateController@shippings' );
$Route->match([ 'get', 'post' ], 'stores/{id}/{any}', 'NexoStoreController@stores' )->where([ 
=======
$Routes->get( 'nexo/templates/customers-main', 'NexoTemplateController@customers_main' );
$Routes->get( 'nexo/templates/customers-form', 'NexoTemplateController@customers_form' );
$Routes->get( 'nexo/templates/shippings', 'NexoTemplateController@shippings' );
$Routes->get( 'nexo/template/{name}', 'NexoTemplateController@template' );
$Routes->match([ 'get', 'post' ], 'stores/{id}/{any}', 'NexoStoreController@stores' )->where([ 
>>>>>>> 652b558... Update
    'id'      => '[0-9]+', 
    'any'     =>   '.*' 
]);

foreach( $getRoutes as $r ) {
    $Routes->get( $r[0], $r[1] );
}

foreach( $getPost as $r ) {
    $Routes->match( $r[0], $r[1], $r[2] );
}

foreach( $postRoutes as $r ) {
    $Routes->post( $r[0], $r[1] );
}
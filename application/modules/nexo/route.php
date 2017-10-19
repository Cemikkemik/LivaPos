<?php
global $Route;

$Route->get( 'nexo/about', 'NexoAboutController@index' );
$Route->get( 'nexo/pos', 'NexoRegistersController@__use' );
$Route->get( 'nexo/settings/{page}', 'NexoSettingsController@settings' );
$Route->get( 'nexo/settings', 'NexoSettingsController@settings' );
$Route->get( 'nexo/orders', 'NexoCommandesController@lists' );

$Route->get( 'nexo/customers', 'NexoCustomersController@lists' );
$Route->get( 'nexo/customers/add', 'NexoCustomersController@add' );
$Route->get( 'nexo/customers/edit/{id}', 'NexoCustomersController@edit' );
$Route->get( 'nexo/customers/delete/{id}', 'NexoCustomersController@lists' );
$Route->post( 'nexo/customers/{param}', 'NexoCustomersController@lists' );

$Route->get( 'nexo/customers-groups/add', 'NexoCustomersController@edit' );
$Route->get( 'nexo/customers-groups/edit/{id}', 'NexoCustomersController@edit' );

$Route->get( 'nexo/templates/customers-main', 'NexoTemplateController@customers_main' );
$Route->get( 'nexo/templates/customers-form', 'NexoTemplateController@customers_form' );

$Route->get( 'nexo/stores', 'NexoStoreController@lists' );
$Route->get( 'nexo/stores/add', 'NexoStoreController@add' );
$Route->get( 'nexo/stores/all', 'NexoStoreController@all' );
$Route->get( 'nexo/stores/{param}/{id?}', 'NexoStoreController@lists' );
$Route->get( 'stores/{id}/{any}', 'NexoStoreController@stores' )->where([ 
     'id'      => '[0-9]+', 
     'any'     =>   '.*' 
]);
$Route->match([ 'post' ], '/nexo/stores/{param}', 'NexoStoreController@lists' );

$this->events->add_action( 'store_route', function( $routes ) {
     $routes[] 	=	register_store_route( '', 'NexoDashboardController@index' );
     $routes[] 	=	register_store_route( 'pos', 'NexoRegistersController@__use' );
     $routes[] 	=	register_store_route( 'settings', 'NexoSettingsController@settings' );
     $routes[] 	=	register_store_route( 'settings/{param?}', 'NexoSettingsController@settings' );
     $routes[] 	=	register_store_route( 'about', 'NexoAboutController@index' );
     return $routes;
});

// $Route->get( 'nexo/about', 'NexoAboutController@index' );
// $Route->get( 'nexo/about', 'NexoAboutController@index' );
// $Route->get( 'nexo/about', 'NexoAboutController@index' );
// $Route->get( 'nexo/about', 'NexoAboutController@index' );
// $Route->get( 'nexo/about', 'NexoAboutController@index' );
// $Route->get( 'nexo/about', 'NexoAboutController@index' );
// $Route->get( 'nexo/about', 'NexoAboutController@index' );
// $Route->get( 'nexo/about', 'NexoAboutController@index' );
// $Route->get( 'nexo/about', 'NexoAboutController@index' );
// $Route->get( 'nexo/about', 'NexoAboutController@index' );
// $Route->get( 'nexo/about', 'NexoAboutController@index' );
// $Route->get( 'nexo/about', 'NexoAboutController@index' );
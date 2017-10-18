<?php
global $Route;

$Route->get( 'nexo/about', 'NexoAboutController@index' );
$Route->get( 'nexo/pos', 'NexoRegistersController@__use' );
$Route->get( 'nexo/settings/{page}', 'NexoSettingsController@settings' );
$Route->get( 'nexo/settings', 'NexoSettingsController@settings' );
$Route->get( 'nexo/sales', 'NexoCommandesController@lists' );

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
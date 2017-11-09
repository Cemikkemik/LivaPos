<?php
global $Route;

$Route->get( 'nexo/about', 'NexoAboutController@index' );
$Route->get( 'nexo/coupons/{param?}/{id?}', 'NexoCouponsController@lists' );
$Route->get( 'nexo/customers', 'NexoCustomersController@lists' );
$Route->get( 'nexo/customers/add', 'NexoCustomersController@add' );
$Route->get( 'nexo/customers/edit/{id}', 'NexoCustomersController@edit' );
$Route->get( 'nexo/customers/delete/{id}', 'NexoCustomersController@lists' );
$Route->get( 'nexo/items/stock-adjustment/', 'NexoItemsController@supply' );
$Route->get( 'nexo/items/import/', 'NexoImportController@items' );
$Route->get( 'nexo/items/history/{barcode}', 'NexoItemsController@history' );
$Route->get( 'nexo/items/supply-history/{barcode}', 'NexoItemsController@supply' );
$Route->get( 'nexo/settings/home', 'NexoSettingsController@home' );
$Route->get( 'nexo/settings/checkout', 'NexoSettingsController@checkout' );
$Route->get( 'nexo/settings/items', 'NexoSettingsController@items' );
$Route->get( 'nexo/settings/customers', 'NexoSettingsController@customers' );
$Route->get( 'nexo/settings/email', 'NexoSettingsController@email' );
$Route->get( 'nexo/settings/payments-gateways', 'NexoSettingsController@payments' );
$Route->get( 'nexo/settings/reset', 'NexoSettingsController@reset' );
$Route->get( 'nexo/settings/invoices', 'NexoSettingsController@invoices' );
$Route->get( 'nexo/settings/keyboard', 'NexoSettingsController@keyboard' );
$Route->get( 'nexo/settings/providers', 'NexoSettingsController@providers' );
$Route->get( 'nexo/settings/orders', 'NexoSettingsController@orders' );
$Route->get( 'nexo/settings/stores', 'NexoSettingsController@stores' );
$Route->get( 'nexo/settings/stripe', 'NexoSettingsController@stripe' );
$Route->get( 'nexo/settings', 'NexoSettingsController@home' );
$Route->get( 'nexo/stores', 'NexoStoreController@lists' );
$Route->get( 'nexo/stores/add', 'NexoStoreController@add' );
$Route->get( 'nexo/stores/all', 'NexoStoreController@all' );
$Route->get( 'nexo/stores/{param}/{id?}', 'NexoStoreController@lists' );
$Route->get( 'nexo/supplies/', 'NexoSuppliesController@lists' );
$Route->get( 'nexo/supplies/add', 'NexoItemsController@add_supply' );
$Route->get( 'nexo/supplies/invoice/{shipping_id}', 'NexoSuppliesController@delivery_invoice' );
$Route->get( 'nexo/supplies/items/{shipping_id}', 'NexoSuppliesController@delivery_items' );
$Route->get( 'nexo/providers', 'NexoProvidersController@lists' );
$Route->get( 'nexo/providers/add', 'NexoProvidersController@add' );
$Route->get( 'nexo/orders', 'NexoCommandesController@lists' );
$Route->get( 'nexo/pos', 'NexoRegistersController@__use' );
$Route->get( 'nexo/templates/customers-main', 'NexoTemplateController@customers_main' );
$Route->get( 'nexo/templates/customers-form', 'NexoTemplateController@customers_form' );


$Route->match([ 'get', 'post' ], 'nexo/providers/{action?}/{id?}', 'NexoProvidersController@lists' );
$Route->match([ 'get', 'post' ], 'nexo/supplies/{action?}/{id?}', 'NexoSuppliesController@lists' );
$Route->match([ 'get', 'post' ], 'nexo/supplies/items/{shipping_id}/{action?}/{id?}', 'NexoSuppliesController@delivery_items' );
$Route->match([ 'get', 'post' ], 'nexo/items/{action?}/{id?}', 'NexoItemsController@lists' );
$Route->match([ 'get', 'post' ], 'nexo/items/supply-history/{barcode}/{action?}/{id?}', 'NexoItemsController@supply' );
$Route->match([ 'get', 'post' ], 'nexo/categories/{action?}/{id?}', 'NexoCategoriesController@lists' );
$Route->match([ 'get', 'post'], 'nexo/taxes/{param?}/{id?}', 'NexoTaxesController@index' );
$Route->match([ 'get', 'post' ], 'nexo/suppliers/{action?}/{id?}', 'NexoProvidersController@lists' );
$Route->match([ 'get', 'post'], 'nexo/customers/groups/{param?}/{id?}', 'NexoCustomersController@groups' );
$Route->match([ 'get', 'post' ], '/nexo/stores/{param}', 'NexoStoreController@lists' );


$Route->post( 'nexo/customers/{param}', 'NexoCustomersController@lists' );
$Route->post( 'nexo/reset', 'NexoResetController@index' );

$Route->get( 'stores/{id}/{any}', 'NexoStoreController@stores' )->where([ 
     'id'      => '[0-9]+', 
     'any'     =>   '.*' 
]);

$this->events->add_action( 'store_route', function( $routes ) {
     $routes[] 	=	register_store_route( '', 'NexoDashboardController@index' );
     $routes[] 	=	register_store_route( 'pos', 'NexoRegistersController@__use' );
     $routes[] 	=	register_store_route( 'settings', 'NexoSettingsController@settings' );
     $routes[] 	=	register_store_route( 'settings/{param?}', 'NexoSettingsController@settings' );
     $routes[] 	=	register_store_route( 'about', 'NexoAboutController@index' );
     return $routes;
});
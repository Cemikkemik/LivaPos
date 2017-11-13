<?php
global $Route;

$getRoutes  =   [];
$crudRoutes     =   [];

$getRoutes[]    =   [ '/gastro/revoke', 'RestaurantController@revoke'];
$getRoutes[]    =   [ '/gastro/callback', 'RestaurantController@callback'];
$getRoutes[]    =   [ '/gastro/settings', 'RestaurantController@settings'];
$getRoutes[]    =   [ '/gastro/templates/{name}', 'RestaurantController@templates'];
$crudRoutes[]   =   [ '/gastro/kitchens/{param?}/{id?}', 'KitchensController@kitchens' ];
$crudRoutes[]   =   [ '/gastro/kitchen-screen/{id?}', 'KitchensController@kitchenScreen' ];
$crudRoutes[]   =   [ '/gastro/waiters-screen/{id?}', 'KitchensController@waiterScreen' ];
$crudRoutes[]   =   [ '/gastro/tables/{param?}/{id?}', 'TablesController@tables' ];
$crudRoutes[]   =   [ '/gastro/areas/{param?}/{id?}', 'AreasController@areas' ];
$crudRoutes[]   =   [ '/gastro/modifiers-groups/{param?}/{id?}', 'ModifiersController@modifiers_groups' ];
$crudRoutes[]   =   [ '/gastro/modifiers/{param?}/{id?}', 'ModifiersController@modifiers' ];

foreach( $crudRoutes as $crudRoute ) {
    $Route->match([ 'get', 'post' ], $crudRoute[0], $crudRoute[1] );
}

foreach( $getRoutes as $getRoute ) {
    $Route->get( $getRoute[0], $getRoute[1] );
}
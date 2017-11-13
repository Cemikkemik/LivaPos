<?php
global $Route;

$definedRoutes  =   [];
$crudRoutes     =   [];

$definedRoutes[]    =   [ '/gastro/revoke', 'RestaurantController@revoke'];
$definedRoutes[]    =   [ '/gastro/callback', 'RestaurantController@callback'];
$definedRoutes[]    =   [ '/gastro/settings', 'RestaurantController@settings'];
$definedRoutes[]    =   [ '/gastro/templates', 'RestaurantController@templates'];
$crudRoutes[]       =   [ '/gastro/modifiers/{param?}/{id?}', 'ModifiersController@modifiers' ];
$crudRoutes[]       =   [ '/gastro/kitchens/{param?}/{id?}', 'KitchensController@kitchens' ];
$crudRoutes[]       =   [ '/gastro/tables/{param?}/{id?}', 'TablesController@tables' ];
$crudRoutes[]       =   [ '/gastro/areas/{param?}/{id?}', 'AreasController@areas' ];
$crudRoutes[]       =   [ '/gastro/modifiers-groups/{param?}/{id?}', 'ModifiersController@modifiers_groups' ];

foreach( $crudRoutes as $crudRoute ) {
    $Route->match([ 'get', 'post' ], $crudRoute[0], $crudRoute[1] );
}